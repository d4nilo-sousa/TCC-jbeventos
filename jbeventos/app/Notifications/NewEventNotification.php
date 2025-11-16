<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Models\Event;

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
        return ['mail', 'database'];
    }

    /**
     * E-mail.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Novo Evento: ' . $this->event->event_name)
            ->view('emails.events.new', [
                'event' => $this->event,
                'user'  => $notifiable,
            ]);
    }

    /**
     * Notificação para o banco de dados.
     */
    public function toArray(object $notifiable): array
    {
        // =====================
        // 1. Pega os cursos do evento
        // =====================
        $eventCourses = $this->event->courses ?? collect();

        // =====================
        // 2. Pega os cursos que o usuário segue
        // =====================
        $followedCourses = $notifiable->followedCourses ?? collect();

        // =====================
        // 3. Descobre se o usuário segue algum curso ligado ao evento
        // =====================
        $matchedCourse = $eventCourses->first(function ($course) use ($followedCourses) {
            return $followedCourses->contains('id', $course->id);
        });

        // =====================
        // 4. Define o nome do curso certo
        // =====================
        $courseName = $matchedCourse
            ? $matchedCourse->course_name
            : (optional($this->event->course)->course_name ?? 'um curso que você segue');

        $coordinatorName = optional(optional($this->event->eventCoordinator)->userAccount)->name ?? 'Coordenador';

        $eventName = e($this->event->event_name);

        // =====================
        // 5. Monta mensagem HTML
        // =====================
        $message = '
            <p class="text-[17px] text-gray-700 mb-1 leading-relaxed">
                🎉 O curso <span class="font-semibold text-indigo-600">' . e($courseName) . '</span>
                que você segue publicou um novo evento:
                <span class="font-semibold">' . $eventName . '</span>!
            </p>
		    <p class="text-sm text-gray-500 mt-1">
                Publicado por <span class="font-medium text-gray-700">' . e($coordinatorName) . '</span>.
            </p>
        ';

        return [
            'type'       => 'new_event',
            'event_id'   => $this->event->id,
            'event_name' => $this->event->event_name,
            'message'    => $message,
            'event_url'  => route('events.show', $this->event->id),
        ];
    }
}
