<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class ChapterFactory extends Factory
{
    public function definition(): array
    {
        $title = "Bab " . $this->faker->numberBetween(1, 100) . ": " . $this->faker->words(3, true);

        return [
            'chapter_number' => $this->faker->numberBetween(1, 100), // 🌟 FIX: Nilai cadangan
            'title' => $title,
            'slug' => Str::slug($title) . '-' . Str::random(5),
            'content' => $this->faker->paragraphs(10, true),
            'word_count' => $this->faker->numberBetween(1000, 2500),
            'status' => 'published',
            'is_premium' => $this->faker->boolean(20),
            'coin_price' => 15,
        ];
    }
}
