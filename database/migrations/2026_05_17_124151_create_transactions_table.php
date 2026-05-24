<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->ulid('id')->primary();

            // [FIX] foreignId → foreignUlid karena users.id adalah ULID
            $table->foreignUlid('user_id')
                ->constrained('users')
                ->restrictOnDelete();

            // wallets.id sekarang juga ULID
            $table->foreignUlid('wallet_id')
                ->constrained('wallets')
                ->restrictOnDelete();

            $table->enum('type', [
                'top_up',
                'withdraw',
                'chapter_purchase',
                'revenue_share',
                'refund',
            ])->index();

            $table->unsignedInteger('coin_amount')->default(0);
            $table->decimal('rupiah_amount', 15, 2)->default(0.00);

            $table->enum('status', ['pending', 'completed', 'failed', 'refunded'])
                ->default('pending')
                ->index();

            $table->string('reference_id')->nullable()->unique();

            // [FIX] Ganti text → json untuk menyimpan log transaksi
            // Memungkinkan query dan validasi struktur data
            $table->json('meta_data')->nullable();

            $table->softDeletes();
            $table->timestamps();

            // [FIX] Composite index untuk laporan finansial per user per periode
            $table->index(['user_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
