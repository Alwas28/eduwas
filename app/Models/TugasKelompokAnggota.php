<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TugasKelompokAnggota extends Model
{
    protected $table = 'tugas_kelompok_anggota';

    protected $fillable = [
        'kelompok_id',
        'mahasiswa_id',
        'topik',
        'konten',
        'status_submit',
        'catatan_instruktur',
        'nilai',
        'submitted_at',
    ];

    protected $casts = [
        'submitted_at' => 'datetime',
        'nilai'        => 'integer',
    ];

    public function kelompok()
    {
        return $this->belongsTo(TugasKelompok::class, 'kelompok_id');
    }

    public function mahasiswa()
    {
        return $this->belongsTo(Mahasiswa::class);
    }
}
