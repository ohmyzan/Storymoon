<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payout_statements', function (Blueprint $table) {
            $table->ulid('id')->primary();

            // [FIX] foreignId → foreignUlid karena users.id adalah ULID
            $table->foreignUlid('author_id')
                ->constrained('users')
                ->restrictOnDelete();

            $table->unsignedSmallInteger('month'); // 1-12
            $table->unsignedSmallInteger('year');  // Misal: 2026

            $table->unsignedInteger('total_gross_coins')->default(0);
            $table->unsignedInteger('platform_fee_coins')->default(0);
            $table->unsignedInteger('tax_coins')->default(0);
            $table->unsignedInteger('net_author_coins')->default(0);

            // [FIX] Perluas status enum untuk menangani kegagalan proses kredit
            $table->enum('status', [
                'calculated',        // Slip sudah dihitung
                'pending_approval',  // Menunggu persetujuan finance
                'credited_to_wallet', // Koin sudah masuk wallet
                'failed',            // Proses kredit gagal — perlu retry
            ])->default('calculated');

            // [FIX] Timestamp untuk audit kapan tepatnya koin dikreditkan
            $table->timestamp('credited_at')->nullable();

            $table->timestamps();

            $table->unique(['author_id', 'month', 'year']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payout_statements');
    }
};
