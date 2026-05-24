<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reviews', function (Blueprint $table) {
            $table->ulid('id')->primary();

            // [FIX] foreignId → foreignUlid karena users.id adalah ULID
            $table->foreignUlid('user_id')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->foreignUlid('novel_id')
                ->constrained('novels')
                ->cascadeOnDelete();

            // [FIX] unsignedTinyInteger agar tidak bisa diisi nilai negatif atau nol
            // Validasi range 1-5 dilakukan di Model/FormRequest layer
            $table->unsignedTinyInteger('rating');

            $table->text('content');

            $table->softDeletes();
            $table->timestamps();

            $table->unique(['user_id', 'novel_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reviews');
    }
};
