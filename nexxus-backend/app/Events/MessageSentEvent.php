<?php

namespace App\Events;

use App\Models\Message;
use App\Models\Chat;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MessageSentEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $message;

    public function __construct(Message $message)
    {
        $this->message = $message;
        $this->message->load('chat', 'user'); // Preload chat and user
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
                'is_edited' => $this->message->is_edited,
                'is_read' => $this->message->is_read,
                'chat' => [
                    'id' => $this->message->chat->id,
                    'name' => $this->message->chat->name,
                ],
            ]
        ];
    }
}
