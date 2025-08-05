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
     * Registra ou remove uma reação do usuário a um evento.
     *
     * Valida o tipo de reação, controla reações opostas (like/dislike),
     * e impede notificações para usuários sem telefone cadastrado.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int|string  $eventId  ID do evento para o qual a reação será aplicada
     * @return \Illuminate\Http\JsonResponse
     */
    public function react(Request $request, $eventId)
    {
        // Validação do tipo de reação
        $request->validate([
            'reaction_type' => 'required|in:like,dislike,save,notify',
        ]);

        // Usuário autenticado
        $user = auth()->user();

        $reactionType = $request->reaction_type;

        // Verifica se a reação já existe para o mesmo tipo
        $existing = EventUserReaction::where('event_id', $eventId)
                    ->where('user_id', $user->id)
                    ->where('reaction_type', $reactionType)
                    ->first();

        // Remove reação existente, se houver
        if ($existing) {
            $existing->delete();
            return response()->json(['status' => 'removed', 'type' => $reactionType]);
        }

        // Caso reação seja 'notify', verifica se o usuário tem telefone cadastrado
        if ($reactionType === 'notify' && empty($user->phone_number)) {
            return response()->json(['error' => 'phone_required'], 400);
        }

        // Remove reações opostas para manter consistência (like vs dislike)
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

        // Cria nova reação
        EventUserReaction::create([
            'event_id' => $eventId,
            'user_id' => $user->id,
            'reaction_type' => $reactionType,
        ]);

        // Retorna status indicando que a reação foi adicionada
        return response()->json(['status' => 'added', 'type' => $reactionType]);
    }
}
