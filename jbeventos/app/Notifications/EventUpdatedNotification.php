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
    protected $oldCourses;
    protected $newCourses;

    public function __construct(Event $event, array $changedFields = [], bool $coursesChanged = false, $oldCourses = [], $newCourses = [])
    {
        $this->event = $event;

        // Filtra apenas os campos importantes
        $this->changedFields = array_filter(
            $changedFields,
            fn($key) => in_array($key, ['event_name', 'event_scheduled_at', 'event_location']),
            ARRAY_FILTER_USE_KEY
        );

        // Se cursos mudaram, adiciona flag descritiva
        if ($coursesChanged) {
            $this->changedFields['courses'] = 'Alteração nos cursos associados';
        }

        $this->oldCourses = $oldCourses;
        $this->newCourses = $newCourses;
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
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

    public function toArray(object $notifiable): array
    {
        return [
            'event_id'       => $this->event->id,
            'event_name'     => $this->event->event_name,
            'changed_fields' => $this->changedFields,
        ];
    }
}
