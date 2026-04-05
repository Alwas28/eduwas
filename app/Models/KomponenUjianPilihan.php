<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KomponenUjianPilihan extends Model
{
    protected $table = 'komponen_ujian_pilihan';

    protected $fillable = [
        'komponen_id', 'mahasiswa_id', 'ujian_sesi_id',
    ];

    public function komponen()
    {
        return $this->belongsTo(KelasKomponenNilai::class, 'komponen_id');
    }

    public function mahasiswa()
    {
        return $this->belongsTo(Mahasiswa::class);
    }

    public function sesi()
    {
        return $this->belongsTo(UjianSesi::class, 'ujian_sesi_id');
    }
}
