<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Access;
use App\Models\Role;
use Illuminate\Http\Request;

class RoleAccessController extends Controller
{
    public function index()
    {
        $roles    = Role::withCount('accesses')->with('accesses')->latest()->get();
        $accesses = Access::orderBy('group')->orderBy('display_name')->get();
        $groups   = $accesses->pluck('group')->unique()->sort()->values();
        return view('admin.role-access.index', compact('roles', 'accesses', 'groups'));
    }

    public function edit(Role $role)
    {
        $accesses = Access::orderBy('group')->orderBy('display_name')->get();
        $groups   = $accesses->pluck('group')->unique()->sort()->values();
        $roleAccessIds = $role->accesses()->pluck('access_id')->toArray();
        return view('admin.role-access.edit', compact('role', 'accesses', 'groups', 'roleAccessIds'));
    }

    public function update(Request $request, Role $role)
    {
        $request->validate([
            'access_ids'   => 'nullable|array',
            'access_ids.*' => 'exists:accesses,id',
        ]);

        $role->accesses()->sync($request->input('access_ids', []));

        return response()->json(['message' => "Access untuk role {$role->display_name} berhasil diperbarui."]);
    }
}
