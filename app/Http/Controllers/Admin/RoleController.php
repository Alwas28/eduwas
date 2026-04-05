<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class RoleController extends Controller
{
    public function index()
    {
        $roles = Role::latest()->get();
        return view('admin.roles.index', compact('roles'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'         => 'required|string|max:50|unique:roles,name|regex:/^[a-z0-9_\-]+$/',
            'display_name' => 'required|string|max:100',
            'description'  => 'nullable|string|max:255',
        ], [
            'name.regex' => 'Nama hanya boleh huruf kecil, angka, strip, dan underscore.',
        ]);

        Role::create($request->only('name', 'display_name', 'description'));

        return response()->json(['message' => 'Role berhasil ditambahkan.']);
    }

    public function update(Request $request, Role $role)
    {
        $request->validate([
            'name'         => ['required', 'string', 'max:50', 'regex:/^[a-z0-9_\-]+$/', Rule::unique('roles')->ignore($role->id)],
            'display_name' => 'required|string|max:100',
            'description'  => 'nullable|string|max:255',
        ], [
            'name.regex' => 'Nama hanya boleh huruf kecil, angka, strip, dan underscore.',
        ]);

        $role->update($request->only('name', 'display_name', 'description'));

        return response()->json(['message' => 'Role berhasil diperbarui.']);
    }

    public function destroy(Role $role)
    {
        $role->delete();

        return response()->json(['message' => 'Role berhasil dihapus.']);
    }
}
