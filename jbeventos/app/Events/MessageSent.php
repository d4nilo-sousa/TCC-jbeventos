<?php

namespace App\Events;

use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use App\Models\Message;

class MessageSent implements ShouldBroadcast
{
    use Dispatchable, SerializesModels;

    public Message $messageModel;

    /**
     * Cria o evento de mensagem
     */
    // O construtor recebe o objeto da mensagem
    public function __construct(Message $messageModel)
    {
        $this->messageModel = $messageModel;
    }

    /**
     * Canal de broadcast
     */
    public function broadcastOn()
    {
        $ids = [$this->messageModel->sender_id, $this->messageModel->receiver_id];
        sort($ids);

        return [new PresenceChannel('chat.' . implode('.', $ids))];
    }

    /**
     * Dados enviados no broadcast
     */
    public function broadcastWith()
    {
        //Retorna os dados completos da mensagem
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