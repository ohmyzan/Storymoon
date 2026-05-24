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

            // [FIX] foreignId → foreignUlid karena users.id adalah ULID
            $table->foreignUlid('user_id')
                ->constrained('users')
                ->restrictOnDelete();

            $table->unsignedInteger('coins_redeemed');
            $table->unsignedInteger('amount_rupiah');

            // Snapshot rekening bank saat pengajuan (anti-masalah ganti rekening di tengah proses)
            // [SECURITY] bank_account_number dienkripsi di Model layer
            // via $casts = ['bank_account_number' => 'encrypted']
            $table->string('bank_name');
            $table->string('bank_account_number');
            $table->string('bank_account_name');

            $table->string('proof_image')->nullable();

            // [FIX] Tambah nullOnDelete agar tidak error jika staff finance dihapus
            $table->foreignUlid('processed_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->enum('status', ['pending', 'approved', 'rejected'])
                ->default('pending')
                ->index();

            $table->text('finance_notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('withdrawals');
    }
};
