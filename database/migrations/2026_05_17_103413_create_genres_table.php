<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('genres', function (Blueprint $table) {
            $table->id();

            // [FIX] cascadeOnDelete → nullOnDelete agar sub-genre tidak ikut terhapus
            // ketika genre induk dihapus (mencegah kehilangan data tidak disengaja)
            $table->foreignId('parent_id')
                ->nullable()
                ->constrained('genres')
                ->nullOnDelete();

            $table->string('name')->unique();
            $table->string('slug')->unique();
            $table->timestamps();
        });

        Schema::create('novel_genre', function (Blueprint $table) {
            $table->id();

            $table->foreignUlid('novel_id')
                ->constrained('novels')
                ->cascadeOnDelete();

            $table->foreignId('genre_id')
                ->constrained('genres')
                ->cascadeOnDelete();

            $table->unique(['novel_id', 'genre_id']);

            // [FIX] Tambah created_at untuk audit "kapan genre ini ditambahkan ke novel"
            $table->timestamp('created_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('novel_genre');
        Schema::dropIfExists('genres');
    }
};
