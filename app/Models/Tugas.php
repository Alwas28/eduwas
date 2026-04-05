<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tugas extends Model
{
    protected $table = 'tugas';

    protected $fillable = [
        'kelas_id',
        'instruktur_id',
        'judul',
        'deskripsi',
        'soal',
        'tipe',
        'deadline',
        'status',
    ];

    protected $casts = [
        'deadline' => 'datetime',
        'status'   => 'string',
    ];

    public function kelas()
    {
        return $this->belongsTo(Kelas::class);
    }

    public function instruktur()
    {
        return $this->belongsTo(Instruktur::class);
    }

    public function kelompok()
    {
        return $this->hasMany(TugasKelompok::class);
    }

    public function individuSubmissions()
    {
        return $this->hasMany(TugasIndividuSubmission::class);
    }
}
