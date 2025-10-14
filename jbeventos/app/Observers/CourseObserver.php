<?php

namespace App\Observers;

use App\Models\Course;
use App\Models\Event;

class CourseObserver
{
    public function updated(Course $course): void
    {
        // Se o coordinator_id do curso mudou
        if ($course->isDirty('coordinator_id')) {

            $oldCoordinatorId = $course->getOriginal('coordinator_id');

            // Busca todos os eventos relacionados ao curso onde o coordenador antigo ainda está
            $events = $course->events()
                ->where('coordinator_id', $oldCoordinatorId)
                ->get();

            foreach ($events as $event) {
                $event->update(['coordinator_id' => null]);
            }
        }
    }
}
