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

            // [FIX] foreignId → foreignUlid karena users.id adalah ULID
            $table->foreignUlid('author_id')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->foreignUlid('editor_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->string('title');
            $table->string('slug')->unique();
            $table->text('synopsis');
            $table->string('cover_image')->nullable();

            // [FIX] Pisah status utama dan status publikasi agar tidak ambigu
            // status: kondisi novel secara keseluruhan
            $table->enum('status', ['draft', 'published', 'frozen'])
                ->default('draft')
                ->index();

            // publish_status: sub-status saat novel sudah published
            // null = belum published, ongoing = bersambung, completed = tamat
            $table->enum('publish_status', ['ongoing', 'completed'])
                ->nullable()
                ->index();

            // [FIX] Hapus is_frozen — sudah direpresentasikan oleh status = 'frozen'
            // Cache counter untuk performa
            $table->unsignedInteger('views_count')->default(0);
            $table->unsignedInteger('favorites_count')->default(0);
            $table->unsignedInteger('total_chapters')->default(0);

            $table->decimal('rating', 3, 2)->default(0.00);

            $table->softDeletes();
            $table->timestamps();
            $table->timestamp('published_at')->nullable()->index();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('novels');
    }
};
