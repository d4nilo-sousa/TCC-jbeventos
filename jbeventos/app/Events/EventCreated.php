<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use App\Models\Event;

class EventCreated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    // A instância do evento (modelo) que será transmitida
    public $event;

    /**
     * Construtor: recebe um objeto Event e guarda na propriedade pública
     * para ser incluído automaticamente no broadcast.
     */
    public function __construct(Event $event)
    {
        $this->event = $event;
    }

    /**
     * Define em qual canal o evento será transmitido.
     * Aqui é um canal público chamado "events".
     */
    public function broadcastOn()
    {
        return new Channel('events');
    }

    /**
     * Define o nome do evento transmitido.
     * O front-end vai "escutar" esse nome.
     */
    public function broadcastAs()
    {
        return 'event.created';
    }
}
