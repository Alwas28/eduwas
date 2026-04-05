<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Fakultas extends Model
{
    protected $table = 'fakultas';

    protected $fillable = ['kode', 'nama', 'singkatan', 'deskripsi', 'aktif'];

    protected $casts = ['aktif' => 'boolean'];

    public function jurusan()
    {
        return $this->hasMany(Jurusan::class);
    }
}
