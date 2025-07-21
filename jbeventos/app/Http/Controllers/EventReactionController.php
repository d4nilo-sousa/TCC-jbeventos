<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Event;
use App\Models\EventUserReaction;
use App\Models\User;

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

        // Obtém o usuário autenticado
        $user = auth()->user();

        // Captura o tipo de reação do request
        $reactionType = $request->reaction_type;

        // Verifica se já existe uma reação do mesmo tipo desse usuário para esse evento
        $existing = EventUserReaction::where('event_id', $eventId)
                    ->where('user_id', $user->id)
                    ->where('reaction_type', $reactionType)
                    ->first();

        // Se existir, remove a reação e retorna status 'removed'
        if ($existing) {
            $existing->delete();
            return response()->json(['status' => 'removed', 'type' => $reactionType]);
        }

        // Se a reação for 'notify' e o usuário não tiver telefone cadastrado, retorna erro
        if ($reactionType === 'notify' && empty($user->phone_number)) {
            return response()->json(['error' => 'phone_required'], 400);
        }

        // Remove reações opostas quando for like/dislike para manter consistência
        if ($reactionType === 'like') {
            EventUserReaction::where('event_id', $eventId)
                ->where('user_id', $user->id)
                ->where('reaction_type', 'dislike')
                ->delete();
        } elseif ($reactionType === 'dislike') {
            EventUserReaction::where('event_id', $eventId)
                ->where('user_id', $user->id)
                ->where('reaction_type', 'like')
                ->delete();
        }

        // Cria a nova reação no banco
        EventUserReaction::create([
            'event_id' => $eventId,
            'user_id' => $user->id,
            'reaction_type' => $reactionType,
        ]);

        // Retorna status 'added' informando que a reação foi registrada
        return response()->json(['status' => 'added', 'type' => $reactionType]);
    }
}
