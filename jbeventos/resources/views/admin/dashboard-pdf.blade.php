<!DOCTYPE html>
<html>
<head>
    <title>Relatório Gerencial da Plataforma (Admin)</title>
    <style>
        body { font-family: sans-serif; margin: 30px; font-size: 12px; }
        .page-break { page-break-after: always; }
        .cover-page { text-align: center; margin-top: 150px; padding: 50px; height: 600px; }
        .cover-page h1 { font-size: 32px; color: #1f2937; margin-bottom: 10px; }
        .cover-page p { font-size: 18px; color: #4b5563; margin-top: 5px; }
        .logo { max-width: 350px; height: auto; margin: 50px auto; display: block; } 
        h2 { font-size: 18px; color: #1f2937; margin-top: 30px; margin-bottom: 15px; border-left: 5px solid #C0392B; padding-left: 10px; }
        h3 { font-size: 14px; color: #4b5563; margin-top: 15px; margin-bottom: 10px; }
        .separator { border: 0; border-top: 3px solid #C0392B; margin: 20px 0 50px 0; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; page-break-inside: avoid; }
        .summary-table th, .summary-table td { border: 1px solid #e5e7eb; padding: 10px 15px; text-align: center; }
        .summary-table th { background-color: #f3f4f6; color: #1f2937; font-weight: bold; }
        .ranking-table { margin-top: 5px; font-size: 12px; }
        .ranking-table th, .ranking-table td { border: none; border-bottom: 1px solid #e5e7eb; padding: 5px 0; text-align: left; }
        .ranking-table th { color: #6b7280; }
        .ranking-table td:last-child { text-align: right; font-weight: bold; }
        .ranking-table th:last-child { text-align: right; }
        .chart-box { width: 100%; margin-bottom: 30px; padding: 10px; border: 1px solid #e5e7eb; border-radius: 8px; display: block; page-break-inside: avoid; }
        .chart-box img { width: 100%; height: auto; max-height: 350px; }
        .clear { clear: both; }
        .ranking-container { width: 100%; margin-top: 15px; page-break-inside: avoid; }
        .ranking-col { width: 48%; display: inline-block; float: left; margin-right: 4%; box-sizing: border-box; }
        .ranking-col.last { float: right; margin-right: 0; }
    </style>
</head>
<body>

    {{-- CAPA --}}
    <div class="cover-page">
        <h1>Relatório Gerencial da Plataforma (Admin)</h1>
        <p>Visão Consolidada de Dados e Evolução de Atividades</p>
        
        @if (!empty($logoBase64))
            <img src="{{ $logoBase64 }}" class="logo" alt="Logo do Sistema">
        @else
            <p style="color: red;">Logo não carregada (Verifique o caminho: public/imgs/logoJb.png)</p>
        @endif
        
        <p style="margin-top: 50px;">
            **Período do Relatório:** Últimos 6 meses <br>
            **Documento Gerado em:** {{ now()->format('d/m/Y H:i') }}
        </p>
    </div>
    <div class="page-break"></div>
    
    {{-- 1. Totais Consolidados --}}
    <h2>1. Totais Consolidados (Últimos 6 Meses)</h2>
    <table class="summary-table">
        <thead>
            <tr>
                <th>Eventos Criados</th>
                <th>Posts Criados</th>
                <th>Curtidas Totais</th>
                <th>Comentários Eventos</th>
                <th>Eventos Salvos</th>
                <th>Cursos Ativos</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>{{ $eventsCount }}</td>
                <td>{{ $postsCount }}</td>
                <td>{{ $likesCount }}</td>
                <td>{{ $commentsCount }}</td>
                <td>{{ $savedEventsCount }}</td>
                <td>{{ $coursesCount }}</td> 
            </tr>
        </tbody>
    </table>

    {{-- 2. Totais Globais --}}
    <h2>2. Totais Globais da Plataforma (Acumulado)</h2>
    <table class="summary-table">
        <thead>
            <tr>
                <th>Cursos</th>
                <th>Eventos</th>
                <th>Posts</th>
                <th>Comentários (Eventos)</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>{{ App\Models\Course::count() }}</td>
                <td>{{ App\Models\Event::count() }}</td>
                <td>{{ App\Models\Post::count() }}</td>
                <td>{{ App\Models\Comment::count() }}</td>
            </tr>
        </tbody>
    </table>
    
    <div class="page-break"></div>

    {{-- 3. Evolução Mensal --}}
    <h2>3. Evolução Mensal de Atividades (Últimos 6 Meses)</h2>

    <div class="chart-box">
        <h3>Interações de Eventos (Curtidas/Comentários)</h3>
        @if(isset($chartImages['interactionsChartImage']) && $chartImages['interactionsChartImage'])
            <img src="{{ $chartImages['interactionsChartImage'] }}" alt="Gráfico de Interações Mensais">
        @else
            <p>Gráfico de Interações não disponível.</p>
        @endif
    </div>
    
    <div class="chart-box">
        <h3>Atividade em Posts (Posts e Respostas)</h3>
        @if(isset($chartImages['postInteractionsChartImage']) && $chartImages['postInteractionsChartImage'])
            <img src="{{ $chartImages['postInteractionsChartImage'] }}" alt="Gráfico de Posts e Respostas">
        @else
            <p>Gráfico de Posts e Respostas não disponível.</p>
        @endif
    </div>
    
    <div class="clear"></div>
    
    {{-- 4. Rankings --}}
    <h2>4. Rankings (Últimos 6 Meses)</h2>

    <div class="chart-box full-width">
        <h3>Ranking de Cursos por Eventos Criados</h3>
        @if(isset($chartImages['coursesChartImage']) && $chartImages['coursesChartImage'])
            <img src="{{ $chartImages['coursesChartImage'] }}" alt="Gráfico de Ranking de Cursos">
        @else
            <p>Gráfico de Ranking de Cursos não disponível.</p>
        @endif
    </div>
    <div class="clear"></div>

    <div class="ranking-container">
        <div class="ranking-col">
            <h3>Top Coordenadores (Mais Eventos)</h3>
            <table class="ranking-table">
                <thead>
                    <tr>
                        <th style="width: 70%;">Nome do Coordenador</th>
                        <th style="width: 30%; text-align: right;">Eventos</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($topCoordinators as $coordinator)
                        @if($coordinator->eventCoordinator && $coordinator->eventCoordinator->userAccount)
                            <tr>
                                <td>{{ $coordinator->eventCoordinator->userAccount->name }}</td>
                                <td>{{ $coordinator->events_count }}</td>
                            </tr>
                        @endif
                    @empty
                        <tr><td colspan="2">Nenhum coordenador encontrado.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <div class="ranking-col last">
            <h3>Top Eventos do Mês</h3>
            <table class="ranking-table">
                <thead>
                    <tr>
                        <th style="width: 70%;">Evento</th>
                        <th style="width: 30%; text-align: right;">Interações</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($topEventsOfTheMonth as $event)
                        <tr>
                            <td>{{ $event->event_name }}</td>
                            <td>{{ $event->total_interactions }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="2">Nenhum evento encontrado.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="clear"></div>
    </div>

</body>
</html>
