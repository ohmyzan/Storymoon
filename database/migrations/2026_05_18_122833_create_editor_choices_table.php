<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('editor_choices', function (Blueprint $table) {
            $table->ulid('id')->primary();

            // Relasi ke novel yang dinominasikan
            $table->foreignUlid('novel_id')->constrained('novels')->cascadeOnDelete();

            // Relasi ke Editor yang mengajukan nominasi
            $table->foreignId('editor_id')->constrained('users')->cascadeOnDelete();

            // Alasan analisis dari editor (misal: data retensi, kualitas plot)
            $table->text('editor_notes');

            // Status persetujuan dari Admin
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending')->index();

            // Catatan umpan balik dari Admin (jika ditolak atau ada catatan tambahan)
            $table->text('admin_notes')->nullable();

            $table->softDeletes();
            $table->timestamps();

            // Keamanan: Mencegah novel yang sama diajukan berkali-kali jika statusnya masih pending/approved
            $table->unique(['novel_id', 'status'], 'unique_active_nomination');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('editor_choices');
    }
};
