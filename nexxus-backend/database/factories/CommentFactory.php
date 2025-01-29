<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Comment>
 */
class CommentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'id_post' => $this->faker->numberBetween(1, 100),
            'id_user' => $this->faker->numberBetween(1, 100),
            'comment_text' => $this->faker->sentence,
            'comment_date' => $this->faker->date(),
        ];
    }
}
