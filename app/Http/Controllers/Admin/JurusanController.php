<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Fakultas;
use App\Models\Jurusan;
use App\Support\ActivityLogger;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class JurusanController extends Controller
{
    public function index()
    {
        $jurusan  = Jurusan::with('fakultas')->latest()->get();
        $fakultas = Fakultas::where('aktif', true)->orderBy('nama')->get();
        return view('admin.jurusan.index', compact('jurusan', 'fakultas'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'fakultas_id' => 'required|exists:fakultas,id',
            'kode'        => 'required|string|max:20|unique:jurusan,kode',
            'nama'        => 'required|string|max:150',
            'singkatan'   => 'nullable|string|max:20',
            'deskripsi'   => 'nullable|string|max:255',
            'aktif'       => 'boolean',
        ]);

        $data['kode'] = strtoupper($data['kode']);
        $jurusan = Jurusan::create($data);
        $jurusan->load('fakultas');

        ActivityLogger::log('created', 'jurusan', "Jurusan {$jurusan->nama} ({$jurusan->kode}) di {$jurusan->fakultas->nama} ditambahkan", $jurusan);

        return response()->json(['message' => "Jurusan {$jurusan->nama} berhasil ditambahkan."]);
    }

    public function update(Request $request, Jurusan $jurusan)
    {
        $data = $request->validate([
            'fakultas_id' => 'required|exists:fakultas,id',
            'kode'        => ['required', 'string', 'max:20', Rule::unique('jurusan', 'kode')->ignore($jurusan->id)],
            'nama'        => 'required|string|max:150',
            'singkatan'   => 'nullable|string|max:20',
            'deskripsi'   => 'nullable|string|max:255',
            'aktif'       => 'boolean',
        ]);

        $data['kode'] = strtoupper($data['kode']);
        $old = $jurusan->only('fakultas_id', 'kode', 'nama', 'singkatan', 'aktif');
        $jurusan->update($data);
        $jurusan->load('fakultas');

        ActivityLogger::log('updated', 'jurusan', "Jurusan {$jurusan->nama} diperbarui", $jurusan, [
            'old' => $old,
            'new' => $jurusan->only('fakultas_id', 'kode', 'nama', 'singkatan', 'aktif'),
        ]);

        return response()->json(['message' => "Jurusan {$jurusan->nama} berhasil diperbarui."]);
    }

    public function destroy(Jurusan $jurusan)
    {
        if ($jurusan->mataKuliah()->exists()) {
            return response()->json(['message' => "Jurusan {$jurusan->nama} tidak dapat dihapus karena masih memiliki mata kuliah."], 422);
        }

        if ($jurusan->mahasiswas()->exists()) {
            return response()->json(['message' => "Jurusan {$jurusan->nama} tidak dapat dihapus karena masih memiliki data mahasiswa."], 422);
        }

        $name = $jurusan->nama;
        ActivityLogger::log('deleted', 'jurusan', "Jurusan {$name} ({$jurusan->kode}) dihapus", $jurusan);
        $jurusan->delete();

        return response()->json(['message' => "Jurusan {$name} berhasil dihapus."]);
    }
}
