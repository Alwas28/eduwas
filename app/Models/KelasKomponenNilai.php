<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KelasKomponenNilai extends Model
{
    protected $table = 'kelas_komponen_nilai';

    protected $fillable = [
        'kelas_id', 'instruktur_id', 'tipe', 'sumber_id', 'label', 'urutan',
    ];

    public function kelas()
    {
        return $this->belongsTo(Kelas::class);
    }

    public function instruktur()
    {
        return $this->belongsTo(Instruktur::class);
    }

    /** Resolve sumber: Tugas or Ujian model */
    public function sumber()
    {
        if ($this->tipe === 'tugas') {
            return $this->belongsTo(Tugas::class, 'sumber_id');
        }
        return $this->belongsTo(Ujian::class, 'sumber_id');
    }

    public function pilihanUjian()
    {
        return $this->hasMany(KomponenUjianPilihan::class, 'komponen_id');
    }
}
