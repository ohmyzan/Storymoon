<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUlids;

class PayoutStatement extends Model
{
    use HasUlids;

    protected $fillable = [
        'author_id',
        'month',
        'year',
        'total_gross_coins',
        'platform_fee_coins',
        'tax_coins',
        'net_author_coins',
        'status'
    ];

    public function author()
    {
        return $this->belongsTo(User::class, 'author_id');
    }
}
