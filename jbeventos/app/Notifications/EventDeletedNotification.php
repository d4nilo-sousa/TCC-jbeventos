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
        return ['mail'];
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
        return [
            'event_id'   => $this->event->id,
            'event_name' => $this->event->event_name,
            'message'    => 'O evento foi cancelado/excluído',
        ];
    }
}
