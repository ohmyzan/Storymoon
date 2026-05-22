<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\SoftDeletes;

class Event extends Model
{
    use HasUlids, SoftDeletes;

    protected $fillable = [
        'title',
        'slug',
        'description',
        'banner_image',
        'start_date',
        'end_date',
        'status'
    ];

    public function participants()
    {
        return $this->hasMany(EventParticipant::class);
    }
}
