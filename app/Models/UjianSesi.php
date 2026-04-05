<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UjianSesi extends Model
{
    protected $table = 'ujian_sesi';

    protected $fillable = [
        'ujian_id', 'mahasiswa_id', 'soal_ids',
        'mulai_at', 'selesai_at', 'submitted_at', 'nilai',
        'nilai_status', 'pelanggaran', 'last_ping_at',
    ];

    protected $casts = [
        'soal_ids'     => 'array',
        'mulai_at'     => 'datetime',
        'selesai_at'   => 'datetime',
        'submitted_at' => 'datetime',
        'last_ping_at' => 'datetime',
    ];

    public function ujian()
    {
        return $this->belongsTo(Ujian::class);
    }

    public function jawaban()
    {
        return $this->hasMany(UjianJawaban::class, 'sesi_id');
    }

    public function riwayatPelanggaran()
    {
        return $this->hasMany(UjianPelanggaran::class, 'sesi_id');
    }

    public function mahasiswa()
    {
        return $this->belongsTo(\App\Models\Mahasiswa::class);
    }

    /** Sisa detik ujian berdasarkan mulai_at + durasi */
    public function sisaDetik(): int
    {
        if (! $this->mulai_at || ! $this->ujian) return 0;
        $selesai = $this->mulai_at->addMinutes($this->ujian->durasi);
        // Also cap by ujian's waktu_selesai
        if ($this->ujian->waktu_selesai && $this->ujian->waktu_selesai->lt($selesai)) {
            $selesai = $this->ujian->waktu_selesai;
        }
        return max(0, (int) now()->diffInSeconds($selesai, false));
    }
}
