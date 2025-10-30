<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use App\Models\Event;

class EventUpdatedNotification extends Notification
{
    use Queueable;

    protected $event;
    protected $changedFields;

    public function __construct(Event $event, array $changedFields = [])
    {
        $this->event = $event;
        $this->changedFields = array_filter(
            $changedFields,
            fn($key) => in_array($key, ['event_name', 'event_scheduled_at', 'event_location', 'event_expired_at']),
            ARRAY_FILTER_USE_KEY
        );
    }

    /**
     * Define os canais (meios) de entrega da notificação.
     * Iremos usar o 'database' para notificação interna e 'mail' para e-mail.
     */
    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Evento Atualizado: ' . $this->event->event_name)
            ->markdown('emails.events.updated', [
                'event'         => $this->event,
                'user'          => $notifiable,
                'changedFields' => $this->changedFields,
            ]);
    }

    /**
     * Obtém a representação da notificação para o canal "database".
     * O conteúdo deste array será armazenado no campo 'data' da tabela 'notifications'.
     */
    public function toArray(object $notifiable): array
    {
        $message = "O evento **{$this->event->event_name}** foi atualizado. Veja as mudanças.";

        // Traduz as chaves dos campos alterados para o português para melhor visualização na interface
        $translationMap = [
            'event_name' => 'Nome',
            'event_scheduled_at' => 'Data e Hora',
            'event_location' => 'Local',
            'event_expired_at' => 'Data de Término',
        ];

        $changes = [];
        foreach ($this->changedFields as $field => $newValue) {
            $changes[ $translationMap[$field] ?? $field ] = $newValue;
        }

        return [
            'type' => 'event_updated', // Identificador da notificação
            'event_id' => $this->event->id,
            'event_name' => $this->event->event_name,
            'event_url' => route('events.show', $this->event->id),
            'message' => $message,
            'changes' => $changes, // Campos alterados
        ];
    }
}