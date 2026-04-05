<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PeriodeAkademik extends Model
{
    protected $table = 'periode_akademik';

    protected $fillable = [
        'kode', 'nama', 'tahun_ajaran', 'semester',
        'tanggal_mulai', 'tanggal_selesai', 'status', 'deskripsi',
    ];

    protected $casts = [
        'tanggal_mulai'   => 'date',
        'tanggal_selesai' => 'date',
    ];

    public function kelas()
    {
        return $this->hasMany(Kelas::class);
    }

    public function isAktif(): bool
    {
        return $this->status === 'Aktif';
    }
}
