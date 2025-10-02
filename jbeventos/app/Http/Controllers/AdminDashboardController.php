<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Event;
use App\Models\EventUserReaction;
use App\Models\User;
use App\Models\Post; 
use App\Models\Reply; 
use App\Models\Course;
use App\Models\Comment; 
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;

class AdminDashboardController extends Controller
{
    /**
     * Coleta os dados do dashboard, fixo para últimos 6 meses.
     */
    private function getDashboardData(): array
    {
        $now = now();

        // Período fixo: últimos 6 meses
        $start = $now->copy()->subMonths(5)->startOfMonth();
        $end = $now->copy()->endOfDay();

        // Período anterior (para tendências)
        $prevMonth = $now->copy()->subMonth();

        // Totais no período
        $eventsCount = Event::whereBetween('created_at', [$start, $end])->count();
        $postsCount = Post::whereBetween('created_at', [$start, $end])->count();
        $usersCount = User::whereBetween('created_at', [$start, $end])->count();
        $coursesCount = Course::count(); 
        
        $likesCount = EventUserReaction::where('reaction_type', 'like')->whereBetween('created_at', [$start, $end])->count();
        $commentsCount = EventUserReaction::where('reaction_type', 'comment')->whereBetween('created_at', [$start, $end])->count();
        $savedEventsCount = EventUserReaction::where('reaction_type', 'save')->whereBetween('created_at', [$start, $end])->count();
        
        $eventCommentsCount = Comment::whereBetween('created_at', [$start, $end])->count();

        // Tendências mês anterior
        $eventsPrev = Event::whereMonth('created_at', $prevMonth->month)->whereYear('created_at', $prevMonth->year)->count();
        $likesPrev = EventUserReaction::where('reaction_type', 'like')->whereMonth('created_at', $prevMonth->month)->whereYear('created_at', $prevMonth->year)->count();
        $commentsPrev = EventUserReaction::where('reaction_type', 'comment')->whereMonth('created_at', $prevMonth->month)->whereYear('created_at', $prevMonth->year)->count();
        $savedPrev = EventUserReaction::where('reaction_type', 'save')->whereMonth('created_at', $prevMonth->month)->whereYear('created_at', $prevMonth->year)->count();
        $postsPrev = Post::whereMonth('created_at', $prevMonth->month)->whereYear('created_at', $prevMonth->year)->count(); 

        $calcTrend = fn($current, $previous) => $previous == 0 ? 100 : round((($current - $previous) / $previous) * 100);

        $eventsTrend = $calcTrend($eventsCount, $eventsPrev);
        $likesTrend = $calcTrend($likesCount, $likesPrev);
        $commentsTrend = $calcTrend($commentsCount, $commentsPrev);
        $savedEventsTrend = $calcTrend($savedEventsCount, $savedPrev);
        $postsTrend = $calcTrend($postsCount, $postsPrev);

        // Top Coordenadores
        $coordinatorsRanking = Event::select('coordinator_id', DB::raw('count(*) as events_count'))
            ->whereBetween('created_at', [$start, $end])
            ->whereNotNull('coordinator_id')
            ->groupBy('coordinator_id')
            ->orderByDesc('events_count')
            ->with('eventCoordinator.userAccount')
            ->get();
        
        $topCoordinators = $coordinatorsRanking->take(3);
        $otherCoordinators = $coordinatorsRanking->skip(3);
        
        // Ranking de Cursos
        $coursesRanking = Event::select('course_id', DB::raw('count(*) as events_count'))
            ->whereBetween('created_at', [$start, $end])
            ->whereNotNull('course_id')
            ->groupBy('course_id')
            ->orderByDesc('events_count')
            ->with('eventCourse')
            ->get();

        $coursesLabels = $coursesRanking->pluck('eventCourse.course_name')->toArray();
        $coursesData = $coursesRanking->pluck('events_count')->toArray();

        // Top 5 eventos do mês
        $topEventsOfTheMonth = Event::whereBetween('event_scheduled_at', [
                $now->copy()->startOfMonth(), 
                $now->copy()->endOfMonth()
            ])
            ->withCount(['reactions as total_interactions'])
            ->orderByDesc('total_interactions')
            ->limit(5)
            ->get();
            
        // Dados mensais últimos 6 meses
        $monthData = $this->getMonthlyInteractionsData();
        
        return array_merge([
            'eventsCount' => $eventsCount,
            'likesCount' => $likesCount,
            'commentsCount' => $commentsCount,
            'savedEventsCount' => $savedEventsCount,
            'postsCount' => $postsCount,
            'eventCommentsCount' => $eventCommentsCount,
            'coursesCount' => $coursesCount,
            
            'eventsTrend' => $eventsTrend,
            'likesTrend' => $likesTrend,
            'commentsTrend' => $commentsTrend,
            'savedEventsTrend' => $savedEventsTrend,
            'postsTrend' => $postsTrend,
            
            'topCoordinators' => $topCoordinators,
            'otherCoordinators' => $otherCoordinators,
            'coursesLabels' => $coursesLabels,
            'coursesData' => $coursesData,
            'topEventsOfTheMonth' => $topEventsOfTheMonth,
        ], $monthData);
    }
    
