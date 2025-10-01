<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Event;
use App\Models\EventUserReaction;
use App\Models\Comment;
use App\Models\Post; 
use App\Models\Reply; 
use Carbon\Carbon;
use Illuminate\Support\Facades\DB; // Adicionado para uso em consultas Raw

class CoordinatorDashboardController extends Controller
{
    /**
     * Prepara os dados do dashboard para exibição ou exportação.
     * @return array
     */
    private function getDashboardData()
    {
        $user = auth()->user();
        $coordinator = $user->coordinator;

        if (! $coordinator) {
            abort(403, 'Perfil de coordenador não encontrado.');
        }

        // IDs dos eventos gerenciados por esse coordenador
        $eventIds = Event::where('coordinator_id', $coordinator->id)->pluck('id');
        
        // IDs dos posts criados por esse coordenador
        $postIds = Post::where('user_id', $user->id)->pluck('id');

        // --- Totais simples ---
        $eventsCount = $eventIds->count();
        $postsCount = Post::where('user_id', $user->id)->count(); // Total de posts criados

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

        // --- Configuração de Meses (Últimos 6 meses) ---
        $now = Carbon::now();
        $months = collect();
        for ($i = 5; $i >= 0; $i--) {
            $months->push($now->copy()->subMonths($i));
        }
        $startDate = $months->first()->copy()->startOfMonth();
        $currentMonthStart = $now->copy()->startOfMonth();
        $lastMonthStart = $now->copy()->subMonth()->startOfMonth();
        $lastMonthEnd = $now->copy()->subMonth()->endOfMonth();


        // Inicializa arrays de dados por mês (que serão preenchidos via query)
        $engagementByMonthArr = [];
        $likesByMonthArr = [];
        $savesByMonthArr = [];
        $commentsByMonthArr = [];
        $eventsByMonthArr = [];
        $postsByMonthArr = []; 
        $postInteractionsByMonthArr = []; 
        
        foreach ($months as $m) {
            $monthNum = (int)$m->format('n');
            // Inicializa todos com 0.
            $engagementByMonthArr[$monthNum] = 0;
            $likesByMonthArr[$monthNum] = 0;
            $savesByMonthArr[$monthNum] = 0;
            $commentsByMonthArr[$monthNum] = 0;
            $eventsByMonthArr[$monthNum] = 0;
            $postsByMonthArr[$monthNum] = 0;
            $postInteractionsByMonthArr[$monthNum] = 0;
        }


        // --- DADOS DOS EVENTOS ---
        if (! $eventIds->isEmpty()) {

            // Interações de Eventos
            $reactionsByMonth = EventUserReaction::whereIn('event_id', $eventIds)
                ->where('created_at', '>=', $startDate)
                ->select(DB::raw('MONTH(created_at) as month, reaction_type, COUNT(*) as total')) // Usando DB::raw
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

            // Comentários de Eventos
            $commentsByMonth = Comment::whereIn('event_id', $eventIds)
                ->where('created_at', '>=', $startDate)
                ->select(DB::raw('MONTH(created_at) as month, COUNT(*) as total')) // Usando DB::raw
                ->groupBy('month')
                ->pluck('total', 'month')
                ->toArray();

            foreach ($commentsByMonth as $month => $total) {
                $commentsByMonthArr[(int)$month] = (int) $total;
                $engagementByMonthArr[(int)$month] += (int) $total;
            }
        }
        
        // Eventos Criados (por mês)
        $eventsByMonthData = Event::where('coordinator_id', $coordinator->id)
            ->where('created_at', '>=', $startDate)
            ->select(DB::raw('MONTH(created_at) as month, COUNT(*) as total')) // Usando DB::raw
            ->groupBy('month')
            ->pluck('total', 'month')
            ->toArray();
        
        foreach ($eventsByMonthData as $month => $total) {
            $eventsByMonthArr[(int)$month] = (int) $total;
        }

        // --- DADOS DOS POSTS ---
        
        // Posts Criados (por mês)
        $postsByMonth = Post::where('user_id', $user->id)
            ->where('created_at', '>=', $startDate)
            ->select(DB::raw('MONTH(created_at) as month, COUNT(*) as total')) // Usando DB::raw
            ->groupBy('month')
            ->pluck('total', 'month')
            ->toArray();
        
        foreach ($postsByMonth as $month => $total) {
            $postsByMonthArr[(int)$month] = (int) $total;
        }
        
        // Interações nos Posts (Respostas)
        if (! $postIds->isEmpty()) {
            $repliesByMonth = Reply::whereIn('post_id', $postIds)
                ->where('created_at', '>=', $startDate)
                ->select(DB::raw('MONTH(created_at) as month, COUNT(*) as total')) // Usando DB::raw
                ->groupBy('month')
                ->pluck('total', 'month')
                ->toArray();
            
            foreach ($repliesByMonth as $month => $total) {
                $postInteractionsByMonthArr[(int)$month] += (int) $total; 
            }
        }


        // --- CÁLCULO DE TENDÊNCIAS (COMPARATIVO MÊS ANTERIOR) ---
        
        $calculateTrend = function ($currentCount, $lastCount) {
            if ($lastCount == 0) {
                return $currentCount > 0 ? 100 : 0;
            }
            return round((($currentCount - $lastCount) / $lastCount) * 100);
        };
        
        // Posts (Tendência)
        $currentPosts = Post::where('user_id', $user->id)->whereBetween('created_at', [$currentMonthStart, $now])->count();
        $lastPosts = Post::where('user_id', $user->id)->whereBetween('created_at', [$lastMonthStart, $lastMonthEnd])->count();
        $postsTrend = $calculateTrend($currentPosts, $lastPosts);


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

        // Garante que todas as variáveis que serão usadas com ->push() são Collections
        $labels = collect();
        $eventEngagementValues = collect();
        $postsValues = collect(); 
        $postInteractionsValues = collect(); 
        
        // Sparklines de Eventos
        $eventsByMonth = collect();
        $likesByMonth = collect();
        $savesByMonth = collect();
        $commentsByMonth = collect();

        foreach ($months as $m) {
            $monthNum = (int)$m->format('n');
            $labels->push($m->format('M'));
            
            // Dados de Eventos
            $eventEngagementValues->push($engagementByMonthArr[$monthNum] ?? 0);
            $eventsByMonth->push($eventsByMonthArr[$monthNum] ?? 0);
            $likesByMonth->push($likesByMonthArr[$monthNum] ?? 0);
            $savesByMonth->push($savesByMonthArr[$monthNum] ?? 0);
            $commentsByMonth->push($commentsByMonthArr[$monthNum] ?? 0);
            
            // Dados de Posts
            $postsValues->push($postsByMonthArr[$monthNum] ?? 0); 
            $postInteractionsValues->push($postInteractionsByMonthArr[$monthNum] ?? 0); 
        }

        // Mensagem dinâmica para Coordenador
        $message = 'Bem-vindo(a) ao seu painel de controle. Acompanhe o desempenho dos seus posts e o engajamento dos eventos que você gerencia.';
        
        return [
            'eventsCount' => $eventsCount,
            'likes' => $likes,
            'saves' => $saves,
            'comments' => $comments,
            'postsCount' => $postsCount, 
            'postsTrend' => $postsTrend, 
            'topEvents' => $topEvents,
            'labels' => $labels,
            'eventEngagementValues' => $eventEngagementValues, 
            'eventsByMonth' => $eventsByMonth,
            'likesByMonth' => $likesByMonth,
            'savesByMonth' => $savesByMonth,
            'commentsByMonth' => $commentsByMonth,
            'postsValues' => $postsValues, 
            'postInteractionsValues' => $postInteractionsValues,
            'name' => $user->name,
            'message' => $message,
            'currentDate' => $now->format('d/m/Y H:i:s'),
            // Adicionar logo base64 aqui se você tiver a lógica no backend
            'logoBase64' => 'data:image/png;base64,' . base64_encode(file_get_contents(public_path('imgs/logoJb.png'))), 
            'reportStartDate' => $startDate->format('d/m/Y'),
            'reportEndDate' => $now->format('d/m/Y'),
        ];
    }

