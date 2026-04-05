<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Mahasiswa extends Model
{
    protected $table = 'mahasiswa';

    protected $fillable = [
        'user_id', 'jurusan_id', 'nim', 'nama', 'email',
        'jenis_kelamin', 'tempat_lahir', 'tanggal_lahir',
        'no_hp', 'alamat', 'angkatan', 'status',
    ];

    protected $casts = [
        'tanggal_lahir' => 'date',
        'angkatan'      => 'integer',
    ];

    public function user()
    {
        return $this->belongsTo(User::class)->withDefault(['name' => '—']);
    }

    public function jurusan()
    {
        return $this->belongsTo(Jurusan::class)->withDefault(['nama' => '—']);
    }

    public function enrollments()
    {
        return $this->hasMany(Enrollment::class);
    }
}
