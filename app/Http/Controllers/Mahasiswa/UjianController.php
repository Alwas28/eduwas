<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Http\Controllers\Controller;
use App\Models\BankSoal;
use App\Models\Mahasiswa;
use App\Models\Ujian;
use App\Models\UjianJawaban;
use App\Models\UjianPelanggaran;
use App\Models\UjianSesi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class UjianController extends Controller
{
    private function getMahasiswa(): Mahasiswa
    {
        return Mahasiswa::where('user_id', Auth::id())->firstOrFail();
    }

    /** Daftar ujian aktif untuk kelas mahasiswa */
    public function index()
    {
        $mahasiswa   = $this->getMahasiswa();
        $kelasIds    = $mahasiswa->enrollments()->where('status', 'Aktif')->pluck('kelas_id');

        $ujianList = Ujian::whereIn('kelas_id', $kelasIds)
            ->where('status', 'aktif')
            ->where('waktu_mulai', '<=', now())
            ->where('waktu_selesai', '>=', now())
            ->with('kelas.mataKuliah', 'instruktur')
            ->orderBy('waktu_selesai')
            ->get();

        // Attach sesi info
        $sesiMap = UjianSesi::where('mahasiswa_id', $mahasiswa->id)
            ->whereIn('ujian_id', $ujianList->pluck('id'))
            ->get()
            ->keyBy('ujian_id');

        return view('mahasiswa.ujian.index', compact('ujianList', 'sesiMap'));
    }

    /** Halaman instruksi / ketentuan ujian */
    public function start(Ujian $ujian)
    {
        $mahasiswa = $this->getMahasiswa();
        $this->authorizeUjian($ujian, $mahasiswa);

        $sesi = UjianSesi::where('ujian_id', $ujian->id)
            ->where('mahasiswa_id', $mahasiswa->id)
            ->first();

        // Already submitted
        if ($sesi && $sesi->submitted_at) {
            return redirect()->route('mahasiswa.ujian.index')
                ->with('info', 'Anda sudah mengumpulkan ujian ini.');
        }

        // Already started — go directly to exam
        if ($sesi && $sesi->mulai_at) {
            return redirect()->route('mahasiswa.ujian.exam', $ujian);
        }

        return view('mahasiswa.ujian.start', compact('ujian'));
    }

    /** Mulai ujian: buat sesi + generate soal_ids */
    public function begin(Request $request, Ujian $ujian)
    {
        $mahasiswa = $this->getMahasiswa();
        $this->authorizeUjian($ujian, $mahasiswa);

        $existing = UjianSesi::where('ujian_id', $ujian->id)
            ->where('mahasiswa_id', $mahasiswa->id)
            ->first();

        if ($existing && $existing->submitted_at) {
            return redirect()->route('mahasiswa.ujian.index')
                ->with('info', 'Ujian sudah dikumpulkan.');
        }

        if ($existing && $existing->mulai_at) {
            return redirect()->route('mahasiswa.ujian.exam', $ujian);
        }

        // Generate soal_ids
        $soalIds = $this->generateSoalIds($ujian);

        $sesi = UjianSesi::create([
            'ujian_id'     => $ujian->id,
            'mahasiswa_id' => $mahasiswa->id,
            'soal_ids'     => $soalIds,
            'mulai_at'     => now(),
            'selesai_at'   => now()->addMinutes($ujian->durasi),
            'last_ping_at' => now(),
        ]);

        return redirect()->route('mahasiswa.ujian.exam', $ujian);
    }

    /** Tampilan ujian */
    public function exam(Ujian $ujian)
    {
        $mahasiswa = $this->getMahasiswa();
        $this->authorizeUjian($ujian, $mahasiswa);

        $sesi = UjianSesi::where('ujian_id', $ujian->id)
            ->where('mahasiswa_id', $mahasiswa->id)
            ->with('jawaban')
            ->first();

        if (! $sesi || ! $sesi->mulai_at) {
            return redirect()->route('mahasiswa.ujian.start', $ujian);
        }

        if ($sesi->submitted_at) {
            return view('mahasiswa.ujian.selesai', compact('ujian', 'sesi'));
        }

        // Load soal sesuai urutan di soal_ids
        $soalData = collect($sesi->soal_ids); // [{id, tipe, pilihan_order?}]
        $soalIds  = $soalData->pluck('id');
        $soalMap  = BankSoal::whereIn('id', $soalIds)->with('pilihan')->get()->keyBy('id');

        $soalList = $soalData->map(function ($item) use ($soalMap) {
            $soal = $soalMap->get($item['id']);
            if (! $soal) return null;
            $soal->pilihan_order = $item['pilihan_order'] ?? null;
            return $soal;
        })->filter()->values();

        // Existing answers
        $jawabanMap = $sesi->jawaban->keyBy('bank_soal_id');

        $sisaDetik = $sesi->sisaDetik();

        return view('mahasiswa.ujian.exam', compact(
            'ujian', 'sesi', 'soalList', 'jawabanMap', 'sisaDetik'
        ));
    }

    /** AJAX: auto-save satu jawaban */
    public function autoSave(Request $request, Ujian $ujian)
    {
        $mahasiswa = $this->getMahasiswa();
        $sesi = $this->getSesiAktif($ujian, $mahasiswa);
        if (! $sesi) return response()->json(['ok' => false], 403);

        $request->validate([
            'soal_id'       => 'required|integer',
            'jawaban_essay' => 'nullable|string|max:10000',
            'jawaban_pg'    => 'nullable|integer|min:0|max:10',
        ]);

        UjianJawaban::updateOrCreate(
            ['sesi_id' => $sesi->id, 'bank_soal_id' => $request->soal_id],
            [
                'jawaban_essay' => $request->jawaban_essay,
                'jawaban_pg'    => $request->jawaban_pg,
            ]
        );

        $sesi->update(['last_ping_at' => now()]);

        return response()->json(['ok' => true]);
    }

    /** AJAX: submit ujian */
    public function submit(Request $request, Ujian $ujian)
    {
        $mahasiswa = $this->getMahasiswa();
        $sesi = $this->getSesiAktif($ujian, $mahasiswa);
        if (! $sesi) return response()->json(['ok' => false, 'message' => 'Sesi tidak ditemukan.'], 403);

        // Save all answers sent in batch
        $jawaban = $request->input('jawaban', []);
        foreach ($jawaban as $soalId => $ans) {
            UjianJawaban::updateOrCreate(
                ['sesi_id' => $sesi->id, 'bank_soal_id' => (int) $soalId],
                [
                    'jawaban_essay' => $ans['essay'] ?? null,
                    'jawaban_pg'    => isset($ans['pg']) ? (int) $ans['pg'] : null,
                ]
            );
        }

        // Auto-grade PG
        $nilai = $this->hitungNilaiPg($sesi);

        $sesi->update([
            'submitted_at' => now(),
            'nilai'        => $nilai,
        ]);

        return response()->json(['ok' => true, 'redirect' => route('mahasiswa.ujian.selesai', $ujian)]);
    }

    /** Halaman selesai */
    public function selesai(Ujian $ujian)
    {
        $mahasiswa = $this->getMahasiswa();
        $sesi = UjianSesi::where('ujian_id', $ujian->id)
            ->where('mahasiswa_id', $mahasiswa->id)
            ->first();

        if (! $sesi || ! $sesi->submitted_at) {
            return redirect()->route('mahasiswa.ujian.index');
        }

        $essayJawaban = collect();
        if ($sesi->nilai_status === 'public') {
            $essayJawaban = $sesi->jawaban()
                ->with('soal')
                ->get()
                ->filter(fn($j) => $j->soal?->tipe === 'essay')
                ->values();
        }

        $ujian->loadMissing('instruktur');
        $instruktur = $ujian->instruktur;
        $sapaan = ($instruktur && $instruktur->jenis_kelamin === 'Laki-laki') ? 'Pak' : 'Bu';
        $namaInstruktur = $instruktur->nama ?? '';

        return view('mahasiswa.ujian.selesai', compact('ujian', 'sesi', 'essayJawaban', 'sapaan', 'namaInstruktur'));
    }

    /** AJAX: record pelanggaran */
    public function violation(Request $request, Ujian $ujian)
    {
        $mahasiswa = $this->getMahasiswa();
        $sesi = UjianSesi::where('ujian_id', $ujian->id)
            ->where('mahasiswa_id', $mahasiswa->id)
            ->first();

        if (! $sesi || $sesi->submitted_at) {
            return response()->json(['ok' => false]);
        }

        $tipe    = $request->input('tipe', 'tab_switch');
        $catatan = $request->input('catatan');

        UjianPelanggaran::create([
            'sesi_id' => $sesi->id,
            'tipe'    => $tipe,
            'catatan' => $catatan,
        ]);

        $sesi->increment('pelanggaran');

        return response()->json(['ok' => true, 'total' => $sesi->pelanggaran + 1]);
    }

    /** AJAX: keep session alive */
    public function keepAlive(Request $request)
    {
        $ujianId = $request->input('ujian_id');
        $mahasiswa = $this->getMahasiswa();

        if ($ujianId) {
            UjianSesi::where('ujian_id', $ujianId)
                ->where('mahasiswa_id', $mahasiswa->id)
                ->whereNull('submitted_at')
                ->update(['last_ping_at' => now()]);
        }

        // Extend PHP session
        $request->session()->regenerateToken();

        return response()->json(['ok' => true, 'time' => now()->toIso8601String()]);
    }

    // ── Private helpers ──────────────────────────────────────

    private function authorizeUjian(Ujian $ujian, Mahasiswa $mahasiswa): void
    {
        $enrolled = $mahasiswa->enrollments()
            ->where('kelas_id', $ujian->kelas_id)
            ->where('status', 'Aktif')
            ->exists();

        if (! $enrolled || $ujian->status !== 'aktif') {
            abort(403, 'Akses ditolak.');
        }
    }

    private function getSesiAktif(Ujian $ujian, Mahasiswa $mahasiswa): ?UjianSesi
    {
        return UjianSesi::where('ujian_id', $ujian->id)
            ->where('mahasiswa_id', $mahasiswa->id)
            ->whereNotNull('mulai_at')
            ->whereNull('submitted_at')
            ->first();
    }

    private function generateSoalIds(Ujian $ujian): array
    {
        $soalIds = [];

        if ($ujian->ada_essay) {
            $pool = $ujian->soalPool()->where('tipe', 'essay')->get();
            if ($ujian->acak_soal_essay) $pool = $pool->shuffle();
            $pool->take($ujian->jumlah_soal_essay ?? $pool->count())
                ->each(function ($s) use (&$soalIds) {
                    $soalIds[] = ['id' => $s->id, 'tipe' => 'essay'];
                });
        }

        if ($ujian->ada_pg) {
            $pool = $ujian->soalPool()->where('tipe', 'pilihan_ganda')->with('pilihan')->get();
            if ($ujian->acak_soal_pg) $pool = $pool->shuffle();
            $pool->take($ujian->jumlah_soal_pg ?? $pool->count())
                ->each(function ($s) use ($ujian, &$soalIds) {
                    $order = range(0, max(0, $s->pilihan->count() - 1));
                    if ($ujian->acak_pilihan_pg) shuffle($order);
                    $soalIds[] = ['id' => $s->id, 'tipe' => 'pilihan_ganda', 'pilihan_order' => $order];
                });
        }

        return $soalIds;
    }

    private function hitungNilaiPg(UjianSesi $sesi): ?float
    {
        $sesi->load('ujian', 'jawaban');
        $jawabans = $sesi->jawaban()->with('soal.pilihan')->get();

        if ($jawabans->isEmpty()) return null;

        $totalBobot = 0;
        $totalBenar = 0;

        foreach ($jawabans as $j) {
            $soal = BankSoal::with('pilihan')->find($j->bank_soal_id);
            if (! $soal || $soal->tipe !== 'pilihan_ganda') continue;

            $totalBobot += $soal->bobot ?? 1;

            // Find the soal's pilihan_order from sesi
            $soalItem = collect($sesi->soal_ids)->firstWhere('id', $soal->id);
            $order    = $soalItem['pilihan_order'] ?? range(0, $soal->pilihan->count() - 1);

            // jawaban_pg is the index in the SHUFFLED order
            if (is_null($j->jawaban_pg)) continue;

            $originalIdx = $order[$j->jawaban_pg] ?? null;
            if (is_null($originalIdx)) continue;

            $pilihan = $soal->pilihan->values()->get($originalIdx);
            $benar   = $pilihan && $pilihan->is_benar;

            $j->update(['is_benar' => $benar, 'nilai' => $benar ? ($soal->bobot ?? 1) : 0]);
            if ($benar) $totalBenar += $soal->bobot ?? 1;
        }

        return $totalBobot > 0 ? round(($totalBenar / $totalBobot) * 100, 2) : null;
    }
}
