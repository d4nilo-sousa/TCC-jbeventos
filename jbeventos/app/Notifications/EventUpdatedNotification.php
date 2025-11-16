<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Messages\BroadcastMessage;
use App\Models\Event;

// Adiciona a interface ShouldQueue para que a notificação vá para a fila, o que é necessário para o Broadcast (WebSockets)
use Illuminate\Contracts\Queue\ShouldQueue;

class EventUpdatedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $event;
    protected $changedFields;
    protected $oldCourses;
    protected $newCourses;

    public function __construct(Event $event, array $changedFields = [], bool $coursesChanged = false, $oldCourses = [], $newCourses = [])
    {
        $this->event = $event;

        // Filtra apenas os campos importantes
        $this->changedFields = array_filter(
            $changedFields,
            fn($key) => in_array($key, ['event_name', 'event_scheduled_at', 'event_location', 'event_expired_at']),
            ARRAY_FILTER_USE_KEY
        );

        // Se cursos mudaram, adiciona flag descritiva
        if ($coursesChanged) {
            $this->changedFields['courses'] = 'Alteração nos cursos associados';
        }

        $this->oldCourses = $oldCourses;
        $this->newCourses = $newCourses;
    }

    /**
     * Define os canais (meios) de entrega da notificação.
     * Adiciona 'broadcast' para o tempo real.
     */
    public function via(object $notifiable): array
    {
        return ['database', 'mail', 'broadcast']; // Adiciona 'broadcast'
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Evento Atualizado: ' . $this->event->event_name)
            ->markdown('emails.events.updated', [
                'event'         => $this->event,
                'user'          => $notifiable,
                'changedFields' => $this->changedFields,
                'oldCourses'    => $this->oldCourses,
                'newCourses'    => $this->newCourses,
            ]);
    }

    /**
     * Obtém a representação da notificação para o canal "broadcast" (WebSockets).
     */
    public function toBroadcast(object $notifiable): BroadcastMessage
    {
        // Reutiliza o payload de dados do toArray() para consistência
        $data = $this->toArray($notifiable);

        return new BroadcastMessage([
            'data' => $data,
            // Adiciona a contagem de não lidas para que o frontend possa atualizar o sino
            'unread_count' => $notifiable->unreadNotifications()->count(),
            'event_id' => $this->event->id,
        ]);
    }

    /**
     * Obtém a representação da notificação para o canal "database".
     * O conteúdo deste array será armazenado no campo 'data' da tabela 'notifications'.
     */
    public function toArray(object $notifiable): array
    {
        $coordinatorName = optional(optional($this->event->eventCoordinator)->userAccount)->name ?? 'Coordenador';

        $message = '<p class="text-[17px] text-gray-700 mb-1 leading-relaxed"> 
                        🔄 O evento <span class="font-semibold">' . e($this->event->event_name) . '</span> que você acompanha foi 
                        atualizado! 
                    </p>
                    <p class="text-sm text-gray-500 mt-1">
                        Atualizado por <span class="font-medium text-gray-700">' . e($coordinatorName) . '</span>.
                    </p>';

        $translationMap = [
            'event_name' => 'Nome',
            'event_scheduled_at' => 'Data e Hora',
            'event_location' => 'Local',
            'event_expired_at' => 'Data de Término',
        ];

        $changes = [];
        foreach ($this->changedFields as $field => $newValue) {
            $changes[$translationMap[$field] ?? $field] = $newValue;
        }

        return [
            'type' => 'event_updated',
            'event_id' => $this->event->id,
            'event_name' => $this->event->event_name,
            'event_url' => route('events.show', $this->event->id),
            'message' => $message,
            'changes' => $changes,
        ];
    }
}
