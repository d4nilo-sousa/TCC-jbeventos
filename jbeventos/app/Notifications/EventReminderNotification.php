<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\Event;
use Carbon\Carbon;

class EventReminderNotification extends Notification
{
    use Queueable;

    protected $event;
    protected $timeBefore; // Ex: '24h' ou '1h'

    public function __construct(Event $event, string $timeBefore)
    {
        $this->event = $event;
        $this->timeBefore = $timeBefore;
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $startDate = Carbon::parse($this->event->event_scheduled_at);
        $diff = Carbon::now()->diffForHumans($startDate, [
            'parts' => 2,
            'short' => true,
            'syntax' => Carbon::DIFF_RELATIVE_TO_NOW,
        ]);

        return (new MailMessage)
            ->subject('Não se esqueça: ' . $this->event->event_name . ' começa em breve!')
            ->greeting('Olá, ' . $notifiable->name . '!')
            ->line('Lembrete: o evento que você segue está quase começando!')
            ->line('**' . $this->event->event_name . '**')
            ->line($this->event->event_description)
            ->line('📅 Data e hora: ' . $startDate->format('d/m/Y H:i'))
            ->line('⏳ Falta ' . $diff . ' para começar.')
            ->action('Ver detalhes do evento', route('events.show', $this->event->id))
            ->line('Esperamos você lá!');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'event_id' => $this->event->id,
            'time_before' => $this->timeBefore,
        ];
    }
}
