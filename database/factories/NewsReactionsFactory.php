<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\NewsReactions>
 */
class NewsReactionsFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'reaction' => fake()->word(),
            'type' => fake()->word(),
            'user_id' => null,
            'news_id' => null
        ];
    }
}
