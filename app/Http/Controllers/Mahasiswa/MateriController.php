<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Http\Controllers\Controller;
use App\Models\Enrollment;
use App\Models\Kelas;
use App\Models\Mahasiswa;
use App\Models\Materi;
use App\Models\MateriAkses;
use App\Models\PbRangkuman;
use App\Models\PokokBahasan;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Collection;

class MateriController extends Controller
{
    /**
     * Halaman daftar semua materi dari semua kelas yang diikuti mahasiswa.
     */
    public function index()
    {
        $mahasiswa = $this->getMahasiswa();

        $enrollments = Enrollment::where('mahasiswa_id', $mahasiswa->id)
            ->with(['kelas.mataKuliah', 'kelas.periodeAkademik', 'kelas.instruktur'])
            ->get();

        // Statistik per kelas: jumlah materi aktif & progress dari MateriAkses
        $kelasIds    = $enrollments->pluck('kelas_id')->filter()->unique()->values();
        $allPb       = PokokBahasan::whereIn('mata_kuliah_id',
                            $enrollments->pluck('kelas.mata_kuliah_id')->filter()->unique()->values()
                        )
                        ->with(['materi' => fn($q) => $q->where('status', 'Aktif')->select('id', 'pokok_bahasan_id')])
                        ->get()
                        ->groupBy('mata_kuliah_id');

        $allMateriIds = $allPb->flatten()->flatMap(fn($pb) => $pb->materi->pluck('id'));

        $aksesMap = MateriAkses::where('user_id', Auth::id())
            ->whereIn('materi_id', $allMateriIds)
            ->get()
            ->keyBy('materi_id');

        // Hitung stats per kelas (indexed by kelas_id)
        $kelasStats = [];
        foreach ($enrollments as $enrollment) {
            $kelas   = $enrollment->kelas;
            $pbList  = $allPb[$kelas->mata_kuliah_id] ?? collect();
            $mIds    = $pbList->flatMap(fn($pb) => $pb->materi->pluck('id'));
            $total   = $mIds->count();
            $selesai = $mIds->filter(fn($id) => ($aksesMap[$id]->progress ?? 0) >= 100)->count();
            $avg     = $total > 0
                ? round($mIds->map(fn($id) => $aksesMap[$id]->progress ?? 0)->avg())
                : 0;
            $kelasStats[$kelas->id] = compact('total', 'selesai', 'avg');
        }

        // Global stats
        $totalMateri = $allMateriIds->count();
        $totalSelesai = $aksesMap->where('progress', 100)->count();
        $totalDurasi  = $aksesMap->sum('durasi_detik');
        $avgProgress  = $totalMateri > 0
            ? round($allMateriIds->map(fn($id) => $aksesMap[$id]->progress ?? 0)->avg())
            : 0;

        return view('mahasiswa.materi.index', compact(
            'enrollments', 'kelasStats',
            'totalMateri', 'totalSelesai', 'totalDurasi', 'avgProgress'
        ));
    }

    private function getMahasiswa(): Mahasiswa
    {
        return Mahasiswa::where('user_id', Auth::id())->firstOrFail();
    }

    /**
     * Halaman baca materi untuk mahasiswa — satu pokok bahasan dalam konteks kelas.
     */
    public function showPB(Kelas $kelas, PokokBahasan $pokokBahasan)
    {
        $mahasiswa = $this->getMahasiswa();

        // Pastikan mahasiswa terdaftar di kelas ini
        $enrollment = Enrollment::where('mahasiswa_id', $mahasiswa->id)
            ->where('kelas_id', $kelas->id)
            ->firstOrFail();

        // Pastikan PB ini memang untuk mata kuliah kelas ini
        abort_if($pokokBahasan->mata_kuliah_id !== $kelas->mata_kuliah_id, 404);

        $kelas->load(['mataKuliah', 'periodeAkademik', 'instruktur']);

        // Hanya materi yang sudah Aktif
        $materi = $pokokBahasan->materi()
            ->where('status', 'Aktif')
            ->orderBy('urutan')
            ->orderBy('created_at')
            ->get();

        // Progress yang sudah tersimpan untuk mahasiswa ini
        $aksesMap = MateriAkses::where('user_id', Auth::id())
            ->where('kelas_id', $kelas->id)
            ->whereIn('materi_id', $materi->pluck('id'))
            ->get()
            ->keyBy('materi_id');

        // Rangkuman PB yang sudah ditulis mahasiswa ini
        $pbRangkuman = PbRangkuman::where('user_id', Auth::id())
            ->where('kelas_id', $kelas->id)
            ->where('pokok_bahasan_id', $pokokBahasan->id)
            ->first();

        // Catat 1 kunjungan per page load untuk setiap materi aktif
        $now = now();
        foreach ($materi as $m) {
            $akses = $aksesMap->get($m->id);
            if ($akses) {
                $akses->increment('jumlah_akses');
                $akses->update(['terakhir_diakses_at' => $now]);
            } else {
                $akses = MateriAkses::create([
                    'materi_id'           => $m->id,
                    'user_id'             => Auth::id(),
                    'kelas_id'            => $kelas->id,
                    'jumlah_akses'        => 1,
                    'progress'            => 0,
                    'durasi_detik'        => 0,
                    'pertama_diakses_at'  => $now,
                    'terakhir_diakses_at' => $now,
                ]);
                $aksesMap->put($m->id, $akses);
            }
        }

        $aiAssistantName = \App\Models\Setting::get('ai_assistant_name', 'Tanya Asdos');

        return view('mahasiswa.materi.show', compact(
            'kelas', 'pokokBahasan', 'materi', 'aksesMap', 'pbRangkuman', 'mahasiswa', 'enrollment', 'aiAssistantName'
        ));
    }

