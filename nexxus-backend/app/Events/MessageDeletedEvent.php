<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MessageDeletedEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $messageId;
    public $chat;

    public function __construct($messageId, $chat)
    {
        $this->messageId = $messageId;
        $this->chat = $chat;
    }

    public function broadcastOn()
    {
        return new Channel("{$this->chat->name}"); // Cambiar a {$this->chat->name} sin prefijo
    }

    public function broadcastWith()
    {
        return [
            'message_id' => $this->messageId,
            'chat_name' => $this->chat->name, // Añadir para depuración y consistencia
        ];
    }
}
