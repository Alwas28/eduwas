<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Http\Controllers\Controller;
use App\Models\Enrollment;
use App\Models\Kelas;
use App\Models\Mahasiswa;
use App\Models\MateriAkses;
use App\Models\PeriodeAkademik;
use App\Models\PokokBahasan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class KelasController extends Controller
{
    public function index()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $mahasiswa = Mahasiswa::with([
            'enrollments.kelas.mataKuliah',
            'enrollments.kelas.periodeAkademik',
            'enrollments.kelas.instruktur',
        ])->where('user_id', $user->id)->first();

        if (!$mahasiswa) {
            return redirect()->route('mahasiswa.dashboard')
                ->with('error', 'Profil mahasiswa belum terhubung ke akun ini.');
        }

        $periodeAktif  = PeriodeAkademik::where('status', 'Aktif')->first();
        $periodeList   = PeriodeAkademik::orderByDesc('created_at')->get();
        $enrollments   = $mahasiswa->enrollments;

        return view('mahasiswa.kelas.index', compact(
            'mahasiswa', 'enrollments', 'periodeAktif', 'periodeList'
        ));
    }

    public function show(Kelas $kelas)
    {
        /** @var \App\Models\User $user */
        $user      = Auth::user();
        $mahasiswa = Mahasiswa::where('user_id', $user->id)->firstOrFail();

        // Verifikasi enrollment
        $enrollment = Enrollment::where('mahasiswa_id', $mahasiswa->id)
            ->where('kelas_id', $kelas->id)
            ->firstOrFail();

        $kelas->load(['mataKuliah', 'periodeAkademik', 'instruktur']);

        // Semua pokok bahasan untuk mata kuliah ini
        $pokokBahasanList = PokokBahasan::where('mata_kuliah_id', $kelas->mata_kuliah_id)
            ->with(['materi' => fn($q) => $q->where('status', 'Aktif')->select('id', 'pokok_bahasan_id')])
            ->orderBy('urutan')
            ->orderBy('pertemuan')
            ->get();

        $allMateriIds = $pokokBahasanList->flatMap(fn($pb) => $pb->materi->pluck('id'));

        $aksesMap = MateriAkses::where('user_id', $user->id)
            ->where('kelas_id', $kelas->id)
            ->whereIn('materi_id', $allMateriIds)
            ->get()
            ->keyBy('materi_id');

        return view('mahasiswa.kelas.show', compact(
            'kelas', 'enrollment', 'pokokBahasanList', 'aksesMap', 'mahasiswa'
        ));
    }

    /**
     * Mahasiswa bergabung ke kelas via QR token.
     */
    public function joinByToken(Request $request)
    {
        $data = $request->validate(['token' => 'required|string']);

        $kelas = Kelas::where('enroll_token', $data['token'])->firstOrFail();

        $user      = Auth::user();
        $mahasiswa = Mahasiswa::where('user_id', $user->id)->firstOrFail();

        // Cek apakah sudah terdaftar
        $existing = Enrollment::where('kelas_id', $kelas->id)
            ->where('mahasiswa_id', $mahasiswa->id)
            ->first();

        if ($existing) {
            return response()->json([
                'message' => 'Kamu sudah terdaftar di kelas ini.',
                'kelas'   => [
                    'nama' => $kelas->mataKuliah?->nama ?? $kelas->kode_display,
                ],
                'already' => true,
            ]);
        }

        $kelas->load('mataKuliah', 'periodeAkademik');

        Enrollment::create([
            'kelas_id'    => $kelas->id,
            'mahasiswa_id'=> $mahasiswa->id,
            'status'      => 'Aktif',
            'enrolled_at' => now(),
        ]);

        return response()->json([
            'message' => 'Berhasil bergabung ke kelas ' . ($kelas->mataKuliah?->nama ?? $kelas->kode_display) . '.',
            'kelas'   => [
                'nama'    => $kelas->mataKuliah?->nama ?? $kelas->kode_display,
                'periode' => $kelas->periodeAkademik?->nama ?? '',
            ],
        ]);
    }
}