    /**
     * Exibe o dashboard do coordenador com todos os dados de performance.
     */
    public function index()
    {
        $data = $this->getDashboardData();

        return view('coordinator.dashboard', $data)->with([
            'name' => $data['name'],
            'message' => $data['message'] 
        ]);
    }

    /**
     * Exporta os dados do dashboard do coordenador para PDF.
     * * @param \Illuminate\Http\Request $request
     */
    public function exportPdf(Request $request)
    {
        $data = $this->getDashboardData();
        
        // 1. COLETAR AS IMAGENS BASE64 DOS GRÁFICOS ENVIADAS PELO FRONTEND
        $chartImages = [
            'eventEngagementChartImage' => $request->input('eventEngagementChartImage'),
            'publicationsChartImage'    => $request->input('publicationsChartImage'),
            'postInteractionsChartImage'  => $request->input('postInteractionsChartImage'),
        ];
        
        // 2. Coletar a logo Base64 (assumindo que a função getDashboardData já buscou)
        $logoBase64 = $data['logoBase64'] ?? '';


        // 3. Juntar todos os dados para a view
        $dataToPdf = array_merge($data, [
            'userName' => $data['name'],
            'chartImages' => $chartImages, // Passa o array de imagens Base64
            'logoBase64' => $logoBase64,   // Garante que a logo Base64 está incluída
        ]);
        
        // Título dinâmico para o PDF
        $filename = 'Relatorio_Coordenador_' . auth()->user()->id . '_' . Carbon::now()->format('Ymd_His') . '.pdf';

        // 4. CORRIGIR A VIEW: Usar a view 'report.pdf.blade.php'
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('coordinator.dashboard-pdf', $dataToPdf);

        // Define as opções do DomPDF para visualização amigável em PDF
        $pdf->setOptions(['defaultFont' => 'sans-serif']);

        return $pdf->download($filename);
    }
}
