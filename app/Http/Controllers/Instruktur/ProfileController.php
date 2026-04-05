<?php

namespace App\Http\Controllers\Instruktur;

use App\Http\Controllers\Controller;
use App\Models\Instruktur;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class ProfileController extends Controller
{
    private function getInstruktur(): Instruktur
    {
        return Instruktur::with(['user', 'kelas.mataKuliah', 'kelas.periodeAkademik'])
            ->where('user_id', Auth::id())
            ->firstOrFail();
    }

    public function show()
    {
        $instruktur = $this->getInstruktur();
        return view('instruktur.profile.show', compact('instruktur'));
    }

    public function update(Request $request)
    {
        $user       = Auth::user();
        $instruktur = Instruktur::where('user_id', $user->id)->firstOrFail();

        $request->validate([
            'name'               => ['required', 'string', 'max:255'],
            'email'              => ['required', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'nama'               => ['required', 'string', 'max:255'],
            'nidn'               => ['nullable', 'string', 'max:20'],
            'nip'                => ['nullable', 'string', 'max:30'],
            'jenis_kelamin'      => ['nullable', 'in:Laki-laki,Perempuan'],
            'pendidikan_terakhir'=> ['nullable', 'string', 'max:100'],
            'bidang_keahlian'    => ['nullable', 'string', 'max:255'],
            'no_hp'              => ['nullable', 'string', 'max:20'],
        ]);

        $user->update([
            'name'  => $request->name,
            'email' => $request->email,
        ]);

        $instruktur->update([
            'nama'               => $request->nama,
            'email'              => $request->email,
            'nidn'               => $request->nidn,
            'nip'                => $request->nip,
            'jenis_kelamin'      => $request->jenis_kelamin,
            'pendidikan_terakhir'=> $request->pendidikan_terakhir,
            'bidang_keahlian'    => $request->bidang_keahlian,
            'no_hp'              => $request->no_hp,
        ]);

        return back()->with('status', 'profile-updated');
    }

    public function passwordPage()
    {
        $instruktur = $this->getInstruktur();
        return view('instruktur.profile.password', compact('instruktur'));
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password'      => ['required', 'current_password'],
            'password'              => ['required', 'confirmed', Password::defaults()],
        ], [
            'current_password.current_password' => 'Password saat ini tidak sesuai.',
            'password.confirmed'                => 'Konfirmasi password tidak cocok.',
        ]);

        Auth::user()->update([
            'password' => Hash::make($request->password),
        ]);

        return back()->with('status', 'password-updated');
    }

    public function updateAvatar(Request $request)
    {
        $request->validate([
            'avatar' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ]);

        $user = Auth::user();
        $path = $request->file('avatar')->store('avatars', 'public');
        $user->update(['avatar' => $path]);

        return back()->with('status', 'avatar-updated');
    }
}
