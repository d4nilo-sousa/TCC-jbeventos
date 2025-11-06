<?php

namespace App\Events;

use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use App\Models\Message;

class MessageSent implements ShouldBroadcast
{
    use Dispatchable, SerializesModels;

    public Message $messageModel;

    /**
     * Create a new event instance.
     *
     * @param \App\Models\Message $messageModel
     * @return void
     */
    public function __construct(Message $messageModel)
    {
        $this->messageModel = $messageModel;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array
     */
    public function broadcastOn()
    {
        $ids = [$this->messageModel->sender_id, $this->messageModel->receiver_id];
        sort($ids);

        return [
            new PresenceChannel('chat.' . implode('.', $ids)),
            new PrivateChannel('user.' . $this->messageModel->receiver_id),
        ];
    }

    /**
     * Get the data to broadcast.
     *
     * @return array
     */
    public function broadcastWith()
    {
        return [
            'id' => $this->messageModel->id,
            'sender_id' => $this->messageModel->sender_id,
            'receiver_id' => $this->messageModel->receiver_id,
            'message' => $this->messageModel->message,
            'attachment_path' => $this->messageModel->attachment_path,
            'attachment_mime' => $this->messageModel->attachment_mime,
            'attachment_name' => $this->messageModel->attachment_name,
        ];
    }
}
