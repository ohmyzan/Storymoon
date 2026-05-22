<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->ulid('id')->primary();
            // UBAH relasi cascade menjadi restrict
            $table->foreignId('user_id')->constrained('users')->restrictOnDelete();
            $table->foreignId('wallet_id')->constrained('wallets')->restrictOnDelete();

            $table->enum('type', ['top_up', 'withdraw', 'chapter_purchase', 'revenue_share', 'refund'])->index();

            // UBAH bagian amount ini menjadi dua kolom:
            $table->integer('coin_amount')->default(0);
            $table->decimal('rupiah_amount', 15, 2)->default(0.00);
            $table->enum('status', ['pending', 'completed', 'failed', 'refunded'])->default('pending')->index();

            $table->string('reference_id')->nullable()->unique(); // ID dari Payment Gateway (Midtrans)
            $table->text('meta_data')->nullable(); // Menyimpan log JSON detail transaksi untuk audit fraud

            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
