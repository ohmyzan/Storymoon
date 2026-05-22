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

            $table->foreignUlid('event_id')->constrained('events')->cascadeOnDelete();
            $table->foreignUlid('novel_id')->constrained('novels')->cascadeOnDelete();

            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending')->index();

            $table->softDeletes();
            $table->timestamps();

            // Satu novel hanya boleh mendaftar satu kali di event yang sama
            $table->unique(['event_id', 'novel_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('event_participants');
    }
};
