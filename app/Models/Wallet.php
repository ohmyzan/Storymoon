<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Wallet extends Model
{
    // [FIX] Tambah HasUlids — wallet.id sekarang ULID sesuai migrasi
    // [FIX] Tambah SoftDeletes — data finansial tidak boleh hard-delete
    use HasFactory, HasUlids, SoftDeletes;

    protected $fillable = [
        'user_id',
        // [FIX] Hapus: 'coin_balance', 'revenue_balance'
        // Saldo tidak boleh bisa di-set bebas lewat mass assignment
        // Hanya boleh diubah via creditCoins() / debitCoins() dengan DB transaction
    ];

    protected $casts = [
        'coin_balance'    => 'integer',
        'revenue_balance' => 'decimal:2',
    ];

    // =========================================================
    // RELASI
    // =========================================================

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    // =========================================================
    // HELPERS — Manipulasi saldo
    // Semua operasi saldo harus lewat method ini,
    // bukan lewat $wallet->coin_balance = X; $wallet->save();
    // =========================================================

    /**
     * Tambah koin ke wallet (top-up, pendapatan chapter)
     * Gunakan di dalam DB::transaction() dari service layer
     */
    public function creditCoins(int $amount): void
    {
        if ($amount <= 0) {
            throw new \InvalidArgumentException('Credit amount harus lebih dari 0.');
        }

        $this->increment('coin_balance', $amount);
    }

    /**
     * Kurangi koin dari wallet (beli chapter, withdraw)
     * Gunakan di dalam DB::transaction() dari service layer
     */
    public function debitCoins(int $amount): void
    {
        if ($amount <= 0) {
            throw new \InvalidArgumentException('Debit amount harus lebih dari 0.');
        }

        // Cegah saldo negatif — cek dengan lockForUpdate() di service layer
        if ($this->coin_balance < $amount) {
            throw new \DomainException('Saldo koin tidak mencukupi.');
        }

        $this->decrement('coin_balance', $amount);
    }

    /**
     * Tambah saldo rupiah (pendapatan author yang sudah dikonversi)
     */
    public function creditRevenue(float $amount): void
    {
        if ($amount <= 0) {
            throw new \InvalidArgumentException('Credit revenue harus lebih dari 0.');
        }

        $this->increment('revenue_balance', $amount);
    }

    /**
     * Cek apakah saldo koin cukup
     */
    public function hasSufficientCoins(int $amount): bool
    {
        return $this->coin_balance >= $amount;
    }
}
