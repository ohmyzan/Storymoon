<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('keyword_filters', function (Blueprint $table) {
            $table->id();

            $table->string('keyword')->unique();
            $table->string('replacement')->default('***');

            // [FIX] Tambah severity untuk moderasi yang lebih granular
            // low = sensor saja, medium = sensor + log, high = sensor + notif moderator
            $table->enum('severity', ['low', 'medium', 'high'])->default('medium');

            $table->boolean('is_active')->default(true);

            // [FIX] foreignId → foreignUlid karena users.id adalah ULID
            $table->foreignUlid('created_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('keyword_filters');
    }
};
