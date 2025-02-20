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
    public $chatName;

    public function __construct($messageId, $chatName)
    {
        $this->messageId = $messageId;
        $this->chatName = $chatName;
    }

    public function broadcastOn()
    {
        return new Channel($this->chatName);
    }

    public function broadcastWith()
    {
        return [
            'message_id' => $this->messageId,
            'content' => '(message deleted)',
        ];
    }
}
