<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Event;
use App\Models\EventUserReaction;
use App\Models\User;

class EventReactionController extends Controller
{
    public function react(Request $request, $eventId)
    {
        $request->validate([
            'reaction_type' => 'required|in:like,save,notify',
        ]);

        $user = auth()->user();
        $reactionType = $request->reaction_type;

        $existing = EventUserReaction::where('event_id', $eventId)
            ->where('user_id', $user->id)
            ->where('reaction_type', $reactionType)
            ->first();

        if ($existing) {
            $existing->delete();

            // NOVO: contar após remover
            $count = EventUserReaction::where('event_id', $eventId)
                ->where('reaction_type', $reactionType)
                ->count();

            return response()->json([
                'status' => 'removed',
                'type'   => $reactionType,
                'count'  => $count
            ]);
        }

        // Se futuramente quiser evitar colisão com outros tipos
        if ($reactionType === 'like') {
            EventUserReaction::where('event_id', $eventId)
                ->where('user_id', $user->id)
                ->where('reaction_type', 'dislike')
                ->delete();
        }

        EventUserReaction::create([
            'event_id' => $eventId,
            'user_id' => $user->id,
            'reaction_type' => $reactionType,
        ]);

        // NOVO: contar após adicionar
        $count = EventUserReaction::where('event_id', $eventId)
            ->where('reaction_type', $reactionType)
            ->count();

        return response()->json([
            'status' => 'added',
            'type'   => $reactionType,
            'count'  => $count
        ]);
    }
}
