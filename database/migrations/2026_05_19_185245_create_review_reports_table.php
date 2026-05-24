<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('review_reports', function (Blueprint $table) {
            $table->ulid('id')->primary();

            // [FIX] foreignId → foreignUlid karena users.id adalah ULID
            $table->foreignUlid('reporter_id')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->foreignUlid('review_id')
                ->constrained('reviews')
                ->cascadeOnDelete();

            $table->enum('reason', ['hate_speech', 'review_bombing', 'harassment', 'spoiler', 'other']);
            $table->text('description')->nullable();

            $table->enum('status', ['pending', 'resolved', 'rejected', 'escalated'])
                ->default('pending')
                ->index();

            $table->text('moderator_notes')->nullable();

            // [FIX] Tambah nullOnDelete agar tidak error jika moderator dihapus
            $table->foreignUlid('handled_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            // [FIX] Unique constraint: satu user hanya bisa report review yang sama sekali
            $table->unique(['reporter_id', 'review_id'], 'unique_report_per_user_review');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('review_reports');
    }
};
