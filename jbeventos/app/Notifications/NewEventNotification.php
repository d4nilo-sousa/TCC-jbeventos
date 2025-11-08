<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Messages\BroadcastMessage; 
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
        return ['mail']; // Apenas e-mail por enquanto
    }

    /**
     * Constrói o e-mail com o template Blade HTML.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Novo Evento: ' . $this->event->event_name)
            ->view('emails.events.new', [ // ← usamos o template Blade em vez do Markdown
                'event' => $this->event,
                'user'  => $notifiable,
            ]);
    }

    /**
     * Representação em array (opcional — usada para salvar no banco, se desejar).
     */
    public function toArray(object $notifiable): array
    {
        $coordinatorName = optional(optional($this->event->eventCoordinator)->userAccount)->name ?? 'Coordenador';
        
        $message = "🎉 **{$coordinatorName}** publicou um novo evento que pode te interessar: **{$this->event->event_name}**!";
        
        return [
            'event_id'    => $this->event->id,
            'event_name'  => $this->event->event_name,
            'course_id'   => $this->event->course_id,
            'course_name' => optional($this->event->course)->course_name,
        ];
    }
}