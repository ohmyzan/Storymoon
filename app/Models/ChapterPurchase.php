<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class ChapterPurchase extends Model
{
    use HasFactory, HasUlids, logsActivity;

    protected $fillable = [
        'reader_id',
        'chapter_id',
        'author_id',
        'price_coined',
        'author_earning',
        'platform_earning',
        'contract_type_snapshot',
        'revenue_share_snapshot',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logFillable() // Rekam semua perubahan pada kolom $fillable
            ->logOnlyDirty() // Hanya rekam kolom yang benar-benar berubah
            ->dontSubmitEmptyLogs(); // Jangan rekam jika tidak ada perubahan
    }

    public function reader()
    {
        return $this->belongsTo(User::class, 'reader_id');
    }
    public function chapter()
    {
        return $this->belongsTo(Chapter::class);
    }
    public function author()
    {
        return $this->belongsTo(User::class, 'author_id');
    }
}
