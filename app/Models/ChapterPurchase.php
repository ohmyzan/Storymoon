<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class ChapterPurchase extends Model
{
    // [FIX] LogsActivity — L kapital (typo diperbaiki)
    use HasFactory, HasUlids, LogsActivity;

    protected $fillable = [
        'reader_id',
        'chapter_id',
        // [FIX] Tambah novel_id yang ada di migrasi tapi hilang dari fillable
        'novel_id',
        'author_id',
        // [FIX] Rename price_coined → coin_price sesuai migrasi yang sudah diperbaiki
        'coin_price',
        'author_earning',
        'platform_earning',
        'contract_type_snapshot',
        'revenue_share_snapshot',
        // [NOTE] Tidak ada 'status' — pembelian adalah ledger immutable
    ];

    // [FIX] Tambah casts lengkap
    protected $casts = [
        'coin_price'             => 'integer',
        'author_earning'         => 'integer',
        'platform_earning'       => 'integer',
        'revenue_share_snapshot' => 'integer',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logFillable()
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    // =========================================================
    // RELASI
    // =========================================================

    public function reader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reader_id');
    }

    public function chapter(): BelongsTo
    {
        return $this->belongsTo(Chapter::class);
    }

    // [FIX] Tambah relasi novel langsung (lebih efisien dari hasManyThrough)
    public function novel(): BelongsTo
    {
        return $this->belongsTo(Novel::class);
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id');
    }
}
