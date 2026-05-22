<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('withdrawals', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignId('user_id')->constrained('users')->restrictOnDelete(); // Author yang menarik dana

            // Logika Konversi: Mengonversi koin pendapatan menjadi Rupiah tunai
            $table->unsignedInteger('coins_redeemed'); // Jumlah koin yang ditarik (Misal: 10000 koin)
            $table->unsignedInteger('amount_rupiah');   // Uang asli yang dicairkan (Misal: Rp 1.000.000)

            // Snapshot Rekening Bank saat penarikan diajukan (Menghindari masalah jika author ganti rekening di tengah jalan)
            $table->string('bank_name');
            $table->string('bank_account_number');
            $table->string('bank_account_name');

            // Bukti Audit Akuntansi
            $table->string('proof_image')->nullable(); // Foto bukti transfer sukses dari Finance
            $table->foreignId('processed_by')->nullable()->constrained('users'); // Staff Finance yang memproses

            $table->enum('status', ['pending', 'approved', 'rejected'])
                ->default('pending')
                ->index();

            $table->text('finance_notes')->nullable(); // Catatan jika ditolak (Misal: No. Rekening salah)
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('withdrawals');
    }
};
