<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Fakultas;
use App\Models\Jurusan;
use App\Models\MataKuliah;
use App\Support\ActivityLogger;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class MataKuliahController extends Controller
{
    public function index()
    {
        $mataKuliah = MataKuliah::with('jurusan.fakultas')->latest()->get();
        $jurusan    = Jurusan::with('fakultas')->where('aktif', true)->orderBy('nama')->get();
        $fakultas   = Fakultas::where('aktif', true)->orderBy('nama')->get();

        $stats = [
            'total'   => $mataKuliah->count(),
            'aktif'   => $mataKuliah->where('aktif', true)->count(),
            'wajib'   => $mataKuliah->where('jenis', 'Wajib')->count(),
            'pilihan' => $mataKuliah->where('jenis', 'Pilihan')->count(),
        ];

        return view('admin.matakuliah.index', compact('mataKuliah', 'jurusan', 'fakultas', 'stats'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'jurusan_id' => 'required|exists:jurusan,id',
            'kode'       => 'required|string|max:20|unique:mata_kuliah,kode',
            'nama'       => 'required|string|max:150',
            'sks'        => 'required|integer|min:1|max:6',
            'semester'   => 'nullable|integer|min:1|max:8',
            'jenis'      => 'required|in:Wajib,Pilihan',
            'deskripsi'  => 'nullable|string|max:255',
            'aktif'      => 'boolean',
        ]);

        $data['kode'] = strtoupper($data['kode']);
        $mk = MataKuliah::create($data);
        $mk->load('jurusan.fakultas');

        ActivityLogger::log('created', 'matakuliah', "Mata kuliah {$mk->nama} ({$mk->kode}) di {$mk->jurusan->nama} ditambahkan", $mk);

        return response()->json(['message' => "Mata kuliah {$mk->nama} berhasil ditambahkan."]);
    }

    public function update(Request $request, MataKuliah $mataKuliah)
    {
        $data = $request->validate([
            'jurusan_id' => 'required|exists:jurusan,id',
            'kode'       => ['required', 'string', 'max:20', Rule::unique('mata_kuliah', 'kode')->ignore($mataKuliah->id)],
            'nama'       => 'required|string|max:150',
            'sks'        => 'required|integer|min:1|max:6',
            'semester'   => 'nullable|integer|min:1|max:8',
            'jenis'      => 'required|in:Wajib,Pilihan',
            'deskripsi'  => 'nullable|string|max:255',
            'aktif'      => 'boolean',
        ]);

        $data['kode'] = strtoupper($data['kode']);
        $old = $mataKuliah->only('jurusan_id', 'kode', 'nama', 'sks', 'semester', 'jenis', 'aktif');
        $mataKuliah->update($data);

        ActivityLogger::log('updated', 'matakuliah', "Mata kuliah {$mataKuliah->nama} diperbarui", $mataKuliah, [
            'old' => $old,
            'new' => $mataKuliah->only('jurusan_id', 'kode', 'nama', 'sks', 'semester', 'jenis', 'aktif'),
        ]);

        return response()->json(['message' => "Mata kuliah {$mataKuliah->nama} berhasil diperbarui."]);
    }

    public function destroy(MataKuliah $mataKuliah)
    {
        if ($mataKuliah->kelas()->exists()) {
            return response()->json(['message' => "Mata kuliah {$mataKuliah->nama} tidak dapat dihapus karena masih digunakan pada kelas yang terdaftar."], 422);
        }

        $nama = $mataKuliah->nama;
        ActivityLogger::log('deleted', 'matakuliah', "Mata kuliah {$nama} ({$mataKuliah->kode}) dihapus", $mataKuliah);
        $mataKuliah->delete();

        return response()->json(['message' => "Mata kuliah {$nama} berhasil dihapus."]);
    }
}
