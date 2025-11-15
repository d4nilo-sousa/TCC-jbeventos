<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use App\Models\Event;

class EventDeletedNotification extends Notification
{
    use Queueable;

    protected $event;

    public function __construct(Event $event)
    {
        $this->event = $event;
    }

    public function via(object $notifiable): array
    {
        // Envia tanto por e-mail quanto para o banco (para aparecer no sino)
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Evento Cancelado: ' . $this->event->event_name)
            ->markdown('emails.events.deleted', [
                'event' => $this->event,
                'user'  => $notifiable,
            ]);
    }

    public function toArray(object $notifiable): array
    {
        $coordinatorName = optional(optional($this->event->eventCoordinator)->userAccount)->name ?? 'Coordenador';

        // Mensagem com estilo visual semelhante à de atualização
        $message = '<p class="text-[17px] text-gray-700 mb-1 leading-relaxed"> 
                        ❌ O evento <span class="font-semibold">' . e($this->event->event_name) . '</span> que você acompanha foi 
                        cancelado/excluído! 
                    </p>
                    <p class="text-sm text-gray-500 mt-1">
                        Cancelado/Excluído por <span class="font-medium text-gray-700">' . e($coordinatorName) . '</span>.
                    </p>';

        return [
            'type'       => 'deleted',
            'event_id'   => $this->event->id,
            'event_name' => $this->event->event_name,
            'message'    => $message,
            'event_url'  => route('events.index'), 
        ];
    }
}
