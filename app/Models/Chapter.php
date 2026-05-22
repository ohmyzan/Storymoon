<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Chapter extends Model
{
    use HasFactory, HasUlids, SoftDeletes;

    protected $fillable = [
        'novel_id',
        'title',
        'slug',
        'content',
        'word_count',
        'chapter_number',
        'editor_notes', // 🌟 Tambahan baru
        'status',
        'published_at',
        'is_premium',
        'coin_price'
    ];

    public function novel()
    {
        return $this->belongsTo(Novel::class);
    }
    //
}
