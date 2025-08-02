<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\Event;
use Carbon\Carbon;

class NewEventNotification extends Notification
{
    use Queueable;

    protected $event;

    /**
     * Create a new notification instance.
     */
    public function __construct(Event $event)
    {
        $this->event = $event;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $startDate = Carbon::parse($this->event->event_scheduled_at);
        $diff = Carbon::now()->diffForHumans($startDate, [
            'parts' => 2,
            'short' => true,
            'syntax' => Carbon::DIFF_RELATIVE_TO_NOW,
        ]);

        return (new MailMessage)
            ->subject('Novo Evento: ' . $this->event->event_name)
            ->greeting('Olá, ' . $notifiable->name . '!')
            ->line('Um novo evento foi adicionado ao curso que você segue:')
            ->line('**' . $this->event->event_name . '**')
            ->line($this->event->event_description)
            ->line('⏳ Começa ' . $diff . '.')
            ->action('Ver detalhes do evento', route('events.show', $this->event->id))
            ->line('Fique ligado para não perder!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'event_id' => $this->event->id,
            'event_name' => $this->event->event_name,
            'course_id' => $this->event->course_id,
        ];
    }
}
