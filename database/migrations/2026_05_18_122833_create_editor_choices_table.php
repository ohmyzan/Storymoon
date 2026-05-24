<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('editor_choices', function (Blueprint $table) {
            $table->ulid('id')->primary();

            $table->foreignUlid('novel_id')
                ->constrained('novels')
                ->cascadeOnDelete();

            // [FIX] foreignId → foreignUlid karena users.id adalah ULID
            $table->foreignUlid('editor_id')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->text('editor_notes');

            $table->enum('status', ['pending', 'approved', 'rejected'])
                ->default('pending')
                ->index();

            $table->text('admin_notes')->nullable();

            $table->softDeletes();
            $table->timestamps();

            // [FIX] Hapus unique(['novel_id', 'status']) — tidak bekerja sesuai harapan.
            // Unique tersebut hanya mencegah kombinasi (novel, status) yang sama,
            // artinya satu novel tetap bisa punya 1 baris 'pending' DAN 1 baris 'approved'
            // sekaligus — yang seharusnya tidak boleh.
            // Pencegahan dilakukan di application layer:
            // "Jika novel sudah punya status pending/approved, tolak pengajuan baru"

            // Index untuk query "apakah novel X sudah dinominasikan?"
            $table->index(['novel_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('editor_choices');
    }
};
