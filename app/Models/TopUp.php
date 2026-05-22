<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class TopUp extends Model
{
    use HasFactory, HasUlids, LogsActivity;

    protected $fillable = [
        'user_id',
        'reference_id',
        'amount_rupiah',
        'coins_granted',
        'payment_method',
        'status',
        'settled_at',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logFillable() // Rekam semua perubahan pada kolom $fillable
            ->logOnlyDirty() // Hanya rekam kolom yang benar-benar berubah
            ->dontSubmitEmptyLogs(); // Jangan rekam jika tidak ada perubahan
    }

    protected $casts = [
        'settled_at' => 'datetime',
        'amount_rupiah' => 'integer',
        'coins_granted' => 'integer',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
