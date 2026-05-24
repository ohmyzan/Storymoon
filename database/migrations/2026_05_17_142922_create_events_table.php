<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('events', function (Blueprint $table) {
            $table->ulid('id')->primary();

            $table->string('title');
            $table->string('slug')->unique();
            $table->text('description');
            $table->string('banner_image')->nullable();

            $table->dateTime('start_date');
            $table->dateTime('end_date');

            // [FIX] Composite index untuk query "event yang sedang aktif"
            $table->index(['start_date', 'end_date']);

            $table->enum('status', ['draft', 'active', 'completed'])
                ->default('draft')
                ->index();

            // [FIX] Tambah created_by untuk audit trail siapa yang membuat event
            $table->foreignUlid('created_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('events');
    }
};
