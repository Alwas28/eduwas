<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Enrollment extends Model
{
    protected $table = 'enrollments';

    protected $fillable = [
        'kelas_id', 'mahasiswa_id', 'status', 'nilai_akhir', 'catatan', 'enrolled_at',
    ];

    protected $casts = [
        'enrolled_at' => 'date',
        'nilai_akhir' => 'decimal:2',
    ];

    public function kelas()
    {
        return $this->belongsTo(Kelas::class);
    }

    public function mahasiswa()
    {
        return $this->belongsTo(Mahasiswa::class);
    }

    public function getGradeAttribute(): ?string
    {
        if ($this->nilai_akhir === null) return null;
        $n = (float) $this->nilai_akhir;
        if ($n >= 85) return 'A';
        if ($n >= 75) return 'B';
        if ($n >= 65) return 'C';
        if ($n >= 55) return 'D';
        return 'E';
    }
}
