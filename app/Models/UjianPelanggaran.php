<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UjianPelanggaran extends Model
{
    protected $table = 'ujian_pelanggaran';

    protected $fillable = ['sesi_id', 'tipe', 'catatan', 'terjadi_at'];

    protected $casts = ['terjadi_at' => 'datetime'];

    public function sesi()
    {
        return $this->belongsTo(UjianSesi::class, 'sesi_id');
    }
}
