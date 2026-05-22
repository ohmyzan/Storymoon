<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Transaction extends Model
{
    use HasUlids, SoftDeletes, LogsActivity;

    protected $fillable = [
        'user_id',
        'wallet_id',
        'type',
        'coin_amount',   // ✅ TAMBAHKAN INI
        'rupiah_amount', // ✅ TAMBAHKAN INI
        'status',
        'reference_id',
        'meta_data'
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logFillable() // Rekam semua perubahan pada kolom $fillable
            ->logOnlyDirty() // Hanya rekam kolom yang benar-benar berubah
            ->dontSubmitEmptyLogs(); // Jangan rekam jika tidak ada perubahan
    }

    // Agar meta_data otomatis jadi array saat dipanggil, bukan string JSON
    protected $casts = [
        'meta_data' => 'array',
    ];
}
