<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Instruktur;
use App\Models\Kelas;
use App\Models\MataKuliah;
use App\Models\PeriodeAkademik;
use App\Support\ActivityLogger;
use Illuminate\Http\Request;

class KelasController extends Controller
{
    public function index()
    {
        $kelas      = Kelas::with(['mataKuliah.jurusan.fakultas', 'periodeAkademik', 'instruktur'])->latest()->get();
        $mataKuliah = MataKuliah::with('jurusan')->where('aktif', true)->orderBy('nama')->get();
        $periodes   = PeriodeAkademik::orderByDesc('created_at')->get();
        $instruktur = Instruktur::where('status', 'Aktif')->orderBy('nama')->get();

        $stats = [
            'total'      => $kelas->count(),
            'aktif'      => $kelas->where('status', 'Aktif')->count(),
            'selesai'    => $kelas->where('status', 'Selesai')->count(),
            'dibatalkan' => $kelas->where('status', 'Dibatalkan')->count(),
        ];

        return view('admin.kelas.index', compact('kelas', 'mataKuliah', 'periodes', 'instruktur', 'stats'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'mata_kuliah_id'     => 'required|exists:mata_kuliah,id',
            'periode_akademik_id'=> 'required|exists:periode_akademik,id',
            'kode_seksi'         => 'nullable|string|max:10',
            'kapasitas'          => 'nullable|integer|min:1|max:999',
            'status'             => 'required|in:Aktif,Selesai,Dibatalkan',
            'instruktur_ids'     => 'nullable|array',
            'instruktur_ids.*'   => 'exists:instruktur,id',
        ]);

        $instrukturIds = $data['instruktur_ids'] ?? [];
        unset($data['instruktur_ids']);

        if (isset($data['kode_seksi'])) {
            $data['kode_seksi'] = strtoupper($data['kode_seksi']);
        }

        $kelas = Kelas::create($data);
        $kelas->instruktur()->sync($instrukturIds);
        $kelas->load('mataKuliah', 'periodeAkademik');

        ActivityLogger::log('created', 'kelas', "Kelas {$kelas->kode_display} ditambahkan", $kelas);

        return response()->json(['message' => "Kelas {$kelas->kode_display} berhasil ditambahkan."]);
    }

    public function update(Request $request, Kelas $kelas)
    {
        $data = $request->validate([
            'mata_kuliah_id'     => 'required|exists:mata_kuliah,id',
            'periode_akademik_id'=> 'required|exists:periode_akademik,id',
            'kode_seksi'         => 'nullable|string|max:10',
            'kapasitas'          => 'nullable|integer|min:1|max:999',
            'status'             => 'required|in:Aktif,Selesai,Dibatalkan',
            'instruktur_ids'     => 'nullable|array',
            'instruktur_ids.*'   => 'exists:instruktur,id',
        ]);

        $instrukturIds = $data['instruktur_ids'] ?? [];
        unset($data['instruktur_ids']);

        if (isset($data['kode_seksi'])) {
            $data['kode_seksi'] = strtoupper($data['kode_seksi']);
        }

        $old = $kelas->only('mata_kuliah_id', 'periode_akademik_id', 'kode_seksi', 'kapasitas', 'status');
        $kelas->update($data);
        $kelas->instruktur()->sync($instrukturIds);
        $kelas->load('mataKuliah', 'periodeAkademik');

        ActivityLogger::log('updated', 'kelas', "Kelas {$kelas->kode_display} diperbarui", $kelas, [
            'old' => $old,
            'new' => $kelas->only('mata_kuliah_id', 'periode_akademik_id', 'kode_seksi', 'kapasitas', 'status'),
        ]);

        return response()->json(['message' => "Kelas {$kelas->kode_display} berhasil diperbarui."]);
    }

    public function destroy(Kelas $kelas)
    {
        if ($kelas->enrollments()->exists()) {
            return response()->json(['message' => "Kelas {$kelas->kode_display} tidak dapat dihapus karena masih memiliki mahasiswa yang terdaftar."], 422);
        }

        $nama = $kelas->kode_display;
        $kelas->instruktur()->detach();
        ActivityLogger::log('deleted', 'kelas', "Kelas {$nama} dihapus", $kelas);
        $kelas->delete();

        return response()->json(['message' => "Kelas {$nama} berhasil dihapus."]);
    }
}
