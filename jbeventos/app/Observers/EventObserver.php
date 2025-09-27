<?php

namespace App\Observers;

use App\Models\Event;
use App\Models\Coordinator;
use App\Models\Course;

class EventObserver
{
    /**
     * Handle the Event "created" event.
     */
    public function created(Event $event): void
    {
        $this->assignCoordinator($event);
    }

    /**
     * Handle the Event "updated" event.
     */
    public function updated(Event $event): void
    {
        // Só age se o coordinator_id virou null
        if ($event->isDirty('coordinator_id') && is_null($event->coordinator_id)) {
            $this->assignCoordinator($event);
        }
    }

    /**
     * Atribui coordenador ao evento com base no tipo
     */
    protected function assignCoordinator(Event $event): void
    {
        // Para eventos do tipo 'course'
        if ($event->event_type === 'course' && $event->course_id) {
            $course = $event->course; // usa a relação do modelo

            if ($course && $course->coordinator_id) {
                $event->updateQuietly([
                    'coordinator_id' => $course->coordinator_id
                ]);
            }
        }

        // Para eventos do tipo 'general'
        if ($event->event_type === 'general') {
            $coordinator = Coordinator::where('coordinator_type', 'general')->first();

            if ($coordinator) {
                $event->updateQuietly([
                    'coordinator_id' => $coordinator->id
                ]);
            }
        }
    }
}
