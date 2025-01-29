<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Server;
use App\Models\Channel;

class Hivemind_Channel extends TestCase
{
    public function test_create_channel(): void
    {
        $id_server = Server::factory()->create();

        // Arrange: Prepare channel data
        $channelData = [
            'name_channel' => 'general',
            'type_channel' => 'text',
            'id_server' => $id_server->id,
        ];

        // Act: Create a channel
        $channel = Channel::create($channelData);

        // Assert: Check if the channel was created successfully
        $this->assertDatabaseHas('channel', [
            'name_channel' => 'general',
        ]);

        $this->assertEquals('General', $channel->name_channel);
        $this->assertEquals('text', $channel->type_channel);
    }
}