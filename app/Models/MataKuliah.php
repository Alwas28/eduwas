<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MataKuliah extends Model
{
    protected $table = 'mata_kuliah';

    protected $fillable = [
        'jurusan_id', 'kode', 'nama', 'sks', 'semester', 'jenis', 'deskripsi', 'aktif',
    ];

    protected $casts = [
        'aktif'    => 'boolean',
        'sks'      => 'integer',
        'semester' => 'integer',
    ];

    public function jurusan()
    {
        return $this->belongsTo(Jurusan::class);
    }

    public function kelas()
    {
        return $this->hasMany(Kelas::class);
    }

    public function pokokBahasan()
    {
        return $this->hasMany(PokokBahasan::class)->orderBy('urutan')->orderBy('pertemuan');
    }

    public function materi()
    {
        return $this->hasMany(Materi::class);
    }
}
