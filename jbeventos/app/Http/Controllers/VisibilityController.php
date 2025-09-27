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

    /**
     * Alterna a visibilidade de um comentário.
     */
    public function updateComment(Comment $comment)
    {
        // Inverte o estado de visibilidade do comentário
        $comment->visible_comment = !$comment->visible_comment;
        $comment->save();

        // Se for um comentário principal (sem parent_id), aplica a visibilidade para todas as respostas
        if (is_null($comment->parent_id)) {
            Comment::where('parent_id', $comment->id)
                ->update(['visible_comment' => $comment->visible_comment]);
        }

        // Define a mensagem de retorno baseada no novo estado
        $message = $comment->visible_comment 
            ? 'Comentário visível com sucesso' 
            : 'Comentário oculto com sucesso';

        return redirect()->back()->with('success', $message);
    }
}
