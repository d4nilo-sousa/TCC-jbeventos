<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Event;
use App\Models\EventUserReaction;
use App\Models\Comment;
use App\Models\Post;
use App\Models\Reply;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class CoordinatorDashboardController extends Controller
{
    /**
     * Prepara os dados do dashboard para exibição ou exportação.
     * @param string|null $startDateString Data de início da filtragem (Y-m-d).
     * @param string|null $endDateString Data de fim da filtragem (Y-m-d).
     * @return array
     */
    private function getDashboardData($startDateString = null, $endDateString = null)
    {
        $user = auth()->user();
        $coordinator = $user->coordinator;
        $now = Carbon::now();

        if (! $coordinator) {
            abort(403, 'Perfil de coordenador não encontrado.');
        }

        // ----------------------------------------------------
        // 1. Definição do Período de Análise e Rótulos (Labels)
        // ----------------------------------------------------
        
        $months = collect();

        if ($startDateString && $endDateString) {
            // Se as datas foram fornecidas (filtro do PDF), usa-as.
            $startDate = Carbon::parse($startDateString)->startOfDay();
            $endDate = Carbon::parse($endDateString)->endOfDay();
            
            // Cria array de meses APENAS dentro do intervalo [startDate, endDate]
            $tempDate = $startDate->copy()->startOfMonth();
            while ($tempDate->lte($endDate)) {
                $months->push($tempDate->copy());
                $tempDate->addMonth();
            }
            
        } else {
            // Padrão: Últimos 6 meses (para o dashboard principal)
            for ($i = 5; $i >= 0; $i--) {
                $months->push($now->copy()->subMonths($i));
            }
            $startDate = $months->first()->copy()->startOfMonth();
            $endDate = $now->copy();
        }

        // Rótulos do gráfico (Mês/Ano) e Chaves Únicas (AnoMês) para o loop de inicialização
        $labels = $months->map(fn ($m) => $m->format('M'));
        $periodKeys = $months->map(fn ($m) => $m->format('Ym'));

        // Define o formato de agrupamento de data para as queries (Ano e Mês para chave única)
        $groupFormat = "DATE_FORMAT(created_at, '%Y%m')";


        // IDs de TODOS os eventos e posts do coordenador (para contagem de interações)
        $allCoordinatorEventIds = Event::where('coordinator_id', $coordinator->id)->pluck('id');
        $allCoordinatorPostIds = Post::where('user_id', $user->id)->pluck('id');

        // ----------------------------------------------------
        // 2. Totais Simples (Dentro do período de $startDate a $endDate)
        // ----------------------------------------------------
        
        // Eventos e Posts: Contamos APENAS os criados DENTRO do período
        $eventsCount = Event::where('coordinator_id', $coordinator->id)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->count();

        $postsCount = Post::where('user_id', $user->id)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->count(); 
            
        // Interações: Contamos APENAS as que aconteceram DENTRO do período
        if ($allCoordinatorEventIds->isEmpty()) {
            $likes = 0;
            $saves = 0;
            $comments = 0;
        } else {
            // Contagem de interações de Eventos
            $likes = EventUserReaction::whereIn('event_id', $allCoordinatorEventIds)
                ->where('reaction_type', 'like')
                ->whereBetween('created_at', [$startDate, $endDate])
                ->count();

            $saves = EventUserReaction::whereIn('event_id', $allCoordinatorEventIds)
                ->where('reaction_type', 'save')
                ->whereBetween('created_at', [$startDate, $endDate])
                ->count();

            $comments = Comment::whereIn('event_id', $allCoordinatorEventIds)
                ->whereBetween('created_at', [$startDate, $endDate])
                ->count();
        }


        // ----------------------------------------------------
        // 3. DADOS POR PERÍODO (Para os gráficos de Evolução)
        // ----------------------------------------------------
        
        // Inicializa arrays de dados usando as chaves de período (Ym) para alinhamento
        $engagementByPeriodArr = [];
        $likesByPeriodArr = [];
        $savesByPeriodArr = [];
        $commentsByPeriodArr = [];
        $eventsByPeriodArr = [];
        $postsByPeriodArr = []; 
        $postInteractionsByPeriodArr = []; 
        
        // Inicializa o array com a chave 'Ym' e valor 0 para todos os meses no filtro
        foreach ($periodKeys as $key) {
            $engagementByPeriodArr[$key] = 0;
            $likesByPeriodArr[$key] = 0;
            $savesByPeriodArr[$key] = 0;
            $commentsByPeriodArr[$key] = 0;
            $eventsByPeriodArr[$key] = 0;
            $postsByPeriodArr[$key] = 0;
            $postInteractionsByPeriodArr[$key] = 0;
        }
        
        // --- Interações de Eventos (Likes, Saves, Comments) ---
        if (! $allCoordinatorEventIds->isEmpty()) {
            // Reações (Likes/Saves)
            $reactionsByPeriod = EventUserReaction::whereIn('event_id', $allCoordinatorEventIds)
                ->whereBetween('created_at', [$startDate, $endDate])
                ->select(DB::raw("{$groupFormat} as period_label, reaction_type, COUNT(*) as total"))
                ->groupBy('period_label', 'reaction_type')
                ->get();
            
            foreach ($reactionsByPeriod as $reaction) {
                $label = $reaction->period_label;
                if (isset($likesByPeriodArr[$label])) {
                    if ($reaction->reaction_type == 'like') {
                        $likesByPeriodArr[$label] = (int) $reaction->total;
                    } elseif ($reaction->reaction_type == 'save') {
                        $savesByPeriodArr[$label] = (int) $reaction->total;
                    }
                    $engagementByPeriodArr[$label] += (int) $reaction->total;
                }
            }

            // Comentários
            $commentsByPeriodData = Comment::whereIn('event_id', $allCoordinatorEventIds)
                ->whereBetween('created_at', [$startDate, $endDate])
                ->select(DB::raw("{$groupFormat} as period_label, COUNT(*) as total"))
                ->groupBy('period_label')
                ->pluck('total', 'period_label')
                ->toArray();

            foreach ($commentsByPeriodData as $label => $total) {
                if(isset($commentsByPeriodArr[$label])) {
                    $commentsByPeriodArr[$label] = (int) $total;
                    $engagementByPeriodArr[$label] += (int) $total;
                }
            }
        }
        
        // Eventos Criados
        $eventsByPeriodData = Event::where('coordinator_id', $coordinator->id)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->select(DB::raw("{$groupFormat} as period_label, COUNT(*) as total"))
            ->groupBy('period_label')
            ->pluck('total', 'period_label')
            ->toArray();
        
        foreach ($eventsByPeriodData as $label => $total) {
            if(isset($eventsByPeriodArr[$label])) {
                $eventsByPeriodArr[$label] = (int) $total;
            }
        }

        // Posts Criados
        $postsByPeriodData = Post::where('user_id', $user->id)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->select(DB::raw("{$groupFormat} as period_label, COUNT(*) as total"))
            ->groupBy('period_label')
            ->pluck('total', 'period_label')
            ->toArray();
        
        foreach ($postsByPeriodData as $label => $total) {
            if(isset($postsByPeriodArr[$label])) {
                $postsByPeriodArr[$label] = (int) $total;
            }
        }
        
        // Interações nos Posts (Respostas)
        if (! $allCoordinatorPostIds->isEmpty()) {
            $repliesByPeriod = Reply::whereIn('post_id', $allCoordinatorPostIds)
                ->whereBetween('created_at', [$startDate, $endDate])
                ->select(DB::raw("{$groupFormat} as period_label, COUNT(*) as total"))
                ->groupBy('period_label')
                ->pluck('total', 'period_label')
                ->toArray();
            
            foreach ($repliesByPeriod as $label => $total) {
                if(isset($postInteractionsByPeriodArr[$label])) {
                    $postInteractionsByPeriodArr[$label] += (int) $total; 
                }
            }
        }
        
        // ----------------------------------------------------
        // 4. TOP EVENTOS MAIS ENGAJADOS
        // ----------------------------------------------------
        
        $topEvents = collect();
        if (! $allCoordinatorEventIds->isEmpty()) {
            $topEvents = Event::whereIn('id', $allCoordinatorEventIds) 
                ->withCount([
                    // Conta as reações DENTRO do período de filtro
                    'reactions as likes_count' => function ($q) use ($startDate, $endDate) { 
                        $q->where('reaction_type', 'like')->whereBetween('created_at', [$startDate, $endDate]); 
                    },
                    'reactions as saves_count' => function ($q) use ($startDate, $endDate) { 
                        $q->where('reaction_type', 'save')->whereBetween('created_at', [$startDate, $endDate]); 
                    },
                    'eventComments' => function ($q) use ($startDate, $endDate) { 
                        $q->whereBetween('created_at', [$startDate, $endDate]); 
                    }
                ])
                ->get()
                ->map(function ($e) {
                    $e->total_engagement = ($e->likes_count ?? 0) + ($e->event_comments_count ?? 0) + ($e->saves_count ?? 0);
                    return $e;
                })
                ->filter(fn($e) => $e->total_engagement > 0) // Remove eventos sem engajamento no período
                ->sortByDesc('total_engagement')
                ->take(3);
        }
        
        // Cálculo de Tendências (Mantido)
        $currentMonthStart = Carbon::now()->startOfMonth();
        $lastMonthStart = Carbon::now()->subMonth()->startOfMonth();
        $lastMonthEnd = Carbon::now()->subMonth()->endOfMonth();
        
        $calculateTrend = function ($currentCount, $lastCount) {
            if ($lastCount == 0) {
                return $currentCount > 0 ? 100 : 0;
            }
            return round((($currentCount - $lastCount) / $lastCount) * 100);
        };
        
        $currentPosts = Post::where('user_id', $user->id)->whereBetween('created_at', [$currentMonthStart, Carbon::now()])->count();
        $lastPosts = Post::where('user_id', $user->id)->whereBetween('created_at', [$lastMonthStart, $lastMonthEnd])->count();
        $postsTrend = $calculateTrend($currentPosts, $lastPosts);


        // Garante que os arrays de valores sigam a ORDEM do $periodKeys
        // Mapeia os arrays de inicialização (que estão na ordem correta) para gerar a Collection final de valores.
        $eventEngagementValues = $periodKeys->map(fn($key) => $engagementByPeriodArr[$key] ?? 0);
        $postsValues = $periodKeys->map(fn($key) => $postsByPeriodArr[$key] ?? 0); 
        $postInteractionsValues = $periodKeys->map(fn($key) => $postInteractionsByPeriodArr[$key] ?? 0); 
        $eventsByPeriod = $periodKeys->map(fn($key) => $eventsByPeriodArr[$key] ?? 0);
        $likesByPeriod = $periodKeys->map(fn($key) => $likesByPeriodArr[$key] ?? 0);
        $savesByPeriod = $periodKeys->map(fn($key) => $savesByPeriodArr[$key] ?? 0);
        $commentsByPeriod = $periodKeys->map(fn($key) => $commentsByPeriodArr[$key] ?? 0);


        $message = 'Bem-vindo(a) ao seu painel de controle. Acompanhe o desempenho dos seus posts e o engajamento dos eventos que você gerencia.';
        
        return [
            'eventsCount' => $eventsCount,
            'likes' => $likes,
            'saves' => $saves,
            'comments' => $comments,
            'postsCount' => $postsCount, 
            'postsTrend' => $postsTrend, 
            'topEvents' => $topEvents,
            
            // Dados para o Gráfico (agora refletem o período filtrado)
            'labels' => $labels,
            'eventEngagementValues' => $eventEngagementValues, 
            'eventsByMonth' => $eventsByPeriod,
            'likesByMonth' => $likesByPeriod,
            'savesByMonth' => $savesByPeriod,
            'commentsByMonth' => $commentsByPeriod,
            'postsValues' => $postsValues, 
            'postInteractionsValues' => $postInteractionsValues,

            'name' => $user->name,
            'message' => $message,
            'currentDate' => Carbon::now()->format('d/m/Y H:i:s'),
            'logoBase64' => 'data:image/png;base64,' . base64_encode(file_get_contents(public_path('imgs/logoJb.png'))), 
            
            // Datas usadas no relatório (para a capa do PDF)
            'reportStartDate' => $startDate->format('d/m/Y'),
            'reportEndDate' => $endDate->format('d/m/Y'),
        ];
    }

    /**
     * Exibe o dashboard do coordenador com todos os dados de performance.
     */
    public function index()
    {
        // O index sempre carrega o padrão de 6 meses (sem argumentos)
        $data = $this->getDashboardData();

        return view('coordinator.dashboard', $data)->with([
            'name' => $data['name'],
            'message' => $data['message'] 
        ]);
    }

    /**
     * Exporta os dados do dashboard do coordenador para PDF.
     * @param \Illuminate\Http\Request $request
     */
    public function exportPdf(Request $request)
    {
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        
        $data = $this->getDashboardData($startDate, $endDate);
        
        $chartImages = [
            'eventEngagementChartImage' => $request->input('eventEngagementChartImage'),
            'publicationsChartImage'    => $request->input('publicationsChartImage'),
            'postInteractionsChartImage'  => $request->input('postInteractionsChartImage'),
        ];
        
        $logoBase64 = $data['logoBase64'] ?? '';

        $dataToPdf = array_merge($data, [
            'userName' => $data['name'],
            'chartImages' => $chartImages,
            'logoBase64' => $logoBase64,
        ]);
        
        $filename = 'Relatorio_Coordenador_' . auth()->user()->id . '_' . Carbon::now()->format('Ymd_His') . '.pdf';

        // Usa o namespace correto do DomPDF, garantindo que o PDF seja gerado.
        $pdf = Pdf::loadView('coordinator.dashboard-pdf', $dataToPdf); 

        $pdf->setOptions(['defaultFont' => 'sans-serif']);

        return $pdf->download($filename);
    }
}