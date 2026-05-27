<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Novel;
use App\Models\Chapter;
use App\Models\Genre;
use App\Models\Contract;
use App\Models\EditorChoice;
use App\Models\ChapterPurchase;
use App\Models\Review;
use Illuminate\Database\Seeder;

class NovelSeeder extends Seeder
{
  public function run(): void
  {
    $author = User::where('email', 'author@storymoon.com')->first();
    $editor = User::where('email', 'editor@storymoon.com')->first();
    $reader = User::where('email', 'reader@storymoon.com')->first();
    $genres = Genre::whereNotNull('parent_id')->get(); // Ambil sub-genre

    if (!$author || !$editor || $genres->isEmpty()) {
      $this->command->warn('Pastikan UserSeeder dan GenreSeeder sudah berjalan!');
      return;
    }

    $this->command->info('Membangun dunia dan menyebarkan qi ke dalam database...');

    // Kita cetak 20 Novel menggunakan NovelFactory
    $novels = Novel::factory(20)->create([
      'author_id' => $author->id,
      'editor_id' => $editor->id,
      'status' => 'published',
    ]);

    // Berikan sentuhan spesifik untuk beberapa novel agar UI Beranda menyala
    $specificTitles = [
      'Sekte Pedang Emas',
      'Kultivator Terakhir di Bumi',
      'Sistem Pembangun Sekte',
      'Reinkarnasi Sang Pendekar Emas',
      'Puncak Keabadian'
    ];

    foreach ($novels as $index => $novel) {
      // Timpa judul untuk 5 novel pertama dengan tema spesifik
      if ($index < count($specificTitles)) {
        $novel->update(['title' => $specificTitles[$index]]);
      }

      // 1. Pasang Sub-Genre + Genre Induknya
      $selectedSubGenres = $genres->random(2);

      $genreIds = collect();

      foreach ($selectedSubGenres as $subGenre) {
        // Tambahkan sub-genre
        $genreIds->push($subGenre->id);

        // Tambahkan parent genre jika ada
        if ($subGenre->parent_id) {
          $genreIds->push($subGenre->parent_id);
        }
      }

      // Hindari duplikat ID
      $genreIds = $genreIds->unique();

      $novel->genres()->attach(
        $genreIds,
        ['created_at' => now()]
      );

      // 2. Berikan Kontrak Aktif menggunakan ContractFactory
      // Kita hanya perlu menimpa (override) field yang spesifik untuk novel ini.
      // Sisanya (seperti foto KTP, bank, dll) akan otomatis diisi oleh Factory.
      Contract::factory()->create([
        'novel_id' => $novel->id,
        'author_id' => $author->id,
        'editor_id' => $editor->id,
        'contract_type' => 'exclusive',
        'status' => 'active',
        'signed_at' => $index % 4 === 0 ? now()->subDays(10) : now()->subMonths(2),
      ]);

      // 3. Cetak 5 Chapter berurutan
      $chapters = collect(); // Siapkan wadah koleksi

      for ($c = 1; $c <= 5; $c++) {
        $chapter = Chapter::factory()->create([
          'novel_id' => $novel->id,
          'chapter_number' => $c, // 🌟 FIX: Masukkan nomor urut chapter
          'title' => 'Bab ' . $c . ': ' . fake()->words(3, true), // Agar judulnya rapi
          // Jika database Anda juga butuh 'order', tambahkan baris di bawah ini:
          // 'order' => $c, 
        ]);
        $chapters->push($chapter);
      }

      // Pemicu "Update Terbaru"
      $novel->touch();

      // 4. Pilihan Editor (Feature 10 novel pertama)
      if ($index < 10) { // 🌟 Ubah dari 5 ke 10
        EditorChoice::create([
          'novel_id' => $novel->id,
          'editor_id' => $editor->id,
          'status' => 'approved',
          'editor_notes' => 'Karya ini memiliki alur yang sangat luar biasa dan tata bahasa yang rapi.'
        ]);
      }

      // 5. Pemicu "Terlaris" (Simulasi pembelian pada novel genap)
      if ($index % 2 === 0) {
        // 🌟 FIX: Lakukan perulangan pada setiap bab yang ada di koleksi $chapters
        foreach ($chapters as $chapter) {
          ChapterPurchase::create([
            'novel_id' => $novel->id,
            'chapter_id' => $chapter->id, // Beli bab 1, lalu bab 2, lalu bab 3, dst
            'reader_id' => $reader->id,
            'author_id' => $author->id,
            'coin_price' => 15,
            'author_earning' => 7,
            'platform_earning' => 8,
            'contract_type_snapshot' => 'exclusive',
            'revenue_share_snapshot' => 50,
          ]);
        }
      }

      // 6. Pemicu "Harta Karun" (Rating tinggi, review bagus, views rendah)
      if ($index === 7 || $index === 8) {
        $novel->update(['rating' => 4.9, 'views_count' => 1200]);
        Review::create([
          'novel_id' => $novel->id,
          'user_id' => $reader->id,
          'rating' => 5,
          'content' => 'Karya kultivasi yang sangat solid! Pengembangan karakternya luar biasa dan alurnya tidak pasaran.'
        ]);
      }
    }

    $this->command->info('✅ 20 Mahakarya berhasil dilahirkan ke dunia Storymoon!');
  }
}
