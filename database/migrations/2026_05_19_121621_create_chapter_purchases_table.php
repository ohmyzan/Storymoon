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

            // [FIX] foreignId → foreignUlid karena users.id adalah ULID
            $table->foreignUlid('reader_id')
                ->constrained('users')
                ->restrictOnDelete();

            $table->foreignUlid('chapter_id')
                ->constrained('chapters')
                ->restrictOnDelete();

            $table->foreignUlid('novel_id')
                ->constrained('novels')
                ->restrictOnDelete();

            $table->foreignUlid('author_id')
                ->constrained('users')
                ->restrictOnDelete();

            // [FIX] Unique constraint mencegah double-purchase akibat race condition
            $table->unique(['reader_id', 'chapter_id'], 'unique_purchase_per_reader_chapter');

            // [FIX] Rename price_coined → coin_price agar konsisten dengan chapters.coin_price
            $table->unsignedInteger('coin_price');
            $table->unsignedInteger('author_earning');
            $table->unsignedInteger('platform_earning');

            // Snapshot kontrak saat transaksi terjadi (audit log hukum)
            $table->enum('contract_type_snapshot', ['exclusive', 'non_exclusive']);
            $table->unsignedTinyInteger('revenue_share_snapshot');

            $table->timestamps();

            // Index untuk laporan keuangan bulanan per author
            $table->index(['author_id', 'created_at']);

            // [FIX] Index tambahan untuk query "berapa pembeli chapter X"
            $table->index('chapter_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('chapter_purchases');
    }
};