    /**
     * API endpoint: update progress membaca materi.
     * Hanya update jika progress baru > yang tersimpan.
     */
    public function updateProgress(Request $request, Materi $materi)
    {
        $request->validate([
            'progress' => 'required|integer|min:0|max:100',
            'kelas_id' => 'required|exists:kelas,id',
            'durasi'   => 'sometimes|integer|min:0|max:7200', // max 2 jam per request
        ]);

        $userId = Auth::id();
        $now    = now();
        $durasi = $request->integer('durasi', 0);

        $akses = MateriAkses::where('materi_id', $materi->id)
            ->where('user_id', $userId)
            ->where('kelas_id', $request->kelas_id)
            ->first();

        if ($akses) {
            $data = ['terakhir_diakses_at' => $now, 'durasi_detik' => $akses->durasi_detik + $durasi];
            if ($request->progress > $akses->progress) {
                $data['progress'] = $request->progress;
            }
            $akses->update($data);
        } else {
            $akses = MateriAkses::create([
                'materi_id'           => $materi->id,
                'user_id'             => $userId,
                'kelas_id'            => $request->kelas_id,
                'jumlah_akses'        => 0,
                'progress'            => $request->progress,
                'durasi_detik'        => $durasi,
                'pertama_diakses_at'  => $now,
                'terakhir_diakses_at' => $now,
            ]);
        }

        $fresh = $akses->fresh();
        return response()->json([
            'ok'           => true,
            'progress'     => $fresh->progress,
            'durasi_detik' => $fresh->durasi_detik,
        ]);
    }

    /**
     * Simpan atau perbarui rangkuman mahasiswa untuk satu pokok bahasan.
     */
    public function storeRangkuman(Request $request, PokokBahasan $pokokBahasan)
    {
        $request->validate([
            'kelas_id' => 'required|exists:kelas,id',
            'isi'      => 'required|string|min:10|max:5000',
        ]);

        abort_if(! $pokokBahasan->rangkuman_aktif, 403, 'Rangkuman tidak diaktifkan untuk pertemuan ini.');

        $mahasiswa = $this->getMahasiswa();
        Enrollment::where('mahasiswa_id', $mahasiswa->id)
            ->where('kelas_id', $request->kelas_id)
            ->firstOrFail();

        $rangkuman = PbRangkuman::updateOrCreate(
            [
                'pokok_bahasan_id' => $pokokBahasan->id,
                'user_id'          => Auth::id(),
                'kelas_id'         => $request->kelas_id,
            ],
            ['isi' => $request->isi]
        );

        return response()->json([
            'ok'         => true,
            'isi'        => $rangkuman->isi,
            'updated_at' => $rangkuman->updated_at->diffForHumans(),
        ]);
    }

    /**
     * Active readers for all materi in this PB × kelas.
     * Returns users active in the last 3 minutes.
     */
    public function activeReaders(Kelas $kelas, PokokBahasan $pokokBahasan)
    {
        $mahasiswa = $this->getMahasiswa();

        Enrollment::where('mahasiswa_id', $mahasiswa->id)
            ->where('kelas_id', $kelas->id)
            ->firstOrFail();

        abort_if($pokokBahasan->mata_kuliah_id !== $kelas->mata_kuliah_id, 404);

        $cutoff = Carbon::now()->subMinutes(3);

        $rows = MateriAkses::with('user:id,name')
            ->where('kelas_id', $kelas->id)
            ->whereHas('materi', fn($q) => $q->where('pokok_bahasan_id', $pokokBahasan->id))
            ->where('terakhir_diakses_at', '>=', $cutoff)
            ->get();

        $colors = ['#6366f1','#8b5cf6','#ec4899','#f59e0b','#10b981','#3b82f6','#ef4444','#14b8a6'];

        $result = [];
        foreach ($rows as $row) {
            $name     = $row->user?->name ?? '?';
            $parts    = preg_split('/\s+/', trim($name));
            $initials = count($parts) >= 2
                ? strtoupper(mb_substr($parts[0], 0, 1) . mb_substr($parts[1], 0, 1))
                : strtoupper(mb_substr($name, 0, 2));

            $result[$row->materi_id][] = [
                'user_id'  => $row->user_id,
                'name'     => $name,
                'initials' => $initials,
                'color'    => $colors[$row->user_id % count($colors)],
                'is_self'  => $row->user_id === Auth::id(),
            ];
        }

        return response()->json($result);
    }
}
