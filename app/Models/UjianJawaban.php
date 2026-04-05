<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UjianJawaban extends Model
{
    protected $table = 'ujian_jawaban';

    protected $fillable = [
        'sesi_id', 'bank_soal_id',
        'jawaban_essay', 'jawaban_pg',
        'is_benar', 'nilai', 'feedback_ai', 'feedback_instruktur',
    ];

    protected $casts = [
        'is_benar' => 'boolean',
    ];

    public function sesi()
    {
        return $this->belongsTo(UjianSesi::class, 'sesi_id');
    }

    public function soal()
    {
        return $this->belongsTo(BankSoal::class, 'bank_soal_id');
    }
}
