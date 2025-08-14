<?php

namespace App\Events;

use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use App\Models\User;

class MessageSent implements ShouldBroadcast
{
    use Dispatchable, SerializesModels;

    public $user;
    public $message;
    public $receiverId;

    public function __construct(User $user, string $message, int $receiverId)
    {
        $this->user = $user;
        $this->message = $message;
        $this->receiverId = $receiverId;
    }

    public function broadcastOn()
    {
        $ids = [$this->user->id, $this->receiverId];
        sort($ids);
        return new PrivateChannel('chat.' . implode('.', $ids));
    }
}
