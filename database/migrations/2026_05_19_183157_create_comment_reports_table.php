    <?php

    use Illuminate\Database\Migrations\Migration;
    use Illuminate\Database\Schema\Blueprint;
    use Illuminate\Support\Facades\Schema;

    return new class extends Migration
    {
        public function up(): void
        {
            Schema::create('comment_reports', function (Blueprint $table) {
                $table->ulid('id')->primary();

                // Relasi
                $table->foreignId('reporter_id')->constrained('users')->cascadeOnDelete(); // Siapa yang melapor
                $table->foreignUlid('comment_id')->constrained('comments')->cascadeOnDelete(); // Komentar mana yang dilaporkan (Asumsi nanti kita buat tabel comments)

                // Detail Laporan
                $table->enum('reason', ['spam', 'toxic', 'spoiler', 'other']);
                $table->text('description')->nullable(); // Alasan tambahan

                // Status Penanganan
                $table->enum('status', ['pending', 'resolved', 'rejected', 'escalated'])->default('pending')->index();

                // Rekam Jejak Moderator
                $table->text('moderator_notes')->nullable();
                $table->foreignId('handled_by')->nullable()->constrained('users');

                $table->timestamps();
            });
        }

        public function down(): void
        {
            Schema::dropIfExists('comment_reports');
        }
    };
