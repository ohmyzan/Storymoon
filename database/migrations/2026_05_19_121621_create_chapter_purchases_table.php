<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('chapter_purchases', function (Blueprint $table) {
            $table->ulid('id')->primary();

            // Relasi Aktor & Konten
            $table->foreignId('reader_id')->constrained('users')->restrictOnDelete(); // Siapa yang beli
            $table->foreignUlid('chapter_id')->constrained('chapters')->restrictOnDelete(); // Bab apa yang dibeli
            $table->foreignId('author_id')->constrained('users')->cascadeOnDelete(); // Ditargetkan langsung ke Author agar query Finance nanti super cepat

            // Logika Pembagian Ledger (Sesuai kesepakatan Opsi B)
            $table->integer('price_coined'); // Harga total bab saat dibeli (misal: 10 koin)
            $table->integer('author_earning'); // Porsi untuk penulis (misal: 7 atau 5 koin)
            $table->integer('platform_earning'); // Porsi untuk platform (misal: 3 atau 5 koin)

            // Snapshot Kontrak saat kejadian (untuk audit log hukum)
            $table->enum('contract_type_snapshot', ['exclusive', 'non_exclusive']);
            $table->integer('revenue_share_snapshot'); // Menyimpan angka 70 atau 50

            $table->timestamps(); // Berguna sebagai tanggal pembukuan keuangan

            // Indeks untuk optimasi performa query laporan bulanan finance
            $table->index(['author_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('chapter_purchases');
    }
};
