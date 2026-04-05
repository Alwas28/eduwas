<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Enrollment;
use App\Models\Kelas;
use App\Models\Mahasiswa;
use App\Models\PeriodeAkademik;
use App\Notifications\EnrolledToKelas;
use App\Support\ActivityLogger;
use Illuminate\Http\Request;

class EnrollmentController extends Controller
{
    public function index(Request $request)
    {
        $query = Enrollment::with([
            'kelas.mataKuliah.jurusan',
            'kelas.periodeAkademik',
            'mahasiswa.jurusan',
        ]);

        if ($request->filled('periode_id')) {
            $query->whereHas('kelas', fn ($q) => $q->where('periode_akademik_id', $request->periode_id));
        }
        if ($request->filled('kelas_id')) {
            $query->where('kelas_id', $request->kelas_id);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $enrollments = $query->latest()->get();
        $allKelas    = Kelas::with(['mataKuliah', 'periodeAkademik'])->latest()->get();
        $mahasiswa   = Mahasiswa::where('status', 'Aktif')->orderBy('nama')->get();
        $periodes    = PeriodeAkademik::orderByDesc('created_at')->get();

        // Map: kelas_id => [mahasiswa_id, ...] — for hiding already-enrolled students
        $enrolledMap = Enrollment::select('kelas_id', 'mahasiswa_id')
            ->get()
            ->groupBy('kelas_id')
            ->map(fn ($g) => $g->pluck('mahasiswa_id')->values()->all());

        // Total stats (unfiltered)
        $totalQuery = Enrollment::selectRaw('status, COUNT(*) as total')->groupBy('status')->pluck('total', 'status');
        $stats = [
            'total'   => Enrollment::count(),
            'aktif'   => $totalQuery->get('Aktif', 0),
            'dropout' => $totalQuery->get('Dropout', 0),
            'lulus'   => $totalQuery->get('Lulus', 0),
        ];

        return view('admin.enrollment.index', compact(
            'enrollments', 'allKelas', 'mahasiswa', 'periodes', 'stats', 'enrolledMap',
        ));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'kelas_id'    => 'required|exists:kelas,id',
            'mahasiswa_id'=> 'required|exists:mahasiswa,id',
            'status'      => 'required|in:Aktif,Dropout,Lulus',
            'enrolled_at' => 'nullable|date',
        ]);

        if (Enrollment::where('kelas_id', $data['kelas_id'])
                      ->where('mahasiswa_id', $data['mahasiswa_id'])
                      ->exists()) {
            return response()->json(['message' => 'Mahasiswa sudah terdaftar di kelas ini.'], 422);
        }

        if (empty($data['enrolled_at'])) {
            $data['enrolled_at'] = now()->toDateString();
        }

        $enrollment = Enrollment::create($data);
        $enrollment->load('kelas.mataKuliah', 'mahasiswa');

        $mk  = $enrollment->kelas->mataKuliah?->nama ?? '?';
        $mhs = $enrollment->mahasiswa->nama;

        ActivityLogger::log('created', 'enrollment', "{$mhs} didaftarkan ke kelas {$mk}", $enrollment);

        // Kirim notifikasi ke mahasiswa jika punya akun
        if ($enrollment->mahasiswa->user_id) {
            $enrollment->mahasiswa->user->notify(new EnrolledToKelas($enrollment));
        }

        return response()->json(['message' => "{$mhs} berhasil didaftarkan ke kelas {$mk}."]);
    }

    public function update(Request $request, Enrollment $enrollment)
    {
        $data = $request->validate([
            'status'      => 'required|in:Aktif,Dropout,Lulus',
            'nilai_akhir' => 'nullable|numeric|min:0|max:100',
            'catatan'     => 'nullable|string|max:500',
        ]);

        $old = $enrollment->only('status', 'nilai_akhir');
        $enrollment->update($data);
        $enrollment->load('kelas.mataKuliah', 'mahasiswa');

        $mk  = $enrollment->kelas->mataKuliah?->nama ?? '?';
        $mhs = $enrollment->mahasiswa->nama;

        ActivityLogger::log('updated', 'enrollment', "Enrollment {$mhs} di {$mk} diperbarui", $enrollment, [
            'old' => $old,
            'new' => $enrollment->only('status', 'nilai_akhir'),
        ]);

        return response()->json(['message' => "Data enrollment {$mhs} berhasil diperbarui."]);
    }

    public function destroy(Enrollment $enrollment)
    {
        $enrollment->load('kelas.mataKuliah', 'mahasiswa');
        $mk  = $enrollment->kelas->mataKuliah?->nama ?? '?';
        $mhs = $enrollment->mahasiswa->nama;

        ActivityLogger::log('deleted', 'enrollment', "{$mhs} dikeluarkan dari kelas {$mk}", $enrollment);
        $enrollment->delete();

        return response()->json(['message' => "{$mhs} berhasil dikeluarkan dari kelas {$mk}."]);
    }
}
