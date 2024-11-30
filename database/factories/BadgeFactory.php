<?php

namespace Database\Factories;

use App\Models\Badge;
use App\Models\BadgeImage;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Badge>
 */
class BadgeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->word(),
            'description' => fake()->paragraph(),
            'type' => fake()->mimeType(),
        ];
    }

    public function configure()
    {
        return $this->afterCreating(function (Badge $badge) {
            BadgeImage::factory()
            ->count(5)
            ->state([
                'badge_id' => $badge->id,
            ])
            ->create();
        });
    }
}
