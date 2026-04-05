<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BankSoalPilihan extends Model
{
    protected $table = 'bank_soal_pilihan';

    protected $fillable = ['bank_soal_id', 'huruf', 'teks', 'is_benar'];

    protected $casts = ['is_benar' => 'boolean'];

    public function soal()
    {
        return $this->belongsTo(BankSoal::class, 'bank_soal_id');
    }
}
