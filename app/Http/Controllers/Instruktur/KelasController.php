<?php

namespace App\Http\Controllers\Instruktur;

use App\Http\Controllers\Controller;
use App\Models\Enrollment;
use App\Models\Kelas;
use App\Models\Mahasiswa;
use App\Models\PeriodeAkademik;
use Illuminate\Http\Request;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class KelasController extends Controller
{
    private function getInstruktur()
    {
        return auth()->user()->instruktur()->firstOrFail();
    }

    public function index()
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();

        $instruktur = $user->instruktur()
            ->with([
                'kelas.mataKuliah.jurusan',
                'kelas.periodeAkademik',
                'kelas' => fn($q) => $q->withCount('enrollments'),
            ])
            ->first();

        $kelas = $instruktur?->kelas ?? collect();

        $periodeList  = PeriodeAkademik::orderByDesc('created_at')->get();
        $periodeAktif = $periodeList->firstWhere('status', 'Aktif');

        $stats = [
            'total'      => $kelas->count(),
            'aktif'      => $kelas->where('status', 'Aktif')->count(),
            'selesai'    => $kelas->where('status', 'Selesai')->count(),
            'dibatalkan' => $kelas->where('status', 'Dibatalkan')->count(),
            'peserta'    => $kelas->sum('enrollments_count'),
        ];

        return view('instruktur.kelas.index', compact(
            'instruktur', 'kelas', 'periodeList', 'periodeAktif', 'stats'
        ));
    }

    /**
     * Halaman daftar peserta suatu kelas.
     */
    public function peserta(Kelas $kelas)
    {
        $instruktur = $this->getInstruktur();
        abort_if(!$kelas->instruktur->contains($instruktur->id), 403);

        $kelas->load(['mataKuliah', 'periodeAkademik', 'instruktur']);

        $enrollments = Enrollment::where('kelas_id', $kelas->id)
            ->with('mahasiswa')
            ->orderBy('created_at')
            ->get();

        $joinUrl = url('/mahasiswa/kelas/join?token=' . $kelas->enroll_token);

        // Generate QR sebagai SVG (server-side, tidak perlu CDN)
        $qrSvg = QrCode::format('svg')
            ->size(200)
            ->errorCorrection('H')
            ->generate($joinUrl);

        return view('instruktur.kelas.peserta', compact('kelas', 'enrollments', 'joinUrl', 'qrSvg'));
    }

    /**
     * Search mahasiswa yang belum terdaftar di kelas (untuk dropdown live-search).
     */
    public function searchMahasiswa(Request $request, Kelas $kelas)
    {
        $instruktur = $this->getInstruktur();
        abort_if(!$kelas->instruktur->contains($instruktur->id), 403);

        $q = trim($request->query('q', ''));
        if (strlen($q) < 1) {
            return response()->json([]);
        }

        $enrolledIds = Enrollment::where('kelas_id', $kelas->id)->pluck('mahasiswa_id');

        $results = Mahasiswa::where(function ($query) use ($q) {
            $query->where('nim', 'like', '%' . $q . '%')
                  ->orWhere('nama', 'like', '%' . $q . '%');
        })
        ->whereNotIn('id', $enrolledIds)
        ->limit(10)
        ->get(['id', 'nama', 'nim']);

        return response()->json($results->map(fn($m) => [
            'id'   => $m->id,
            'nama' => $m->nama,
            'nim'  => $m->nim ?? '—',
        ]));
    }

    /**
     * Instruktur tambah mahasiswa ke kelas (by NIM/nama).
     */
    public function enrollMahasiswa(Request $request, Kelas $kelas)
    {
        $instruktur = $this->getInstruktur();
        abort_if(!$kelas->instruktur->contains($instruktur->id), 403);

        $data = $request->validate([
            'query' => 'required|string|min:1|max:100',
        ]);

        // Cari mahasiswa by NIM atau nama
        $mahasiswa = Mahasiswa::where('nim', $data['query'])
            ->orWhere('nama', 'like', '%' . $data['query'] . '%')
            ->get();

        if ($mahasiswa->isEmpty()) {
            return response()->json(['message' => 'Mahasiswa tidak ditemukan.'], 404);
        }

        if ($mahasiswa->count() > 1) {
            return response()->json([
                'ambiguous' => true,
                'results'   => $mahasiswa->map(fn($m) => [
                    'id'   => $m->id,
                    'nama' => $m->nama,
                    'nim'  => $m->nim ?? '—',
                ]),
            ]);
        }

        return $this->doEnroll($kelas, $mahasiswa->first());
    }

    /**
     * Instruktur enroll mahasiswa by ID (setelah disambiguate).
     */
    public function enrollById(Request $request, Kelas $kelas)
    {
        $instruktur = $this->getInstruktur();
        abort_if(!$kelas->instruktur->contains($instruktur->id), 403);

        $data = $request->validate(['mahasiswa_id' => 'required|integer|exists:mahasiswa,id']);
        $mahasiswa = Mahasiswa::findOrFail($data['mahasiswa_id']);

        return $this->doEnroll($kelas, $mahasiswa);
    }

    private function doEnroll(Kelas $kelas, Mahasiswa $mahasiswa)
    {
        $existing = Enrollment::where('kelas_id', $kelas->id)
            ->where('mahasiswa_id', $mahasiswa->id)
            ->first();

        if ($existing) {
            return response()->json([
                'message' => "({$mahasiswa->nim}) {$mahasiswa->nama} sudah terdaftar di kelas ini.",
            ], 422);
        }

        $enrollment = Enrollment::create([
            'kelas_id'    => $kelas->id,
            'mahasiswa_id'=> $mahasiswa->id,
            'status'      => 'Aktif',
            'enrolled_at' => now(),
        ]);

        return response()->json([
            'message'    => "{$mahasiswa->nama} berhasil didaftarkan.",
            'enrollment' => [
                'id'           => $enrollment->id,
                'mahasiswa_id' => $mahasiswa->id,
                'nama'         => $mahasiswa->nama,
                'nim'          => $mahasiswa->nim ?? '—',
                'status'       => $enrollment->status,
                'enrolled_at'  => $enrollment->enrolled_at?->format('d M Y'),
            ],
        ], 201);
    }

    /**
     * Instruktur keluarkan mahasiswa dari kelas.
     */
    public function unenroll(Kelas $kelas, Enrollment $enrollment)
    {
        $instruktur = $this->getInstruktur();
        abort_if(!$kelas->instruktur->contains($instruktur->id), 403);
        abort_if($enrollment->kelas_id !== $kelas->id, 404);

        $nama = $enrollment->mahasiswa?->nama ?? 'Mahasiswa';
        $enrollment->delete();

        return response()->json(['message' => "{$nama} berhasil dikeluarkan dari kelas."]);
    }
}
