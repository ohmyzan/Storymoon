<?php

namespace App\Models;

use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Authenticatable implements FilamentUser
{
    use HasFactory, Notifiable, HasRoles, SoftDeletes;

    protected $fillable = [
        'name',
        'pen_name',
        'email',
        'phone_number',
        'password',

        'google2fa_secret', // 🌟 Tambahkan ini

        // 🌟 PERBAIKAN: Sesuaikan persis dengan Migration
        'muted_until',
        'suspended_until',
        'banned_at',

        'author_verified_at',
        'editor_verified_at',
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

            // 🌟 PERBAIKAN: Jadikan datetime agar mudah dimanipulasi
            'muted_until' => 'datetime',
            'suspended_until' => 'datetime',
            'banned_at' => 'datetime',
        ];
    }

    public function canAccessPanel(\Filament\Panel $panel): bool
    {
        if ($this->hasRole('super_admin')) {
            return true;
        }

        return match ($panel->getId()) {
            'admin'       => $this->hasRole('Admin'),
            'editor'      => $this->hasRole('Editor') && $this->editor_verified_at !== null,
            'moderator'   => $this->hasRole('Moderator'),
            'finance'     => $this->hasRole('Finance'),
            'super-admin' => $this->hasRole('super_admin'),
            default       => false,
        };
    }

    public function supervisedNovels()
    {
        return $this->hasMany(Novel::class, 'editor_id');
    }

    public function canImpersonate(): bool
    {
        // Hanya yang punya role 'super_admin' yang bisa meminjam identitas
        return $this->hasRole('super_admin');
    }

    public function canBeImpersonated(): bool
    {
        // Cegah Super Admin meminjam identitas Super Admin lainnya
        return ! $this->hasRole('super_admin');
    }

    public function wallet()
    {
        return $this->hasOne(Wallet::class);
    }
}
