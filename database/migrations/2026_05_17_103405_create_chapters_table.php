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

            // Relasi ke novel
            $table->foreignUlid('novel_id')->constrained('novels')->cascadeOnDelete();

            $table->string('title');
            $table->string('slug'); // 🌟 Tambahkan ini
            $table->unique(['novel_id', 'slug'], 'unique_chapter_slug_per_novel');
            $table->longText('content'); // Isi cerita
            $table->unsignedInteger('word_count')->default(0);

            // Order untuk mengurutkan chapter
            $table->integer('chapter_number');

            // 🌟 TAMBAHAN: Kolom untuk Editor meninggalkan jejak revisi
            $table->text('editor_notes')->nullable();

            // 🌟 UPDATE: Status digabung antara kebutuhan Author & Editor
            $table->enum('status', ['draft', 'review', 'revision_needed', 'scheduled', 'published'])
                ->default('draft')
                ->index();

            $table->timestamp('published_at')->nullable(); // Untuk fitur jadwal rilis

            // Fitur premium/koin
            $table->boolean('is_premium')
                ->default(false)
                ->index();

            $table->integer('coin_price')->default(0);

            // Soft delete agar chapter tidak benar-benar terhapus
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('chapters');
    }
};
