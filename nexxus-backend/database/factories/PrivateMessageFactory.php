<?php

namespace Database\Factories;

use App\Models\PrivateMessage;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\PrivateMessage>
 */
class PrivateMessageFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'id_sender' => $this->faker->numberBetween(1, 100),
            'id_receiver' => $this->faker->numberBetween(1, 100),
            'content' => $this->faker->sentence,
            'send_date' => Carbon::now(),
            'read_status' => $this->faker->boolean,
        ];
    }
}
