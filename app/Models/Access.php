<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Access extends Model
{
    protected $fillable = ['name', 'display_name', 'group', 'description'];

    public function roles()
    {
        return $this->belongsToMany(Role::class, 'role_accesses')->withTimestamps();
    }
}
