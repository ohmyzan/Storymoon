<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('comments', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignUlid('novel_id')->constrained('novels')->cascadeOnDelete();
            $table->foreignUlid('chapter_id')->nullable()->constrained('chapters')->cascadeOnDelete();

            $table->string('paragraph_id')->nullable()->index(); // Untuk fitur komentar per paragraf
            $table->text('content');
            $table->boolean('is_hidden')->default(false)->index(); // Untuk moderasi/hide spam

            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('comments');
    }
};
