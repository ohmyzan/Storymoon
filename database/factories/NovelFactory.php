<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class NovelFactory extends Factory
{
    public function definition(): array
    {
        $title = $this->faker->sentence(nbWords: 4, variableNbWords: true);

        return [
            'title' => $title,
            'slug' => Str::slug($title) . '-' . Str::random(5),
            'synopsis' => $this->faker->paragraphs(3, true),
            'status' => $this->faker->randomElement(['bersambung', 'tamat']),
            'views_count' => $this->faker->numberBetween(100, 5000),
            'rating' => $this->faker->randomFloat(2, 3, 5), // Rating 3.00 - 5.00
        ];
    }
}
