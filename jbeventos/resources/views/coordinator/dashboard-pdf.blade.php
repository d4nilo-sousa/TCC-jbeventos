<!DOCTYPE html>

<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Relatório de Desempenho do Coordenador</title>
    <style>
        /* Cor Principal: #C0392B (Vermelho Vinho) */
        
        body {
            font-family: sans-serif;
            margin: 30px;
            font-size: 12px;
            color: #333;
        }
        
        /* Quebra de Página */
        .page-break {
            page-break-after: always;
        }

        /* Estilo da CAPA */
        .cover-page {
            text-align: center;
            margin-top: 150px;
            padding: 50px;
            height: 600px;
        }
        .cover-page h1 {
            font-size: 32px;
            color: #1f2937;
            margin-bottom: 10px;
        }
        .cover-page p {
            font-size: 18px;
            color: #4b5563;
            margin-top: 5px;
        }

        /* Estilo do Logo (Usa Base64 ou URL) */
        .logo {
            max-width: 350px;
            height: auto;
            margin: 50px auto;
            display: block;
        }

        /* Títulos Padrão de Seção (Conteúdo após a Capa) */
        h2 {
            font-size: 18px;
            color: #1f2937;
            margin-top: 30px;
            margin-bottom: 15px;
            border-left: 5px solid #C0392B; /* Cor Vermelha Vinho */
            padding-left: 10px;
        }
        h3 {
            font-size: 14px;
            color: #4b5563;
            margin-top: 15px;
            margin-bottom: 10px;
        }
        
        /* Tabelas Resumo (Cards) */
        .summary-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            page-break-inside: avoid;
        }
        .summary-table th, .summary-table td {
            border: 1px solid #e5e7eb;
            padding: 10px 15px;
            text-align: center;
        }
        .summary-table th {
            background-color: #f3f4f6;
            color: #1f2937;
            font-weight: bold;
        }
        
        /* Tabela de Rankings/Listas */
        .ranking-table {
            width: 100%; 
            border-collapse: collapse; 
            margin-top: 5px;
            font-size: 12px;
        }
        .ranking-table th, .ranking-table td {
            border: 1px solid #e5e7eb;
            padding: 8px 10px;
            text-align: left;
        }
        .ranking-table th {
            background-color: #f3f4f6;
            color: #1f2937;
            font-weight: bold;
        }
        
        /* Gráficos */
        .chart-box {
            width: 100%;
            margin-bottom: 30px;
            padding: 10px;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            box-sizing: border-box;
            display: block;
            page-break-inside: avoid;
        }
        .chart-box img {
            width: 100%;
            height: auto;
            /* Altura máxima para melhor distribuição no PDF */
            max-height: 350px; 
            display: block;
        }

        /* Limpeza de floats */
        .clear {
            clear: both;
        }

        /* Layout de Gráficos em Colunas (2 por linha) */
        /* Mantive este estilo caso haja a necessidade de usá-lo para outros gráficos,
           mas não será mais aplicado aos dois primeiros. */
        .chart-container {
            width: 100%;
            margin-top: 15px;
            /* Usar overflow: hidden para conter os floats */
            overflow: hidden; 
            page-break-inside: avoid;
        }
        .chart-col {
            width: 48%;
            float: left;
            margin-right: 4%;
            box-sizing: border-box;
            page-break-inside: avoid;
        }
        .chart-col.last {
            float: right;
            margin-right: 0;
        }
        
        /* Rodapé */
        .footer {
            margin-top: 50px;
            text-align: right;
            font-size: 8pt;
            color: #6B7280;
        }
    </style>

