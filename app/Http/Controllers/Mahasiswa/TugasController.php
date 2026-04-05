<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Http\Controllers\Controller;
use App\Models\Enrollment;
use App\Models\Mahasiswa;
use App\Models\TugasKelompok;
use App\Models\TugasKelompokAnggota;
use App\Notifications\TugasKelompokNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class TugasController extends Controller
{
    private function getMahasiswa(): Mahasiswa
    {
        return Mahasiswa::where('user_id', Auth::id())->firstOrFail();
    }

    /**
     * Daftar semua tugas kelompok (sebagai ketua atau anggota).
     */
    public function index()
    {
        $mahasiswa = $this->getMahasiswa();

        // Kelompok di mana mahasiswa adalah ketua
        $sebagaiKetua = TugasKelompok::where('ketua_mahasiswa_id', $mahasiswa->id)
            ->with([
                'tugas.kelas.mataKuliah',
                'tugas.kelas.periodeAkademik',
                'anggota.mahasiswa',
            ])
            ->withCount('anggota')
            ->get();

        // Kelompok di mana mahasiswa adalah anggota
        $sebagaiAnggota = TugasKelompokAnggota::where('mahasiswa_id', $mahasiswa->id)
            ->with([
                'kelompok.tugas.kelas.mataKuliah',
                'kelompok.tugas.kelas.periodeAkademik',
                'kelompok.ketua',
                'kelompok' => fn($q) => $q->withCount('anggota'),
            ])
            ->get();

        // Tugas individu: semua kelas aktif di mana mahasiswa enrolled
        $enrolledKelasIds = \App\Models\Enrollment::where('mahasiswa_id', $mahasiswa->id)
            ->where('status', 'Aktif')
            ->pluck('kelas_id');

        $mhsId = $mahasiswa->id;

        $tugasIndividu = \App\Models\Tugas::whereIn('kelas_id', $enrolledKelasIds)
            ->where('tipe', 'individu')
            ->whereIn('status', ['aktif', 'selesai'])
            ->with([
                'kelas.mataKuliah',
                'kelas.periodeAkademik',
                'individuSubmissions' => fn($q) => $q->where('mahasiswa_id', $mhsId),
            ])
            ->orderByDesc('created_at')
            ->get()
            ->each(function ($t) {
                $sub = $t->individuSubmissions->first();
                $t->mySubmission    = $sub;
                $t->sudah_submit    = $sub && $sub->status_submit === 'submitted' ? 1 : 0;
                $t->nilai_individu  = $sub?->nilai;
            });

        return view('mahasiswa.tugas.index', compact('mahasiswa', 'sebagaiKetua', 'sebagaiAnggota', 'tugasIndividu'));
    }

    /**
     * Detail kelompok — hanya ketua yang bisa akses halaman pengelolaan anggota.
     */
    public function show(TugasKelompok $kelompok)
    {
        $mahasiswa = $this->getMahasiswa();

        abort_if($kelompok->ketua_mahasiswa_id !== $mahasiswa->id, 403);

        $kelompok->load([
            'tugas.kelas.mataKuliah',
            'tugas.kelas.periodeAkademik',
            'anggota.mahasiswa',
        ]);

        // Entry milik ketua sendiri (jika ada)
        $ketuaEntry = TugasKelompokAnggota::where('kelompok_id', $kelompok->id)
            ->where('mahasiswa_id', $mahasiswa->id)
            ->first();

        // Daftar mahasiswa terdaftar di kelas ini (kecuali yang sudah jadi anggota + ketua)
        $anggotaIds    = $kelompok->anggota->pluck('mahasiswa_id')->push($mahasiswa->id);
        $mahasiswaList = Enrollment::where('kelas_id', $kelompok->tugas->kelas_id)
            ->where('status', 'Aktif')
            ->with('mahasiswa')
            ->get()
            ->pluck('mahasiswa')
            ->filter()
            ->whereNotIn('id', $anggotaIds)
            ->values();

        return view('mahasiswa.tugas.kelompok', compact('kelompok', 'mahasiswaList', 'ketuaEntry'));
    }

    /**
     * Ketua menambahkan anggota ke kelompok.
     */
    public function storeAnggota(Request $request, TugasKelompok $kelompok)
    {
        $mahasiswa = $this->getMahasiswa();
        abort_if($kelompok->ketua_mahasiswa_id !== $mahasiswa->id, 403);

        $data = $request->validate([
            'mahasiswa_id' => 'required|integer|exists:mahasiswa,id',
            'topik'        => 'nullable|string|max:300',
        ]);

        // Pastikan tidak duplikat dan bukan ketua sendiri
        abort_if($data['mahasiswa_id'] == $mahasiswa->id, 422);
        abort_if(
            TugasKelompokAnggota::where('kelompok_id', $kelompok->id)
                ->where('mahasiswa_id', $data['mahasiswa_id'])
                ->exists(),
            422
        );

        $anggota = TugasKelompokAnggota::create([
            'kelompok_id'  => $kelompok->id,
            'mahasiswa_id' => $data['mahasiswa_id'],
            'topik'        => $data['topik'] ?? null,
            'status_submit'=> 'belum',
        ]);

        $anggota->load('mahasiswa');
        $tugas = $kelompok->tugas ?? $kelompok->load('tugas')->tugas;

        // Notifikasi ke anggota baru
        $anggotaUser = $anggota->mahasiswa?->user;
        if ($anggotaUser) {
            $anggotaUser->notify(new TugasKelompokNotification(
                title: 'Ditambahkan ke Kelompok',
                body:  "Kamu ditambahkan ke \"{$kelompok->nama_kelompok}\" pada tugas \"{$tugas->judul}\"."
                       . ($data['topik'] ? " Topikmu: {$data['topik']}." : ''),
                url:   '/mahasiswa/tugas',
                icon:  'fa-user-plus',
                color: 'emerald',
            ));
        }

        return response()->json([
            'message' => 'Anggota berhasil ditambahkan.',
            'anggota' => [
                'id'           => $anggota->id,
                'mahasiswa_id' => $anggota->mahasiswa_id,
                'nama'         => $anggota->mahasiswa?->nama ?? '—',
                'nim'          => $anggota->mahasiswa?->nim  ?? '—',
                'topik'        => $anggota->topik,
                'status_submit'=> $anggota->status_submit,
            ],
        ], 201);
    }

    /**
     * Ketua menghapus anggota dari kelompok.
     */
    public function destroyAnggota(TugasKelompok $kelompok, TugasKelompokAnggota $anggota)
    {
        $mahasiswa = $this->getMahasiswa();
        abort_if($kelompok->ketua_mahasiswa_id !== $mahasiswa->id, 403);
        abort_if($anggota->kelompok_id !== $kelompok->id, 404);

        $anggota->delete();

        return response()->json(['message' => 'Anggota dihapus dari kelompok.']);
    }

    /**
     * Ketua mengubah topik anggota.
     */
    public function updateTopik(Request $request, TugasKelompok $kelompok, TugasKelompokAnggota $anggota)
    {
        $mahasiswa = $this->getMahasiswa();
        abort_if($kelompok->ketua_mahasiswa_id !== $mahasiswa->id, 403);
        abort_if($anggota->kelompok_id !== $kelompok->id, 404);

        $data = $request->validate(['topik' => 'nullable|string|max:300']);
        $anggota->update(['topik' => $data['topik'] ?? null]);

        return response()->json(['message' => 'Topik diperbarui.', 'topik' => $anggota->topik]);
    }

    /**
     * Halaman pengerjaan & pengumpulan tugas oleh anggota.
     */
    public function showSubmit(TugasKelompokAnggota $anggota)
    {
        $mahasiswa = $this->getMahasiswa();
        abort_if($anggota->mahasiswa_id !== $mahasiswa->id, 403);

        $anggota->load([
            'kelompok.tugas.kelas.mataKuliah',
            'kelompok.tugas.kelas.periodeAkademik',
            'kelompok.ketua',
        ]);

        return view('mahasiswa.tugas.submit', compact('anggota'));
    }

    /**
     * Anggota menyimpan draft konten.
     */
    public function saveKonten(Request $request, TugasKelompokAnggota $anggota)
    {
        $mahasiswa = $this->getMahasiswa();
        abort_if($anggota->mahasiswa_id !== $mahasiswa->id, 403);
        abort_if($anggota->status_submit === 'submitted', 422);

        $data = $request->validate(['konten' => 'nullable|string']);
        $anggota->update(['konten' => $data['konten'] ?? null]);

        return response()->json(['message' => 'Draft disimpan.']);
    }

    /**
     * Anggota mengumpulkan (submit) tugasnya.
     */
    public function submit(Request $request, TugasKelompokAnggota $anggota)
    {
        $mahasiswa = $this->getMahasiswa();
        abort_if($anggota->mahasiswa_id !== $mahasiswa->id, 403);
        abort_if($anggota->status_submit === 'submitted', 422);

        $data = $request->validate(['konten' => 'nullable|string']);
        $anggota->update([
            'konten'        => $data['konten'] ?? $anggota->konten,
            'status_submit' => 'submitted',
            'submitted_at'  => now(),
        ]);

        return response()->json(['message' => 'Tugas berhasil dikumpulkan.']);
    }

    /**
     * Halaman kompilasi final — ketua lihat semua tugas anggota + editor final.
     */
    public function showFinal(TugasKelompok $kelompok)
    {
        $mahasiswa = $this->getMahasiswa();
        abort_if($kelompok->ketua_mahasiswa_id !== $mahasiswa->id, 403);

        $kelompok->load([
            'tugas.kelas.mataKuliah',
            'tugas.kelas.periodeAkademik',
            'anggota.mahasiswa',
        ]);

        return view('mahasiswa.tugas.final', compact('kelompok'));
    }

    /**
     * Ketua simpan draft konten final.
     */
    public function saveFinal(Request $request, TugasKelompok $kelompok)
    {
        $mahasiswa = $this->getMahasiswa();
        abort_if($kelompok->ketua_mahasiswa_id !== $mahasiswa->id, 403);
        abort_if($kelompok->status_submit === 'submitted', 422);

        $data = $request->validate(['konten_final' => 'nullable|string']);
        $kelompok->update(['konten_final' => $data['konten_final'] ?? null]);

        return response()->json(['message' => 'Draft disimpan.']);
    }

    /**
     * Ketua submit final → upload PDF langsung dari client.
     */
    public function submitFinal(Request $request, TugasKelompok $kelompok)
    {
        $mahasiswa = $this->getMahasiswa();
        abort_if($kelompok->ketua_mahasiswa_id !== $mahasiswa->id, 403);
        abort_if($kelompok->status_submit === 'submitted', 422);

        $request->validate([
            'pdf' => 'required|file|mimes:pdf|max:20480',
        ]);

        // Hapus PDF lama jika ada
        if ($kelompok->pdf_path) {
            Storage::disk('public')->delete($kelompok->pdf_path);
        }

        $pdfPath = $request->file('pdf')->store('tugas-pdf', 'public');

        $kelompok->update([
            'status_submit' => 'submitted',
            'submitted_at'  => now(),
            'pdf_path'      => $pdfPath,
        ]);

        return response()->json([
            'message' => 'Tugas berhasil dikumpulkan.',
            'pdf_url' => Storage::url($pdfPath),
        ]);
    }

    /**
     * Ketua tarik kembali submission final.
     */
    public function unsubmitFinal(TugasKelompok $kelompok)
    {
        $mahasiswa = $this->getMahasiswa();
        abort_if($kelompok->ketua_mahasiswa_id !== $mahasiswa->id, 403);

        $tugas = $kelompok->tugas ?? $kelompok->load('tugas')->tugas;
        abort_if($tugas->status === 'selesai', 422);

        // Hapus PDF lama
        if ($kelompok->pdf_path) {
            Storage::disk('public')->delete($kelompok->pdf_path);
        }

        $kelompok->update([
            'status_submit' => 'belum',
            'submitted_at'  => null,
            'pdf_path'      => null,
        ]);

        return response()->json(['message' => 'Pengumpulan dibatalkan.']);
    }

    /**
     * Ketua membuat entry pengumpulan untuk dirinya sendiri.
     */
    public function storeKetuaEntry(Request $request, TugasKelompok $kelompok)
    {
        $mahasiswa = $this->getMahasiswa();
        abort_if($kelompok->ketua_mahasiswa_id !== $mahasiswa->id, 403);

        // Cek belum ada entry
        $existing = TugasKelompokAnggota::where('kelompok_id', $kelompok->id)
            ->where('mahasiswa_id', $mahasiswa->id)
            ->first();

        if ($existing) {
            return response()->json([
                'message' => 'Entry sudah ada.',
                'id'      => $existing->id,
            ]);
        }

        $data = $request->validate(['topik' => 'nullable|string|max:300']);

        $entry = TugasKelompokAnggota::create([
            'kelompok_id'   => $kelompok->id,
            'mahasiswa_id'  => $mahasiswa->id,
            'topik'         => $data['topik'] ?? null,
            'status_submit' => 'belum',
        ]);

        return response()->json(['message' => 'Entry dibuat.', 'id' => $entry->id], 201);
    }

    /**
     * Upload gambar untuk konten tugas.
     */
    public function uploadGambar(Request $request)
    {
        $request->validate([
            'gambar' => 'required|image|max:4096',
        ]);

        $path = $request->file('gambar')->store('tugas-gambar', 'public');

        return response()->json(['url' => Storage::url($path)]);
    }

    /**
     * Anggota menarik kembali (unsubmit) tugasnya — hanya jika tugas belum selesai.
     */
    public function unsubmit(TugasKelompokAnggota $anggota)
    {
        $mahasiswa = $this->getMahasiswa();
        abort_if($anggota->mahasiswa_id !== $mahasiswa->id, 403);

        $tugas = $anggota->kelompok->tugas ?? $anggota->load('kelompok.tugas')->kelompok->tugas;
        abort_if($tugas->status === 'selesai', 422);

        $anggota->update([
            'status_submit' => 'belum',
            'submitted_at'  => null,
        ]);

        return response()->json(['message' => 'Pengumpulan dibatalkan.']);
    }
}
