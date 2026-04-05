<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Jurusan;
use App\Models\Mahasiswa;
use App\Models\Role;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    public function create(): View
    {
        $jurusans = Jurusan::orderBy('nama')->get();

        return view('auth.register', compact('jurusans'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'nama'            => ['required', 'string', 'max:255'],
            'nim'             => ['required', 'string', 'max:30', 'unique:mahasiswa,nim'],
            'email_username'  => ['required', 'string', 'max:100', 'regex:/^[a-zA-Z0-9._-]+$/'],
            'jurusan_id'      => ['required', 'exists:jurusan,id'],
            'angkatan'        => ['required', 'integer', 'min:2000', 'max:' . (date('Y') + 1)],
            'jenis_kelamin'   => ['required', 'in:L,P'],
            'password'        => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $email = strtolower($data['email_username']) . '@umkendari.ac.id';

        // Check email uniqueness manually after combining
        if (User::where('email', $email)->exists()) {
            return back()
                ->withInput()
                ->withErrors(['email_username' => 'Email ini sudah terdaftar.']);
        }

        $user = DB::transaction(function () use ($data, $email) {
            $user = User::create([
                'name'     => $data['nama'],
                'email'    => $email,
                'password' => Hash::make($data['password']),
            ]);

            Mahasiswa::create([
                'user_id'       => $user->id,
                'nim'           => strtoupper($data['nim']),
                'nama'          => $data['nama'],
                'email'         => $email,
                'jurusan_id'    => $data['jurusan_id'],
                'angkatan'      => $data['angkatan'],
                'jenis_kelamin' => $data['jenis_kelamin'],
                'status'        => 'Aktif',
            ]);

            $role = Role::where('name', 'mahasiswa')
                        ->orWhere('name', 'Mahasiswa')
                        ->first();
            if ($role) {
                $user->roles()->attach($role->id);
            }

            return $user;
        });

        event(new Registered($user));
        Auth::login($user);

        return redirect()->route('verification.notice');
    }
}
