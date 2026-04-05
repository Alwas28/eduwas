<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Instruktur extends Model
{
    protected $table = 'instruktur';

    protected $fillable = [
        'user_id', 'nidn', 'nip', 'nama', 'email',
        'jenis_kelamin', 'bidang_keahlian', 'pendidikan_terakhir',
        'no_hp', 'status',
    ];

    public function user()
    {
        return $this->belongsTo(User::class)->withDefault(['name' => '—']);
    }

    public function kelas()
    {
        return $this->belongsToMany(Kelas::class, 'kelas_instruktur');
    }

    public function materi()
    {
        return $this->hasMany(Materi::class);
    }
}
