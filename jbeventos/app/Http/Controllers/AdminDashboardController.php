<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Event;
use App\Models\EventUserReaction;
use App\Models\User;
use App\Models\Coordinator;
use Carbon\Carbon;

class AdminDashboardController extends Controller
{
    /**
     * Exibe o painel de controle do administrador.
     */
    public function index()
    {
        // 1. Ranking de Coordenadores
        $coordinatorsRanking = Event::select('coordinator_id', \DB::raw('count(*) as events_count'))
            ->whereNotNull('coordinator_id')
            ->groupBy('coordinator_id')
            ->orderByDesc('events_count')
            ->with('eventCoordinator.userAccount')
            ->get();
            
        $topCoordinators = $coordinatorsRanking->take(3);
        $otherCoordinators = $coordinatorsRanking->skip(3);

        // 2. Ranking de Cursos
        $coursesRanking = Event::select('course_id', \DB::raw('count(*) as events_count'))
            ->whereNotNull('course_id')
            ->groupBy('course_id')
            ->orderByDesc('events_count')
            ->with('eventCourse')
            ->get();

        $coursesLabels = $coursesRanking->pluck('eventCourse.course_name');
        $coursesData = $coursesRanking->pluck('events_count');

        // 3. Top Eventos do Mês
        $topEventsOfTheMonth = Event::whereMonth('event_scheduled_at', now()->month)
            ->whereYear('event_scheduled_at', now()->year)
            ->withCount(['reactions as total_interactions'])
            ->orderByDesc('total_interactions')
            ->limit(5)
            ->get();
            
        // 4. Evolução no Tempo (Interações por Mês)
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

        // Montar labels e dados fixos (últimos 6 meses)
        $interactionsLabels = [];
        $interactionsData = [];

        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $key = $date->format('Y-m');
            $label = $date->format('M/Y');

            $interactionsLabels[] = $label;
            $interactionsData[] = $interactionsByMonth[$key]->total_interactions ?? 0;
        }

        // Dados de Resumo
        $eventsCount = Event::count();
        $likesCount = EventUserReaction::where('reaction_type', 'like')->count();
        $commentsCount = EventUserReaction::where('reaction_type', 'comment')->count();
        $savedEventsCount = EventUserReaction::where('reaction_type', 'save')->count();
        
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
            'savedEventsCount'
        ));
    }
}