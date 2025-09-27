<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Event;
use App\Models\EventUserReaction;
use App\Models\User;
use App\Models\Coordinator;
use App\Models\Post; // NOVO
use App\Models\Reply; // NOVO
use Carbon\Carbon;

class AdminDashboardController extends Controller
{
    public function index()
    {
        // Dados de Resumo
        $eventsCount = Event::count();
        $likesCount = EventUserReaction::where('reaction_type', 'like')->count();
        $commentsCount = EventUserReaction::where('reaction_type', 'comment')->count();
        $savedEventsCount = EventUserReaction::where('reaction_type', 'save')->count();
        $postsCount = Post::count(); // NOVO: Total de Posts

        // --- Calcular tendências mês a mês ---
        $prevMonth = now()->subMonth();

        $eventsPrev = Event::whereMonth('created_at', $prevMonth->month)
                            ->whereYear('created_at', $prevMonth->year)
                            ->count();

        $likesPrev = EventUserReaction::where('reaction_type', 'like')
                            ->whereMonth('created_at', $prevMonth->month)
                            ->whereYear('created_at', $prevMonth->year)
                            ->count();

        $commentsPrev = EventUserReaction::where('reaction_type', 'comment')
                            ->whereMonth('created_at', $prevMonth->month)
                            ->whereYear('created_at', $prevMonth->year)
                            ->count();

        $savedPrev = EventUserReaction::where('reaction_type', 'save')
                            ->whereMonth('created_at', $prevMonth->month)
                            ->whereYear('created_at', $prevMonth->year)
                            ->count();
        
        $postsPrev = Post::whereMonth('created_at', $prevMonth->month) // NOVO: Posts do mês anterior
                            ->whereYear('created_at', $prevMonth->year)
                            ->count();

        // Função helper para calcular % de variação
        $calcTrend = fn($current, $previous) => $previous == 0 ? 100 : round((($current - $previous) / $previous) * 100);

        $eventsTrend = $calcTrend($eventsCount, $eventsPrev);
        $likesTrend = $calcTrend($likesCount, $likesPrev);
        $commentsTrend = $calcTrend($commentsCount, $commentsPrev);
        $savedEventsTrend = $calcTrend($savedEventsCount, $savedPrev);
        $postsTrend = $calcTrend($postsCount, $postsPrev); // NOVO: Tendência de Posts

        // Restante do controller (rankings, top eventos, gráficos)
        $coordinatorsRanking = Event::select('coordinator_id', \DB::raw('count(*) as events_count'))
            ->whereNotNull('coordinator_id')
            ->groupBy('coordinator_id')
            ->orderByDesc('events_count')
            ->with('eventCoordinator.userAccount')
            ->get();

        $topCoordinators = $coordinatorsRanking->take(3);
        $otherCoordinators = $coordinatorsRanking->skip(3);

        $coursesRanking = Event::select('course_id', \DB::raw('count(*) as events_count'))
            ->whereNotNull('course_id')
            ->groupBy('course_id')
            ->orderByDesc('events_count')
            ->with('eventCourse')
            ->get();

        $coursesLabels = $coursesRanking->pluck('eventCourse.course_name');
        $coursesData = $coursesRanking->pluck('events_count');

        $topEventsOfTheMonth = Event::whereMonth('event_scheduled_at', now()->month)
            ->whereYear('event_scheduled_at', now()->year)
            ->withCount(['reactions as total_interactions'])
            ->orderByDesc('total_interactions')
            ->limit(5)
            ->get();

        $interactionsByMonth = EventUserReaction::select(
                \DB::raw('YEAR(created_at) as year'),
                \DB::raw('MONTH(created_at) as month'),
                \DB::raw('count(*) as total_interactions')
            )
            ->where('created_at', '>=', now()->subMonths(5)->startOfMonth())
            ->groupBy('year', 'month')
            ->orderBy('year', 'asc')
            ->orderBy('month', 'asc')
            ->get()
            ->keyBy(function($item) {
                return $item->year . '-' . str_pad($item->month, 2, '0', STR_PAD_LEFT);
            });

        $interactionsLabels = [];
        $interactionsData = [];
        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $key = $date->format('Y-m');
            $label = $date->format('M/Y');
            $interactionsLabels[] = $label;
            $interactionsData[] = $interactionsByMonth[$key]->total_interactions ?? 0;
        }

        // NOVO: Coletar Posts e Respostas por mês (últimos 6 meses)
        $postsByMonth = Post::select(
            \DB::raw('YEAR(created_at) as year'),
            \DB::raw('MONTH(created_at) as month'),
            \DB::raw('count(*) as total_posts')
        )
        ->where('created_at', '>=', now()->subMonths(5)->startOfMonth())
        ->groupBy('year', 'month')
        ->orderBy('year', 'asc')
        ->orderBy('month', 'asc')
        ->get()
        ->keyBy(fn($item) => $item->year . '-' . str_pad($item->month, 2, '0', STR_PAD_LEFT));

        $repliesByMonth = Reply::select(
            \DB::raw('YEAR(created_at) as year'),
            \DB::raw('MONTH(created_at) as month'),
            \DB::raw('count(*) as total_replies')
        )
        ->where('created_at', '>=', now()->subMonths(5)->startOfMonth())
        ->groupBy('year', 'month')
        ->orderBy('year', 'asc')
        ->orderBy('month', 'asc')
        ->get()
        ->keyBy(fn($item) => $item->year . '-' . str_pad($item->month, 2, '0', STR_PAD_LEFT));

        $postInteractionsLabels = [];
        $postsData = [];
        $repliesData = [];

        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $key = $date->format('Y-m');
            $label = $date->format('M/Y');
            
            $postInteractionsLabels[] = $label;
            $postsData[] = $postsByMonth[$key]->total_posts ?? 0;
            $repliesData[] = $repliesByMonth[$key]->total_replies ?? 0;
        }

        $user = auth()->user();
        $message = 'Bem-vindo(a) ao seu painel de controle. Aqui você pode acompanhar o total de interações do sistema, principais eventos, atividades recentes dos coordenadores/cursos/ eventos da nossa escola.';

        return view('admin.dashboard', compact(
            'topCoordinators',
            'otherCoordinators',
            'coursesLabels',
            'coursesData',
            'topEventsOfTheMonth',
            'interactionsLabels',
            'interactionsData',
            'eventsCount',
            'likesCount',
            'commentsCount',
            'savedEventsCount',
            'postsCount', // NOVO
            'eventsTrend',
            'likesTrend',
            'commentsTrend',
            'savedEventsTrend',
            'postsTrend', // NOVO
            'postInteractionsLabels', // NOVO
            'postsData', // NOVO
            'repliesData' // NOVO
        ))->with([
            'name' => $user->name,
            'message' => $message
        ]);
    }
}