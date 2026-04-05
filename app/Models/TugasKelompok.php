<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TugasKelompok extends Model
{
    protected $table = 'tugas_kelompok';

    protected $fillable = [
        'tugas_id',
        'nama_kelompok',
        'ketua_mahasiswa_id',
        'konten_final',
        'status_submit',
        'submitted_at',
        'pdf_path',
        'nilai_kelompok',
        'catatan_kelompok',
    ];

    protected $casts = [
        'submitted_at' => 'datetime',
        'nilai_kelompok' => 'integer',
    ];

    public function tugas()
    {
        return $this->belongsTo(Tugas::class);
    }

    public function ketua()
    {
        return $this->belongsTo(Mahasiswa::class, 'ketua_mahasiswa_id');
    }

    public function anggota()
    {
        return $this->hasMany(TugasKelompokAnggota::class, 'kelompok_id');
    }
}
