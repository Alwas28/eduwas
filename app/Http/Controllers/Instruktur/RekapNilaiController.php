<?php

namespace App\Http\Controllers\Instruktur;

use App\Http\Controllers\Controller;
use App\Models\Enrollment;
use App\Models\Instruktur;
use App\Models\Kelas;
use App\Models\KelasKomponenNilai;
use App\Models\KomponenUjianPilihan;
use App\Models\Tugas;
use App\Models\Ujian;
use App\Models\UjianSesi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class RekapNilaiController extends Controller
{
    private function instruktur(): Instruktur
    {
        return Instruktur::where('user_id', Auth::id())->firstOrFail();
    }

    // ── List kelas per periode ─────────────────────────────────
    public function index()
    {
        $instruktur = $this->instruktur();

        $kelasList = Kelas::whereHas('instruktur', fn($q) => $q->where('instruktur.id', $instruktur->id))
            ->with(['mataKuliah', 'periodeAkademik', 'instruktur'])
            ->get()
            ->groupBy(fn($k) => $k->periodeAkademik?->nama ?? 'Tanpa Periode');

        return view('instruktur.rekap-nilai.index', compact('kelasList'));
    }

    // ── Rekap per kelas ────────────────────────────────────────
    public function show(Kelas $kelas)
    {
        $instruktur = $this->instruktur();

        // Pastikan instruktur terdaftar di kelas ini
        abort_unless(
            $kelas->instruktur->contains($instruktur->id),
            403
        );

        $kelas->load(['mataKuliah', 'periodeAkademik', 'instruktur.user']);

        // Komponen nilai milik instruktur ini untuk kelas ini
        $komponen = KelasKomponenNilai::where('kelas_id', $kelas->id)
            ->where('instruktur_id', $instruktur->id)
            ->orderBy('urutan')
            ->get();

        $komponenTugas = $komponen->where('tipe', 'tugas');
        $komponenUjian = $komponen->where('tipe', 'ujian');

        // Semua tugas individu di kelas ini milik instruktur ini
        $tugasOptions = Tugas::where('kelas_id', $kelas->id)
            ->where('instruktur_id', $instruktur->id)
            ->where('tipe', 'individu')
            ->orderBy('deadline')
            ->get();

        // Semua ujian di kelas ini milik instruktur ini
        $ujianOptions = Ujian::where('kelas_id', $kelas->id)
            ->where('instruktur_id', $instruktur->id)
            ->orderBy('waktu_mulai')
            ->get();

        // Mahasiswa terdaftar di kelas
        $enrollments = Enrollment::where('kelas_id', $kelas->id)
            ->with('mahasiswa')
            ->get()
            ->sortBy('mahasiswa.nama');

        // Hitung nilai per mahasiswa
        $nilaiData = $this->hitungNilai($enrollments, $komponenTugas, $komponenUjian, $instruktur);

        // Pilihan ujian per mahasiswa (override susulan)
        $pilihanMap = KomponenUjianPilihan::whereIn('komponen_id', $komponenUjian->pluck('id'))
            ->get()
            ->groupBy('komponen_id')
            ->map(fn($rows) => $rows->keyBy('mahasiswa_id'));

        // Semua sesi ujian yang tersedia per ujian per mahasiswa (untuk dropdown susulan)
        $sesiAvailable = [];
        foreach ($komponenUjian as $komp) {
            if (! $komp->sumber_id) continue;
            // Cari semua ujian di kelas ini yang milik instruktur ini (termasuk susulan)
            $ujianIds = Ujian::where('kelas_id', $kelas->id)
                ->where('instruktur_id', $instruktur->id)
                ->pluck('id');

            $sesiPerMhs = UjianSesi::whereIn('ujian_id', $ujianIds)
                ->whereNotNull('submitted_at')
                ->where('nilai_status', 'public')
                ->with('ujian')
                ->get()
                ->groupBy('mahasiswa_id');

            $sesiAvailable[$komp->id] = $sesiPerMhs;
        }

        return view('instruktur.rekap-nilai.show', compact(
            'kelas', 'instruktur', 'komponen', 'komponenTugas', 'komponenUjian',
            'tugasOptions', 'ujianOptions', 'enrollments', 'nilaiData',
            'pilihanMap', 'sesiAvailable'
        ));
    }

    // ── Hitung nilai tugas & ujian per mahasiswa ───────────────
    private function hitungNilai($enrollments, $komponenTugas, $komponenUjian, $instruktur): array
    {
        $result = [];

        // Pre-load pilihan ujian
        $pilihanAll = KomponenUjianPilihan::whereIn('komponen_id', $komponenUjian->pluck('id'))
            ->get()
            ->groupBy('komponen_id')
            ->map(fn($rows) => $rows->keyBy('mahasiswa_id'));

        foreach ($enrollments as $enrollment) {
            $mhsId = $enrollment->mahasiswa_id;

            // ── Nilai Tugas: rata-rata semua komponen tugas ──
            $nilaiTugas = null;
            if ($komponenTugas->count() > 0) {
                $tugasIds = $komponenTugas->pluck('sumber_id')->filter();
                if ($tugasIds->count() > 0) {
                    $submissions = \App\Models\TugasIndividuSubmission::where('mahasiswa_id', $mhsId)
                        ->whereIn('tugas_id', $tugasIds)
                        ->whereNotNull('nilai')
                        ->pluck('nilai', 'tugas_id');

                    $total = 0;
                    foreach ($tugasIds as $tId) {
                        $total += $submissions->get($tId, 0);
                    }
                    $nilaiTugas = round($total / $tugasIds->count(), 2);
                }
            }

            // ── Nilai Ujian: per komponen ujian ──
            $nilaiUjianPerKomp = [];
            foreach ($komponenUjian as $komp) {
                if (! $komp->sumber_id) {
                    $nilaiUjianPerKomp[$komp->id] = null;
                    continue;
                }

                // Cek override pilihan
                $pilihan = $pilihanAll->get($komp->id)?->get($mhsId);
                if ($pilihan) {
                    $sesi = UjianSesi::find($pilihan->ujian_sesi_id);
                    $nilaiUjianPerKomp[$komp->id] = $sesi?->nilai;
                } else {
                    // Default: sesi dari ujian sumber untuk mahasiswa ini
                    $sesi = UjianSesi::where('ujian_id', $komp->sumber_id)
                        ->where('mahasiswa_id', $mhsId)
                        ->whereNotNull('submitted_at')
                        ->where('nilai_status', 'public')
                        ->latest('submitted_at')
                        ->first();
                    $nilaiUjianPerKomp[$komp->id] = $sesi?->nilai;
                }
            }

            // Rata-rata semua komponen ujian (jika lebih dari 1)
            $nilaiUjian = null;
            if (count($nilaiUjianPerKomp) > 0) {
                $vals = array_filter($nilaiUjianPerKomp, fn($v) => $v !== null);
                if (count($vals) > 0) {
                    $nilaiUjian = round(array_sum($vals) / count($nilaiUjianPerKomp), 2);
                }
            }

            $result[$mhsId] = [
                'nilai_tugas'          => $nilaiTugas,
                'nilai_ujian'          => $nilaiUjian,
                'nilai_ujian_per_komp' => $nilaiUjianPerKomp,
            ];
        }

        return $result;
    }

    // ── Tambah komponen ────────────────────────────────────────
    public function storeKomponen(Request $request, Kelas $kelas)
    {
        $instruktur = $this->instruktur();
        abort_unless($kelas->instruktur->contains($instruktur->id), 403);

        $request->validate([
            'tipe'      => ['required', 'in:tugas,ujian'],
            'sumber_id' => ['required', 'integer'],
            'label'     => ['required', 'string', 'max:100'],
        ]);

        // Cek sumber_id valid dan milik instruktur + kelas ini
        if ($request->tipe === 'tugas') {
            $sumber = Tugas::where('id', $request->sumber_id)
                ->where('kelas_id', $kelas->id)
                ->where('instruktur_id', $instruktur->id)
                ->firstOrFail();
        } else {
            $sumber = Ujian::where('id', $request->sumber_id)
                ->where('kelas_id', $kelas->id)
                ->where('instruktur_id', $instruktur->id)
                ->firstOrFail();
        }

        // Cegah duplikat sumber
        $exists = KelasKomponenNilai::where('kelas_id', $kelas->id)
            ->where('instruktur_id', $instruktur->id)
            ->where('tipe', $request->tipe)
            ->where('sumber_id', $sumber->id)
            ->exists();

        if ($exists) {
            return back()->with('error', 'Komponen ini sudah ditambahkan.');
        }

        $maxUrutan = KelasKomponenNilai::where('kelas_id', $kelas->id)
            ->where('instruktur_id', $instruktur->id)
            ->max('urutan') ?? 0;

        KelasKomponenNilai::create([
            'kelas_id'      => $kelas->id,
            'instruktur_id' => $instruktur->id,
            'tipe'          => $request->tipe,
            'sumber_id'     => $sumber->id,
            'label'         => $request->label,
            'urutan'        => $maxUrutan + 1,
        ]);

        return back()->with('success', 'Komponen nilai berhasil ditambahkan.');
    }

    // ── Hapus komponen ─────────────────────────────────────────
    public function destroyKomponen(Kelas $kelas, KelasKomponenNilai $komponen)
    {
        $instruktur = $this->instruktur();
        abort_unless(
            $komponen->kelas_id === $kelas->id && $komponen->instruktur_id === $instruktur->id,
            403
        );

        $komponen->delete();
        return back()->with('success', 'Komponen dihapus.');
    }

    // ── Simpan pilihan ujian per mahasiswa ─────────────────────
    public function simpanPilihan(Request $request, Kelas $kelas, KelasKomponenNilai $komponen)
    {
        $instruktur = $this->instruktur();
        abort_unless(
            $komponen->kelas_id === $kelas->id && $komponen->instruktur_id === $instruktur->id,
            403
        );

        $request->validate([
            'mahasiswa_id'  => ['required', 'exists:mahasiswa,id'],
            'ujian_sesi_id' => ['required', 'exists:ujian_sesi,id'],
        ]);

        // Pastikan sesi ini milik mahasiswa dan sudah submitted + public
        $sesi = UjianSesi::where('id', $request->ujian_sesi_id)
            ->where('mahasiswa_id', $request->mahasiswa_id)
            ->whereNotNull('submitted_at')
            ->where('nilai_status', 'public')
            ->firstOrFail();

        KomponenUjianPilihan::updateOrCreate(
            ['komponen_id' => $komponen->id, 'mahasiswa_id' => $request->mahasiswa_id],
            ['ujian_sesi_id' => $sesi->id]
        );

        return response()->json(['ok' => true, 'nilai' => $sesi->nilai]);
    }
}
