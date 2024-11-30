<?php

namespace Database\Factories;

use App\Models\News;
use App\Models\User;
use App\Enums\UserRoles;
use App\Models\Badge;
use App\Models\NewsCategories;
use App\Models\Reaction;
use App\Models\Notification;
use App\Models\Warning;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name,
            'lastname' => fake()->lastName(),
            'username' => fake()->userName(),
            'email' => fake()->email,
            'password' => Hash::make('Asf123123'),
            'blocked' => false,
            'remember_token' => null,
            'role_id' => UserRoles::Writer->value,
        ];
    }

    public function configure()
    {
        return $this->afterCreating(function (User $user) {
            $adminId = '9d7ca52c-440e-405e-816b-13361167368e';

            News::factory()
                ->count(5)
                ->state([
                    'user_id' => $user->id,
                    'approved_by' => $adminId,
                    'category_id' => NewsCategories::factory()->create()->id
                ])
                ->create();

            Badge::factory()
            ->count(5)
            ->create();

            $badges = Badge::inRandomOrder()->take(5)->pluck('id');
            $user->badges()->attach($badges);

            Notification::factory()
                ->count(5)
                ->state([
                    'user_id' => $user->id
                ])
                ->create();

            Reaction::factory()
                ->count(5)
                ->state([
                    'user_id' => $user->id
                ])
                ->create();

            Warning::factory()
                ->count(5)
                ->state([
                    'user_id' => $user->id
                ])
                ->create();
        });
    }
}