<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\PrivateMessage;

class Hivemind_PrivateMessage extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic feature test to send a private message.
     */

     public function test_send_private_message(): void
     {
        $sender = User::factory()->create();
        $receiver = User::factory()->create();

         // Arrange: Prepare private message data
         $privateMessageData = [
             'id_sender' => $sender->id,
             'id_receiver' => $receiver->id,
             'content' => 'Hello, this is a test. BTW, how are you?',
             'send_date' => now(),
             'read_status' => false,
         ];
 
         // Act: Send a private message
         $privateMessage = PrivateMessage::create($privateMessageData);
 
         // Assert: Check if the private message was sent successfully
         $this->assertDatabaseHas('privatemessage', [
             'content' => 'Hello, this is a test. BTW, how are you?',
         ]);
 
         $this->assertEquals('Hello, this is a test. BTW, how are you?', $privateMessage->content);
         $this->assertEquals('Unread', $privateMessage->read_status);
     }
}
