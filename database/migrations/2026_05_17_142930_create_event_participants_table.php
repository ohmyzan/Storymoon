<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('event_participants', function (Blueprint $table) {
            $table->ulid('id')->primary();

            $table->foreignUlid('event_id')
                ->constrained('events')
                ->cascadeOnDelete();

            $table->foreignUlid('novel_id')
                ->constrained('novels')
                ->cascadeOnDelete();

            $table->enum('status', ['pending', 'approved', 'rejected'])
                ->default('pending')
                ->index();

            // [FIX] Rekam jejak siapa yang mereview peserta event
            $table->foreignUlid('reviewed_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->text('reviewer_notes')->nullable();

            $table->softDeletes();
            $table->timestamps();

            $table->unique(['event_id', 'novel_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('event_participants');
    }
};
