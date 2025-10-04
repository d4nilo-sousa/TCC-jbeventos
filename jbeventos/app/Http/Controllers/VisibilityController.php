<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Event;
use App\Models\Comment;

class VisibilityController extends Controller
{
    /**
     * Alterna a visibilidade de um evento.
     */
    public function updateEvent(Event $event)
    {
        // Inverte o estado de visibilidade do evento
        $event->visible_event = !$event->visible_event;
        $event->save();

        // Define a mensagem de retorno baseada no novo estado
        $message = $event->visible_event 
            ? 'Evento visível com sucesso' 
            : 'Evento oculto com sucesso';

        return redirect()->back()->with('success', $message);
    }
}