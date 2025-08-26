<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Event;

class EventPartialController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function getPartial(string $id)
    {
        $event = Event::findOrFail($id);

        return view('partials.event-card', compact('event'))->render();
    }
}
