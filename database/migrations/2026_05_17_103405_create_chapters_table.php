<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('chapters', function (Blueprint $table) {
            $table->ulid('id')->primary();

            $table->foreignUlid('novel_id')
                ->constrained('novels')
                ->cascadeOnDelete();

            $table->string('title');
            $table->string('slug');
            $table->unique(['novel_id', 'slug'], 'unique_chapter_slug_per_novel');

            $table->longText('content');
            $table->unsignedInteger('word_count')->default(0);

            // [FIX] unsignedInteger agar tidak bisa negatif
            $table->unsignedInteger('chapter_number');

            // [FIX] Tambah unique constraint agar nomor chapter tidak duplikat dalam satu novel
            $table->unique(['novel_id', 'chapter_number'], 'unique_chapter_number_per_novel');

            $table->text('editor_notes')->nullable();

            $table->enum('status', ['draft', 'review', 'revision_needed', 'scheduled', 'published'])
                ->default('draft')
                ->index();

            // [FIX] Tambah index untuk query chapter terjadwal
            $table->timestamp('published_at')->nullable()->index();

            $table->boolean('is_premium')->default(false)->index();
            $table->unsignedInteger('coin_price')->default(0);

            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('chapters');
    }
};
