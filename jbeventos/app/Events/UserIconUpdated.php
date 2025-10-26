<?php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\Channel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;

class UserIconUpdated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets;

    public $userId;
    public $iconUrl;

    public function __construct($userId, $iconUrl)
    {
        $this->userId = $userId;
        $this->iconUrl = $iconUrl;
    }

    public function broadcastOn()
    {
        return new Channel('user-icon.' . $this->userId);
    }

    public function broadcastWith()
    {
        return ['icon_url' => $this->iconUrl];
    }
}
