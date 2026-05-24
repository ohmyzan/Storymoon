<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('wallets', function (Blueprint $table) {
            // [FIX] Ganti id() → ulid() agar konsisten dengan seluruh skema
            $table->ulid('id')->primary();

            // [FIX] foreignId → foreignUlid karena users.id adalah ULID
            $table->foreignUlid('user_id')
                ->unique()
                ->constrained('users')
                ->cascadeOnDelete();

            $table->unsignedInteger('coin_balance')->default(0);
            $table->decimal('revenue_balance', 15, 2)->default(0.00);

            // [FIX] Tambah softDeletes — wallet adalah data finansial, tidak boleh hard-delete
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('wallets');
    }
};
