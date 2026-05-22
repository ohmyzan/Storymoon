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

            $table->foreignId('author_id')->constrained('users')->restrictOnDelete();

            // Periode Slip (Misal: Bulan 5, Tahun 2026)
            $table->integer('month');
            $table->integer('year');

            // Detail Transparansi Angka
            $table->integer('total_gross_coins')->default(0); // Total koin kotor
            $table->integer('platform_fee_coins')->default(0); // Potongan platform
            $table->integer('tax_coins')->default(0); // Persiapan jika nanti kena pajak
            $table->integer('net_author_coins')->default(0); // Koin bersih yang masuk dompet

            // Status Slip
            $table->enum('status', ['calculated', 'credited_to_wallet'])->default('calculated');

            $table->timestamps();

            // Mencegah sistem membuat 2 slip untuk bulan dan author yang sama
            $table->unique(['author_id', 'month', 'year']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payout_statements');
    }
};
