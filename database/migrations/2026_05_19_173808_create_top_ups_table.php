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

            // [FIX] foreignId → foreignUlid karena users.id adalah ULID
            $table->foreignUlid('user_id')
                ->constrained('users')
                ->restrictOnDelete();

            $table->string('reference_id')->unique()->index();

            $table->unsignedInteger('amount_rupiah');
            $table->unsignedInteger('coins_granted');

            $table->string('payment_method')->nullable();

            $table->enum('status', ['pending', 'success', 'failed', 'expired'])
                ->default('pending')
                ->index();

            // [FIX] Composite index untuk reconciliation: semua top-up pending milik user X
            $table->index(['user_id', 'status']);

            // [FIX] Simpan payload callback dari payment gateway untuk debugging & audit fraud
            $table->json('gateway_payload')->nullable();

            $table->timestamp('settled_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('top_ups');
    }
};
