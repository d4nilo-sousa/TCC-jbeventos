<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Event;
use App\Models\EventUserReaction;

class EventReactionController extends Controller
{
    /**
    * Método responsável por registrar ou remover uma reação de um usuário a um evento.
    */
    public function react(Request $request, $eventId)
    {
        // Valida o tipo de reação enviada na requisição
        $request->validate([
            'reaction_type' => 'required|in:like,dislike,save,notify',
        ]);

        // Pega o usuário autenticado
        $user = auth()->user();

        // Armazena o tipo de reação
        $reactionType = $request->reaction_type;

        // Verifica se já existe uma reação desse tipo para o mesmo evento e usuário
        $reaction = EventUserReaction::where('event_id', $eventId)
                    ->where('user_id', $user->id)
                    ->where('reaction_type', $reactionType)
                    ->first();

        if ($reaction) {
            // Se já existir, remove (funciona como um toggle)
            $reaction->delete();

            // Retorna resposta HTTP 204 (sem conteúdo)
            return response()->noContent();
        }

        // Regras para garantir que não haja like e dislike ao mesmo tempo

        if ($reactionType === 'like') {
            // Se o usuário reagiu com dislike antes, remove
            EventUserReaction::where('event_id', $eventId)
                ->where('user_id', $user->id)
                ->where('reaction_type', 'dislike')
                ->delete();
        } elseif ($reactionType === 'dislike') {
            // Se o usuário reagiu com like antes, remove
            EventUserReaction::where('event_id', $eventId)
                ->where('user_id', $user->id)
                ->where('reaction_type', 'like')
                ->delete();
        }

        // Cria nova reação no banco
        EventUserReaction::create([
            'event_id' => $eventId,
            'user_id' => $user->id,
            'reaction_type' => $reactionType,
        ]);

        // Retorna resposta HTTP 204 (sem conteúdo)
        return response()->noContent();
    }
}
