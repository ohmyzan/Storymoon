<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Novel;
use App\Models\Chapter;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Jalankan Role Seeder terlebih dahulu
        $this->call(RoleSeeder::class);

        // 2. Buat akun Penulis Utama
        $author = User::factory()->create([
            'name' => 'Heavenly Dao',
            'email' => 'author@storymoon.com',
            'password' => bcrypt('password'), // Password default untuk testing
        ]);

        // Berikan role Author ke user tersebut
        $author->assignRole('Author');

        // 3. Generate 5 Novel untuk Penulis tersebut
        $novels = Novel::factory(5)->create([
            'author_id' => $author->id,
        ]);

        // 4. Generate 20 Chapter untuk masing-masing Novel
        foreach ($novels as $novel) {
            for ($i = 1; $i <= 20; $i++) {
                Chapter::factory()->create([
                    'novel_id' => $novel->id,
                    'chapter_number' => $i, // Langsung masukkan nomor chapter di sini!
                ]);
            }

            // Update total_chapters di tabel novel
            $novel->update(['total_chapters' => 20]);
        }
    }
}
