<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Novel extends Model
{
    use HasFactory, HasUlids, SoftDeletes, LogsActivity;
    // Hapus $guarded, ganti dengan $fillable yang ketat
    protected $fillable = [
        'author_id',
        'editor_id',
        'title',
        'slug',
        'synopsis',
        'cover_image',
        'status',
        'views_count',
        'favorites_count',
        'total_chapters',
        'rating',
        'is_frozen',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logFillable() // Rekam semua perubahan pada kolom $fillable
            ->logOnlyDirty() // Hanya rekam kolom yang benar-benar berubah
            ->dontSubmitEmptyLogs(); // Jangan rekam jika tidak ada perubahan
    }

    public function chapters()
    {
        return $this->hasMany(Chapter::class);
    }

    public function genres()
    {
        return $this->belongsToMany(Genre::class, 'novel_genre');
    }

    public function author()
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    public function editorChoices()
    {
        return $this->hasMany(EditorChoice::class);
    }
    //
}
