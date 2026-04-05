<?php

namespace App\Http\Controllers\Instruktur;

use App\Http\Controllers\Controller;
use App\Models\Enrollment;
use App\Models\Tugas;
use App\Models\TugasIndividuSubmission;
use App\Models\TugasKelompok;
use App\Models\TugasKelompokAnggota;
use App\Notifications\TugasKelompokNotification;
use App\Services\AiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class TugasController extends Controller
{
    public function index(Request $request)
    {
        $instruktur = auth()->user()->instruktur()->with(['kelas.mataKuliah', 'kelas.periodeAkademik'])->firstOrFail();
        $kelasList = $instruktur->kelas->sortByDesc(fn($k) => $k->periodeAkademik?->created_at)->values();
        $selectedKelas = $kelasList->firstWhere('id', $request->integer('kelas_id')) ?? $kelasList->first();

        $tugasList    = collect();
        $mahasiswaList = collect();

        if ($selectedKelas) {
            $tugasList = Tugas::where('kelas_id', $selectedKelas->id)
                ->where('instruktur_id', $instruktur->id)
                ->with(['kelompok' => fn($q) => $q
                    ->withCount(['anggota as anggota_count' => fn($a) => $a->whereColumn('mahasiswa_id', '!=', 'tugas_kelompok.ketua_mahasiswa_id')])
                    ->with('ketua')
                    ->select('id','tugas_id','nama_kelompok','ketua_mahasiswa_id','status_submit','nilai_kelompok')
                ])
                ->orderByDesc('created_at')
                ->get();

            $mahasiswaList = \App\Models\Enrollment::where('kelas_id', $selectedKelas->id)
                ->where('status', 'Aktif')
                ->with('mahasiswa')
                ->get()
                ->pluck('mahasiswa')
                ->filter()
                ->values();
        }

        return view('instruktur.tugas.index', compact(
            'instruktur',
            'kelasList',
            'selectedKelas',
            'tugasList',
            'mahasiswaList'
        ));
    }

    public function store(Request $request)
    {
        $instruktur = auth()->user()->instruktur()->firstOrFail();

        $data = $request->validate([
            'judul'    => 'required|string|max:200',
            'deskripsi'=> 'nullable|string',
            'soal'     => 'nullable|string',
            'kelas_id' => 'required|integer|exists:kelas,id',
            'deadline' => 'nullable|date',
            'status'   => 'required|in:draft,aktif',
            'tipe'     => 'required|in:kelompok,individu',
        ]);

        $tugas = Tugas::create([
            'instruktur_id' => $instruktur->id,
            'kelas_id'      => $data['kelas_id'],
            'judul'         => $data['judul'],
            'deskripsi'     => $data['deskripsi'] ?? null,
            'soal'          => $data['soal'] ?? null,
            'tipe'          => $data['tipe'],
            'deadline'      => $data['deadline'] ?? null,
            'status'        => $data['status'],
        ]);

        return response()->json(['message' => 'Tugas berhasil dibuat.', 'tugas' => $tugas], 201);
    }

    public function update(Request $request, Tugas $tugas)
    {
        $instruktur = auth()->user()->instruktur()->firstOrFail();

        if ($tugas->instruktur_id !== $instruktur->id) {
            return response()->json(['message' => 'Akses ditolak.'], 403);
        }

        $data = $request->validate([
            'judul'    => 'required|string|max:200',
            'deskripsi'=> 'nullable|string',
            'soal'     => 'nullable|string',
            'kelas_id' => 'required|integer|exists:kelas,id',
            'deadline' => 'nullable|date',
            'status'   => 'required|in:draft,aktif,selesai',
        ]);

        $tugas->update([
            'kelas_id'  => $data['kelas_id'],
            'judul'     => $data['judul'],
            'deskripsi' => $data['deskripsi'] ?? null,
            'soal'      => $data['soal'] ?? null,
            'deadline'  => $data['deadline'] ?? null,
            'status'    => $data['status'],
        ]);

        return response()->json(['message' => 'Tugas berhasil diperbarui.', 'tugas' => $tugas]);
    }

    public function destroy(Tugas $tugas)
    {
        $instruktur = auth()->user()->instruktur()->firstOrFail();

        if ($tugas->instruktur_id !== $instruktur->id) {
            return response()->json(['message' => 'Akses ditolak.'], 403);
        }

        $tugas->delete();

        return response()->json(['message' => 'Tugas berhasil dihapus.']);
    }

    public function storeKelompok(Request $request, Tugas $tugas)
    {
        $instruktur = auth()->user()->instruktur()->firstOrFail();

        if ($tugas->instruktur_id !== $instruktur->id) {
            return response()->json(['message' => 'Akses ditolak.'], 403);
        }

        $data = $request->validate([
            'nama_kelompok'      => 'required|string|max:100',
            'ketua_mahasiswa_id' => 'nullable|integer|exists:mahasiswa,id',
        ]);

        $kelompok = TugasKelompok::create([
            'tugas_id'           => $tugas->id,
            'nama_kelompok'      => $data['nama_kelompok'],
            'ketua_mahasiswa_id' => $data['ketua_mahasiswa_id'] ?? null,
        ]);

        $kelompok->load('ketua');
        $kelompok->loadCount('anggota');

        // Notifikasi ke ketua jika ditentukan
        $this->notifyKetua($kelompok, $tugas);

        return response()->json(['message' => 'Kelompok berhasil dibuat.', 'kelompok' => $kelompok], 201);
    }

    public function updateKelompok(Request $request, Tugas $tugas, TugasKelompok $kelompok)
    {
        $instruktur = auth()->user()->instruktur()->firstOrFail();

        if ($tugas->instruktur_id !== $instruktur->id || $kelompok->tugas_id !== $tugas->id) {
            return response()->json(['message' => 'Akses ditolak.'], 403);
        }

        $data = $request->validate([
            'nama_kelompok'      => 'required|string|max:100',
            'ketua_mahasiswa_id' => 'nullable|integer|exists:mahasiswa,id',
        ]);

        $oldKetua = $kelompok->ketua_mahasiswa_id;

        $kelompok->update([
            'nama_kelompok'      => $data['nama_kelompok'],
            'ketua_mahasiswa_id' => $data['ketua_mahasiswa_id'] ?? null,
        ]);

        $kelompok->load('ketua');
        $kelompok->loadCount('anggota');

        // Notifikasi jika ketua berubah
        if (($data['ketua_mahasiswa_id'] ?? null) && $data['ketua_mahasiswa_id'] != $oldKetua) {
            $this->notifyKetua($kelompok, $tugas);
        }

        return response()->json(['message' => 'Kelompok berhasil diperbarui.', 'kelompok' => $kelompok]);
    }

    private function notifyKetua(TugasKelompok $kelompok, Tugas $tugas): void
    {
        $mhs = $kelompok->ketua;
        if (!$mhs) return;
        $user = $mhs->user;
        if (!$user) return;

        $user->notify(new TugasKelompokNotification(
            title: 'Ditunjuk sebagai Ketua Kelompok',
            body:  "Kamu ditunjuk sebagai ketua \"{$kelompok->nama_kelompok}\" pada tugas \"{$tugas->judul}\".",
            url:   '/mahasiswa/tugas',
            icon:  'fa-crown',
            color: 'amber',
        ));
    }

    public function destroyKelompok(Tugas $tugas, TugasKelompok $kelompok)
    {
        $instruktur = auth()->user()->instruktur()->firstOrFail();

        if ($tugas->instruktur_id !== $instruktur->id || $kelompok->tugas_id !== $tugas->id) {
            return response()->json(['message' => 'Akses ditolak.'], 403);
        }

        $kelompok->delete();

        return response()->json(['message' => 'Kelompok berhasil dihapus.']);
    }

    /**
     * Instruktur lihat detail submission kelompok — JSON untuk modal.
     */
    public function showSubmission(Tugas $tugas, TugasKelompok $kelompok)
    {
        $instruktur = auth()->user()->instruktur()->firstOrFail();
        abort_if($tugas->instruktur_id !== $instruktur->id, 403);
        abort_if($kelompok->tugas_id !== $tugas->id, 404);

        $kelompok->load(['ketua', 'anggota.mahasiswa']);

        // Anggota tanpa ketua untuk payload
        $anggota = $kelompok->anggota
            ->where('mahasiswa_id', '!=', $kelompok->ketua_mahasiswa_id)
            ->values()
            ->map(fn($a) => [
                'id'             => $a->id,
                'nama'           => $a->mahasiswa?->nama ?? '—',
                'nim'            => $a->mahasiswa?->nim  ?? '—',
                'topik'          => $a->topik,
                'konten'         => $a->konten,
                'status_submit'  => $a->status_submit,
                'submitted_at'   => $a->submitted_at?->format('d M Y, H:i'),
                'nilai'          => $a->nilai,
                'catatan'        => $a->catatan_instruktur,
                'is_ketua'       => false,
            ]);

        // Tambahkan entry ketua jika ada
        $ketuaEntry = $kelompok->anggota->firstWhere('mahasiswa_id', $kelompok->ketua_mahasiswa_id);
        if ($ketuaEntry) {
            $anggota = $anggota->prepend([
                'id'            => $ketuaEntry->id,
                'nama'          => $ketuaEntry->mahasiswa?->nama ?? '—',
                'nim'           => $ketuaEntry->mahasiswa?->nim  ?? '—',
                'topik'         => $ketuaEntry->topik,
                'konten'        => $ketuaEntry->konten,
                'status_submit' => $ketuaEntry->status_submit,
                'submitted_at'  => $ketuaEntry->submitted_at?->format('d M Y, H:i'),
                'nilai'         => $ketuaEntry->nilai,
                'catatan'       => $ketuaEntry->catatan_instruktur,
                'is_ketua'      => true,
            ]);
        }

        return response()->json([
            'kelompok' => [
                'id'               => $kelompok->id,
                'nama_kelompok'    => $kelompok->nama_kelompok,
                'ketua_nama'       => $kelompok->ketua?->nama,
                'status_submit'    => $kelompok->status_submit,
                'submitted_at'     => $kelompok->submitted_at?->format('d M Y, H:i'),
                'konten_final'     => $kelompok->konten_final,
                'pdf_url'          => $kelompok->pdf_path ? \Illuminate\Support\Facades\Storage::url($kelompok->pdf_path) : null,
                'nilai_kelompok'   => $kelompok->nilai_kelompok,
                'catatan_kelompok' => $kelompok->catatan_kelompok,
            ],
            'tugas' => [
                'id'        => $tugas->id,
                'judul'     => $tugas->judul,
                'deskripsi' => $tugas->deskripsi,
            ],
            'anggota' => $anggota->values(),
        ]);
    }

    /**
     * Instruktur beri penilaian kelompok + individu.
     */
    public function grade(Request $request, Tugas $tugas, TugasKelompok $kelompok)
    {
        $instruktur = auth()->user()->instruktur()->firstOrFail();
        abort_if($tugas->instruktur_id !== $instruktur->id, 403);
        abort_if($kelompok->tugas_id !== $tugas->id, 404);

        $data = $request->validate([
            'nilai_kelompok'    => 'nullable|integer|min:0|max:100',
            'catatan_kelompok'  => 'nullable|string|max:1000',
            'anggota'           => 'nullable|array',
            'anggota.*.id'      => 'required|integer|exists:tugas_kelompok_anggota,id',
            'anggota.*.nilai'   => 'nullable|integer|min:0|max:100',
            'anggota.*.catatan' => 'nullable|string|max:500',
        ]);

        $kelompok->update([
            'nilai_kelompok'   => $data['nilai_kelompok'] ?? null,
            'catatan_kelompok' => $data['catatan_kelompok'] ?? null,
        ]);

        foreach ($data['anggota'] ?? [] as $item) {
            TugasKelompokAnggota::where('id', $item['id'])
                ->where('kelompok_id', $kelompok->id)
                ->update([
                    'nilai'              => $item['nilai'] ?? null,
                    'catatan_instruktur' => $item['catatan'] ?? null,
                ]);
        }

        return response()->json(['message' => 'Penilaian berhasil disimpan.']);
    }

    /**
     * Nilai otomatis berbasis AI untuk satu anggota.
     */
    public function aiGrade(Request $request, Tugas $tugas, TugasKelompok $kelompok)
    {
        $instruktur = auth()->user()->instruktur()->firstOrFail();
        abort_if($tugas->instruktur_id !== $instruktur->id, 403);
        abort_if($kelompok->tugas_id !== $tugas->id, 404);

        $data = $request->validate([
            'anggota_id' => 'required|integer|exists:tugas_kelompok_anggota,id',
        ]);

        $anggota = TugasKelompokAnggota::where('id', $data['anggota_id'])
            ->where('kelompok_id', $kelompok->id)
            ->with('mahasiswa')
            ->firstOrFail();

        if (!$anggota->konten) {
            return response()->json(['error' => 'Anggota belum memiliki konten yang bisa dinilai.'], 422);
        }

        // Strip HTML, ambil max 3000 karakter
        $kontenBersih = mb_substr(strip_tags($anggota->konten), 0, 3000);
        $finalBersih  = $kelompok->konten_final
            ? mb_substr(strip_tags($kelompok->konten_final), 0, 1500)
            : null;

        $systemPrompt = <<<PROMPT
Kamu adalah asisten penilaian akademik. Berikan penilaian objektif terhadap tugas mahasiswa.

Tugas: {$tugas->judul}
{$tugas->deskripsi}

Topik anggota: {$anggota->topik}

Berikan respons HANYA dalam format JSON berikut (tanpa markdown, tanpa penjelasan lain):
{
  "nilai": <angka 0-100>,
  "catatan": "<umpan balik singkat dalam Bahasa Indonesia, maks 200 kata>"
}
PROMPT;

        $userMessage = "Konten tugas:\n\n{$kontenBersih}";
        if ($finalBersih) {
            $userMessage .= "\n\nDokumen final kelompok (konteks):\n\n{$finalBersih}";
        }

        $ai = app(AiService::class);
        $result = $ai->chat(
            [['role' => 'user', 'content' => $userMessage]],
            $systemPrompt
        );

        if (isset($result['error'])) {
            return response()->json(['error' => $result['error']], 502);
        }

        // Parse JSON dari AI
        $raw = trim($result['reply']);
        // Hapus code fence jika ada
        $raw = preg_replace('/^```[a-z]*\n?|\n?```$/i', '', $raw);
        $parsed = json_decode($raw, true);

        if (!$parsed || !isset($parsed['nilai'])) {
            return response()->json(['error' => 'AI memberikan respons yang tidak valid. Coba lagi.'], 502);
        }

        return response()->json([
            'nilai'   => max(0, min(100, (int) $parsed['nilai'])),
            'catatan' => $parsed['catatan'] ?? '',
        ]);
    }

    /**
     * Upload gambar untuk konten soal tugas individu.
     */
    public function uploadSoalGambar(Request $request)
    {
        $request->validate(['gambar' => 'required|image|max:4096']);
        $path = $request->file('gambar')->store('tugas-soal-gambar', 'public');
        return response()->json(['url' => Storage::url($path)]);
    }

    /**
     * List semua submission tugas individu (JSON untuk modal).
     */
    public function showIndividuSubmissions(Tugas $tugas)
    {
        $instruktur = auth()->user()->instruktur()->firstOrFail();
        abort_if($tugas->instruktur_id !== $instruktur->id, 403);
        abort_if($tugas->tipe !== 'individu', 422);

        // Semua mahasiswa aktif di kelas
        $mahasiswaList = Enrollment::where('kelas_id', $tugas->kelas_id)
            ->where('status', 'Aktif')
            ->with('mahasiswa')
            ->get()
            ->pluck('mahasiswa')
            ->filter()
            ->values();

        // Semua submission yang ada (keyed by mahasiswa_id)
        $submissions = TugasIndividuSubmission::where('tugas_id', $tugas->id)
            ->get()
            ->keyBy('mahasiswa_id');

        $data = $mahasiswaList->map(function ($mhs) use ($submissions) {
            $sub = $submissions->get($mhs->id);
            return [
                'mahasiswa_id'      => $mhs->id,
                'nama'              => $mhs->nama,
                'nim'               => $mhs->nim ?? '—',
                'submission_id'     => $sub?->id,
                'status_submit'     => $sub?->status_submit ?? 'belum',
                'submitted_at'      => $sub?->submitted_at?->format('d M Y, H:i'),
                'pdf_url'           => $sub?->pdf_path ? Storage::url($sub->pdf_path) : null,
                'nilai'             => $sub?->nilai,
                'catatan_instruktur'=> $sub?->catatan_instruktur,
                'catatan_ai'        => $sub?->catatan_ai,
            ];
        });

        $submittedCount = $data->where('status_submit', 'submitted')->count();

        return response()->json([
            'tugas' => [
                'id'        => $tugas->id,
                'judul'     => $tugas->judul,
                'soal'      => $tugas->soal,
                'deskripsi' => $tugas->deskripsi,
            ],
            'submissions'    => $data->values(),
            'total'          => $data->count(),
            'submitted_count'=> $submittedCount,
        ]);
    }

    /**
     * Simpan penilaian untuk semua (atau sebagian) mahasiswa tugas individu.
     */
    public function gradeAll(Request $request, Tugas $tugas)
    {
        $instruktur = auth()->user()->instruktur()->firstOrFail();
        abort_if($tugas->instruktur_id !== $instruktur->id, 403);
        abort_if($tugas->tipe !== 'individu', 422);

        $data = $request->validate([
            'grades'             => 'required|array',
            'grades.*.mahasiswa_id' => 'required|integer|exists:mahasiswa,id',
            'grades.*.nilai'        => 'nullable|integer|min:0|max:100',
            'grades.*.catatan'      => 'nullable|string|max:1000',
        ]);

        foreach ($data['grades'] as $item) {
            TugasIndividuSubmission::updateOrCreate(
                ['tugas_id' => $tugas->id, 'mahasiswa_id' => $item['mahasiswa_id']],
                [
                    'nilai'             => $item['nilai'] ?? null,
                    'catatan_instruktur'=> $item['catatan'] ?? null,
                ]
            );
        }

        return response()->json(['message' => 'Penilaian berhasil disimpan.']);
    }

    /**
     * Ekstrak teks dari PDF menggunakan smalot/pdfparser.
     */
    private function extractPdfText(string $storagePath): string
    {
        try {
            $fullPath = Storage::disk('public')->path($storagePath);
            $parser   = new \Smalot\PdfParser\Parser();
            $pdf      = $parser->parseFile($fullPath);
            $text     = $pdf->getText();
            // Bersihkan whitespace berlebih
            $text = preg_replace('/\s+/', ' ', $text);
            return mb_substr(trim($text), 0, 4000);
        } catch (\Throwable $e) {
            return '';
        }
    }

    /**
     * Hitung similarity sederhana antara dua string (Jaccard pada kata).
     */
    private function similarityScore(string $a, string $b): float
    {
        if (!$a || !$b) return 0.0;
        $wordsA = array_unique(str_word_count(strtolower($a), 1));
        $wordsB = array_unique(str_word_count(strtolower($b), 1));
        $intersect = count(array_intersect($wordsA, $wordsB));
        $union     = count(array_unique(array_merge($wordsA, $wordsB)));
        return $union > 0 ? round($intersect / $union, 2) : 0.0;
    }

    /**
     * AI grading untuk SEMUA submission tugas individu sekaligus.
     * - Ekstrak teks PDF tiap mahasiswa
     * - Deteksi tingkat kesamaan antar jawaban
     * - Nilai + catatan detail per mahasiswa dari AI
     */
    public function aiGradeIndividu(Request $request, Tugas $tugas)
    {
        $instruktur = auth()->user()->instruktur()->firstOrFail();
        abort_if($tugas->instruktur_id !== $instruktur->id, 403);
        abort_if($tugas->tipe !== 'individu', 422);

        $submissions = TugasIndividuSubmission::where('tugas_id', $tugas->id)
            ->where('status_submit', 'submitted')
            ->with('mahasiswa')
            ->get();

        if ($submissions->isEmpty()) {
            return response()->json(['error' => 'Belum ada submission yang dikumpulkan.'], 422);
        }

        $soalBersih = mb_substr(strip_tags($tugas->soal ?? $tugas->deskripsi ?? '—'), 0, 1500);

        // ── 1. Ekstrak teks PDF tiap mahasiswa ──────────────────────
        $pdfTexts = [];
        foreach ($submissions as $sub) {
            $pdfTexts[$sub->mahasiswa_id] = $sub->pdf_path
                ? $this->extractPdfText($sub->pdf_path)
                : '';
        }

        // ── 2. Deteksi kesamaan antar pasangan ──────────────────────
        $similarityFlags = []; // mahasiswa_id => [{other_nama, score}]
        $subList = $submissions->values();
        for ($i = 0; $i < $subList->count(); $i++) {
            for ($j = $i + 1; $j < $subList->count(); $j++) {
                $a = $subList[$i];
                $b = $subList[$j];
                $score = $this->similarityScore(
                    $pdfTexts[$a->mahasiswa_id] ?? '',
                    $pdfTexts[$b->mahasiswa_id] ?? ''
                );
                if ($score >= 0.6) { // threshold 60% kesamaan kata
                    $similarityFlags[$a->mahasiswa_id][] = ['nama' => $b->mahasiswa->nama, 'score' => $score];
                    $similarityFlags[$b->mahasiswa_id][] = ['nama' => $a->mahasiswa->nama, 'score' => $score];
                }
            }
        }

        // ── 3. Susun payload untuk AI ───────────────────────────────
        $jawabanList = $submissions->map(function ($sub) use ($pdfTexts, $similarityFlags) {
            $teks  = $pdfTexts[$sub->mahasiswa_id] ?? '';
            $flags = $similarityFlags[$sub->mahasiswa_id] ?? [];
            $flagStr = '';
            foreach ($flags as $f) {
                $pct = (int)($f['score'] * 100);
                $flagStr .= " [MIRIP dengan {$f['nama']}: {$pct}%]";
            }
            return "### {$sub->mahasiswa->nama} (NIM: {$sub->mahasiswa->nim}){$flagStr}\n"
                . ($teks ?: '[PDF tidak dapat dibaca]');
        })->implode("\n\n---\n\n");

        $systemPrompt = <<<PROMPT
Kamu adalah dosen penilai akademik yang teliti. Nilailah jawaban tugas berikut secara objektif dan detail.

SOAL/PERTANYAAN TUGAS:
{$soalBersih}

INSTRUKSI PENILAIAN:
1. Bandingkan setiap jawaban dengan soal yang diberikan
2. Identifikasi bagian-bagian jawaban yang kurang lengkap atau tidak relevan dengan soal
3. Jika dua jawaban ditandai MIRIP, sebutkan secara eksplisit dalam catatan
4. Berikan nilai 0-100 berdasarkan: kelengkapan jawaban, kesesuaian dengan soal, kedalaman analisis
5. Catatan harus spesifik: sebutkan bagian mana yang kurang, bukan hanya "bagus" atau "kurang"

Berikan respons HANYA dalam format JSON berikut (tanpa markdown):
{
  "results": [
    {
      "nama": "<nama mahasiswa>",
      "nilai": <0-100>,
      "catatan": "<catatan detail: bagian yang kurang, kesesuaian topik, saran perbaikan — min 2 kalimat>",
      "flag_duplikat": <true/false>,
      "bagian_kurang": ["<poin 1>", "<poin 2>"]
    }
  ],
  "catatan_umum": "<ringkasan keseluruhan: tren umum, potensi kecurangan, saran instruktur>"
}
PROMPT;

        $ai = app(AiService::class);
        $result = $ai->chat(
            [['role' => 'user', 'content' => "Berikut jawaban para mahasiswa:\n\n{$jawabanList}"]],
            $systemPrompt
        );

        if (isset($result['error'])) {
            return response()->json(['error' => $result['error']], 502);
        }

        $raw = trim($result['reply']);
        $raw = preg_replace('/^```[a-z]*\n?|\n?```$/i', '', $raw);
        $parsed = json_decode($raw, true);

        if (!$parsed || !isset($parsed['results'])) {
            return response()->json(['error' => 'AI memberikan respons yang tidak valid.'], 502);
        }

        // ── 4. Map hasil AI ke mahasiswa_id ─────────────────────────
        $namaMap = $submissions->keyBy(fn($s) => $s->mahasiswa->nama);
        $grades  = [];
        foreach ($parsed['results'] as $item) {
            $sub = $namaMap->get($item['nama']);
            if (!$sub) continue;

            $bagianKurang = isset($item['bagian_kurang']) && is_array($item['bagian_kurang'])
                ? implode('; ', $item['bagian_kurang'])
                : '';
            $catatanAi = $item['catatan'] ?? '';
            if ($bagianKurang) {
                $catatanAi .= "\n\nBagian yang perlu diperbaiki: " . $bagianKurang;
            }
            // Tambahkan info similarity ke catatan jika ada
            if (!empty($similarityFlags[$sub->mahasiswa_id])) {
                foreach ($similarityFlags[$sub->mahasiswa_id] as $f) {
                    $pct = (int)($f['score'] * 100);
                    $catatanAi .= "\n⚠ Tingkat kesamaan dengan {$f['nama']}: {$pct}%";
                }
            }

            $grades[] = [
                'submission_id' => $sub->id,
                'mahasiswa_id'  => $sub->mahasiswa_id,
                'nilai'         => max(0, min(100, (int)($item['nilai'] ?? 70))),
                'catatan_ai'    => trim($catatanAi),
                'flag_duplikat' => (bool)($item['flag_duplikat'] ?? false)
                                    || !empty($similarityFlags[$sub->mahasiswa_id]),
            ];
        }

        return response()->json([
            'grades'       => $grades,
            'catatan_umum' => $parsed['catatan_umum'] ?? '',
        ]);
    }
}
