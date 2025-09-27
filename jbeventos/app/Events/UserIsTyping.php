<?php

namespace App\Events;

use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class UserIsTyping implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public int $user_id;
    public int $other_user_id;
    public bool $isTyping;

    public function __construct(int $user_id, int $other_user_id, bool $isTyping)
    {
        $this->user_id = $user_id;
        $this->other_user_id = $other_user_id;
        $this->isTyping = $isTyping;
    }

    public function broadcastOn()
    {
        $ids = [auth()->id(), $this->other_user_id];
        sort($ids);
        return new PresenceChannel('chat.' . implode('.', $ids));
    }
}