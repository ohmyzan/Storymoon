<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('novel_reports', function (Blueprint $table) {
            $table->ulid('id')->primary();

            // [FIX] foreignId → foreignUlid karena users.id adalah ULID
            $table->foreignUlid('reporter_id')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->foreignUlid('novel_id')
                ->constrained('novels')
                ->cascadeOnDelete();

            $table->enum('category', ['plagiarism', 'inappropriate_content', 'spam', 'other']);
            $table->text('description');
            $table->string('proof_image')->nullable();

            $table->enum('status', ['pending', 'reviewed', 'resolved', 'rejected', 'escalated'])
                ->default('pending')
                ->index();

            $table->text('editor_notes')->nullable();

            // [FIX] Tambah nullOnDelete agar tidak error jika moderator dihapus
            $table->foreignUlid('handled_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            // [FIX] Unique constraint: satu user hanya bisa buat 1 laporan aktif per novel
            // Mencegah spam laporan dari user yang sama
            $table->unique(['reporter_id', 'novel_id'], 'unique_report_per_user_novel');

            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('novel_reports');
    }
};
