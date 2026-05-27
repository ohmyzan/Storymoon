<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            UserSeeder::class,  // Menggabungkan Role & User Testing Anda
            GenreSeeder::class, // Menggunakan GenreSeeder bawaan Anda
            NovelSeeder::class, // Menggunakan pabrik untuk mencetak novel lengkap
        ]);
    }
}
