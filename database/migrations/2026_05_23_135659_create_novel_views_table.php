<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('novel_views', function (Blueprint $table) {
            $table->ulid('id')->primary();

            $table->foreignUlid('novel_id')
                ->constrained('novels')
                ->cascadeOnDelete();

            // [FIX] foreignId → foreignUlid karena users.id adalah ULID
            // Nullable: view bisa dari guest
            $table->foreignUlid('user_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->string('ip_address', 45)->nullable();
            $table->string('session_id')->nullable();

            $table->timestamp('viewed_at')->useCurrent()->index();

            // Composite index untuk query "views novel X dalam 7 hari terakhir"
            $table->index(['novel_id', 'viewed_at']);

            // [NOTE - SCALABILITY] Tabel ini akan tumbuh sangat cepat (ratusan juta baris).
            // Strategi yang WAJIB diimplementasikan sebelum production:
            //
            // 1. Deduplikasi via Redis sebelum insert:
            //    Cache::put("view:{$novel_id}:{$session_id}", true, now()->addHours(24));
            //    Insert ke DB hanya jika key belum ada di cache.
            //
            // 2. Scheduled cleanup job (simpan hanya 90 hari terakhir):
            //    NovelView::where('viewed_at', '<', now()->subDays(90))->delete();
            //
            // 3. Untuk trending/ranking, gunakan tabel agregat harian:
            //    novel_view_aggregates: novel_id, date, view_count
            //    Lebih efisien daripada COUNT(*) dari tabel ini.
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('novel_views');
    }
};
