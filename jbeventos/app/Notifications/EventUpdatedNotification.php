<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\Event;
use Carbon\Carbon;

class EventUpdatedNotification extends Notification
{
    use Queueable;

    protected $event;
    protected $changedFields; // Lista dos campos alterados

    public function __construct(Event $event, array $changedFields = [])
    {
        $this->event = $event;
        $this->changedFields = $changedFields;
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $startDate = Carbon::parse($this->event->event_scheduled_at);

        $message = (new MailMessage)
            ->subject('Atualização no evento: ' . $this->event->event_name)
            ->greeting('Olá, ' . $notifiable->name . '!')
            ->line('O evento que você está seguindo foi atualizado.')
            ->line('**' . $this->event->event_name . '**')
            ->line($this->event->event_description);

        if (!empty($this->changedFields)) { // Se houver campos alterados
            $message->line('Alterações recentes:'); // Adiciona uma linha
            foreach ($this->changedFields as $field => $value) { // Para cada campo alterado 
                $message->line("• **{$field}**: {$value}"); // Adiciona uma linha com o campo e o novo valor
            }
        }

        $message->action('Ver detalhes do evento', route('events.show', $this->event->id))
                ->line('Fique atento para não perder nada!');

        return $message;
    }

    public function toArray(object $notifiable): array
    {
        return [
            'event_id' => $this->event->id,
            'changed_fields' => $this->changedFields,
        ];
    }
}
