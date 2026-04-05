<?php

namespace App\Http\Controllers\Instruktur;

use App\Http\Controllers\Controller;
use App\Models\BankSoal;
use App\Models\Instruktur;
use App\Models\Kelas;
use App\Models\Mahasiswa;
use App\Models\Ujian;
use App\Models\UjianJawaban;
use App\Models\UjianSesi;
use App\Models\UjianPelanggaran;
use App\Services\AiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class UjianController extends Controller
{
    private function getInstruktur(): Instruktur
    {
        return Instruktur::where('user_id', Auth::id())->firstOrFail();
    }

    /**
     * Daftar ujian per kelas.
     */
    public function index(Request $request)
    {
        $instruktur = $this->getInstruktur();

        $kelasList = $instruktur->kelas()
            ->with('mataKuliah', 'periodeAkademik')
            ->orderBy('id', 'desc')
            ->get();

        $kelasId = $request->query('kelas');
        $kelas   = $kelasId ? $kelasList->firstWhere('id', $kelasId) : $kelasList->first();

        $ujianList = collect();
        if ($kelas) {
            $ujianList = Ujian::with('soalPool')
                ->where('instruktur_id', $instruktur->id)
                ->where('kelas_id', $kelas->id)
                ->orderBy('waktu_mulai', 'desc')
                ->get();
        }

        return view('instruktur.ujian.index', compact('instruktur', 'kelasList', 'kelas', 'ujianList'));
    }

    /**
     * Soal dari bank berdasarkan mata kuliah kelas (AJAX).
     */
    public function getSoalByKelas(Request $request)
    {
        $instruktur = $this->getInstruktur();

        $kelas = Kelas::findOrFail($request->kelas_id);
        $soal  = BankSoal::with('pilihan')
            ->where('instruktur_id', $instruktur->id)
            ->where('mata_kuliah_id', $kelas->mata_kuliah_id)
            ->orderBy('tipe')
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($soal);
    }

    /**
     * Buat ujian baru.
     */
    public function store(Request $request)
    {
        $instruktur = $this->getInstruktur();

        $data = $request->validate([
            'kelas_id'         => ['required', 'exists:kelas,id'],
            'judul'            => ['required', 'string', 'max:255'],
            'deskripsi'        => ['nullable', 'string'],
            'waktu_mulai'      => ['required', 'date'],
            'waktu_selesai'    => ['required', 'date', 'after:waktu_mulai'],
            'durasi'           => ['required', 'integer', 'min:1', 'max:480'],
            'status'           => ['required', 'in:draft,aktif,selesai'],
            // Essay
            'ada_essay'         => ['boolean'],
            'jumlah_soal_essay' => ['nullable', 'integer', 'min:1'],
            'acak_soal_essay'   => ['boolean'],
            'soal_essay_ids'    => ['nullable', 'array'],
            'soal_essay_ids.*'  => ['integer', 'exists:bank_soal,id'],
            // PG
            'ada_pg'            => ['boolean'],
            'jumlah_soal_pg'    => ['nullable', 'integer', 'min:1'],
            'acak_soal_pg'      => ['boolean'],
            'acak_pilihan_pg'   => ['boolean'],
            'soal_pg_ids'       => ['nullable', 'array'],
            'soal_pg_ids.*'     => ['integer', 'exists:bank_soal,id'],
        ]);

        DB::transaction(function () use ($instruktur, $data, $request) {
            $ujian = Ujian::create([
                'instruktur_id'    => $instruktur->id,
                'kelas_id'         => $data['kelas_id'],
                'judul'            => $data['judul'],
                'deskripsi'        => $data['deskripsi'] ?? null,
                'waktu_mulai'      => $data['waktu_mulai'],
                'waktu_selesai'    => $data['waktu_selesai'],
                'durasi'           => $data['durasi'],
                'status'           => $data['status'],
                'ada_essay'        => $request->boolean('ada_essay'),
                'jumlah_soal_essay'=> $data['jumlah_soal_essay'] ?? null,
                'acak_soal_essay'  => $request->boolean('acak_soal_essay'),
                'ada_pg'           => $request->boolean('ada_pg'),
                'jumlah_soal_pg'   => $data['jumlah_soal_pg'] ?? null,
                'acak_soal_pg'     => $request->boolean('acak_soal_pg'),
                'acak_pilihan_pg'  => $request->boolean('acak_pilihan_pg'),
            ]);

            $allSoalIds = array_merge(
                $request->input('soal_essay_ids', []),
                $request->input('soal_pg_ids', [])
            );
            $ujian->soalPool()->sync($allSoalIds);
        });

        return response()->json(['message' => 'Ujian berhasil dibuat.']);
    }

    /**
     * Update ujian.
     */
    public function update(Request $request, Ujian $ujian)
    {
        $instruktur = $this->getInstruktur();
        abort_if($ujian->instruktur_id !== $instruktur->id, 403);

        $data = $request->validate([
            'judul'            => ['required', 'string', 'max:255'],
            'deskripsi'        => ['nullable', 'string'],
            'waktu_mulai'      => ['required', 'date'],
            'waktu_selesai'    => ['required', 'date', 'after:waktu_mulai'],
            'durasi'           => ['required', 'integer', 'min:1', 'max:480'],
            'status'           => ['required', 'in:draft,aktif,selesai'],
            'ada_essay'        => ['boolean'],
            'jumlah_soal_essay'=> ['nullable', 'integer', 'min:1'],
            'acak_soal_essay'  => ['boolean'],
            'soal_essay_ids'   => ['nullable', 'array'],
            'soal_essay_ids.*' => ['integer', 'exists:bank_soal,id'],
            'ada_pg'           => ['boolean'],
            'jumlah_soal_pg'   => ['nullable', 'integer', 'min:1'],
            'acak_soal_pg'     => ['boolean'],
            'acak_pilihan_pg'  => ['boolean'],
            'soal_pg_ids'      => ['nullable', 'array'],
            'soal_pg_ids.*'    => ['integer', 'exists:bank_soal,id'],
        ]);

        DB::transaction(function () use ($ujian, $data, $request) {
            $ujian->update([
                'judul'            => $data['judul'],
                'deskripsi'        => $data['deskripsi'] ?? null,
                'waktu_mulai'      => $data['waktu_mulai'],
                'waktu_selesai'    => $data['waktu_selesai'],
                'durasi'           => $data['durasi'],
                'status'           => $data['status'],
                'ada_essay'        => $request->boolean('ada_essay'),
                'jumlah_soal_essay'=> $data['jumlah_soal_essay'] ?? null,
                'acak_soal_essay'  => $request->boolean('acak_soal_essay'),
                'ada_pg'           => $request->boolean('ada_pg'),
                'jumlah_soal_pg'   => $data['jumlah_soal_pg'] ?? null,
                'acak_soal_pg'     => $request->boolean('acak_soal_pg'),
                'acak_pilihan_pg'  => $request->boolean('acak_pilihan_pg'),
            ]);

            $allSoalIds = array_merge(
                $request->input('soal_essay_ids', []),
                $request->input('soal_pg_ids', [])
            );
            $ujian->soalPool()->sync($allSoalIds);
        });

        return response()->json(['message' => 'Ujian berhasil diperbarui.']);
    }

    /**
     * Hapus ujian.
     */
    public function destroy(Ujian $ujian)
    {
        $instruktur = $this->getInstruktur();
        abort_if($ujian->instruktur_id !== $instruktur->id, 403);
        $ujian->delete();
        return response()->json(['message' => 'Ujian berhasil dihapus.']);
    }

    /**
     * Detail ujian lengkap (untuk modal edit, AJAX).
     */
    public function show(Ujian $ujian)
    {
        $instruktur = $this->getInstruktur();
        abort_if($ujian->instruktur_id !== $instruktur->id, 403);

        $ujian->load('soalPool.pilihan');

        return response()->json($ujian);
    }

    /** Halaman pengawas ujian realtime */
    public function pengawas(Ujian $ujian)
    {
        $instruktur = $this->getInstruktur();
        abort_if($ujian->instruktur_id !== $instruktur->id, 403);

        $ujian->load('kelas.enrollments.mahasiswa');

        return view('instruktur.ujian.pengawas', compact('ujian'));
    }

    /** Reset sesi mahasiswa → izinkan ujian ulang */
    public function resetSesi(Ujian $ujian, Mahasiswa $mahasiswa)
    {
        $instruktur = $this->getInstruktur();
        abort_if($ujian->instruktur_id !== $instruktur->id, 403);

        $sesi = UjianSesi::where('ujian_id', $ujian->id)
            ->where('mahasiswa_id', $mahasiswa->id)
            ->first();

        if ($sesi) {
            $sesi->riwayatPelanggaran()->delete();
            $sesi->delete();
        }

        return response()->json([
            'ok'      => true,
            'message' => "Sesi {$mahasiswa->nama} berhasil direset. Mahasiswa dapat mengulang ujian.",
        ]);
    }

    /** Halaman penilaian essay */
    public function penilaian(Ujian $ujian)
    {
        $instruktur = $this->getInstruktur();
        abort_if($ujian->instruktur_id !== $instruktur->id, 403);

        $ujian->loadMissing('kelas.mataKuliah');

        $sesiList = UjianSesi::where('ujian_id', $ujian->id)
            ->whereNotNull('submitted_at')
            ->with(['mahasiswa', 'jawaban.soal'])
            ->orderBy('submitted_at')
            ->get()
            ->map(function ($sesi) use ($ujian) {
                $essayJawaban = $sesi->jawaban->filter(fn($j) => $j->soal?->tipe === 'essay');
                $pgJawaban    = $sesi->jawaban->filter(fn($j) => $j->soal?->tipe === 'pilihan_ganda');

                $essayGraded  = $essayJawaban->filter(fn($j) => $j->nilai !== null)->count();
                $essayTotal   = $essayJawaban->count();

                $sesi->_essay_graded = $essayGraded;
                $sesi->_essay_total  = $essayTotal;
                $sesi->_pg_score     = $pgJawaban->sum('nilai');
                $sesi->_pg_bobot     = $pgJawaban->sum(fn($j) => $j->soal?->bobot ?? 0);
                return $sesi;
            });

        return view('instruktur.ujian.penilaian', compact('ujian', 'sesiList'));
    }

    /** AJAX: nilai satu jawaban essay (manual) */
    public function gradeJawaban(Request $request, Ujian $ujian, UjianSesi $sesi, UjianJawaban $jawaban)
    {
        $instruktur = $this->getInstruktur();
        abort_if($ujian->instruktur_id !== $instruktur->id || $sesi->ujian_id !== $ujian->id, 403);
        abort_if($jawaban->sesi_id !== $sesi->id, 403);

        $maxBobot = $jawaban->soal?->bobot ?? 100;

        $data = $request->validate([
            'nilai'                => ['required', 'numeric', 'min:0', 'max:' . $maxBobot],
            'feedback_instruktur'  => ['nullable', 'string', 'max:2000'],
        ]);

        $jawaban->update([
            'nilai'               => $data['nilai'],
            'feedback_instruktur' => $data['feedback_instruktur'] ?? null,
        ]);

        return response()->json(['ok' => true]);
    }

    /** AJAX: AI grade semua jawaban essay dalam satu sesi */
    public function aiGradeEssay(Ujian $ujian, UjianSesi $sesi)
    {
        $instruktur = $this->getInstruktur();
        abort_if($ujian->instruktur_id !== $instruktur->id || $sesi->ujian_id !== $ujian->id, 403);

        $essayJawaban = $sesi->jawaban()->with('soal')->get()
            ->filter(fn($j) => $j->soal?->tipe === 'essay');

        if ($essayJawaban->isEmpty()) {
            return response()->json(['ok' => false, 'message' => 'Tidak ada jawaban essay.'], 422);
        }

        $ai      = app(AiService::class);
        $results = [];

        foreach ($essayJawaban as $jawaban) {
            $soal  = $jawaban->soal;
            $bobot = $soal->bobot ?? 10;

            if (blank($jawaban->jawaban_essay)) {
                $jawaban->update(['nilai' => 0, 'feedback_ai' => 'Tidak ada jawaban yang diberikan.']);
                $results[] = ['jawaban_id' => $jawaban->id, 'ok' => true, 'nilai' => 0];
                continue;
            }

            $prompt = <<<PROMPT
Kamu adalah penilai ujian akademik yang objektif dan adil.
Nilai jawaban essay mahasiswa berikut dengan skor 0 hingga {$bobot} (bilangan bulat).

Soal: {$soal->pertanyaan}

Jawaban Mahasiswa:
{$jawaban->jawaban_essay}

Berikan respons HANYA dalam format JSON berikut, tanpa teks lain:
{"nilai": <angka 0 sampai {$bobot}>, "feedback": "<jelaskan HANYA alasan mengapa jawaban ini mendapat nilai tersebut, dalam Bahasa Indonesia, maksimal 80 kata. JANGAN memberikan saran, koreksi, atau menyebutkan jawaban yang seharusnya>"}
PROMPT;

            $result = $ai->chat([['role' => 'user', 'content' => $prompt]], '');

            if (isset($result['error'])) {
                $results[] = ['jawaban_id' => $jawaban->id, 'ok' => false, 'error' => $result['error']];
                continue;
            }

            $reply = $result['reply'];
            if (preg_match('/```(?:json)?\s*([\s\S]*?)```/i', $reply, $m)) $reply = trim($m[1]);
            if (preg_match('/(\{[\s\S]*?\})/u', $reply, $m)) $reply = $m[1];

            $data = json_decode($reply, true);

            if (!$data || !array_key_exists('nilai', $data)) {
                $results[] = ['jawaban_id' => $jawaban->id, 'ok' => false, 'error' => 'AI tidak mengembalikan format valid.'];
                continue;
            }

            $nilai = max(0, min($bobot, (int) round((float) $data['nilai'])));

            $jawaban->update([
                'nilai'       => $nilai,
                'feedback_ai' => $data['feedback'] ?? null,
            ]);

            $results[] = [
                'jawaban_id' => $jawaban->id,
                'ok'         => true,
                'nilai'      => $nilai,
                'feedback'   => $data['feedback'] ?? null,
            ];
        }

        return response()->json(['ok' => true, 'results' => $results]);
    }

    /** AJAX: toggle draft / public satu sesi */
    public function publishNilai(Request $request, Ujian $ujian, UjianSesi $sesi)
    {
        $instruktur = $this->getInstruktur();
        abort_if($ujian->instruktur_id !== $instruktur->id || $sesi->ujian_id !== $ujian->id, 403);

        $newStatus = $request->input('status', $sesi->nilai_status === 'public' ? 'draft' : 'public');

        $updates = ['nilai_status' => $newStatus];
        if ($newStatus === 'public') {
            $updates['nilai'] = $this->hitungNilaiFinal($sesi);
        }

        $sesi->update($updates);

        return response()->json([
            'ok'     => true,
            'status' => $newStatus,
            'nilai'  => $sesi->fresh()->nilai,
        ]);
    }

    /** AJAX: publish semua nilai dalam satu ujian */
    public function publishAllNilai(Ujian $ujian)
    {
        $instruktur = $this->getInstruktur();
        abort_if($ujian->instruktur_id !== $instruktur->id, 403);

        $sesiList = UjianSesi::where('ujian_id', $ujian->id)
            ->whereNotNull('submitted_at')
            ->get();

        foreach ($sesiList as $sesi) {
            $sesi->update([
                'nilai_status' => 'public',
                'nilai'        => $this->hitungNilaiFinal($sesi),
            ]);
        }

        return response()->json(['ok' => true, 'count' => $sesiList->count()]);
    }

    /** Hitung nilai final gabungan PG + essay */
    private function hitungNilaiFinal(UjianSesi $sesi): ?float
    {
        $jawabans = $sesi->jawaban()->with('soal')->get();
        if ($jawabans->isEmpty()) return null;

        $totalBobot = 0;
        $totalNilai = 0;

        foreach ($jawabans as $j) {
            $bobot = $j->soal?->bobot ?? 1;
            $totalBobot += $bobot;
            $totalNilai += $j->nilai ?? 0;
        }

        return $totalBobot > 0 ? round(($totalNilai / $totalBobot) * 100, 2) : null;
    }

    /** AJAX: data realtime pengawas */
    public function pengawasData(Request $request, Ujian $ujian)
    {
        $instruktur = $this->getInstruktur();
        abort_if($ujian->instruktur_id !== $instruktur->id, 403);

        $since = $request->query('since'); // ISO timestamp of last poll

        $sesiList = UjianSesi::where('ujian_id', $ujian->id)
            ->with(['mahasiswa', 'riwayatPelanggaran' => fn($q) => $q->orderBy('terjadi_at', 'desc')])
            ->get();

        // New violations since last poll
        $newViolations = [];
        if ($since) {
            $sinceTime = \Carbon\Carbon::parse($since);
            foreach ($sesiList as $sesi) {
                foreach ($sesi->riwayatPelanggaran as $p) {
                    if ($p->terjadi_at->gt($sinceTime)) {
                        $newViolations[] = [
                            'sesi_id'        => $sesi->id,
                            'mahasiswa_id'   => $sesi->mahasiswa_id,
                            'mahasiswa_nama' => $sesi->mahasiswa?->nama,
                            'mahasiswa_nim'  => $sesi->mahasiswa?->nim,
                            'tipe'           => $p->tipe,
                            'catatan'        => $p->catatan,
                            'waktu'          => $p->terjadi_at->format('H:i:s'),
                        ];
                    }
                }
            }
        }

        $ujian->loadMissing('kelas.enrollments.mahasiswa');
        $mahasiswaKelas = $ujian->kelas->enrollments->map(fn($e) => $e->mahasiswa)->filter()->values();
        $sesiMap = $sesiList->keyBy('mahasiswa_id');

        $rows = $mahasiswaKelas->map(function ($mhs) use ($sesiMap, $ujian) {
            $sesi = $sesiMap->get($mhs->id);
            $lastPelanggaran = $sesi?->riwayatPelanggaran?->first();

            $status = 'belum_mulai';
            if ($sesi) {
                $status = $sesi->submitted_at ? 'selesai'
                    : ($sesi->mulai_at ? 'mengerjakan' : 'belum_mulai');
            }

            $sisaDetik = 0;
            if ($sesi && $sesi->mulai_at && ! $sesi->submitted_at) {
                $sisaDetik = $sesi->sisaDetik();
            }

            $lastPing = null;
            if ($sesi?->last_ping_at) {
                $diff = $sesi->last_ping_at->diffInSeconds(now());
                $lastPing = $diff < 60 ? "{$diff}s lalu" : $sesi->last_ping_at->format('H:i:s');
            }

            return [
                'id'                  => $mhs->id,
                'sesi_id'             => $sesi?->id,
                'nama'                => $mhs->nama,
                'nim'                 => $mhs->nim,
                'status'              => $status,
                'jml_pelanggaran'     => $sesi?->pelanggaran ?? 0,
                'sisa_detik'          => $sisaDetik,
                'mulai_at'            => $sesi?->mulai_at?->format('H:i:s'),
                'submitted_at'        => $sesi?->submitted_at?->format('H:i:s'),
                'last_ping'           => $lastPing,
                'pelanggaran_terbaru' => $lastPelanggaran
                    ? "[{$lastPelanggaran->terjadi_at->format('H:i:s')}] {$lastPelanggaran->tipe}"
                    : null,
            ];
        })->values();

        $stats = [
            'total'        => $mahasiswaKelas->count(),
            'mengerjakan'  => $rows->where('status', 'mengerjakan')->count(),
            'selesai'      => $rows->where('status', 'selesai')->count(),
            'belum_mulai'  => $rows->where('status', 'belum_mulai')->count(),
            'pelanggaran'  => $rows->sum('jml_pelanggaran'),
        ];

        return response()->json([
            'rows'           => $rows,
            'stats'          => $stats,
            'new_violations' => $newViolations,
            'server_time'    => now()->toIso8601String(),
        ]);
    }
}
