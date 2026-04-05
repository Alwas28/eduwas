<?php

namespace App\Http\Controllers\Instruktur;

use App\Http\Controllers\Controller;

class DashboardController extends Controller
{
    public function index()
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();

        $instruktur = $user->instruktur()
            ->with([
                'kelas.mataKuliah',
                'kelas.periodeAkademik',
                'kelas' => fn($q) => $q->withCount('enrollments'),
            ])
            ->first();

        $kelas = $instruktur?->kelas ?? collect();

        $stats = [
            'total'      => $kelas->count(),
            'aktif'      => $kelas->where('status', 'Aktif')->count(),
            'selesai'    => $kelas->where('status', 'Selesai')->count(),
            'peserta'    => $kelas->sum('enrollments_count'),
        ];

        $kelasAktif = $kelas->where('status', 'Aktif')->sortByDesc(fn($k) => $k->periodeAkademik?->created_at);

        return view('instruktur.dashboard', compact('instruktur', 'stats', 'kelasAktif', 'kelas'));
    }
}
