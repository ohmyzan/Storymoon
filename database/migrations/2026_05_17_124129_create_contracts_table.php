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

            $table->foreignUlid('novel_id')
                ->constrained('novels')
                ->cascadeOnDelete();

            $table->foreignUlid('author_id')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->foreignUlid('editor_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->enum('contract_type', ['exclusive', 'non_exclusive']);

            $table->unsignedTinyInteger('revenue_share_author');
            $table->unsignedTinyInteger('revenue_share_platform');

            // KYC — ubah semua kolom yang di-cast 'encrypted' menjadi text
            $table->text('real_name');
            $table->text('id_card_number');
            $table->string('id_card_image');
            $table->string('selfie_image')->nullable();
            $table->string('bank_name');
            $table->text('bank_account_number');
            $table->text('bank_account_name');
            $table->string('external_links')->nullable();

            $table->string('signature_image_path')->nullable();
            $table->string('contract_document_path')->nullable();

            $table->enum('status', [
                'text_review',
                'kyc_submission',
                'kyc_review',
                'signing',
                'active',
                'rejected',
            ])->default('text_review')->index();

            // ✅ FIX UTAMA: kontrol kontrak aktif
            $table->boolean('is_active')->default(false);

            $table->text('editor_notes')->nullable();
            $table->timestamp('signed_at')->nullable();

            $table->softDeletes();
            $table->timestamps();

            // Index performa
            $table->index(['novel_id', 'status']);

            // ✅ ENFORCEMENT: hanya 1 kontrak aktif per novel
            $table->unique(['novel_id', 'is_active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contracts');
    }
};
