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
        // Lista de nomes técnicos para amigáveis
        $fieldNames = [
            'event_name'         => 'Nome',
            'event_description'  => 'Descrição',
            'event_scheduled_at' => 'Data',
            'event_location'     => 'Local',
        ]; // ← Faltava o ponto e vírgula aqui

        $startDate = Carbon::parse($this->event->event_scheduled_at);

        $message = (new MailMessage)
            ->subject('Atualização no evento: ' . $this->event->event_name)
            ->greeting('Olá, ' . $notifiable->name . '!')
            ->line('O evento que você está seguindo foi atualizado.')
            ->line('**' . $this->event->event_name . '**')
            ->line($this->event->event_description);

        if (!empty($this->changedFields)) {
            $message->line('Alterações recentes:');
            foreach ($this->changedFields as $field => $value) {
                // Usa o nome amigável se existir, senão gera automaticamente
                $label = $fieldNames[$field] ?? ucfirst(str_replace('_', ' ', $field));
                $message->line("• **{$label}**: {$value}");
            }
        }

        $message->action('Ver detalhes do evento', route('events.show', $this->event->id))
                ->line('Fique atento para não perder nada!');

        return $message;
    }

    public function toArray(object $notifiable): array
    {
        return [
            'event_id'        => $this->event->id,
            'changed_fields'  => $this->changedFields,
        ];
    }
}
