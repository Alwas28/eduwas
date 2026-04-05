<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class Kelas extends Model
{
    protected $table = 'kelas';

    protected $fillable = [
        'mata_kuliah_id', 'periode_akademik_id', 'kode_seksi', 'kapasitas', 'status', 'enroll_token',
        'rps_path', 'rps_nama_file', 'rps_ukuran',
    ];

    protected static function boot(): void
    {
        parent::boot();
        static::creating(function (self $kelas) {
            $kelas->enroll_token ??= Str::uuid()->toString();
        });
    }

    protected $casts = [
        'kapasitas' => 'integer',
    ];

    public function mataKuliah()
    {
        return $this->belongsTo(MataKuliah::class);
    }

    public function periodeAkademik()
    {
        return $this->belongsTo(PeriodeAkademik::class);
    }

    public function instruktur()
    {
        return $this->belongsToMany(Instruktur::class, 'kelas_instruktur');
    }

    public function enrollments()
    {
        return $this->hasMany(Enrollment::class);
    }

    public function rpsUrl(): ?string
    {
        return $this->rps_path ? Storage::url($this->rps_path) : null;
    }

    public function rpsUkuranHuman(): string
    {
        if (!$this->rps_ukuran) return '—';
        $b = $this->rps_ukuran;
        if ($b >= 1_048_576) return number_format($b / 1_048_576, 1) . ' MB';
        if ($b >= 1_024)    return number_format($b / 1_024, 1) . ' KB';
        return $b . ' B';
    }

    public function getKodeDisplayAttribute(): string
    {
        $kode = $this->mataKuliah?->kode ?? '?';
        return $this->kode_seksi ? "{$kode}-{$this->kode_seksi}" : $kode;
    }
}
