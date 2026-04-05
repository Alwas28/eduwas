<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Http\Controllers\Controller;
use App\Models\Enrollment;
use App\Models\KelasKomponenNilai;
use App\Models\KomponenUjianPilihan;
use App\Models\Mahasiswa;
use App\Models\Materi;
use App\Models\MateriAkses;
use App\Models\TugasIndividuSubmission;
use App\Models\UjianSesi;
use Illuminate\Support\Facades\Auth;

class NilaiController extends Controller
{
    public function index()
    {
        $user      = Auth::user();
        $mahasiswa = Mahasiswa::where('user_id', $user->id)->firstOrFail();

        $enrollments = Enrollment::where('mahasiswa_id', $mahasiswa->id)
            ->with([
                'kelas.mataKuliah',
                'kelas.periodeAkademik',
                'kelas.instruktur',
            ])
            ->orderByDesc('enrolled_at')
            ->get();

        $kelasData = $enrollments->map(function ($enrollment) use ($mahasiswa, $user) {
            $kelas = $enrollment->kelas;

            // ── Komponen nilai per instruktur ──
            $komponen = KelasKomponenNilai::where('kelas_id', $kelas->id)
                ->with('instruktur')
                ->orderBy('instruktur_id')
                ->orderBy('urutan')
                ->get();

            // Pre-load pilihan ujian untuk mahasiswa ini
            $pilihanMap = KomponenUjianPilihan::whereIn('komponen_id', $komponen->pluck('id'))
                ->where('mahasiswa_id', $mahasiswa->id)
                ->pluck('ujian_sesi_id', 'komponen_id');

            // Hitung nilai per instruktur
            $nilaiPerInstruktur = [];
            foreach ($komponen->groupBy('instruktur_id') as $instrId => $komps) {
                $instruktur    = $komps->first()->instruktur;
                $kompsTugas    = $komps->where('tipe', 'tugas');
                $kompsUjian    = $komps->where('tipe', 'ujian');

                // Nilai tugas: rata-rata semua komponen tugas
                $nilaiTugas = null;
                $tugasIds   = $kompsTugas->pluck('sumber_id')->filter();
                if ($tugasIds->count() > 0) {
                    $submissions = TugasIndividuSubmission::where('mahasiswa_id', $mahasiswa->id)
                        ->whereIn('tugas_id', $tugasIds)
                        ->whereNotNull('nilai')
                        ->pluck('nilai', 'tugas_id');

                    $total = 0;
                    foreach ($tugasIds as $tId) {
                        $total += $submissions->get($tId, 0);
                    }
                    $nilaiTugas = round($total / $tugasIds->count(), 2);
                }

                // Nilai ujian: per komponen ujian
                $nilaiUjianDetail = [];
                foreach ($kompsUjian as $komp) {
                    if (! $komp->sumber_id) continue;

                    $overrideSesiId = $pilihanMap->get($komp->id);
                    if ($overrideSesiId) {
                        $sesi = UjianSesi::find($overrideSesiId);
                    } else {
                        $sesi = UjianSesi::where('ujian_id', $komp->sumber_id)
                            ->where('mahasiswa_id', $mahasiswa->id)
                            ->whereNotNull('submitted_at')
                            ->where('nilai_status', 'public')
                            ->latest('submitted_at')
                            ->first();
                    }
                    $nilaiUjianDetail[] = [
                        'label' => $komp->label,
                        'nilai' => $sesi?->nilai,
                    ];
                }

                $nilaiPerInstruktur[] = [
                    'instruktur'        => $instruktur,
                    'nilai_tugas'       => $nilaiTugas,
                    'ujian_detail'      => $nilaiUjianDetail,
                    'has_tugas'         => $kompsTugas->isNotEmpty(),
                    'has_ujian'         => $kompsUjian->isNotEmpty(),
                ];
            }

            // ── Materi progress ──
            $totalMateri    = Materi::whereHas('pokokBahasan', fn($q) => $q->where('mata_kuliah_id', $kelas->mata_kuliah_id))->count();
            $accessedMateri = MateriAkses::where('user_id', $user->id)->where('kelas_id', $kelas->id)->count();

            return [
                'enrollment'          => $enrollment,
                'kelas'               => $kelas,
                'nilai_per_instruktur'=> $nilaiPerInstruktur,
                'komponen_setup'      => $komponen->isNotEmpty(),
                'total_materi'        => $totalMateri,
                'accessed_materi'     => $accessedMateri,
            ];
        });

        // ── Summary stats ──
        $rataRata      = $enrollments->whereNotNull('nilai_akhir')->avg('nilai_akhir');
        $totalSksLulus = $enrollments->where('status', 'Lulus')
                            ->sum(fn($e) => $e->kelas->mataKuliah?->sks ?? 0);
        $kelasAktif    = $enrollments->where('status', 'Aktif')->count();
        $kelasLulus    = $enrollments->where('status', 'Lulus')->count();

        return view('mahasiswa.nilai.index', compact(
            'mahasiswa', 'kelasData', 'rataRata', 'totalSksLulus', 'kelasAktif', 'kelasLulus'
        ));
    }
}
