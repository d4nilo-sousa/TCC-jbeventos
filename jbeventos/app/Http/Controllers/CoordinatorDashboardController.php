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

        // Inicializa array com 0 para cada mês
        $engagementByMonthArr = [];
        foreach ($months as $m) {
            $engagementByMonthArr[(int)$m->format('n')] = 0;
        }

        if (! $eventIds->isEmpty()) {
            $startDate = $months->first()->copy()->startOfMonth();

            $reactions = EventUserReaction::whereIn('event_id', $eventIds)
                ->where('created_at', '>=', $startDate)
                ->selectRaw('MONTH(created_at) as month, COUNT(*) as total')
                ->groupBy('month')
                ->pluck('total', 'month')
                ->toArray();

            foreach ($reactions as $month => $total) {
                $engagementByMonthArr[(int)$month] = (int) $total;
            }
        }

        // Transforma em Collection preservando a ordem dos meses
        $engagementByMonth = collect();
        foreach ($months as $m) {
            $num = (int)$m->format('n');
            $engagementByMonth->put($num, $engagementByMonthArr[$num] ?? 0);
        }

        // --- Labels e values prontos para o gráfico ---
        $labels = $engagementByMonth->keys()->map(fn($m) => Carbon::create()->month($m)->format('F'))->values();
        $values = $engagementByMonth->values();

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

        return view('coordinator.dashboard', compact(
            'eventsCount',
            'likes',
            'saves',
            'comments',
            'engagementByMonth',
            'labels',
            'values',
            'topEvents'
        ));
    }
}
