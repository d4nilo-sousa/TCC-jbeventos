<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Event;
use App\Models\Course;

class PartialController extends Controller
{
    public function eventPartial(string $id)
    {
        $event = Event::findOrFail($id);
        
        return view('partials.events.event-card', compact('event'))->render();
    }

    public function coursePartial(string $id)
    {
        $course = Course::findOrFail($id);

        return view('partials.courses.course-card', compact('course'))->render();
    }
}
