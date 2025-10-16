<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <title>Relatório de Desempenho do Coordenador</title>
    <style>
        /* Paleta: #C0392B (vermelho vinho), #1f2937 (cinza escuro), #f3f4f6 (cinza claro) */
        body {
            font-family: DejaVu Sans, sans-serif;
            margin: 35px;
            font-size: 12px;
            color: #2d3748;
            line-height: 1.5;
        }

        .page-break {
            page-break-after: always;
        }

        /* ---------- CAPA ---------- */
        .cover-page {
            text-align: center;
            padding-top: 180px;
        }

        .cover-page h1 {
            font-size: 30px;
            color: #1f2937;
            margin-bottom: 15px;
            letter-spacing: 0.5px;
        }

        .cover-page p {
            font-size: 16px;
            color: #4b5563;
            margin: 6px 0;
        }

        .logo {
            max-width: 300px;
            margin: 60px auto 40px;
            display: block;
        }

        .cover-meta {
            margin-top: 40px;
            font-size: 13px;
            color: #374151;
            line-height: 1.6;
        }

        /* ---------- SEÇÕES ---------- */
        h2 {
            font-size: 17px;
            color: #1f2937;
            margin-top: 40px;
            margin-bottom: 15px;
            border-left: 5px solid #C0392B;
            padding-left: 12px;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }

        h3 {
            font-size: 14px;
            color: #374151;
            margin-bottom: 8px;
            font-weight: 600;
        }

        /* ---------- TABELAS ---------- */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 25px;
        }

        th,
        td {
            border: 1px solid #e5e7eb;
            padding: 10px 12px;
            text-align: center;
        }

        th {
            background-color: #f3f4f6;
            color: #1f2937;
            font-weight: bold;
        }

        tbody tr:nth-child(even) {
            background-color: #fafafa;
        }

        /* ---------- GRÁFICOS ---------- */
        .chart-box {
            width: 100%;
            margin: 25px 0;
            padding: 12px;
            border: 1px solid #e5e7eb;
            border-radius: 6px;
            text-align: center;
            page-break-inside: avoid;
        }

        .chart-box img {
            width: 100%;
            height: auto;
            max-height: 340px;
            display: block;
            margin: 8px auto;
        }

        /* ---------- TABELA DE RANKING ---------- */
        .ranking-table th {
            background-color: #f3f4f6;
        }

        .ranking-table td {
            text-align: left;
        }

        .highlight {
            background-color: #fef3f2;
            font-weight: bold;
            color: #b91c1c;
        }

        /* ---------- RODAPÉ ---------- */
        .footer {
            margin-top: 50px;
            text-align: right;
            font-size: 10px;
            color: #6b7280;
            border-top: 1px solid #e5e7eb;
            padding-top: 8px;
        }
    </style>
</head>

<body>

    {{-- CAPA --}}
    <div class="cover-page">
        <h1>Relatório de Desempenho do Coordenador</h1>
        <p>Desempenho estatístico dos eventos criados por:</p>
        <p style="font-size: 18px; font-weight: bold; color: #C0392B;">{{ $userName }}</p>

        @if (!empty($logoBase64))
            <img src="{{ $logoBase64 }}" class="logo" alt="Logo do Sistema">
        @endif

        <div class="cover-meta">
            <p><strong>Análise Padrão:</strong> Últimos 6 meses</p>
            <p><strong>Data de Geração:</strong> {{ now()->format('d/m/Y H:i') }}</p>
        </div>
    </div>

    <div class="page-break"></div>

    {{-- 1. RESUMO GERAL --}}
    <h2>1. Resumo Geral</h2>
    <table>
        <thead>
            <tr>
                <th>Eventos Criados</th>
                <th>Posts Criados</th>
                <th>Curtidas</th>
                <th>Comentários</th>
                <th>Salvamentos</th>
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

    {{-- 2. EVOLUÇÃO MENSAL --}}
    <h2>2. Evolução Mensal de Atividades</h2>

    <div class="chart-box">
        <h3>Engajamento em Eventos</h3>
        @if (!empty($chartImages['eventEngagementChartImage']))
            <img src="{{ $chartImages['eventEngagementChartImage'] }}" alt="Gráfico de Engajamento">
        @else
            <p style="color: #9ca3af;">(Gráfico não disponível)</p>
        @endif
    </div>

    <div class="chart-box">
        <h3>Eventos e Posts Criados</h3>
        @if (!empty($chartImages['publicationsChartImage']))
            <img src="{{ $chartImages['publicationsChartImage'] }}" alt="Gráfico de Publicações">
        @else
            <p style="color: #9ca3af;">(Gráfico não disponível)</p>
        @endif
    </div>

    <div class="chart-box">
        <h3>Interações em Posts</h3>
        @if (!empty($chartImages['postInteractionsChartImage']))
            <img src="{{ $chartImages['postInteractionsChartImage'] }}" alt="Gráfico de Interações">
        @else
            <p style="color: #9ca3af;">(Gráfico não disponível)</p>
        @endif
    </div>

    <div class="page-break"></div>

    {{-- 3. TOP EVENTOS --}}
    <h2>3. Top Eventos Mais Engajados</h2>
    <p style="color: #4b5563; margin-bottom: 10px;">
        Eventos com maior engajamento (Curtidas + Comentários + Salvamentos) nos últimos 6 meses.
    </p>

    @if ($topEvents->isEmpty())
        <p style="color: #9ca3af;">Nenhum evento com interações registrado no período.</p>
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
                @foreach ($topEvents as $event)
                    <tr>
                        <td>{{ $event->event_name }}</td>
                        <td style="text-align: center;">{{ $event->likes_count ?? 0 }}</td>
                        <td style="text-align: center;">{{ $event->event_comments_count ?? 0 }}</td>
                        <td style="text-align: center;">{{ $event->saves_count ?? 0 }}</td>
                        <td class="highlight" style="text-align: center;">
                            {{ $event->total_engagement }}
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    <div class="footer">
        Relatório gerado automaticamente em {{ now()->format('d/m/Y H:i:s') }}
    </div>
</body>
</html>
