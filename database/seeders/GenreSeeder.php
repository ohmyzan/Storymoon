<?php

namespace Database\Seeders;

use App\Models\Genre;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class GenreSeeder extends Seeder
{
  public function run(): void
  {
    $genres = [
      [
        'name'     => 'Fantasi',
        'children' => ['Fantasi Epik', 'Fantasi Urban', 'Xianxia', 'Wuxia', 'Isekai', 'Dark Fantasy'],
      ],
      [
        'name'     => 'Perkotaan',
        'children' => ['Romansa', 'Slice of Life', 'Drama Keluarga', 'Karir & Ambisi', 'Persahabatan'],
      ],
      [
        'name'     => 'Misteri',
        'children' => ['Detektif', 'Thriller', 'Kriminal', 'Konspirasi', 'Psychological'],
      ],
      [
        'name'     => 'Horor',
        'children' => ['Supernatural', 'Survival Horror', 'Gore', 'Paranormal', 'Creepypasta'],
      ],
      [
        'name'     => 'Literatur',
        'children' => ['Sastra Sejarah', 'Puisi Prosa', 'Biografi Fiksi', 'Filosofi', 'Realisme Sosial'],
      ],
    ];

    foreach ($genres as $genreData) {
      $parentSlug = Str::slug($genreData['name']);

      // 1. Buat atau Update Genre Induk
      $parent = Genre::updateOrCreate(
        ['slug' => $parentSlug],
        [
          'parent_id' => null,
          'name'      => $genreData['name'],
        ]
      );

      // 2. Buat atau Update Sub-genre (Anak)
      foreach ($genreData['children'] as $childName) {
        $childSlug = Str::slug($childName);

        Genre::updateOrCreate(
          ['slug' => $childSlug],
          [
            'parent_id' => $parent->id,
            'name'      => $childName,
          ]
        );
      }
    }

    $this->command->info('✅ Genre berhasil disemai dengan aman dan bersih!');
  }
}
