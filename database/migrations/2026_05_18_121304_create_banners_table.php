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
            $table->string('image_path'); // Tempat menyimpan nama file gambar
            $table->string('target_url')->nullable(); // URL tujuan saat gambar diklik
            $table->boolean('is_active')->default(true)->index(); // Status tayang
            $table->integer('sort_order')->default(0); // Untuk mengatur urutan slide
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('banners');
    }
};
