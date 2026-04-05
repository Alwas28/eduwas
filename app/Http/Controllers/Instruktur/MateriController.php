<?php

namespace App\Http\Controllers\Instruktur;

use App\Http\Controllers\Controller;
use App\Models\Kelas;
use App\Models\Materi;
use App\Models\MateriAkses;
use App\Models\MataKuliah;
use App\Models\PokokBahasan;
use App\Support\ActivityLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MateriController extends Controller
{
    private function instruktur()
    {
        return auth()->user()->instruktur()
            ->with(['kelas.mataKuliah', 'kelas.periodeAkademik'])
            ->firstOrFail();
    }

    public function index(Request $request)
    {
        $instruktur = $this->instruktur();

        // Unique mata kuliah from all kelas the instruktur teaches
        $mataKuliahList = $instruktur->kelas
            ->map(fn($k) => $k->mataKuliah)
            ->filter()
            ->unique('id')
            ->sortBy('nama')
            ->values();

        $selectedId = $request->integer('mk_id') ?: $mataKuliahList->first()?->id;
        $selectedMk = $mataKuliahList->firstWhere('id', $selectedId);

        // All kelas for the selected mata_kuliah (for RPS management)
        $kelasList = collect();
        if ($selectedMk) {
            $kelasList = $instruktur->kelas
                ->where('mata_kuliah_id', $selectedMk->id)
                ->sortByDesc(fn($k) => $k->periodeAkademik?->created_at)
                ->values();
        }

        $pokokBahasanList = collect();
        if ($selectedMk) {
            $pokokBahasanList = PokokBahasan::where('instruktur_id', $instruktur->id)
                ->where('mata_kuliah_id', $selectedMk->id)
                ->orderBy('urutan')
                ->orderBy('pertemuan')
                ->get();
        }

        $stats = [
            'total'   => $instruktur->materi()->count(),
            'aktif'   => $instruktur->materi()->where('status', 'Aktif')->count(),
            'draft'   => $instruktur->materi()->where('status', 'Draft')->count(),
            'dokumen' => $instruktur->materi()->where('tipe', 'dokumen')->count(),
        ];

        $nextPertemuan = $selectedMk
            ? (PokokBahasan::where('instruktur_id', $instruktur->id)
                ->where('mata_kuliah_id', $selectedMk->id)
                ->max('pertemuan') ?? 0) + 1
            : 1;

        return view('instruktur.materi.index', compact(
            'instruktur', 'mataKuliahList', 'selectedMk',
            'kelasList', 'pokokBahasanList', 'stats', 'nextPertemuan'
        ));
    }

    // ── PB Materi page ──────────────────────────────────────────

    public function showPB(PokokBahasan $pokokBahasan)
    {
        $instruktur = auth()->user()->instruktur()->firstOrFail();
        abort_if($pokokBahasan->instruktur_id !== $instruktur->id, 403);

        $mataKuliah = $pokokBahasan->mataKuliah()->firstOrFail();
        $materi     = $pokokBahasan->materi()->get();

        $kelasList = $instruktur->kelas()
            ->where('mata_kuliah_id', $mataKuliah->id)
            ->select('id', 'kode_seksi')
            ->get();

        return view('instruktur.materi.pokok-bahasan', compact('pokokBahasan', 'materi', 'mataKuliah', 'kelasList'));
    }

    public function reorder(Request $request)
    {
        $instruktur = auth()->user()->instruktur()->firstOrFail();
        $request->validate(['items' => 'required|array', 'items.*' => 'integer']);

        foreach ($request->items as $urutan => $id) {
            Materi::where('id', $id)
                ->where('instruktur_id', $instruktur->id)
                ->update(['urutan' => $urutan + 1]);
        }

        return response()->json(['message' => 'Urutan berhasil disimpan.']);
    }

    // ── Materi CRUD ─────────────────────────────────────────────

    public function store(Request $request)
    {
        $instruktur = auth()->user()->instruktur()->firstOrFail();

        $pb = PokokBahasan::where('id', $request->pokok_bahasan_id)
            ->where('instruktur_id', $instruktur->id)
            ->firstOrFail();

        $data = $request->validate([
            'pokok_bahasan_id'=> 'required|integer',
            'judul'           => 'required|string|max:200',
            'deskripsi'       => 'nullable|string|max:1000',
            'tipe'            => 'required|in:dokumen,video,link,teks',
            'url'             => 'nullable|url|max:1000|required_if:tipe,link,video',
            'konten'          => 'nullable|string|required_if:tipe,teks',
            'urutan'          => 'nullable|integer|min:0|max:999',
            'status'          => 'required|in:Aktif,Draft',
            'allow_download'  => 'nullable|boolean',
            'file'            => 'nullable|file|max:51200|mimes:pdf,doc,docx,ppt,pptx,xls,xlsx,zip,mp4,png,jpg,jpeg',
        ]);

        $data['instruktur_id']  = $instruktur->id;
        $data['mata_kuliah_id'] = $pb->mata_kuliah_id;
        $data['urutan']         = $data['urutan'] ?? ($pb->materi()->max('urutan') + 1);
        unset($data['file']);

        if ($request->hasFile('file') && $request->tipe === 'dokumen') {
            $file               = $request->file('file');
            $data['file_path']  = $file->store("materi/{$pb->mata_kuliah_id}", 'public');
            $data['nama_file']  = $file->getClientOriginalName();
            $data['ukuran_file']= $file->getSize();
        }

        $materi = Materi::create($data);

        ActivityLogger::log(
            'created', 'materi',
            "Materi \"{$materi->judul}\" ditambahkan di Pertemuan {$pb->pertemuan} — {$pb->mataKuliah?->kode}",
            $materi
        );

        return response()->json([
            'message' => "Materi \"{$materi->judul}\" berhasil ditambahkan.",
            'materi'  => $this->materiFrontend($materi),
        ]);
    }

    public function update(Request $request, Materi $materi)
    {
        $instruktur = auth()->user()->instruktur()->firstOrFail();
        abort_if($materi->instruktur_id !== $instruktur->id, 403);

        $data = $request->validate([
            'judul'     => 'required|string|max:200',
            'deskripsi' => 'nullable|string|max:1000',
            'tipe'      => 'required|in:dokumen,video,link,teks',
            'url'       => 'nullable|url|max:1000|required_if:tipe,link,video',
            'konten'    => 'nullable|string|required_if:tipe,teks',
            'urutan'         => 'nullable|integer|min:0|max:999',
            'status'         => 'required|in:Aktif,Draft',
            'allow_download' => 'nullable|boolean',
            'file'           => 'nullable|file|max:51200|mimes:pdf,doc,docx,ppt,pptx,xls,xlsx,zip,mp4,png,jpg,jpeg',
        ]);

        $old = $materi->only('judul', 'tipe', 'status', 'urutan');
        $data['urutan'] = $data['urutan'] ?? $materi->urutan;
        unset($data['file']);

        if ($request->hasFile('file') && $request->tipe === 'dokumen') {
            // Only delete file if no other materi references it
            if ($materi->file_path) {
                $refs = Materi::where('file_path', $materi->file_path)->count();
                if ($refs <= 1) Storage::disk('public')->delete($materi->file_path);
            }
            $file               = $request->file('file');
            $data['file_path']  = $file->store("materi/{$materi->mata_kuliah_id}", 'public');
            $data['nama_file']  = $file->getClientOriginalName();
            $data['ukuran_file']= $file->getSize();
        }

        $materi->update($data);

        ActivityLogger::log(
            'updated', 'materi',
            "Materi \"{$materi->judul}\" diperbarui",
            $materi,
            ['old' => $old, 'new' => $materi->only('judul', 'tipe', 'status', 'urutan')]
        );

        return response()->json([
            'message' => "Materi \"{$materi->judul}\" berhasil diperbarui.",
            'materi'  => $this->materiFrontend($materi),
        ]);
    }

    public function destroy(Materi $materi)
    {
        $instruktur = auth()->user()->instruktur()->firstOrFail();
        abort_if($materi->instruktur_id !== $instruktur->id, 403);

        $judul = $materi->judul;

        if ($materi->file_path) {
            $refs = Materi::where('file_path', $materi->file_path)->count();
            if ($refs <= 1) Storage::disk('public')->delete($materi->file_path);
        }

        ActivityLogger::log('deleted', 'materi', "Materi \"{$judul}\" dihapus", $materi);
        $materi->delete();

        return response()->json(['message' => "Materi \"{$judul}\" berhasil dihapus."]);
    }

    public function togglePublish(Materi $materi)
    {
        $instruktur = auth()->user()->instruktur()->firstOrFail();
        abort_if($materi->instruktur_id !== $instruktur->id, 403);

        $newStatus = $materi->status === 'Aktif' ? 'Draft' : 'Aktif';
        $materi->update(['status' => $newStatus]);

        ActivityLogger::log(
            'updated', 'materi',
            "Materi \"{$materi->judul}\" " . ($newStatus === 'Aktif' ? 'dipublikasikan' : 'disembunyikan'),
            $materi
        );

        return response()->json([
            'message' => $newStatus === 'Aktif'
                ? "Materi \"{$materi->judul}\" sekarang dapat dilihat mahasiswa."
                : "Materi \"{$materi->judul}\" disembunyikan dari mahasiswa.",
            'status'  => $newStatus,
        ]);
    }

    // ── PB Rangkuman toggle ─────────────────────────────────────

    public function togglePbRangkuman(PokokBahasan $pokokBahasan)
    {
        $instruktur = auth()->user()->instruktur()->firstOrFail();
        abort_if($pokokBahasan->instruktur_id !== $instruktur->id, 403);

        $pokokBahasan->update(['rangkuman_aktif' => ! $pokokBahasan->rangkuman_aktif]);

        return response()->json([
            'rangkuman_aktif' => (bool) $pokokBahasan->rangkuman_aktif,
            'message'         => $pokokBahasan->rangkuman_aktif
                ? 'Rangkuman mahasiswa diaktifkan.'
                : 'Rangkuman mahasiswa dinonaktifkan.',
        ]);
    }

    public function gradeRangkuman(Request $request, \App\Models\PbRangkuman $pbRangkuman)
    {
        $instruktur = auth()->user()->instruktur()->firstOrFail();
        abort_if($pbRangkuman->pokokBahasan->instruktur_id !== $instruktur->id, 403);

        $data = $request->validate([
            'nilai'   => 'nullable|integer|min:0|max:100',
            'catatan' => 'nullable|string|max:1000',
        ]);

        $pbRangkuman->update($data);

        return response()->json(['ok' => true, 'nilai' => $pbRangkuman->nilai]);
    }

    // ── RPS ─────────────────────────────────────────────────────

    public function uploadRps(Request $request, Kelas $kelas)
    {
        $instruktur = auth()->user()->instruktur()->firstOrFail();
        abort_if(!$instruktur->kelas->contains($kelas->id), 403);

        $request->validate(['rps' => 'required|file|max:10240|mimes:pdf,doc,docx']);

        if ($kelas->rps_path) Storage::disk('public')->delete($kelas->rps_path);

        $file = $request->file('rps');
        $kelas->update([
            'rps_path'      => $file->store("rps/{$kelas->id}", 'public'),
            'rps_nama_file' => $file->getClientOriginalName(),
            'rps_ukuran'    => $file->getSize(),
        ]);

        ActivityLogger::log(
            'updated', 'materi',
            "RPS kelas {$kelas->kode_display} diunggah: {$kelas->rps_nama_file}",
            $kelas
        );

        return response()->json([
            'message'   => 'RPS berhasil diunggah.',
            'rps_url'   => $kelas->rpsUrl(),
            'nama_file' => $kelas->rps_nama_file,
            'ukuran'    => $kelas->rpsUkuranHuman(),
        ]);
    }

    public function pbPreview(PokokBahasan $pokokBahasan)
    {
        $instruktur = auth()->user()->instruktur()->firstOrFail();
        abort_if($pokokBahasan->instruktur_id !== $instruktur->id, 403);

        $mataKuliah = $pokokBahasan->mataKuliah()->firstOrFail();
        $materi     = $pokokBahasan->materi()->orderBy('urutan')->get();

        return view('instruktur.materi.preview', compact('pokokBahasan', 'mataKuliah', 'materi'));
    }

    public function pbRekap(PokokBahasan $pokokBahasan)
    {
        $instruktur = auth()->user()->instruktur()->firstOrFail();
        abort_if($pokokBahasan->instruktur_id !== $instruktur->id, 403);

        $mataKuliah = $pokokBahasan->mataKuliah()->firstOrFail();

        $materiList = $pokokBahasan->materi()
            ->orderBy('urutan')
            ->with(['akses' => fn($q) => $q
                ->with('user:id,name')
                ->orderBy('terakhir_diakses_at', 'desc')
            ])
            ->get();

        $totalPengakses = $materiList->flatMap(fn($m) => $m->akses->pluck('user_id'))->unique()->count();
        $totalAkses     = $materiList->sum(fn($m) => $m->akses->sum('jumlah_akses'));

        // Kelas instruktur untuk mata kuliah ini
        $kelasList = $instruktur->kelas()
            ->where('mata_kuliah_id', $mataKuliah->id)
            ->select('id', 'kode_seksi', 'mata_kuliah_id')
            ->get();

        // Rangkuman mahasiswa untuk PB ini
        $rangkumanList = \App\Models\PbRangkuman::with('user:id,name')
            ->where('pokok_bahasan_id', $pokokBahasan->id)
            ->orderBy('created_at')
            ->get();

        return view('instruktur.materi.rekap', compact(
            'pokokBahasan', 'mataKuliah', 'materiList', 'totalPengakses', 'totalAkses', 'kelasList', 'rangkumanList'
        ));
    }

    public function deleteRps(Kelas $kelas)
    {
        $instruktur = auth()->user()->instruktur()->firstOrFail();
        abort_if(!$instruktur->kelas->contains($kelas->id), 403);

        if ($kelas->rps_path) Storage::disk('public')->delete($kelas->rps_path);
        $kelas->update(['rps_path' => null, 'rps_nama_file' => null, 'rps_ukuran' => null]);

        ActivityLogger::log('deleted', 'materi', "RPS kelas {$kelas->kode_display} dihapus", $kelas);

        return response()->json(['message' => 'RPS berhasil dihapus.']);
    }

    // ── Helpers ─────────────────────────────────────────────────

    public function materiFrontend(Materi $materi): array
    {
        return [
            'id'              => $materi->id,
            'pokok_bahasan_id'=> $materi->pokok_bahasan_id,
            'mata_kuliah_id'  => $materi->mata_kuliah_id,
            'judul'           => $materi->judul,
            'deskripsi'       => $materi->deskripsi,
            'tipe'            => $materi->tipe,
            'tipe_label'      => $materi->tipeLabel(),
            'tipe_icon'       => $materi->tipeIcon(),
            'tipe_color'      => $materi->tipeColor(),
            'url'             => $materi->url,
            'file_url'        => $materi->fileUrl(),
            'nama_file'       => $materi->nama_file,
            'ukuran'          => $materi->ukuranHuman(),
            'konten'          => $materi->konten,
            'urutan'          => $materi->urutan,
            'status'          => $materi->status,
            'allow_download'  => (bool) $materi->allow_download,
            'created_at'      => $materi->created_at->diffForHumans(),
        ];
    }
}
