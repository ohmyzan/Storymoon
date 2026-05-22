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

            // Relasi Aktor & Konten
            $table->foreignId('reporter_id')->constrained('users')->cascadeOnDelete(); // Siapa yang melapor
            $table->foreignUlid('review_id')->constrained('reviews')->cascadeOnDelete(); // Ulasan mana yang dilaporkan (Asumsi tabel reviews sudah ada/akan dibuat)

            // Detail Laporan
            $table->enum('reason', ['hate_speech', 'review_bombing', 'harassment', 'spoiler', 'other']);
            $table->text('description')->nullable();

            // Workflow Penanganan (Status Ekskalasi)
            $table->enum('status', ['pending', 'resolved', 'rejected', 'escalated'])->default('pending')->index();
            $table->text('moderator_notes')->nullable();
            $table->foreignId('handled_by')->nullable()->constrained('users'); // Moderator yang menangani

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('review_reports');
    }
};
