<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Notification>
 */
class NotificationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $startDate = now();
        $endDate = now()->addDays(15);
        $randomDate = $this->randomDateBetween($startDate, $endDate);
        return [
            'type' => fake()->word(),
            'title' => fake()->sentence(),
            'message' => fake()->paragraph(),
            'is_read' => (bool) rand(0, 1),
            'user_id' => null,
            'created_at' => $randomDate,
            'updated_at' => $randomDate
        ];
    }

    private function randomDateBetween($startDate, $endDate) {
        $startTimestamp = strtotime($startDate);
        $endTimestamp = strtotime($endDate);
        $randomTimestamp = mt_rand($startTimestamp, $endTimestamp);

        return date("Y-m-d H:i:s", $randomTimestamp);
    }
}
