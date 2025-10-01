<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use App\Models\Event;

class NewEventNotification extends Notification
{
    use Queueable;

    protected $event;

    /**
     * Cria uma nova instância da notificação.
     */
    public function __construct(Event $event)
    {
        $this->event = $event;
    }

    /**
     * Define os canais de envio.
     */
    public function via(object $notifiable): array
    {
        return ['mail']; // apenas email por enquanto
    }

    /**
     * Constrói o email usando Markdown.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Novo Evento: ' . $this->event->event_name)
            ->markdown('emails.events.new', [
                'event' => $this->event,
                'user'  => $notifiable,
            ]);
    }

    /**
     * Representação em array (para database ou outros usos, opcional).
     */
    public function toArray(object $notifiable): array
    {
        return [
            'event_id'   => $this->event->id,
            'event_name' => $this->event->event_name,
            'course_id'  => $this->event->course_id,
            'course_name'=> $this->event->course->course_name,
        ];
    }
}
