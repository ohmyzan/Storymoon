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

            // Relasi
            $table->foreignId('reporter_id')->constrained('users')->cascadeOnDelete(); // Pembaca yang melapor
            $table->foreignUlid('novel_id')->constrained('novels')->cascadeOnDelete(); // Novel yang dilaporkan

            // Detail Laporan
            $table->enum('category', ['plagiarism', 'inappropriate_content', 'spam', 'other']);
            $table->text('description');
            $table->string('proof_image')->nullable(); // Screenshot bukti dari pelapor

            // Status & Resolusi (Workflow Editor)
            $table->enum('status', ['pending', 'reviewed', 'resolved', 'rejected', 'escalated'])->default('pending')->index();
            $table->text('editor_notes')->nullable(); // Catatan tindakan Editor
            $table->foreignId('handled_by')->nullable()->constrained('users'); // Editor/Admin yang menangani

            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('novel_reports');
    }
};
