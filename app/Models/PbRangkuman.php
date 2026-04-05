<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PbRangkuman extends Model
{
    protected $table = 'pb_rangkuman';

    protected $fillable = ['pokok_bahasan_id', 'user_id', 'kelas_id', 'isi', 'nilai', 'catatan'];

    protected $casts = [
        'nilai' => 'integer',
    ];

    public function pokokBahasan()
    {
        return $this->belongsTo(PokokBahasan::class);
    }

    public function user()
    {
        return $this->belongsTo(\App\Models\User::class);
    }
}
