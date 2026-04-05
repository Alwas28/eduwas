<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Jurusan;
use App\Models\Mahasiswa;
use App\Support\ActivityLogger;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class MahasiswaController extends Controller
{
    public function index()
    {
        $mahasiswa = Mahasiswa::with('jurusan.fakultas')->latest()->get();
        $jurusan   = Jurusan::with('fakultas')->where('aktif', true)->orderBy('nama')->get();

        $stats = [
            'total'   => $mahasiswa->count(),
            'aktif'   => $mahasiswa->where('status', 'Aktif')->count(),
            'cuti'    => $mahasiswa->where('status', 'Cuti')->count(),
            'dropout' => $mahasiswa->where('status', 'Dropout')->count(),
            'lulus'   => $mahasiswa->where('status', 'Lulus')->count(),
        ];

        return view('admin.peserta.index', compact('mahasiswa', 'jurusan', 'stats'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'jurusan_id'    => 'nullable|exists:jurusan,id',
            'nim'           => 'required|string|max:20|unique:mahasiswa,nim',
            'nama'          => 'required|string|max:150',
            'email'         => 'nullable|email|max:100|unique:mahasiswa,email',
            'jenis_kelamin' => 'nullable|in:L,P',
            'tempat_lahir'  => 'nullable|string|max:100',
            'tanggal_lahir' => 'nullable|date',
            'no_hp'         => 'nullable|string|max:20',
            'alamat'        => 'nullable|string',
            'angkatan'      => 'nullable|integer|min:2000|max:2099',
            'status'        => 'required|in:Aktif,Cuti,Dropout,Lulus',
        ]);

        $data['nim'] = strtoupper($data['nim']);
        $mhs = Mahasiswa::create($data);
        $mhs->load('jurusan');

        ActivityLogger::log('created', 'peserta', "Mahasiswa {$mhs->nama} ({$mhs->nim}) ditambahkan", $mhs);

        return response()->json(['message' => "Mahasiswa {$mhs->nama} berhasil ditambahkan."]);
    }

    public function update(Request $request, Mahasiswa $mahasiswa)
    {
        $data = $request->validate([
            'jurusan_id'    => 'nullable|exists:jurusan,id',
            'nim'           => ['required', 'string', 'max:20', Rule::unique('mahasiswa', 'nim')->ignore($mahasiswa->id)],
            'nama'          => 'required|string|max:150',
            'email'         => ['nullable', 'email', 'max:100', Rule::unique('mahasiswa', 'email')->ignore($mahasiswa->id)],
            'jenis_kelamin' => 'nullable|in:L,P',
            'tempat_lahir'  => 'nullable|string|max:100',
            'tanggal_lahir' => 'nullable|date',
            'no_hp'         => 'nullable|string|max:20',
            'alamat'        => 'nullable|string',
            'angkatan'      => 'nullable|integer|min:2000|max:2099',
            'status'        => 'required|in:Aktif,Cuti,Dropout,Lulus',
        ]);

        $data['nim'] = strtoupper($data['nim']);
        $old = $mahasiswa->only('nim', 'nama', 'jurusan_id', 'angkatan', 'status');
        $mahasiswa->update($data);

        ActivityLogger::log('updated', 'peserta', "Mahasiswa {$mahasiswa->nama} diperbarui", $mahasiswa, [
            'old' => $old,
            'new' => $mahasiswa->only('nim', 'nama', 'jurusan_id', 'angkatan', 'status'),
        ]);

        return response()->json(['message' => "Mahasiswa {$mahasiswa->nama} berhasil diperbarui."]);
    }

    public function show(Mahasiswa $mahasiswa)
    {
        $mahasiswa->load([
            'jurusan.fakultas',
            'user',
            'enrollments.kelas.mataKuliah',
            'enrollments.kelas.periodeAkademik',
            'enrollments.kelas.instruktur',
        ]);

        $jurusan = Jurusan::where('aktif', true)->orderBy('nama')->get();

        return view('profile.show', compact('mahasiswa', 'jurusan'));
    }

    public function destroy(Mahasiswa $mahasiswa)
    {
        $nama = $mahasiswa->nama;
        ActivityLogger::log('deleted', 'peserta', "Mahasiswa {$nama} ({$mahasiswa->nim}) dihapus", $mahasiswa);
        $mahasiswa->delete();

        return response()->json(['message' => "Mahasiswa {$nama} berhasil dihapus."]);
    }
}
