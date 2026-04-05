<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TugasIndividuSubmission extends Model
{
    protected $table = 'tugas_individu_submission';

    protected $fillable = [
        'tugas_id',
        'mahasiswa_id',
        'pdf_path',
        'status_submit',
        'submitted_at',
        'nilai',
        'catatan_instruktur',
        'catatan_ai',
    ];

    protected $casts = [
        'submitted_at' => 'datetime',
        'nilai'        => 'integer',
    ];

    public function tugas()
    {
        return $this->belongsTo(Tugas::class);
    }

    public function mahasiswa()
    {
        return $this->belongsTo(Mahasiswa::class);
    }
}
