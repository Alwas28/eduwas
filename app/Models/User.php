<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Models\Role;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;

#[Fillable(['name', 'email', 'password', 'email_verified_at', 'avatar'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * Generate, save, and send a 6-digit PIN instead of a signed URL.
     */
    public function sendEmailVerificationNotification(): void
    {
        $pin = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        $this->forceFill([
            'verification_pin'            => $pin,
            'verification_pin_expires_at' => now()->addMinutes(10),
        ])->save();

        $this->notify(new \App\Notifications\EmailVerificationPin($pin));
    }

    public function avatarUrl(): ?string
    {
        return $this->avatar ? Storage::url($this->avatar) : null;
    }

    public function initials(): string
    {
        $words = explode(' ', trim($this->name));
        if (count($words) >= 2) {
            return strtoupper(substr($words[0], 0, 1) . substr($words[1], 0, 1));
        }
        return strtoupper(substr($this->name, 0, 2));
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at'            => 'datetime',
            'verification_pin_expires_at'  => 'datetime',
            'password'                     => 'hashed',
        ];
    }

    public function roles()
    {
        return $this->belongsToMany(Role::class, 'user_roles')->withTimestamps();
    }

    public function mahasiswa()
    {
        return $this->hasOne(\App\Models\Mahasiswa::class);
    }

    public function instruktur()
    {
        return $this->hasOne(\App\Models\Instruktur::class);
    }

    /**
     * Redirect destination after login, based on role.
     */
    public function homeRoute(): string
    {
        $roles = $this->roles->pluck('name')->map(fn($n) => strtolower($n));

        if ($roles->isEmpty()) {
            return route('access.status', ['reason' => 'no_role']);
        }

        if ($roles->contains('mahasiswa')) {
            return route('mahasiswa.dashboard');
        }

        if ($roles->contains('instruktur') && !$roles->contains('admin')) {
            return route('instruktur.dashboard');
        }

        return route('admin.dashboard');
    }

    /**
     * Cek apakah user memiliki access tertentu (lewat roles).
     */
    public function hasAccess(string $access): bool
    {
        return $this->roles()
            ->whereHas('accesses', fn($q) => $q->where('name', $access))
            ->exists();
    }

    /**
     * Cek semua access yang dimiliki user (cached per request).
     */
    public function allAccesses(): \Illuminate\Support\Collection
    {
        if (!isset($this->_accessCache)) {
            $this->_accessCache = \App\Models\Access::whereHas('roles', function ($q) {
                $q->whereHas('users', fn($u) => $u->where('users.id', $this->id));
            })->pluck('name');
        }
        return $this->_accessCache;
    }
}
