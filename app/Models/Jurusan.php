<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Jurusan extends Model
{
    protected $table = 'jurusan';

    protected $fillable = ['fakultas_id', 'kode', 'nama', 'singkatan', 'deskripsi', 'aktif'];

    protected $casts = ['aktif' => 'boolean'];

    public function fakultas()
    {
        return $this->belongsTo(Fakultas::class);
    }

    public function mataKuliah()
    {
        return $this->hasMany(MataKuliah::class);
    }

    public function mahasiswas()
    {
        return $this->hasMany(Mahasiswa::class);
    }
}
