<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Http\Controllers\Controller;
use App\Models\Mahasiswa;
use App\Models\PeriodeAkademik;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class DashboardController extends Controller
{
    public function index()
    {
        $mahasiswa = Mahasiswa::with([
            'jurusan',
            'enrollments.kelas.mataKuliah.jurusan',
            'enrollments.kelas.instruktur',
            'enrollments.kelas.periodeAkademik',
        ])->where('user_id', auth()->id())->first();

        $periodeAktif = PeriodeAkademik::where('status', 'Aktif')->first();

        if (!$mahasiswa) {
            return view('mahasiswa.dashboard', [
                'mahasiswa'    => null,
                'periodeAktif' => $periodeAktif,
                'enrollments'  => collect(),
                'stats'        => ['kelas_aktif' => 0, 'total_sks' => 0, 'rata_nilai' => null, 'total_kelas' => 0],
                'kelasPeriodeAktif' => collect(),
                'kelasLainnya'      => collect(),
            ]);
        }

        $enrollments = $mahasiswa->enrollments;

        $kelasPeriodeAktif = $periodeAktif
            ? $enrollments->filter(fn($e) => $e->kelas->periode_akademik_id === $periodeAktif->id)
            : collect();

        $kelasLainnya = $periodeAktif
            ? $enrollments->filter(fn($e) => $e->kelas->periode_akademik_id !== $periodeAktif->id)
            : $enrollments;

        $aktifEnrollments = $enrollments->where('status', 'Aktif');

        $stats = [
            'kelas_aktif' => $kelasPeriodeAktif->where('status', 'Aktif')->count(),
            'total_sks'   => $kelasPeriodeAktif->where('status', 'Aktif')
                                ->sum(fn($e) => $e->kelas->mataKuliah?->sks ?? 0),
            'rata_nilai'  => $aktifEnrollments->whereNotNull('nilai_akhir')->avg('nilai_akhir'),
            'total_kelas' => $enrollments->count(),
        ];

        return view('mahasiswa.dashboard', compact(
            'mahasiswa', 'periodeAktif', 'enrollments',
            'stats', 'kelasPeriodeAktif', 'kelasLainnya',
        ));
    }

    public function profile()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $mahasiswa = Mahasiswa::with([
            'jurusan.fakultas',
            'user',
            'enrollments.kelas.mataKuliah',
            'enrollments.kelas.periodeAkademik',
            'enrollments.kelas.instruktur',
        ])->where('user_id', $user->id)->first();

        if (!$mahasiswa) {
            return redirect()->route('mahasiswa.dashboard')
                ->with('error', 'Profil mahasiswa belum terhubung ke akun ini.');
        }

        return view('profile.show', ['mahasiswa' => $mahasiswa, 'jurusan' => collect()]);
    }

    public function updateProfile(Request $request)
    {
        $user      = Auth::user();
        $mahasiswa = Mahasiswa::where('user_id', $user->id)->firstOrFail();

        $request->validate([
            'nama'          => ['required', 'string', 'max:255'],
            'email'         => ['required', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'jenis_kelamin' => ['nullable', 'in:L,P'],
            'tempat_lahir'  => ['nullable', 'string', 'max:100'],
            'tanggal_lahir' => ['nullable', 'date'],
            'no_hp'         => ['nullable', 'string', 'max:20'],
            'alamat'        => ['nullable', 'string', 'max:500'],
        ]);

        $originalEmail = $user->email;

        $user->update([
            'name'  => $request->nama,
            'email' => $request->email,
        ]);

        $mahasiswa->update([
            'nama'          => $request->nama,
            'email'         => $request->email,
            'jenis_kelamin' => $request->jenis_kelamin,
            'tempat_lahir'  => $request->tempat_lahir,
            'tanggal_lahir' => $request->tanggal_lahir,
            'no_hp'         => $request->no_hp,
            'alamat'        => $request->alamat,
        ]);

        if ($request->email !== $originalEmail) {
            $user->forceFill(['email_verified_at' => null])->save();
            $user->sendEmailVerificationNotification();
            return redirect()->route('verification.notice');
        }

        return back()->with('status', 'profile-updated');
    }

    public function updateAvatar(Request $request)
    {
        $request->validate([
            'avatar' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ]);

        $user = Auth::user();

        if ($user->avatar) {
            Storage::disk('public')->delete($user->avatar);
        }

        $path = $request->file('avatar')->store('avatars', 'public');
        $user->update(['avatar' => $path]);

        return back()->with('status', 'avatar-updated');
    }
}
