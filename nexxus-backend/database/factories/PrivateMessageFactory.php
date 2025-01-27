<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;
use App\Models\PrivateMessage;
use App\Models\User;

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
        $sender = User::factory()->create();
        $receiver = User::factory()->create();

        return [
            'id_sender' => $sender->id,
            'id_receiver' => $receiver->id,
            'content' => $this->faker->sentence,
            'send_date' => Carbon::now(),
            'read_status' => $this->faker->boolean,
        ];
    }
}
