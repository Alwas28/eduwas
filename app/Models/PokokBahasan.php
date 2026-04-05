<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PokokBahasan extends Model
{
    protected $table = 'pokok_bahasan';

    protected $fillable = [
        'mata_kuliah_id', 'instruktur_id', 'pertemuan', 'judul', 'deskripsi', 'urutan', 'rangkuman_aktif',
    ];

    protected $casts = [
        'pertemuan'       => 'integer',
        'urutan'          => 'integer',
        'rangkuman_aktif' => 'boolean',
    ];

    public function mataKuliah()
    {
        return $this->belongsTo(MataKuliah::class);
    }

    public function instruktur()
    {
        return $this->belongsTo(Instruktur::class);
    }

    public function materi()
    {
        return $this->hasMany(Materi::class)->orderBy('urutan')->orderBy('created_at');
    }

    public function rangkuman()
    {
        return $this->hasMany(PbRangkuman::class);
    }
}
