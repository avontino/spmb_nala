<?php

namespace App\Models;

use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Contracts\Auth\CanResetPassword;

class User extends Authenticatable implements FilamentUser, CanResetPassword
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'nik',
        'nokk',
        'nisn',
        'name',
        'email',
        'password',
        'role',
    ];

    

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function canAccessPanel(Panel $panel): bool
    {
        return true;
    }

    public function pendaftaran(): HasOne
    {
        return $this->hasOne(Pendaftaran::class);
    }

    public function isVerifikator(): bool
    {
        return $this->role === 'verifikator';
    }

    public function isPendaftar(): bool
    {
        return $this->role === 'pendaftar';
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    protected static function boot()
    {
        parent::boot();

        static::created(function ($user) {
            if ($user->isPendaftar() && !$user->pendaftaran) {
                Pendaftaran::create([
                    'user_id' => $user->id,
                    'status' => 'draft',
                ]);
            }
        });
    }
}