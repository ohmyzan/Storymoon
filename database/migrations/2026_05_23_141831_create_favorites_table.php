<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('favorites', function (Blueprint $table) {
            $table->ulid('id')->primary();

            // [FIX] foreignId → foreignUlid karena users.id adalah ULID
            $table->foreignUlid('user_id')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->foreignUlid('novel_id')
                ->constrained('novels')
                ->cascadeOnDelete();

            $table->timestamps();

            $table->unique(['user_id', 'novel_id']);

            // Index: "novel apa saja yang difavoritkan user ini" (sorted by latest)
            $table->index(['user_id', 'created_at']);

            // [FIX] Tambah index untuk leaderboard "novel paling banyak difavoritkan bulan ini"
            $table->index(['novel_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('favorites');
    }
};