    private function getMonthlyInteractionsData(): array
    {
        $startDate = now()->subMonths(5)->startOfMonth();
        $months = [];
        
        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $months[$date->format('Y-m')] = $date->format('M/Y');
        }
        
        $interactionsByMonth = EventUserReaction::select(
            DB::raw('DATE_FORMAT(created_at, "%Y-%m") as month_year'),
            DB::raw('count(*) as total_interactions')
        ) 
        ->where('created_at', '>=', $startDate)
        ->groupBy('month_year')
        ->orderBy('month_year', 'asc')
        ->get()
        ->keyBy('month_year');
        
        $postsByMonth = Post::select(
            DB::raw('DATE_FORMAT(created_at, "%Y-%m") as month_year'),
            DB::raw('count(*) as total_posts')
        )
        ->where('created_at', '>=', $startDate)
        ->groupBy('month_year')
        ->orderBy('month_year', 'asc')
        ->get()
        ->keyBy('month_year');
        
        $repliesByMonth = Reply::select(
            DB::raw('DATE_FORMAT(created_at, "%Y-%m") as month_year'),
            DB::raw('count(*) as total_replies')
        )
        ->where('created_at', '>=', $startDate)
        ->groupBy('month_year')
        ->orderBy('month_year', 'asc')
        ->get()
        ->keyBy('month_year');
        
        $interactionsLabels = [];
        $interactionsData = [];
        $postInteractionsLabels = [];
        $postsData = [];
        $repliesData = [];

        foreach ($months as $key => $label) {
            $interactionsLabels[] = $label;
            $postInteractionsLabels[] = $label;
            
            $interactionsData[] = $interactionsByMonth[$key]->total_interactions ?? 0;
            $postsData[] = $postsByMonth[$key]->total_posts ?? 0;
            $repliesData[] = $repliesByMonth[$key]->total_replies ?? 0;
        }

        return compact(
            'interactionsLabels',
            'interactionsData',
            'postInteractionsLabels',
            'postsData',
            'repliesData'
        );
    }

    public function index()
    {
        $data = $this->getDashboardData();

        $user = auth()->user();
        $message = 'Bem-vindo(a) ao seu painel de controle. Aqui você pode acompanhar o total de interações do sistema, atividades recentes dos coordenadores/cursos/eventos da nossa escola.';

        $data['chartImages'] = [
            'interactionsChartImage' => '',
            'postInteractionsChartImage' => '',
            'coursesChartImage' => '',
        ];

        return view('admin.dashboard', $data)->with([
            'name' => $user->name,
            'message' => $message
        ]);
    }

    public function exportPdf(Request $request)
    {
        $data = $this->getDashboardData();
        
        $data['chartImages'] = [
            'interactionsChartImage' => $request->input('interactionsChartImage'),
            'postInteractionsChartImage' => $request->input('postInteractionsChartImage'),
            'coursesChartImage' => $request->input('coursesChartImage'),
        ];

        $logoPath = public_path('imgs/logoJb.png');
        if (file_exists($logoPath)) {
            $data['logoBase64'] = 'data:image/' . pathinfo($logoPath, PATHINFO_EXTENSION) . ';base64,' . base64_encode(file_get_contents($logoPath));
        } else {
            $data['logoBase64'] = null;
        }
        
        $pdf = Pdf::loadView('admin.dashboard-pdf', $data);

        $pdf->setOptions([
            'defaultPaperSize' => 'A4',
            'tempDir' => storage_path('temp'),
            'dpi' => 96,
            'isHtml5ParserEnabled' => true
        ])->setWarnings(false);

        set_time_limit(120);

        return $pdf->download('relatorio-admin-' . now()->format('Y-m-d') . '.pdf');
    }
}
