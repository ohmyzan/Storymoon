<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('novels', function (Blueprint $table) {
            $table->ulid('id')->primary();

            // Relasi ke tabel users sebagai author
            $table->foreignId('author_id')
                ->constrained('users')
                ->cascadeOnDelete();

            // Relasi ke editor (nullable karena novel bisa belum punya editor)
            $table->foreignId('editor_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->string('title');
            $table->string('slug')->unique();

            $table->text('synopsis');

            $table->string('cover_image')->nullable();

            // Status novel
            // index() penting untuk filtering ranking/status
            $table->enum('status', [
                'bersambung',
                'tamat'
            ])
                ->default('bersambung')
                ->index();

            // Statistik cache untuk performa
            $table->unsignedInteger('views_count')->default(0);
            $table->unsignedInteger('favorites_count')->default(0);
            $table->unsignedInteger('total_chapters')->default(0);

            $table->decimal('rating', 3, 2)->default(0.00);

            // Jika true, novel disembunyikan karena melanggar
            $table->boolean('is_frozen')
                ->default(false)
                ->index();

            // Soft delete untuk restore novel
            $table->softDeletes();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('novels');
    }
};
