<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\NewsImages>
 */
class NewsImagesFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'ext' => fake()->fileExtension(),
            'fullpath' => fake()->filePath(),
            'user_id' => null,
            'news_id' => null,
        ];
    }
}
