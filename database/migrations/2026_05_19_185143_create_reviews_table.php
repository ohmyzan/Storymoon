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
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignUlid('novel_id')->constrained('novels')->cascadeOnDelete();
            $table->tinyInteger('rating'); // Bintang 1 sampai 5
            $table->text('content'); // Isi ulasan
            $table->timestamps();
            $table->unique(['user_id', 'novel_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reviews');
    }
};
