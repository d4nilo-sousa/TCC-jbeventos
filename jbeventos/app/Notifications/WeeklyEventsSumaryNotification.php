<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Collection;
use Carbon\Carbon;

class WeeklyEventsSummaryNotification extends Notification
{
    use Queueable;

    protected $events;

    public function __construct(Collection $events)
    {
        $this->events = $events;
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

   public function toMail(object $notifiable): MailMessage
    {
        $mail = (new MailMessage)
            ->subject('Confira os eventos da semana')
            ->greeting('Olá, ' . $notifiable->name . '!')
            ->line('Aqui estão os eventos desta semana nos cursos que você segue:');

        foreach ($this->events as $event) {
            $startDate = Carbon::parse($event->event_scheduled_at)->format('d/m/Y H:i');
            $mail->line("**{$event->event_name}** – {$startDate}");
            $mail->line($event->event_description);
            $mail->line("[Ver evento](" . route('events.show', $event->id) . ")");
            $mail->line(''); // linha em branco para espaçamento
    }

        $mail->line('Não perca nenhuma oportunidade!');

        return $mail;
    }

    public function toArray(object $notifiable): array
    {
        return [
            'events_count' => $this->events->count(),
        ];
    }
}
