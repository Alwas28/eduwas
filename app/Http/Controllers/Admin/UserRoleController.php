<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Instruktur;
use App\Models\Mahasiswa;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;

class UserRoleController extends Controller
{
    public function index()
    {
        $users = User::with('roles')->latest()->get();
        $roles = Role::orderBy('display_name')->get();
        return view('admin.user-roles.index', compact('users', 'roles'));
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'role_ids'   => 'nullable|array',
            'role_ids.*' => 'exists:roles,id',
        ]);

        $roleIds = $request->input('role_ids', []);
        $user->roles()->sync($roleIds);

        // Auto-link ke tabel instruktur/mahasiswa berdasarkan email jika belum terhubung
        $roleNames = Role::whereIn('id', $roleIds)->pluck('name');

        if ($roleNames->contains('instruktur')) {
            Instruktur::where('email', $user->email)
                ->whereNull('user_id')
                ->update(['user_id' => $user->id]);
        }

        if ($roleNames->contains('mahasiswa')) {
            Mahasiswa::where('email', $user->email)
                ->whereNull('user_id')
                ->update(['user_id' => $user->id]);
        }

        return response()->json(['message' => "Roles untuk {$user->name} berhasil diperbarui."]);
    }
}
