<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Genre extends Model
{
    use HasFactory;

    // Keamanan: Hanya mengizinkan 3 kolom ini yang bisa diisi secara massal
    protected $fillable = [
        'parent_id',
        'name',
        'slug'
    ];

    /**
     * Relasi untuk mendapatkan Sub-Genre (Anak)
     * Satu Genre Utama bisa memiliki banyak Sub-Genre
     */
    public function children()
    {
        return $this->hasMany(Genre::class, 'parent_id');
    }

    /**
     * Relasi untuk mendapatkan Genre Utama (Induk)
     * Sub-Genre ini milik satu Genre Utama tertentu
     */
    public function parent()
    {
        return $this->belongsTo(Genre::class, 'parent_id');
    }

    /**
     * Relasi ke Novel
     * Menggunakan tabel pivot 'novel_genre' sesuai migration yang kita buat di awal
     */
    public function novels()
    {
        return $this->belongsToMany(Novel::class, 'novel_genre');
    }
}
