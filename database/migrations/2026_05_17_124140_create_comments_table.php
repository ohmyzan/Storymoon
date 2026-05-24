<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('comments', function (Blueprint $table) {
            $table->ulid('id')->primary();

            // [FIX] foreignId → foreignUlid karena users.id adalah ULID
            $table->foreignUlid('user_id')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->foreignUlid('novel_id')
                ->constrained('novels')
                ->cascadeOnDelete();

            $table->foreignUlid('chapter_id')
                ->nullable()
                ->constrained('chapters')
                ->cascadeOnDelete();

            // [FIX] Self-referencing untuk fitur reply/nested comment
            // Nullable agar komentar root tetap bisa dibuat tanpa parent
            $table->foreignUlid('parent_id')
                ->nullable()
                ->constrained('comments')
                ->cascadeOnDelete();

            $table->string('paragraph_id')->nullable()->index();
            $table->text('content');

            // [FIX] Cache counter likes untuk menghindari COUNT query saat listing komentar
            $table->unsignedInteger('likes_count')->default(0);

            $table->boolean('is_hidden')->default(false)->index();

            $table->softDeletes();
            $table->timestamps();

            // [FIX] Composite index untuk query komentar per chapter
            $table->index(['novel_id', 'chapter_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('comments');
    }
};
