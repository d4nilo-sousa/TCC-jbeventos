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
        // ✅ Valida apenas tipos realmente usados no seu sistema
        $request->validate([
            'reaction_type' => 'required|in:like,save,notify',
        ]);

        $user = auth()->user();
        $reactionType = $request->reaction_type;

        // ✅ Verifica reação existente
        $existing = EventUserReaction::where('event_id', $eventId)
            ->where('user_id', $user->id)
            ->where('reaction_type', $reactionType)
            ->first();

        if ($existing) {
            $existing->delete();
            return response()->json([
                'status' => 'removed',
                'type'   => $reactionType
            ]);
        }

        // ✅ Remove reações opostas (se futuramente quiser like/dislike)
        if ($reactionType === 'like') {
            EventUserReaction::where('event_id', $eventId)
                ->where('user_id', $user->id)
                ->where('reaction_type', 'dislike')
                ->delete();
        }

        // ✅ Cria nova reação
        EventUserReaction::create([
            'event_id' => $eventId,
            'user_id' => $user->id,
            'reaction_type' => $reactionType,
        ]);

        return response()->json([
            'status' => 'added',
            'type'   => $reactionType
        ]);
    }
}
