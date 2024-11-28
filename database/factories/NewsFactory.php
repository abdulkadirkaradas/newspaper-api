<?php

namespace Database\Factories;

use App\Models\News;
use App\Models\NewsImages;
use App\Models\NewsReactions;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\News>
 */
class NewsFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $randomBool = (bool)random_int(0, 1);
        return [
            'title' => fake()->title(),
            'content' => fake()->paragraph(10),
            'priority' => random_int(1, 3),
            'pinned' => $randomBool,
            'visibility' =>$randomBool,
            'approved' => $randomBool,
            'approved_by' => null,
            'user_id' => null,
            'category_id' => null,
        ];
    }

    public function configure()
    {
        return $this->afterCreating(function (News $post) {
            NewsImages::factory()
            ->count(5)
            ->state([
                'user_id' => $post->user_id,
                'news_id' => $post->id
            ])
            ->create();

            NewsReactions::factory()
            ->count(5)
            ->state([
                'user_id' => $post->user_id,
                'news_id' => $post->id
            ])
            ->create();
        });
    }
}
