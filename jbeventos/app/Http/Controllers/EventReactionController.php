<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Event;
use App\Models\EventUserReaction;

class EventReactionController extends Controller
{
    /**
     * Store a newly created resource in storage.
     */
    public function react(Request $request, $eventId)
{
    $request->validate([
        'reaction_type' => 'required|in:like,dislike,save,notify',
    ]);

    $user = auth()->user();

    $reactionType = $request->reaction_type;

    // Busca reação do mesmo tipo
    $reaction = EventUserReaction::where('event_id', $eventId)
                ->where('user_id', $user->id)
                ->where('reaction_type', $reactionType)
                ->first();

    if ($reaction) {
        // Se já tem essa reação, remove (toggle)
        $reaction->delete();

        return response()->noContent(); // HTTP 204
    }

    // Regra de conflito entre like e dislike
    if ($reactionType === 'like') {
        // Se tiver dislike, remove
        EventUserReaction::where('event_id', $eventId)
            ->where('user_id', $user->id)
            ->where('reaction_type', 'dislike')
            ->delete();
    } elseif ($reactionType === 'dislike') {
        // Se tiver like, remove
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

    return response()->noContent(); // HTTP 204
    }
}
