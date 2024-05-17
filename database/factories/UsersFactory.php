<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Users;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Users>
 */
class UsersFactory extends Factory
{
    protected $model = Users::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            "name"=> $this->faker->name,
            "lastname"=> $this->faker->lastName,
            "username"=> $this->faker->userName,
            "email"=> $this->faker->safeEmail,
            "password"=> $this->faker->password,
            "blocked"=> $this->faker->boolean,
            "warning_count"=> random_int(0, 10),
            "last_login"=> $this->faker->date,
            "remember_token"=> $this->faker->iosMobileToken,
        ];
    }
}
