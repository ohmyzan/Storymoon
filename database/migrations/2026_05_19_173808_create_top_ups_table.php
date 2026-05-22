<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('top_ups', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignId('user_id')->constrained('users')->restrictOnDelete(); // Pembaca yang top-up

            // Kode unik transaksi dari sistem kita atau Payment Gateway
            $table->string('reference_id')->unique()->index();

            // Aplikasi Aturan: 1 Koin = Rp 100
            $table->unsignedInteger('amount_rupiah'); // Misal: 50000
            $table->unsignedInteger('coins_granted');  // Otomatis terhitung: 500 koin

            $table->string('payment_method')->nullable(); // Misal: GoPay, ShopeePay, BCA_VA

            $table->enum('status', ['pending', 'success', 'failed', 'expired'])
                ->default('pending')
                ->index();

            $table->timestamp('settled_at')->nullable(); // Waktu pembayaran sukses diverifikasi
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('top_ups');
    }
};
