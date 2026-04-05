<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PeriodeAkademik;
use App\Support\ActivityLogger;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PeriodeAkademikController extends Controller
{
    public function index()
    {
        $periodes = PeriodeAkademik::latest()->get();

        $stats = [
            'total'       => $periodes->count(),
            'aktif'       => $periodes->where('status', 'Aktif')->count(),
            'tidak_aktif' => $periodes->where('status', 'Tidak Aktif')->count(),
            'selesai'     => $periodes->where('status', 'Selesai')->count(),
        ];

        return view('admin.periode-akademik.index', compact('periodes', 'stats'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'kode'            => 'required|string|max:20|unique:periode_akademik,kode',
            'nama'            => 'required|string|max:150',
            'tahun_ajaran'    => 'required|string|max:20',
            'semester'        => 'required|in:Ganjil,Genap',
            'tanggal_mulai'   => 'required|date',
            'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
            'status'          => 'required|in:Aktif,Tidak Aktif,Selesai',
            'deskripsi'       => 'nullable|string|max:255',
        ], [
            'tanggal_selesai.after_or_equal' => 'Tanggal selesai harus setelah atau sama dengan tanggal mulai.',
        ]);

        $data['kode'] = strtoupper($data['kode']);

        // Jika set Aktif, nonaktifkan yang lain
        if ($data['status'] === 'Aktif') {
            PeriodeAkademik::where('status', 'Aktif')->update(['status' => 'Tidak Aktif']);
        }

        $periode = PeriodeAkademik::create($data);

        ActivityLogger::log('created', 'periode-akademik', "Periode {$periode->nama} ditambahkan", $periode);

        return response()->json(['message' => "Periode {$periode->nama} berhasil ditambahkan."]);
    }

    public function update(Request $request, PeriodeAkademik $periodeAkademik)
    {
        $data = $request->validate([
            'kode'            => ['required', 'string', 'max:20', Rule::unique('periode_akademik', 'kode')->ignore($periodeAkademik->id)],
            'nama'            => 'required|string|max:150',
            'tahun_ajaran'    => 'required|string|max:20',
            'semester'        => 'required|in:Ganjil,Genap',
            'tanggal_mulai'   => 'required|date',
            'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
            'status'          => 'required|in:Aktif,Tidak Aktif,Selesai',
            'deskripsi'       => 'nullable|string|max:255',
        ], [
            'tanggal_selesai.after_or_equal' => 'Tanggal selesai harus setelah atau sama dengan tanggal mulai.',
        ]);

        $data['kode'] = strtoupper($data['kode']);

        // Jika set Aktif, nonaktifkan yang lain
        if ($data['status'] === 'Aktif') {
            PeriodeAkademik::where('status', 'Aktif')
                ->where('id', '!=', $periodeAkademik->id)
                ->update(['status' => 'Tidak Aktif']);
        }

        $old = $periodeAkademik->only('kode', 'nama', 'tahun_ajaran', 'semester', 'status');
        $periodeAkademik->update($data);

        ActivityLogger::log('updated', 'periode-akademik', "Periode {$periodeAkademik->nama} diperbarui", $periodeAkademik, [
            'old' => $old,
            'new' => $periodeAkademik->only('kode', 'nama', 'tahun_ajaran', 'semester', 'status'),
        ]);

        return response()->json(['message' => "Periode {$periodeAkademik->nama} berhasil diperbarui."]);
    }

    public function destroy(PeriodeAkademik $periodeAkademik)
    {
        if ($periodeAkademik->kelas()->exists()) {
            return response()->json(['message' => "Periode {$periodeAkademik->nama} tidak dapat dihapus karena masih memiliki kelas yang terdaftar."], 422);
        }

        $nama = $periodeAkademik->nama;
        ActivityLogger::log('deleted', 'periode-akademik', "Periode {$nama} dihapus", $periodeAkademik);
        $periodeAkademik->delete();

        return response()->json(['message' => "Periode {$nama} berhasil dihapus."]);
    }
}
