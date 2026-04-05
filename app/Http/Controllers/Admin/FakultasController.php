<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Fakultas;
use App\Support\ActivityLogger;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class FakultasController extends Controller
{
    public function index()
    {
        $fakultas = Fakultas::withCount('jurusan')->latest()->get();
        return view('admin.fakultas.index', compact('fakultas'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'kode'      => 'required|string|max:20|unique:fakultas,kode',
            'nama'      => 'required|string|max:150',
            'singkatan' => 'nullable|string|max:20',
            'deskripsi' => 'nullable|string|max:255',
            'aktif'     => 'boolean',
        ]);

        $data['kode'] = strtoupper($data['kode']);
        $fakultas = Fakultas::create($data);

        ActivityLogger::log('created', 'fakultas', "Fakultas {$fakultas->nama} ({$fakultas->kode}) ditambahkan", $fakultas);

        return response()->json(['message' => "Fakultas {$fakultas->nama} berhasil ditambahkan."]);
    }

    public function update(Request $request, Fakultas $fakultas)
    {
        $data = $request->validate([
            'kode'      => ['required', 'string', 'max:20', Rule::unique('fakultas', 'kode')->ignore($fakultas->id)],
            'nama'      => 'required|string|max:150',
            'singkatan' => 'nullable|string|max:20',
            'deskripsi' => 'nullable|string|max:255',
            'aktif'     => 'boolean',
        ]);

        $data['kode'] = strtoupper($data['kode']);
        $old = $fakultas->only('kode', 'nama', 'singkatan', 'aktif');
        $fakultas->update($data);

        ActivityLogger::log('updated', 'fakultas', "Fakultas {$fakultas->nama} diperbarui", $fakultas, [
            'old' => $old,
            'new' => $fakultas->only('kode', 'nama', 'singkatan', 'aktif'),
        ]);

        return response()->json(['message' => "Fakultas {$fakultas->nama} berhasil diperbarui."]);
    }

    public function destroy(Fakultas $fakultas)
    {
        if ($fakultas->jurusan()->exists()) {
            return response()->json(['message' => "Fakultas {$fakultas->nama} tidak dapat dihapus karena masih memiliki jurusan."], 422);
        }

        $name = $fakultas->nama;
        ActivityLogger::log('deleted', 'fakultas', "Fakultas {$name} ({$fakultas->kode}) dihapus", $fakultas);
        $fakultas->delete();

        return response()->json(['message' => "Fakultas {$name} berhasil dihapus."]);
    }
}
