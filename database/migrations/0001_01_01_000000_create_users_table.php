<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            // [FIX] Ganti id() → ulid() agar konsisten dengan seluruh skema
            // Pastikan Model User menggunakan trait HasUlids
            $table->ulid('id')->primary();

            $table->string('name');
            $table->string('pen_name')->unique()->nullable();
            $table->string('phone_number')->nullable();

            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->rememberToken();

            // Moderasi bertingkat (muted < suspended < banned)
            $table->timestamp('muted_until')->nullable();
            $table->timestamp('suspended_until')->nullable();
            $table->timestamp('banned_at')->nullable();

            // Verifikasi role
            $table->timestamp('author_verified_at')->nullable();
            $table->timestamp('editor_verified_at')->nullable();

            // [FIX] Index untuk query moderasi & pengecekan role yang sering dijalankan
            // Contoh: middleware SuspensionCheck, AuthorOnly gate, dll.
            $table->index('banned_at');
            $table->index('author_verified_at');
            $table->index('editor_verified_at');

            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();

            // [FIX] Ganti foreignId → ulid column biasa (tanpa constrained)
            // agar session tetap valid saat user di-soft-delete
            $table->ulid('user_id')->nullable()->index();

            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};
