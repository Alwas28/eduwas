<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BankSoal extends Model
{
    protected $table = 'bank_soal';

    protected $fillable = [
        'instruktur_id', 'mata_kuliah_id', 'pertanyaan',
        'tipe', 'tingkat_kesulitan', 'bobot', 'pembahasan',
    ];

    public function instruktur()
    {
        return $this->belongsTo(Instruktur::class);
    }

    public function mataKuliah()
    {
        return $this->belongsTo(MataKuliah::class);
    }

    public function pilihan()
    {
        return $this->hasMany(BankSoalPilihan::class)->orderBy('huruf');
    }
}
