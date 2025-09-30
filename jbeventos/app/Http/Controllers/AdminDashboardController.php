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
use Illuminate\Support\Facades\DB; // Usar DB::raw

class AdminDashboardController extends Controller
{
    /**
     * Coleta os dados do dashboard, com a opção de filtrar por período.
     * @param string|null $startDateString
     * @param string|null $endDateString
     * @return array
     */

    private function getDashboardData(?string $startDateString = null, ?string $endDateString = null): array
    {

        $now = now();
        
        // Define o período padrão para tendências (Mês anterior)
        $prevMonth = $now->copy()->subMonth();
        
        // --- 1. Definir o Período de Filtragem (para a lógica de gráficos) ---
        // Se as datas não forem fornecidas, usamos os últimos 6 meses
        $start = $startDateString 
            ? Carbon::createFromFormat('Y-m-d', $startDateString)->startOfDay() 
            : $now->copy()->subMonths(5)->startOfMonth();

        $end = $endDateString 
            ? Carbon::createFromFormat('Y-m-d', $endDateString)->endOfDay() 
            : $now->endOfDay();

        // --- 2. Totais (Gerais ou dentro do Período de Relatório) ---
        $eventsCount = Event::whereBetween('created_at', [$start, $end])->count();
        $postsCount = Post::whereBetween('created_at', [$start, $end])->count();
        $usersCount = User::whereBetween('created_at', [$start, $end])->count();
        $coursesCount = Course::count(); 
        
        $likesCount = EventUserReaction::where('reaction_type', 'like')->whereBetween('created_at', [$start, $end])->count();
        $commentsCount = EventUserReaction::where('reaction_type', 'comment')->whereBetween('created_at', [$start, $end])->count();
        $savedEventsCount = EventUserReaction::where('reaction_type', 'save')->whereBetween('created_at', [$start, $end])->count();
        
        // Comentários de eventos
        $eventCommentsCount = Comment::whereBetween('created_at', [$start, $end])->count();


        // --- 3. Tendências Mês a Mês (Baseado no Mês Anterior) ---
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


        // --- 4. Dados para Gráficos (Interações, Posts e Cursos) ---
        
        // Coordenadores (Top 3)
        $coordinatorsRanking = Event::select('coordinator_id', DB::raw('count(*) as events_count'))
            ->whereBetween('created_at', [$start, $end]) // Filtra por período
            ->whereNotNull('coordinator_id')
            ->groupBy('coordinator_id')
            ->orderByDesc('events_count')
            ->with('eventCoordinator.userAccount')
            ->get();
        
        $topCoordinators = $coordinatorsRanking->take(3); // Primeiros 3
        $otherCoordinators = $coordinatorsRanking->skip(3); // Demais coordenadores
        
        // Ranking de Cursos (Todos os cursos no período)
        $coursesRanking = Event::select('course_id', DB::raw('count(*) as events_count'))
            ->whereBetween('created_at', [$start, $end]) // Filtra por período
            ->whereNotNull('course_id')
            ->groupBy('course_id')
            ->orderByDesc('events_count')
            ->with('eventCourse')
            ->get();

        $coursesLabels = $coursesRanking->pluck('eventCourse.course_name')->toArray(); // Nomes dos cursos
        $coursesData = $coursesRanking->pluck('events_count')->toArray(); // Quantidade de eventos por curso

        // Top Eventos do Mês
        $topEventsOfTheMonth = Event::whereBetween('event_scheduled_at', [
                $now->copy()->startOfMonth(), 
                $now->copy()->endOfMonth()
            ])
            ->withCount(['reactions as total_interactions'])
            ->orderByDesc('total_interactions')
            ->limit(5)
            ->get();
            
        // Dados Mensais (Últimos 6 Meses)
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
            
            // Dados de período para exibição no PDF
            'reportStartDate' => $start->format('d/m/Y'),
            'reportEndDate' => $end->format('d/m/Y'),
            
        ], $monthData);
    }
    
    /**
     * Coleta os dados de evolução mensal (Posts e Interações) para os últimos 6 meses.
     * @return array
     */
    private function getMonthlyInteractionsData(): array
    {
        // Define o período dos últimos 6 meses
        $startDate = now()->subMonths(5)->startOfMonth();
        $months = [];
        
        // Gera os últimos 6 meses (incluindo o mês atual)
        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $months[$date->format('Y-m')] = $date->format('M/Y'); // 'M/Y' para labels
        }
        
        // Coleta os dados agrupados por mês
        $interactionsByMonth = EventUserReaction::select(
            DB::raw('DATE_FORMAT(created_at, "%Y-%m") as month_year'),
            DB::raw('count(*) as total_interactions')
        ) 
        ->where('created_at', '>=', $startDate) // Filtra por período
        ->groupBy('month_year')
        ->orderBy('month_year', 'asc')
        ->get()
        ->keyBy('month_year'); // Facilita o acesso por chave
        

        // Coleta os dados de posts e respostas
        $postsByMonth = Post::select(
            DB::raw('DATE_FORMAT(created_at, "%Y-%m") as month_year'),
            DB::raw('count(*) as total_posts')
        )
        ->where('created_at', '>=', $startDate)
        ->groupBy('month_year')
        ->orderBy('month_year', 'asc')
        ->get()
        ->keyBy('month_year');
        
        // Coleta os dados de respostas
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

        // Adiciona campos vazios para as imagens, necessários na view do dashboard (HTML)
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

    /**
     * Exporta o dashboard para PDF com base no período fornecido.
     */
    public function exportPdf(Request $request)
    {
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        
        // Coleta os dados usando o método refatorado, com as datas de filtro
        $data = $this->getDashboardData($startDate, $endDate);
        
        // Adiciona as imagens Base64 capturadas pelo JavaScript do dashboard
        $data['chartImages'] = [
            'interactionsChartImage' => $request->input('interactionsChartImage'),
            'postInteractionsChartImage' => $request->input('postInteractionsChartImage'),
            'coursesChartImage' => $request->input('coursesChartImage'),
        ];

        // Carrega a logo do sistema (se existir)
        $logoPath = public_path('imgs/logoJb.png');
        if (file_exists($logoPath)) {
            // Converte a imagem para Base64
            $data['logoBase64'] = 'data:image/' . pathinfo($logoPath, PATHINFO_EXTENSION) . ';base64,' . base64_encode(file_get_contents($logoPath));
        } else {
            $data['logoBase64'] = null;
        }
        
        $pdf = Pdf::loadView('admin.dashboard-pdf', $data); // Renderiza a view do PDF

        // Remove opções de JS/Remote, pois o gráfico agora é uma imagem estática
        $pdf->setOptions([
            'defaultPaperSize' => 'A4',
            'tempDir' => storage_path('temp'),
            'dpi' => 96,
            'isHtml5ParserEnabled' => true
        ])->setWarnings(false);

        // Define um tempo limite maior para renderização (pode ser necessário)
        set_time_limit(120);

        return $pdf->download('relatorio-admin-' . now()->format('Y-m-d') . '.pdf'); // Download do PDF
    }
}