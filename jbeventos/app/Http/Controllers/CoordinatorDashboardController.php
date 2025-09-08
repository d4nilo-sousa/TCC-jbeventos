<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Event;
use App\Models\EventUserReaction;
use App\Models\Comment;
use Carbon\Carbon;

class CoordinatorDashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $coordinator = $user->coordinator;

        if (! $coordinator) {
            abort(403, 'Perfil de coordenador não encontrado.');
        }

        // IDs dos eventos gerenciados por esse coordenador
        $eventIds = Event::where('coordinator_id', $coordinator->id)->pluck('id');

        // Totais simples
        $eventsCount = $eventIds->count();

        if ($eventIds->isEmpty()) {
            $likes = 0;
            $saves = 0;
            $comments = 0;
        } else {
            $likes = EventUserReaction::whereIn('event_id', $eventIds)
                ->where('reaction_type', 'like')
                ->count();

            $saves = EventUserReaction::whereIn('event_id', $eventIds)
                ->where('reaction_type', 'save')
                ->count();

            $comments = Comment::whereIn('event_id', $eventIds)->count();
        }

        // --- Engajamento por mês (últimos 6 meses) ---
        $now = Carbon::now();
        $months = collect();
        for ($i = 5; $i >= 0; $i--) {
            $months->push($now->copy()->subMonths($i));
        }

        // Inicializa arrays com 0 para cada mês
        $engagementByMonthArr = [];
        $likesByMonthArr = [];
        $savesByMonthArr = [];
        $commentsByMonthArr = [];
        $eventsByMonthArr = [];
        foreach ($months as $m) {
            $monthNum = (int)$m->format('n');
            $engagementByMonthArr[$monthNum] = 0;
            $likesByMonthArr[$monthNum] = 0;
            $savesByMonthArr[$monthNum] = 0;
            $commentsByMonthArr[$monthNum] = 0;
            $eventsByMonthArr[$monthNum] = 0;
        }

        if (! $eventIds->isEmpty()) {
            $startDate = $months->first()->copy()->startOfMonth();

            // Interações
            $reactionsByMonth = EventUserReaction::whereIn('event_id', $eventIds)
                ->where('created_at', '>=', $startDate)
                ->selectRaw('MONTH(created_at) as month, reaction_type, COUNT(*) as total')
                ->groupBy('month', 'reaction_type')
                ->get();
            
            foreach ($reactionsByMonth as $reaction) {
                if ($reaction->reaction_type == 'like') {
                    $likesByMonthArr[(int)$reaction->month] = (int) $reaction->total;
                } elseif ($reaction->reaction_type == 'save') {
                    $savesByMonthArr[(int)$reaction->month] = (int) $reaction->total;
                }
                $engagementByMonthArr[(int)$reaction->month] += (int) $reaction->total;
            }

            // Comentários
            $commentsByMonth = Comment::whereIn('event_id', $eventIds)
                ->where('created_at', '>=', $startDate)
                ->selectRaw('MONTH(created_at) as month, COUNT(*) as total')
                ->groupBy('month')
                ->pluck('total', 'month')
                ->toArray();

            foreach ($commentsByMonth as $month => $total) {
                $commentsByMonthArr[(int)$month] = (int) $total;
                $engagementByMonthArr[(int)$month] += (int) $total;
            }
            
            // Eventos Criados
            $eventsByMonth = Event::where('coordinator_id', $coordinator->id)
                ->where('created_at', '>=', $startDate)
                ->selectRaw('MONTH(created_at) as month, COUNT(*) as total')
                ->groupBy('month')
                ->pluck('total', 'month')
                ->toArray();
            
            foreach ($eventsByMonth as $month => $total) {
                $eventsByMonthArr[(int)$month] = (int) $total;
            }
        }
        
        // Transforma em Collection preservando a ordem dos meses
        $likesByMonth = collect();
        $savesByMonth = collect();
        $commentsByMonth = collect();
        $eventsByMonth = collect();
        $labels = collect();
        $values = collect(); 
        
        foreach ($months as $m) {
            $monthNum = (int)$m->format('n');
            $labels->push($m->format('M'));
            $likesByMonth->push($likesByMonthArr[$monthNum] ?? 0);
            $savesByMonth->push($savesByMonthArr[$monthNum] ?? 0);
            $commentsByMonth->push($commentsByMonthArr[$monthNum] ?? 0);
            $eventsByMonth->push($eventsByMonthArr[$monthNum] ?? 0);
            $values->push($engagementByMonthArr[$monthNum] ?? 0);
        }

        // --- Top 3 eventos mais engajados ---
        $topEvents = collect();
        if (! $eventIds->isEmpty()) {
            $topEvents = Event::where('coordinator_id', $coordinator->id)
                ->withCount([
                    'reactions as likes_count' => function ($q) { $q->where('reaction_type', 'like'); },
                    'reactions as saves_count' => function ($q) { $q->where('reaction_type', 'save'); },
                    'eventComments'
                ])
                ->get()
                ->map(function ($e) {
                    $e->total_engagement = ($e->likes_count ?? 0) + ($e->event_comments_count ?? 0) + ($e->saves_count ?? 0);
                    return $e;
                })
                ->sortByDesc('total_engagement')
                ->take(3);
        }

        // Mensagem dinâmica para Coordenador
        $message = 'Bem-vindo(a) ao seu painel de controle. Aqui você pode acompanhar as interações dos usuários nos eventos que você cria/criou!';

        return view('coordinator.dashboard', compact(
            'eventsCount',
            'likes',
            'saves',
            'comments',
            'labels',
            'values',
            'topEvents',
            'likesByMonth',
            'savesByMonth',
            'commentsByMonth',
            'eventsByMonth'
        ))->with([
            'name' => $user->name,
            'message' => $message // Passa a nova variável para a view
        ]);
    }
}
