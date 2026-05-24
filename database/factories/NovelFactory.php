<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class NovelFactory extends Factory
{
    public function definition(): array
    {
        $title = $this->faker->sentence(
            nbWords: 4,
            variableNbWords: true
        );

        $status = $this->faker->randomElement([
            'draft',
            'published',
            'frozen',
        ]);

        return [

            /*
            |--------------------------------------------------------------------------
            | Core Identity
            |--------------------------------------------------------------------------
            */
            'title' => $title,

            'slug' => Str::slug($title) . '-' . Str::random(5),

            'synopsis' => $this->faker->paragraphs(3, true),

            /*
            |--------------------------------------------------------------------------
            | Status
            |--------------------------------------------------------------------------
            */
            'status' => $status,

            'publish_status' => $status === 'published'
                ? $this->faker->randomElement([
                    'ongoing',
                    'completed',
                ])
                : null,

            /*
            |--------------------------------------------------------------------------
            | Statistics
            |--------------------------------------------------------------------------
            */
            'views_count' => $this->faker->numberBetween(100, 5000),

            'favorites_count' => $this->faker->numberBetween(0, 1000),

            'total_chapters' => $this->faker->numberBetween(1, 200),

            /*
            |--------------------------------------------------------------------------
            | Rating
            |--------------------------------------------------------------------------
            */
            'rating' => $this->faker->randomFloat(2, 3, 5),

            /*
            |--------------------------------------------------------------------------
            | Publication
            |--------------------------------------------------------------------------
            */
            'published_at' => $status === 'published'
                ? now()
                : null,
        ];
    }
}
