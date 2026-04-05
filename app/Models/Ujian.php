<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ujian extends Model
{
    protected $table = 'ujian';

    protected $fillable = [
        'instruktur_id', 'kelas_id', 'judul', 'deskripsi',
        'waktu_mulai', 'waktu_selesai', 'durasi', 'status',
        'ada_essay', 'jumlah_soal_essay', 'acak_soal_essay',
        'ada_pg', 'jumlah_soal_pg', 'acak_soal_pg', 'acak_pilihan_pg',
    ];

    protected $casts = [
        'waktu_mulai'    => 'datetime',
        'waktu_selesai'  => 'datetime',
        'ada_essay'      => 'boolean',
        'acak_soal_essay'=> 'boolean',
        'ada_pg'         => 'boolean',
        'acak_soal_pg'   => 'boolean',
        'acak_pilihan_pg'=> 'boolean',
    ];

    public function instruktur()
    {
        return $this->belongsTo(Instruktur::class);
    }

    public function kelas()
    {
        return $this->belongsTo(Kelas::class);
    }

    public function soalPool()
    {
        return $this->belongsToMany(BankSoal::class, 'ujian_soal', 'ujian_id', 'bank_soal_id')
                    ->withTimestamps();
    }

    public function sesi()
    {
        return $this->hasMany(UjianSesi::class);
    }

    /** Total soal essay dalam pool */
    public function totalEssay(): int
    {
        return $this->soalPool()->where('tipe', 'essay')->count();
    }

    /** Total soal PG dalam pool */
    public function totalPg(): int
    {
        return $this->soalPool()->where('tipe', 'pilihan_ganda')->count();
    }

    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            'draft'   => 'Draft',
            'aktif'   => 'Aktif',
            'selesai' => 'Selesai',
            default   => $this->status,
        };
    }
}
