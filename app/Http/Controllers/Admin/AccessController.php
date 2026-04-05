<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Access;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class AccessController extends Controller
{
    public function index()
    {
        $accesses = Access::orderBy('group')->orderBy('display_name')->get();
        $groups   = $accesses->pluck('group')->unique()->sort()->values();
        return view('admin.access.index', compact('accesses', 'groups'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'         => 'required|string|max:100|unique:accesses,name|regex:/^[a-z0-9\-\.]+$/',
            'display_name' => 'required|string|max:100',
            'group'        => 'required|string|max:50',
            'description'  => 'nullable|string|max:255',
        ], [
            'name.regex' => 'Nama hanya boleh huruf kecil, angka, strip (-), dan titik (.).',
        ]);

        Access::create($request->only('name', 'display_name', 'group', 'description'));

        return response()->json(['message' => 'Access berhasil ditambahkan.']);
    }

    public function update(Request $request, Access $access)
    {
        $request->validate([
            'name'         => ['required', 'string', 'max:100', 'regex:/^[a-z0-9\-\.]+$/', Rule::unique('accesses')->ignore($access->id)],
            'display_name' => 'required|string|max:100',
            'group'        => 'required|string|max:50',
            'description'  => 'nullable|string|max:255',
        ], [
            'name.regex' => 'Nama hanya boleh huruf kecil, angka, strip (-), dan titik (.).',
        ]);

        $access->update($request->only('name', 'display_name', 'group', 'description'));

        return response()->json(['message' => 'Access berhasil diperbarui.']);
    }

    public function destroy(Access $access)
    {
        $access->delete();
        return response()->json(['message' => 'Access berhasil dihapus.']);
    }
}
