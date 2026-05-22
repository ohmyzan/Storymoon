<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Tabel Master Genre (Fantasi, Perkotaan, Horor, dll)
        Schema::create('genres', function (Blueprint $table) {
            $table->id();
            // Tambahkan baris ini di bawah id()
            $table->foreignId('parent_id')->nullable()->constrained('genres')->cascadeOnDelete();

            $table->string('name')->unique();
            $table->string('slug')->unique();
            $table->timestamps();
        });

        // Tabel Pivot untuk Relasi Many-to-Many (Novel bisa punya banyak genre)
        Schema::create('novel_genre', function (Blueprint $table) {
            $table->id();
            $table->foreignUlid('novel_id')->constrained('novels')->cascadeOnDelete();
            $table->foreignId('genre_id')->constrained('genres')->cascadeOnDelete();

            // Mencegah duplikasi genre pada novel yang sama
            $table->unique(['novel_id', 'genre_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('novel_genre');
        Schema::dropIfExists('genres');
    }
};
