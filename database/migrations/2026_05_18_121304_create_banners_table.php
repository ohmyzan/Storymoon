<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('banners', function (Blueprint $table) {
            $table->id();

            $table->string('title');
            $table->string('image_path');
            $table->string('target_url')->nullable();
            $table->boolean('is_active')->default(true)->index();
            $table->integer('sort_order')->default(0);

            // [FIX] Tambah periode aktif agar banner bisa dijadwalkan otomatis
            // is_active tetap dipertahankan untuk toggle manual override
            $table->timestamp('start_date')->nullable();
            $table->timestamp('end_date')->nullable();
            $table->index(['start_date', 'end_date']);

            // [FIX] Audit trail siapa yang membuat banner
            $table->foreignUlid('created_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('banners');
    }
};
