<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Http\Controllers\Controller;
use App\Models\Diskusi;
use App\Models\Enrollment;
use App\Models\Instruktur;
use App\Models\Mahasiswa;
use App\Models\PokokBahasan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DiskusiController extends Controller
{
    /**
     * Verifikasi akses ke diskusi pokok bahasan.
     * - Mahasiswa : harus terdaftar di kelas yang diberikan
     * - Instruktur: harus menjadi instruktur pokok bahasan ini
     */
    private function verifyAccess(PokokBahasan $pb, int $kelasId): void
    {
        $user  = Auth::user();
        $roles = $user->roles->pluck('name');

        if ($roles->contains('instruktur')) {
            $instruktur = Instruktur::where('user_id', $user->id)->firstOrFail();
            abort_if($pb->instruktur_id !== $instruktur->id, 403, 'Bukan pokok bahasan Anda.');
            return;
        }

        $mahasiswa = Mahasiswa::where('user_id', $user->id)->firstOrFail();
        Enrollment::where('mahasiswa_id', $mahasiswa->id)
            ->where('kelas_id', $kelasId)
            ->firstOrFail();
    }

    /**
     * Ambil daftar diskusi untuk satu pokok bahasan + kelas.
     */
    public function index(PokokBahasan $pokokBahasan, Request $request)
    {
        $request->validate(['kelas_id' => 'required|exists:kelas,id']);
        $this->verifyAccess($pokokBahasan, $request->kelas_id);

        $query = Diskusi::with('user:id,name')
            ->where('pokok_bahasan_id', $pokokBahasan->id)
            ->where('kelas_id', $request->kelas_id)
            ->orderBy('created_at');

        if ($request->filled('after_id')) {
            $query->where('id', '>', (int) $request->after_id);
        }

        $list = $query->get()->map(fn($d) => $this->formatDiskusi($d, $pokokBahasan));

        $othersCount = Diskusi::where('pokok_bahasan_id', $pokokBahasan->id)
            ->where('kelas_id', $request->kelas_id)
            ->where('user_id', '!=', Auth::id())
            ->count();

        return response()->json([
            'diskusi'      => $list,
            'total'        => $list->count(),
            'others_count' => $othersCount,
        ]);
    }

    /**
     * Kirim pesan diskusi baru.
     */
    public function store(Request $request, PokokBahasan $pokokBahasan)
    {
        $data = $request->validate([
            'kelas_id' => 'required|exists:kelas,id',
            'pesan'    => 'required|string|max:2000',
        ]);

        $this->verifyAccess($pokokBahasan, $data['kelas_id']);

        $diskusi = Diskusi::create([
            'pokok_bahasan_id' => $pokokBahasan->id,
            'kelas_id'         => $data['kelas_id'],
            'user_id'          => Auth::id(),
            'pesan'            => $data['pesan'],
        ]);

        $diskusi->load('user:id,name');

        return response()->json(['diskusi' => $this->formatDiskusi($diskusi, $pokokBahasan)], 201);
    }

    /**
     * Hapus pesan diskusi.
     * - Pengirim dapat menghapus pesannya sendiri
     * - Instruktur dapat menghapus pesan mana pun di pokok bahasan miliknya
     */
    public function destroy(Diskusi $diskusi)
    {
        $user  = Auth::user();
        $roles = $user->roles->pluck('name');

        if ($diskusi->user_id !== $user->id) {
            if ($roles->contains('instruktur')) {
                $instruktur = Instruktur::where('user_id', $user->id)->firstOrFail();
                abort_if($diskusi->pokokBahasan->instruktur_id !== $instruktur->id, 403);
            } else {
                abort(403);
            }
        }

        $diskusi->delete();
        return response()->json(['message' => 'Pesan berhasil dihapus.']);
    }

    /**
     * Cek pesan diskusi baru lintas semua PB yang dapat diakses user.
     * Digunakan oleh global polling navbar (semua halaman).
     */
    public function globalCheck(Request $request)
    {
        $afterId = max(0, (int) $request->get('after_id', 0));
        $user    = Auth::user();
        $roles   = $user->roles->pluck('name');

        $query = Diskusi::with(['user:id,name', 'pokokBahasan:id,judul'])
            ->where('user_id', '!=', $user->id)
            ->where('id', '>', $afterId)
            ->orderBy('id');

        if ($roles->contains('instruktur')) {
            $instruktur = Instruktur::where('user_id', $user->id)->first();
            if (! $instruktur) {
                return response()->json(['items' => [], 'max_id' => $afterId]);
            }
            $query->whereHas('pokokBahasan', fn($q) => $q->where('instruktur_id', $instruktur->id));
        } else {
            $mahasiswa = Mahasiswa::where('user_id', $user->id)->first();
            if (! $mahasiswa) {
                return response()->json(['items' => [], 'max_id' => $afterId]);
            }
            $kelasIds = Enrollment::where('mahasiswa_id', $mahasiswa->id)->pluck('kelas_id');
            $query->whereIn('kelas_id', $kelasIds);
        }

        $messages = $query->limit(50)->get();

        if ($messages->isEmpty()) {
            return response()->json(['items' => [], 'max_id' => $afterId]);
        }

        $isInstruktur = $roles->contains('instruktur');

        $items = $messages
            ->groupBy(fn($m) => $m->pokok_bahasan_id . '_' . $m->kelas_id)
            ->map(function ($msgs) use ($isInstruktur) {
                $last = $msgs->last();
                $pb   = $last->pokokBahasan;

                $url = $isInstruktur
                    ? route('instruktur.pokok-bahasan.materi', $pb->id)
                    : route('mahasiswa.materi.show', [$last->kelas_id, $pb->id]);

                return [
                    'pb_id'     => $pb->id,
                    'pb_judul'  => $pb->judul,
                    'kelas_id'  => $last->kelas_id,
                    'count'     => $msgs->count(),
                    'last_name' => $last->user?->name ?? 'Pengguna',
                    'last_pesan'=> $last->pesan,
                    'url'       => $url,
                    'messages'  => $msgs->map(fn($m) => [
                        'id'     => $m->id,
                        'is_own' => false,
                        'name'   => $m->user?->name ?? 'Pengguna',
                        'pesan'  => $m->pesan,
                    ])->values(),
                ];
            })
            ->values();

        return response()->json([
            'items'  => $items,
            'max_id' => $messages->max('id'),
        ]);
    }

    private function formatDiskusi(Diskusi $d, PokokBahasan $pb): array
    {
        $name = $d->user?->name ?? 'Pengguna';

        $instruktur   = Instruktur::where('user_id', $d->user_id)->first();
        $isInstruktur = $instruktur && $instruktur->id === $pb->instruktur_id;

        return [
            'id'            => $d->id,
            'pesan'         => $d->pesan,
            'user_id'       => $d->user_id,
            'name'          => $name,
            'initials'      => $this->initials($name),
            'color'         => $isInstruktur ? '#f59e0b' : $this->userColor($d->user_id),
            'waktu'         => $d->created_at->diffForHumans(),
            'is_own'        => $d->user_id === Auth::id(),
            'is_instruktur' => $isInstruktur,
        ];
    }

    private function initials(string $name): string
    {
        $parts = preg_split('/\s+/', trim($name));
        if (count($parts) >= 2) {
            return strtoupper(mb_substr($parts[0], 0, 1) . mb_substr($parts[1], 0, 1));
        }
        return strtoupper(mb_substr($name, 0, 2));
    }

    private function userColor(int $id): string
    {
        $colors = ['#6366f1','#8b5cf6','#ec4899','#f59e0b','#10b981','#3b82f6','#ef4444','#14b8a6'];
        return $colors[$id % count($colors)];
    }
}
