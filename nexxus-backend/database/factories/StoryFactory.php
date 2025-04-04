<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Story>
 */
class StoryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $randomId = rand(1, 500);
        return [
            'description' => $this->faker->text,
            'publish_date' => $this->faker->date(),
            'id_user' => $this->faker->numberBetween(1, 10),
            'file_path' => "https://picsum.photos/id/{$randomId}/1920/1080",
        ];
    }
}
