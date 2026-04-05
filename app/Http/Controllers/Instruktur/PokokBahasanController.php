<?php

namespace App\Http\Controllers\Instruktur;

use App\Http\Controllers\Controller;
use App\Models\PokokBahasan;
use App\Support\ActivityLogger;
use Illuminate\Http\Request;

class PokokBahasanController extends Controller
{
    public function store(Request $request)
    {
        $instruktur = auth()->user()->instruktur()->firstOrFail();

        // Verify instruktur teaches this mata_kuliah
        $mataKuliahId = (int) $request->mata_kuliah_id;
        $teaches = $instruktur->kelas()->where('mata_kuliah_id', $mataKuliahId)->exists();
        abort_if(!$teaches, 403);

        $data = $request->validate([
            'mata_kuliah_id' => 'required|integer|exists:mata_kuliah,id',
            'pertemuan'      => 'required|integer|min:1|max:99',
            'judul'          => 'required|string|max:200',
            'deskripsi'      => 'nullable|string|max:500',
            'urutan'         => 'nullable|integer|min:0|max:999',
        ]);

        $data['instruktur_id'] = $instruktur->id;
        $data['urutan']        = $data['urutan'] ?? (
            PokokBahasan::where('instruktur_id', $instruktur->id)
                ->where('mata_kuliah_id', $mataKuliahId)
                ->max('urutan') + 1
        );

        $pb = PokokBahasan::create($data);

        ActivityLogger::log(
            'created', 'materi',
            "Pokok bahasan \"{$pb->judul}\" (Pertemuan {$pb->pertemuan}) ditambahkan",
            $pb
        );

        return response()->json([
            'message'       => "Pokok bahasan \"{$pb->judul}\" berhasil ditambahkan.",
            'pokok_bahasan' => $this->pbFrontend($pb),
        ]);
    }

    public function update(Request $request, PokokBahasan $pokokBahasan)
    {
        $instruktur = auth()->user()->instruktur()->firstOrFail();
        abort_if($pokokBahasan->instruktur_id !== $instruktur->id, 403);

        $data = $request->validate([
            'pertemuan' => 'required|integer|min:1|max:99',
            'judul'     => 'required|string|max:200',
            'deskripsi' => 'nullable|string|max:500',
            'urutan'    => 'nullable|integer|min:0|max:999',
        ]);

        $data['urutan'] = $data['urutan'] ?? $pokokBahasan->urutan;
        $pokokBahasan->update($data);

        ActivityLogger::log(
            'updated', 'materi',
            "Pokok bahasan \"{$pokokBahasan->judul}\" diperbarui",
            $pokokBahasan
        );

        return response()->json([
            'message'       => "Pokok bahasan \"{$pokokBahasan->judul}\" berhasil diperbarui.",
            'pokok_bahasan' => $this->pbFrontend($pokokBahasan),
        ]);
    }

    public function destroy(PokokBahasan $pokokBahasan)
    {
        $instruktur = auth()->user()->instruktur()->firstOrFail();
        abort_if($pokokBahasan->instruktur_id !== $instruktur->id, 403);

        if ($pokokBahasan->materi()->exists()) {
            return response()->json([
                'message' => 'Hapus semua materi dalam pokok bahasan ini terlebih dahulu.',
            ], 422);
        }

        $judul = $pokokBahasan->judul;
        ActivityLogger::log('deleted', 'materi', "Pokok bahasan \"{$judul}\" dihapus", $pokokBahasan);
        $pokokBahasan->delete();

        return response()->json(['message' => "Pokok bahasan \"{$judul}\" berhasil dihapus."]);
    }

    private function pbFrontend(PokokBahasan $pb): array
    {
        return [
            'id'             => $pb->id,
            'mata_kuliah_id' => $pb->mata_kuliah_id,
            'pertemuan'      => $pb->pertemuan,
            'judul'          => $pb->judul,
            'deskripsi'      => $pb->deskripsi,
            'urutan'         => $pb->urutan,
            'materi_count'   => $pb->materi()->count(),
        ];
    }
}
