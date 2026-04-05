<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Diskusi extends Model
{
    protected $table = 'diskusi';

    protected $fillable = ['pokok_bahasan_id', 'kelas_id', 'user_id', 'pesan'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function pokokBahasan()
    {
        return $this->belongsTo(PokokBahasan::class);
    }

    public function kelas()
    {
        return $this->belongsTo(Kelas::class);
    }
}
