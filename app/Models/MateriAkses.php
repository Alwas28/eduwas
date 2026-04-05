<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MateriAkses extends Model
{
    protected $table = 'materi_akses';

    protected $fillable = [
        'materi_id', 'user_id', 'kelas_id',
        'jumlah_akses', 'progress', 'durasi_detik',
        'pertama_diakses_at', 'terakhir_diakses_at',
    ];

    protected $casts = [
        'progress'            => 'integer',
        'durasi_detik'        => 'integer',
        'pertama_diakses_at'  => 'datetime',
        'terakhir_diakses_at' => 'datetime',
    ];

    /** Format durasi: "5 mnt 30 dtk" */
    public function durasiHuman(): string
    {
        $d = (int) $this->durasi_detik;
        if ($d <= 0)  return '—';
        if ($d < 60)  return $d . ' dtk';
        $mnt = intdiv($d, 60);
        $dtk = $d % 60;
        if ($mnt < 60) return $mnt . ' mnt' . ($dtk > 0 ? ' ' . $dtk . ' dtk' : '');
        $jam = intdiv($mnt, 60);
        $mnt = $mnt % 60;
        return $jam . 'j ' . ($mnt > 0 ? $mnt . ' mnt' : '');
    }

    public function materi()
    {
        return $this->belongsTo(Materi::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function kelas()
    {
        return $this->belongsTo(Kelas::class);
    }
}
