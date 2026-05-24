<?php

namespace App\Models;

use Filament\Models\Contracts\FilamentUser;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements FilamentUser
{
    // [FIX] Tambah HasUlids — WAJIB agar ULID di-generate otomatis saat user dibuat
    use HasFactory, HasUlids, Notifiable, HasRoles, SoftDeletes, LogsActivity;

    protected $fillable = [
        'name',
        'pen_name',
        'email',
        'phone_number',
        'password',
        'google2fa_secret',
        // [NOTE] Kolom moderasi tidak di fillable — diubah eksplisit oleh ModerationService
        // 'muted_until', 'suspended_until', 'banned_at' diset via method di bawah
        // [NOTE] Kolom verifikasi role diset via method, bukan mass assignment
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'google2fa_secret',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at'  => 'datetime',
            'password'           => 'hashed',
            'muted_until'        => 'datetime',
            'suspended_until'    => 'datetime',
            'banned_at'          => 'datetime',
            'author_verified_at' => 'datetime',
            'editor_verified_at' => 'datetime',
        ];
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['name', 'pen_name', 'email', 'muted_until', 'suspended_until', 'banned_at'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    // =========================================================
    // FILAMENT ACCESS
    // =========================================================

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

    // =========================================================
    // IMPERSONATION
    // =========================================================

    public function canImpersonate(): bool
    {
        return $this->hasRole('super_admin');
    }

    public function canBeImpersonated(): bool
    {
        return ! $this->hasRole('super_admin');
    }

    // =========================================================
    // STATUS HELPERS
    // =========================================================

    public function isMuted(): bool
    {
        return $this->muted_until !== null && $this->muted_until->isFuture();
    }

    public function isSuspended(): bool
    {
        return $this->suspended_until !== null && $this->suspended_until->isFuture();
    }

    public function isBanned(): bool
    {
        return $this->banned_at !== null;
    }

    public function isActiveAuthor(): bool
    {
        return $this->author_verified_at !== null
            && ! $this->isBanned()
            && ! $this->isSuspended();
    }

    // [FIX] Tambah isActiveEditor helper yang hilang
    public function isActiveEditor(): bool
    {
        return $this->editor_verified_at !== null
            && ! $this->isBanned()
            && ! $this->isSuspended();
    }

    // =========================================================
    // MODERASI — dipanggil eksplisit oleh ModerationService
    // =========================================================

    public function mute(\Carbon\Carbon $until): void
    {
        $this->muted_until = $until;
        $this->save();
    }

    public function unmute(): void
    {
        $this->muted_until = null;
        $this->save();
    }

    public function suspend(\Carbon\Carbon $until): void
    {
        $this->suspended_until = $until;
        $this->save();
    }

    public function ban(): void
    {
        $this->banned_at = now();
        $this->save();
    }

    public function unban(): void
    {
        $this->banned_at = null;
        $this->save();
    }

    // =========================================================
    // RELASI — SEBAGAI PENULIS
    // =========================================================

    public function novels(): HasMany
    {
        return $this->hasMany(Novel::class, 'author_id');
    }

    public function earningsFromPurchases(): HasMany
    {
        return $this->hasMany(ChapterPurchase::class, 'author_id');
    }

    public function contracts(): HasMany
    {
        return $this->hasMany(Contract::class, 'author_id');
    }

    public function payoutStatements(): HasMany
    {
        return $this->hasMany(PayoutStatement::class, 'author_id');
    }

    // =========================================================
    // RELASI — SEBAGAI EDITOR
    // =========================================================

    public function supervisedNovels(): HasMany
    {
        return $this->hasMany(Novel::class, 'editor_id');
    }

    public function handledContracts(): HasMany
    {
        return $this->hasMany(Contract::class, 'editor_id');
    }

    public function editorChoices(): HasMany
    {
        return $this->hasMany(EditorChoice::class, 'editor_id');
    }

    // =========================================================
    // RELASI — SEBAGAI PEMBACA
    // =========================================================

    public function chapterPurchases(): HasMany
    {
        return $this->hasMany(ChapterPurchase::class, 'reader_id');
    }

    public function favorites(): HasMany
    {
        return $this->hasMany(Favorite::class);
    }

    public function novelViews(): HasMany
    {
        return $this->hasMany(NovelView::class);
    }

    // [FIX] Tambah relasi reviews yang hilang
    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    // =========================================================
    // RELASI — WALLET & KEUANGAN
    // =========================================================

    public function wallet(): HasOne
    {
        return $this->hasOne(Wallet::class);
    }

    // [FIX] Tambah relasi transactions yang hilang
    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    public function topUps(): HasMany
    {
        return $this->hasMany(TopUp::class);
    }

    public function withdrawals(): HasMany
    {
        return $this->hasMany(Withdrawal::class);
    }
}
