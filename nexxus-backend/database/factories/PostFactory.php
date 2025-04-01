<?php

namespace Database\Factories;

use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class PostFactory extends Factory
{
    protected $model = Post::class;

    public function definition(): array
    {
        // Usar una URL directa y única de Lorem Picsum
        $randomId = rand(1, 500);
        return [
            'file_path' => "https://picsum.photos/id/{$randomId}/1920/1080", // URL específica con ID
            'description' => $this->faker->sentence(),
            'publish_date' => $this->faker->dateTimeBetween('-1 month', 'now'),
            'id_user' => User::factory(),
        ];
    }
}
