<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\EventUserReaction;
use App\Models\Comment;
use App\Models\Event;
use Carbon\Carbon;

class UserDashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        // Totais principais
        $savedEventsCount = EventUserReaction::where('user_id', $user->id)
            ->where('reaction_type', 'save')
            ->count();
        $likesCount = EventUserReaction::where('user_id', $user->id)
            ->where('reaction_type', 'like')
            ->count();
        $commentsCount = Comment::where('user_id', $user->id)->count();
        $notifiedEventsCount = EventUserReaction::where('user_id', $user->id)
            ->where('reaction_type', 'notify')
            ->count();

        // Destaque dinâmico
        $now = Carbon::now();
        $startOfThisMonth = $now->copy()->startOfMonth();
        $startOfLastMonth = $now->copy()->subMonth()->startOfMonth();
        $endOfLastMonth = $now->copy()->subMonth()->endOfMonth();

        $interactionsThisMonth = EventUserReaction::where('user_id', $user->id)
            ->where('created_at', '>=', $startOfThisMonth)
            ->count();
        $interactionsLastMonth = EventUserReaction::where('user_id', $user->id)
            ->whereBetween('created_at', [$startOfLastMonth, $endOfLastMonth])
            ->count();

        if ($interactionsThisMonth > $interactionsLastMonth) {
            $dynamicHighlight = 'Você interagiu com mais eventos este mês do que no anterior.';
        } elseif ($interactionsThisMonth < $interactionsLastMonth) {
            $dynamicHighlight = 'Suas interações diminuíram este mês. Que tal explorar novos eventos?';
        } else {
            $dynamicHighlight = 'Seu nível de engajamento se manteve estável este mês.';
        }

        // Distribuição das interações
        $distributionData = [
            'likes' => $likesCount,
            'comments' => $commentsCount,
            'saves' => $savedEventsCount,
        ];

        // Atividade recente (últimos 3 comentários ou reações)
        $recentComments = Comment::where('user_id', $user->id)
            ->with('event')
            ->orderBy('created_at', 'desc')
            ->take(3)
            ->get()
            ->map(function ($comment) {
                return (object) [
                    'message' => 'Você comentou no evento "' . ($comment->event->event_name ?? 'Evento não disponível') . '"',
                    'created_at' => $comment->created_at,
                ];
            });

        $recentReactions = EventUserReaction::where('user_id', $user->id)
            ->with('reactedEvent')
            ->orderBy('created_at', 'desc')
            ->take(3)
            ->get()
            ->map(function ($reaction) {
                $message = '';
                switch ($reaction->reaction_type) {
                    case 'like':
                        $message = 'Você curtiu o evento "' . ($reaction->reactedEvent->event_name ?? 'Evento não disponível') . '"';
                        break;
                    case 'dislike':
                        $message = 'Você não gostou do evento "' . ($reaction->reactedEvent->event_name ?? 'Evento não disponível') . '"';
                        break;
                    case 'save':
                        $message = 'Você salvou o evento "' . ($reaction->reactedEvent->event_name ?? 'Evento não disponível') . '"';
                        break;
                    case 'notify':
                        $message = 'Você ativou a notificação para o evento "' . ($reaction->reactedEvent->event_name ?? 'Evento não disponível') . '"';
                        break;
                }
                return (object) [
                    'message' => $message,
                    'created_at' => $reaction->created_at,
                ];
            });

        $recentActivities = $recentComments->concat($recentReactions)
            ->sortByDesc('created_at')
            ->take(3);

        // Mensagem dinâmica de boas-vindas
        $message = 'Bem-vindo(a) ao seu painel de controle. Aqui você pode acompanhar suas interações e atividade recente nos eventos da nossa escola.';

        $name = $user->name;

        return view('user.dashboard', compact(
            'savedEventsCount',
            'likesCount',
            'commentsCount',
            'notifiedEventsCount',
            'distributionData',
            'dynamicHighlight',
            'recentActivities',
            'name',
            'message'
        ));
    }
}
