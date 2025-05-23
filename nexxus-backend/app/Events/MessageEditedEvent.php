<?php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\Channel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use App\Models\Message;

class MessageEditedEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $message;
    public $chat;

    public function __construct(Message $message, $chat)
    {
        $this->message = $message;
        $this->chat = $chat;
        $this->message->load('user'); // Preload user to avoid lazy loading
    }

    public function broadcastOn()
    {
        return new Channel("{$this->message->chat->name}");
    }

    public function broadcastWith()
    {
        return [
            'message' => [
                'id' => $this->message->id,
                'content' => $this->message->content,
                'user_id' => $this->message->user_id,
                'user' => [
                    'id' => $this->message->user->id,
                    'name' => $this->message->user->name,
                    'profile_photo_url' => $this->message->user->profile_photo_url,
                ],
                'created_at' => $this->message->created_at->toDateTimeString(),
                'is_edited' => $this->message->is_edited, // Añadir este campo
            ]
        ];
    }
}
