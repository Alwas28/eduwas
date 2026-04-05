<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Http\Controllers\Controller;
use App\Models\Mahasiswa;
use App\Models\Tugas;
use App\Models\TugasIndividuSubmission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class TugasIndividuController extends Controller
{
    private function getMahasiswa(): Mahasiswa
    {
        return Mahasiswa::where('user_id', Auth::id())->firstOrFail();
    }

    /**
     * Halaman pengerjaan tugas individu.
     */
    public function show(Tugas $tugas)
    {
        $mahasiswa = $this->getMahasiswa();

        abort_if($tugas->tipe !== 'individu', 404);

        // Pastikan mahasiswa terdaftar di kelas
        $enrolled = \App\Models\Enrollment::where('kelas_id', $tugas->kelas_id)
            ->where('mahasiswa_id', $mahasiswa->id)
            ->where('status', 'Aktif')
            ->exists();
        abort_if(!$enrolled, 403);

        $tugas->load('kelas.mataKuliah', 'kelas.periodeAkademik', 'instruktur');

        $submission = TugasIndividuSubmission::where('tugas_id', $tugas->id)
            ->where('mahasiswa_id', $mahasiswa->id)
            ->first();

        return view('mahasiswa.tugas.individu', compact('tugas', 'mahasiswa', 'submission'));
    }

    /**
     * Upload PDF dan submit tugas individu.
     */
    public function submit(Request $request, Tugas $tugas)
    {
        $mahasiswa = $this->getMahasiswa();

        abort_if($tugas->tipe !== 'individu', 404);
        abort_if($tugas->status === 'selesai', 422);

        // Cek deadline
        if ($tugas->deadline && $tugas->deadline->isPast()) {
            return response()->json(['message' => 'Deadline telah lewat, pengumpulan tidak bisa dilakukan.'], 422);
        }

        $existing = TugasIndividuSubmission::where('tugas_id', $tugas->id)
            ->where('mahasiswa_id', $mahasiswa->id)
            ->first();

        abort_if($existing && $existing->status_submit === 'submitted', 422);

        $request->validate([
            'pdf' => 'required|file|mimes:pdf|max:20480',
        ]);

        // Hapus PDF lama jika ada
        if ($existing?->pdf_path) {
            Storage::disk('public')->delete($existing->pdf_path);
        }

        $pdfPath = $request->file('pdf')->store('tugas-individu-pdf', 'public');

        TugasIndividuSubmission::updateOrCreate(
            ['tugas_id' => $tugas->id, 'mahasiswa_id' => $mahasiswa->id],
            [
                'pdf_path'      => $pdfPath,
                'status_submit' => 'submitted',
                'submitted_at'  => now(),
            ]
        );

        return response()->json([
            'message' => 'Tugas berhasil dikumpulkan.',
            'pdf_url' => Storage::url($pdfPath),
        ]);
    }

    /**
     * Tarik kembali submission tugas individu.
     */
    public function unsubmit(Tugas $tugas)
    {
        $mahasiswa = $this->getMahasiswa();

        abort_if($tugas->tipe !== 'individu', 404);
        abort_if($tugas->status === 'selesai', 422);

        $submission = TugasIndividuSubmission::where('tugas_id', $tugas->id)
            ->where('mahasiswa_id', $mahasiswa->id)
            ->where('status_submit', 'submitted')
            ->firstOrFail();

        // Tidak bisa ditarik kembali jika sudah dinilai
        if ($submission->nilai !== null) {
            return response()->json(['message' => 'Tugas sudah dinilai, tidak bisa ditarik kembali.'], 422);
        }

        if ($submission->pdf_path) {
            Storage::disk('public')->delete($submission->pdf_path);
        }

        $submission->update([
            'status_submit' => 'belum',
            'submitted_at'  => null,
            'pdf_path'      => null,
        ]);

        return response()->json(['message' => 'Pengumpulan dibatalkan.']);
    }
}
