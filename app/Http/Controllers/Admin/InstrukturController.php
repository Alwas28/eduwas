<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Instruktur;
use App\Models\Role;
use App\Models\User;
use App\Support\ActivityLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class InstrukturController extends Controller
{
    public function index()
    {
        $instruktur = Instruktur::latest()->get();

        $stats = [
            'total'    => $instruktur->count(),
            'aktif'    => $instruktur->where('status', 'Aktif')->count(),
            'nonaktif' => $instruktur->where('status', 'Nonaktif')->count(),
            's3'       => $instruktur->where('pendidikan_terakhir', 'S3')->count(),
        ];

        return view('admin.instruktur.index', compact('instruktur', 'stats'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nidn'               => 'nullable|string|max:20|unique:instruktur,nidn',
            'nip'                => 'nullable|string|max:30|unique:instruktur,nip',
            'nama'               => 'required|string|max:150',
            'email'              => 'nullable|email|max:100|unique:instruktur,email',
            'jenis_kelamin'      => 'nullable|in:L,P',
            'bidang_keahlian'    => 'nullable|string|max:150',
            'pendidikan_terakhir'=> 'nullable|in:S1,S2,S3',
            'no_hp'              => 'nullable|string|max:20',
            'status'             => 'required|in:Aktif,Nonaktif',
        ]);

        $instruktur = Instruktur::create($data);

        ActivityLogger::log('created', 'instruktur', "Instruktur {$instruktur->nama} ditambahkan", $instruktur);

        return response()->json(['message' => "Instruktur {$instruktur->nama} berhasil ditambahkan."]);
    }

    public function update(Request $request, Instruktur $instruktur)
    {
        $data = $request->validate([
            'nidn'               => ['nullable', 'string', 'max:20', Rule::unique('instruktur', 'nidn')->ignore($instruktur->id)],
            'nip'                => ['nullable', 'string', 'max:30', Rule::unique('instruktur', 'nip')->ignore($instruktur->id)],
            'nama'               => 'required|string|max:150',
            'email'              => ['nullable', 'email', 'max:100', Rule::unique('instruktur', 'email')->ignore($instruktur->id)],
            'jenis_kelamin'      => 'nullable|in:L,P',
            'bidang_keahlian'    => 'nullable|string|max:150',
            'pendidikan_terakhir'=> 'nullable|in:S1,S2,S3',
            'no_hp'              => 'nullable|string|max:20',
            'status'             => 'required|in:Aktif,Nonaktif',
        ]);

        $old = $instruktur->only('nidn', 'nip', 'nama', 'email', 'status');
        $instruktur->update($data);

        ActivityLogger::log('updated', 'instruktur', "Instruktur {$instruktur->nama} diperbarui", $instruktur, [
            'old' => $old,
            'new' => $instruktur->only('nidn', 'nip', 'nama', 'email', 'status'),
        ]);

        return response()->json(['message' => "Instruktur {$instruktur->nama} berhasil diperbarui."]);
    }

    public function show(Instruktur $instruktur)
    {
        // Auto-link akun jika email instruktur cocok dengan user yang sudah ada
        if (!$instruktur->user_id && $instruktur->email) {
            $user = User::where('email', $instruktur->email)->first();
            if ($user) {
                $instruktur->update(['user_id' => $user->id]);
                ActivityLogger::log('updated', 'instruktur', "Akun instruktur {$instruktur->nama} otomatis ditautkan berdasarkan email", $instruktur);
            }
        }

        $instruktur->load([
            'user',
            'kelas.mataKuliah',
            'kelas.periodeAkademik',
        ]);

        return view('admin.instruktur.show', compact('instruktur'));
    }

    public function createAccount(Request $request, Instruktur $instruktur)
    {
        if (!auth()->user()->allAccesses()->contains('tambah.user')) {
            return response()->json(['message' => 'Anda tidak memiliki hak akses untuk membuat akun.'], 403);
        }

        if ($instruktur->user_id) {
            return response()->json(['message' => 'Instruktur ini sudah memiliki akun.'], 422);
        }

        // Jika sudah ada user dengan email yang sama, tautkan saja
        $existingUser = User::where('email', $instruktur->email)->first();
        if ($existingUser) {
            $instruktur->update(['user_id' => $existingUser->id]);
            ActivityLogger::log('updated', 'instruktur', "Akun instruktur {$instruktur->nama} ditautkan ke akun existing", $existingUser);
            return response()->json(['message' => "Akun yang ada berhasil ditautkan ke profil {$instruktur->nama}."]);
        }

        $data = $request->validate([
            'name'     => 'required|string|max:150',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|string|min:8',
        ]);

        DB::transaction(function () use ($data, $instruktur) {
            $user = User::create([
                'name'     => $data['name'],
                'email'    => $data['email'],
                'password' => Hash::make($data['password']),
            ]);
            $role = Role::whereRaw('LOWER(name) = ?', ['instruktur'])->first();
            if ($role) $user->roles()->attach($role->id);
            $instruktur->update(['user_id' => $user->id]);

            ActivityLogger::log('created', 'user', "Akun dibuat untuk instruktur {$instruktur->nama}", $user);
        });

        return response()->json(['message' => "Akun berhasil dibuat untuk {$instruktur->nama}."]);
    }

    public function resetPassword(Request $request, Instruktur $instruktur)
    {
        if (!auth()->user()->allAccesses()->contains('reset-password.user')) {
            return response()->json(['message' => 'Anda tidak memiliki hak akses untuk mereset password.'], 403);
        }

        if (!$instruktur->user_id) {
            return response()->json(['message' => 'Instruktur ini belum memiliki akun.'], 422);
        }

        $newPassword = Str::random(12);
        $instruktur->user->update(['password' => Hash::make($newPassword)]);

        ActivityLogger::log('updated', 'user', "Password instruktur {$instruktur->nama} direset oleh admin", $instruktur->user);

        return response()->json([
            'message'      => 'Password berhasil direset.',
            'new_password' => $newPassword,
        ]);
    }

    public function uploadAvatar(Request $request, Instruktur $instruktur)
    {
        if (!$instruktur->user_id) {
            return response()->json(['message' => 'Instruktur belum memiliki akun.'], 422);
        }

        $request->validate(['avatar' => 'required|image|mimes:jpg,jpeg,png,webp|max:2048']);

        $user = $instruktur->user;
        if ($user->avatar) {
            Storage::disk('public')->delete($user->avatar);
        }

        $path = $request->file('avatar')->store('avatars', 'public');
        $user->update(['avatar' => $path]);

        ActivityLogger::log('updated', 'instruktur', "Foto profil instruktur {$instruktur->nama} diperbarui", $instruktur);

        return response()->json([
            'message' => 'Foto profil berhasil diperbarui.',
            'url'     => Storage::url($path),
        ]);
    }

    public function destroy(Instruktur $instruktur)
    {
        $nama = $instruktur->nama;
        ActivityLogger::log('deleted', 'instruktur', "Instruktur {$nama} dihapus", $instruktur);
        $instruktur->delete();

        return response()->json(['message' => "Instruktur {$nama} berhasil dihapus."]);
    }
}
