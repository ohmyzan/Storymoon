<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('contracts', function (Blueprint $table) {
            $table->ulid('id')->primary();

            // Relasi Inti
            $table->foreignUlid('novel_id')->constrained('novels')->cascadeOnDelete();
            $table->foreignId('author_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('editor_id')->nullable()->constrained('users')->nullOnDelete();

            // Detail Bisnis (Eksklusif / Non-Eksklusif)
            $table->enum('contract_type', ['exclusive', 'non_exclusive']);
            $table->integer('revenue_share_author');   // Misal: 70
            $table->integer('revenue_share_platform'); // Misal: 30

            // Data Hukum & Finansial (KYC)
            $table->string('real_name');
            $table->string('id_card_number'); // NIK KTP
            $table->string('id_card_image');  // Bukti KTP
            $table->string('selfie_image')->nullable();
            $table->string('bank_name');
            $table->string('bank_account_number');
            $table->string('bank_account_name');
            $table->string('external_links')->nullable(); // Bukti link dihapus dari platform lain

            // 🌟 LEGALITAS & TANDA TANGAN (Sesuai ide Anda!)
            $table->text('signature_image'); // Menyimpan base64/path gambar coretan tanda tangan
            $table->string('contract_document_path')->nullable(); // Nanti sistem akan meng-generate PDF sahnya ke sini

            // Status Persetujuan Editor
            $table->enum('status', [
                'text_review',     // Tahap 1: Editor baca naskah
                'kyc_submission',  // Tahap 2: Minta penulis isi KTP/Rekening (Khusus Eksklusif)
                'kyc_review',      // Tahap 3: Admin Finance cek dokumen
                'signing',         // Tahap 4: Tunggu Tanda Tangan & OTP
                'active',          // Tahap 5: Kontrak Sah!
                'rejected'         // Ditolak
            ])->default('text_review')->index();
            $table->text('editor_notes')->nullable(); // Alasan revisi/tolak

            $table->timestamp('signed_at')->nullable(); // Tanggal sah disetujui
            $table->softDeletes();
            $table->timestamps();

            // Mencegah 1 novel mengajukan lebih dari 1 kontrak aktif secara bersamaan
            $table->unique(['novel_id', 'status'], 'unique_active_contract');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contracts');
    }
};
