<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Withdrawal extends Model
{
    use HasFactory, HasUlids, LogsActivity;

    protected $fillable = [
        'user_id',
        'coins_redeemed',
        'amount_rupiah',
        'bank_name',
        'bank_account_number',
        'bank_account_name',
        'proof_image',
        'processed_by',
        'status',
        'finance_notes',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logFillable() // Rekam semua perubahan pada kolom $fillable
            ->logOnlyDirty() // Hanya rekam kolom yang benar-benar berubah
            ->dontSubmitEmptyLogs(); // Jangan rekam jika tidak ada perubahan
    }

    protected $casts = [
        'coins_redeemed' => 'integer',
        'amount_rupiah' => 'integer',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function processor()
    {
        return $this->belongsTo(User::class, 'processed_by');
    }
}