</head>
<body>

    {{-- ********************************************************* --}}
    {{-- CAPA DO RELATÓRIO (PÁGINA 1) --}}
    {{-- ********************************************************* --}}
    <div class="cover-page">
        <h1>Relatório de Desempenho do Coordenador</h1>
        <p>
            Desempenho e Produtividade do Coordenador(a): **{{ $userName }}**
        </p>

        @if (!empty($logoBase64))
            <img src="{{ $logoBase64 }}" class="logo" alt="Logo do Sistema">
        @else
            <p style="color: red;">Logo não carregada (Variável $logoBase64 vazia).</p>
        @endif
        
        <p style="margin-top: 50px;">
            Período de Análise: {{ $reportStartDate ?? 'N/A' }} a {{ $reportEndDate ?? 'N/A' }}
            <br>
            Documento Gerado em: {{ now()->format('d/m/Y H:i') }}
        </p>
    </div>
    <div class="page-break"></div> {{-- FORÇA QUEBRA DE PÁGINA --}}
    
    {{-- ********************************************************* --}}
    {{-- 1. RESUMO GERAL --}}
    {{-- ********************************************************* --}}
    <h2>1. Resumo Geral de Atividades</h2>
    <table class="summary-table">
        <thead>
            <tr>
                <th>Eventos Criados</th>
                <th>Posts Criados</th>
                <th>Total de Curtidas (Eventos)</th>
                <th>Total de Comentários (Eventos)</th>
                <th>Total de Salvamentos (Eventos)</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>{{ $eventsCount }}</td>
                <td>{{ $postsCount }}</td>
                <td>{{ $likes }}</td>
                <td>{{ $comments }}</td>
                <td>{{ $saves }}</td>
            </tr>
        </tbody>
    </table>
    
    {{-- ********************************************************* --}}
    {{-- 2. EVOLUÇÃO MENSAL (GRÁFICOS) --}}
    {{-- ********************************************************* --}}
    <h2>2. Evolução Mensal de Atividades (Últimos 6 Meses)</h2>

    {{-- GRÁFICO 2.1: INTERAÇÕES DE EVENTOS (AGORA EM LINHA ÚNICA) --}}
    <div class="chart-box">
        <h3>Interações de Eventos (Curtidas, Comentários, Salvos)</h3>
        @if(isset($chartImages['eventEngagementChartImage']) && $chartImages['eventEngagementChartImage'])
            <img src="{{ $chartImages['eventEngagementChartImage'] }}" alt="Gráfico de Engajamento Mensal em Eventos">
        @else
            <p style="color: #999;">Gráfico de Engajamento em Eventos não disponível.</p>
        @endif
    </div>
    
    {{-- GRÁFICO 2.2: EVENTOS E POSTS CRIADOS (AGORA EM NOVA LINHA ÚNICA) --}}
    <div class="chart-box">
        <h3>Publicações (Eventos e Posts Criados)</h3>
        @if(isset($chartImages['publicationsChartImage']) && $chartImages['publicationsChartImage'])
            <img src="{{ $chartImages['publicationsChartImage'] }}" alt="Gráfico de Eventos e Posts Criados">
        @else
            <p style="color: #999;">Gráfico de Publicações não disponível.</p>
        @endif
    </div>
    
    {{-- GRÁFICO 2.3: INTERAÇÕES DE POSTS (Já estava em linha inteira) --}}
    <div class="chart-box" style="margin-top: 20px;">
        <h3>Interações de Posts (Respostas Recebidas)</h3>
        @if(isset($chartImages['postInteractionsChartImage']) && $chartImages['postInteractionsChartImage'])
            <img src="{{ $chartImages['postInteractionsChartImage'] }}" alt="Gráfico de Interações em Posts">
        @else
            <p style="color: #999;">Gráfico de Interações em Posts não disponível.</p>
        @endif
    </div>
    
    <div class="page-break"></div> {{-- Quebra de página para começar os rankings --}}
    
    {{-- ********************************************************* --}}
    {{-- 3. TOP EVENTOS MAIS ENGAJADOS --}}
    {{-- ********************************************************* --}}
    <h2>3. Top Eventos Mais Engajados</h2>
    <p style="color: #4b5563; margin-bottom: 10px;">Eventos criados pelo(a) coordenador(a) com maior engajamento (Curtidas + Comentários + Salvos).</p>
    
    @if($topEvents->isEmpty())
        Nenhum evento com interações registrado no período.
    @else
    <table class="ranking-table">
        <thead>
            <tr>
                <th>Nome do Evento</th>
                <th style="text-align: center;">Curtidas</th>
                <th style="text-align: center;">Comentários</th>
                <th style="text-align: center;">Salvos</th>
                <th style="text-align: center;">Engajamento Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($topEvents as $event)
                <tr>
                    <td>{{ $event->event_name }}</td>
                    <td style="text-align: center;">{{ $event->likes_count ?? 0 }}</td>
                    <td style="text-align: center;">{{ $event->event_comments_count ?? 0 }}</td>
                    <td style="text-align: center;">{{ $event->saves_count ?? 0 }}</td>
                    <td style="text-align: center; font-weight: bold; background-color: #f0f8ff;">{{ $event->total_engagement }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endif

<div class="footer">
    Relatório confidencial. Uso interno da plataforma.
    <br>
    Gerado em: {{ now()->format('d/m/Y H:i:s') }}
</div>

</body>
</html>
