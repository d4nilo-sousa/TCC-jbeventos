<?php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use App\Models\Message;

class MessageEdited implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $messageModel;

    public function __construct(Message $messageModel)
    {
        $this->messageModel = $messageModel;
    }

    public function broadcastOn(): array
    {
        $ids = [$this->messageModel->sender_id, $this->messageModel->receiver_id];
        sort($ids);
       
        return [new PresenceChannel('chat.' . implode('.', $ids))];
    }

    public function broadcastWith(){
        return [
            'id' => $this->messageModel->id,
            'message' => $this->messageModel->message,
            'is_edited' => true, 
        ];
    }
}