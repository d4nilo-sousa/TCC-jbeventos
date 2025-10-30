<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use App\Models\Event;
use Illuminate\Contracts\Queue\ShouldQueue;

class NewEventNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $event;

    public function __construct(Event $event)
    {
        $this->event = $event;
    }

    /**
     * Define os canais de envio.
     */
    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
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
     * Obtém a representação da notificação para o canal "database".
     * O conteúdo deste array será armazenado no campo 'data' da tabela 'notifications'.
     */
    public function toArray(object $notifiable): array
    {
        $coordinatorName = optional(optional($this->event->eventCoordinator)->userAccount)->name ?? 'Coordenador';
        
        $message = "🎉 **{$coordinatorName}** publicou um novo evento que pode te interessar: **{$this->event->event_name}**!";
        
        return [
            'type' => 'new_event', // Identificador da notificação
            'event_id' => $this->event->id,
            'event_name' => $this->event->event_name,
            'event_url' => route('events.show', $this->event->id),
            'message' => $message,
            'event_scheduled_at' => optional($this->event->event_scheduled_at)->format('d/m H:i'),
        ];
    }
}