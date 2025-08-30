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

class EventDeleted implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    // Propriedade pública que será enviada no broadcast
    public $eventId;

    /**
     * Construtor: recebe o ID do evento que foi deletado
     * e guarda para ser transmitido.
     */
    public function __construct($eventId)
    {
        $this->eventId = $eventId;
    }

    /**
     * Define o canal de transmissão do evento.
     * Aqui é o mesmo canal público "events".
     */
    public function broadcastOn()
    {
        return new Channel('events'); // canal
    }

    /**
     * Define o nome do evento transmitido.
     * O frontend vai escutar por "event.deleted".
     */
    public function broadcastAs()
    {
        return 'event.deleted'; // nome do evento
    }
}
