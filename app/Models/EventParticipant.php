<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\SoftDeletes;

class EventParticipant extends Model
{
    use HasUlids, SoftDeletes;

    protected $fillable = [
        'event_id',
        'novel_id',
        'status'
    ];

    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    public function novel()
    {
        return $this->belongsTo(Novel::class);
    }
}
