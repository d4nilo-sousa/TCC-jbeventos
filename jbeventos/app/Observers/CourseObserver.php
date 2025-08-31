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

            // Todos os eventos desse curso que tinham o coordenador antigo ficam null
            Event::where('course_id', $course->id)
                ->where('coordinator_id', $course->getOriginal('coordinator_id'))
                ->update(['coordinator_id' => null]);
        }
    }
}
