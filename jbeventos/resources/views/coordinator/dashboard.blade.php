<x-app-layout>
    <div class="py-6 max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

        {{-- Boas-vindas e Botão de Exportar PDF --}}
        <div class="p-5 bg-white rounded-2xl shadow-sm flex flex-col sm:flex-row sm:justify-between sm:items-center space-y-4 sm:space-y-0">
            <div>
                <h2 class="text-2xl font-bold font-ubuntu text-gray-800">Olá, {{ $name }}!</h2>
                <p class="text-gray-600 mt-1">{{ $message }}</p>
            </div>
            
            {{-- Botão Exportar para PDF: Agora submete o formulário oculto --}}
            <button type="button" id="submitExportButton" class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-500 active:bg-red-700 focus:outline-none focus:border-red-700 focus:ring focus:ring-red-300 disabled:opacity-25 transition">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                Gerar PDF (6 Últimos Meses)
            </button>
        </div>

        {{-- Cards de Resumo (5 CARDS) --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-6">

            {{-- Eventos Criados --}}
            <div class="p-4 bg-white rounded-xl shadow-sm border border-gray-200 relative transform hover:scale-[1.02] transition-transform duration-200">
                <div class="flex items-center space-x-3">
                    <div class="p-2 bg-blue-100 rounded-full flex items-center justify-center text-blue-600">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h.01M12 11h.01M15 11h.01M7 15h.01M11 15h.01M15 15h.01M4 20h16a2 2 0 002-2V6a2 2 0 00-2-2H4a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-sm font-medium text-gray-600">Eventos Criados por Você</h3>
                        <p class="text-3xl font-bold text-gray-800 mt-1">{{ $eventsCount }}</p>
                    </div>
                </div>
                <div class="mt-2 h-10 w-full">
                    <canvas id="eventsSparkline"></canvas>
                </div>
            </div>

            {{-- Posts Criados --}}
            <div class="p-4 bg-white rounded-xl shadow-sm border border-gray-200 relative transform hover:scale-[1.02] transition-transform duration-200">
                <div class="flex items-center space-x-3">
                    <div class="p-2 bg-yellow-100 rounded-full flex items-center justify-center text-yellow-600">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-sm font-medium text-gray-600">Posts Criados por Você</h3>
                        <p class="text-3xl font-bold text-gray-800 mt-1">{{ $postsCount }}</p>
                    </div>
                </div>
                <div class="mt-2 h-10 w-full">
                    <canvas id="postsSparkline"></canvas>
                </div>
            </div>

            {{-- Curtidas --}}
            <div class="p-4 bg-white rounded-xl shadow-sm border border-gray-200 relative transform hover:scale-[1.02] transition-transform duration-200">
                <div class="flex items-center space-x-3">
                    <div class="p-2 bg-green-100 rounded-full flex items-center justify-center text-green-600">
                        <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-sm font-medium text-gray-600">Curtidas em Seus Eventos</h3>
                        <p class="text-3xl font-bold text-gray-800 mt-1">{{ $likes }}</p>
                    </div>
                </div>
                <div class="mt-2 h-10 w-full">
                    <canvas id="likesSparkline"></canvas>
                </div>
            </div>

            {{-- Comentários --}}
            <div class="p-4 bg-white rounded-xl shadow-sm border border-gray-200 relative transform hover:scale-[1.02] transition-transform duration-200">
                <div class="flex items-center space-x-3">
                    <div class="p-2 bg-purple-100 rounded-full flex items-center justify-center text-purple-600">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M8 10h.01M12 10h.01M16 10h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-sm font-medium text-gray-600">Comentários em seus Eventos</h3>
                        <p class="text-3xl font-bold text-gray-800 mt-1">{{ $comments }}</p>
                    </div>
                </div>
                <div class="mt-2 h-10 w-full">
                    <canvas id="commentsSparkline"></canvas>
                </div>
            </div>

            {{-- Eventos Salvos --}}
            <div class="p-4 bg-white rounded-xl shadow-sm border border-gray-200 relative transform hover:scale-[1.02] transition-transform duration-200">
                <div class="flex items-center space-x-3">
                    <div class="p-2 bg-pink-100 rounded-full flex items-center justify-center text-pink-600">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-sm font-medium text-gray-600">Qtd. de Salvamentos em Seus Eventos</h3>
                        <p class="text-3xl font-bold text-gray-800 mt-1">{{ $saves }}</p>
                    </div>
                </div>
                <div class="mt-2 h-10 w-full">
                    <canvas id="savesSparkline"></canvas>
                </div>
            </div>
        </div>

        {{-- Gráficos Principais --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            
            {{-- Engajamento Mês a Mês (Eventos) --}}
            <div class="p-5 bg-white rounded-2xl shadow-sm">
                <h3 class="text-lg font-semibold text-gray-700 mb-3 truncate">Evolução de Engajamento em Eventos (6M)</h3>
                <div class="relative h-64">
                    <canvas id="engagementChart"></canvas>
                </div>
            </div>

            {{-- Interações em Posts --}}
            <div class="p-5 bg-white rounded-2xl shadow-sm">
                <h3 class="text-lg font-semibold text-gray-700 mb-3 truncate">Interações nos Seus Posts (6M)</h3>
                <div class="relative h-64">
                    <canvas id="postInteractionsChart"></canvas>
                </div>
            </div>
        </div>
        
        {{-- Canvas invisível para gerar o Gráfico de Publicações (necessário para o PDF) --}}
        <div style="width: 600px; height: 300px; position: absolute; left: -9999px; visibility: hidden;">
            <canvas id="publicationsChartCanvas"></canvas>
        </div>
        
        {{-- Ranking de Engajamento e Distribuição --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

            {{-- Top Eventos --}}
            <div class="p-5 bg-white rounded-2xl shadow-sm">
                <h3 class="text-lg font-semibold text-gray-700 mb-3">Seus Top Eventos por Engajamento</h3>
                @if($topEvents->isEmpty())
                    <p class="text-gray-500">Nenhum evento com interações ainda.</p>
                @else
                    <ul class="divide-y divide-gray-200">
                        @foreach($topEvents as $event)
                            <li class="flex justify-between items-center py-3">
                                <a href="#" class="font-medium text-gray-700 hover:text-blue-600 transition duration-150 ease-in-out truncate max-w-[70%]">
                                    {{ $event->event_name }}
                                </a>
                                <span class="text-gray-500 font-semibold text-sm bg-gray-100 px-2 py-1 rounded-full">{{ $event->total_engagement }} interações</span>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>

            {{-- Distribuição Interações --}}
            <div class="p-5 bg-white rounded-2xl shadow-sm">
                <h3 class="text-lg font-semibold text-gray-700 mb-3">Distribuição das Interações em Eventos</h3>
                <div class="relative h-64 flex justify-center items-center">
                    <canvas id="distributionChart"></canvas>
                </div>
            </div>
        </div>
        
        {{-- ********************************************************* --}}
        {{-- FORMULÁRIO OCULTO PARA EXPORTAÇÃO PDF (SIMPLIFICADO) --}}
        {{-- ********************************************************* --}}
        <form method="POST" action="{{ route('coordinator.dashboard.export.pdf') }}" id="exportForm" class="hidden">
            @csrf
            {{-- CAMPOS HIDDEN PARA AS IMAGENS BASE64 DOS GRÁFICOS --}}
            <input type="hidden" name="eventEngagementChartImage" id="eventEngagementChartImage">
            <input type="hidden" name="publicationsChartImage" id="publicationsChartImage">
            <input type="hidden" name="postInteractionsChartImage" id="postInteractionsChartImage">
        </form>

    </div>

    {{-- Scripts Chart.js e Lógica do Modal --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            
            // Variáveis de dados do PHP
            const labels = @json($labels);
            const eventsByMonth = @json($eventsByMonth);
            const postsValues = @json($postsValues);
            const likesByMonth = @json($likesByMonth);
            const commentsByMonth = @json($commentsByMonth);
            const savesByMonth = @json($savesByMonth);
            const eventEngagementValues = @json($eventEngagementValues);
            const postInteractionsValues = @json($postInteractionsValues);
            const totalLikes = {{ $likes }};
            const totalComments = {{ $comments }};
            const totalSaves = {{ $saves }};

            let charts = {}; // Objeto para armazenar as instâncias dos gráficos

            // Funções de Criação de Gráficos (mantidas)
            function createSparkline(elementId, data, color) {
                charts[elementId] = new Chart(document.getElementById(elementId), {
                    type: 'line',
                    data: {
                        labels: labels,
                        datasets: [{ 
                            data: data, 
                            borderColor: color, 
                            borderWidth: 2, 
                            fill: false, 
                            tension: 0.4, 
                            pointRadius: 0 
                        }]
                    },
                    options: { 
                        responsive: true, 
                        maintainAspectRatio: false, 
                        plugins: { 
                            legend: { display: false }, 
                            tooltip: { enabled: false } 
                        }, 
                        scales: { 
                            x: { display: false }, 
                            y: { display: false } 
                        } 
                    }
                });
            }
            
            function createChart(elementId, config) {
                 charts[elementId] = new Chart(document.getElementById(elementId), config);
                 return charts[elementId];
            }
            
            // 1. Sparklines dos Cards
            createSparkline('eventsSparkline', eventsByMonth, 'rgb(59, 130, 246)');
            createSparkline('postsSparkline', postsValues, 'rgb(245, 158, 11)');
            createSparkline('likesSparkline', likesByMonth, 'rgb(34, 197, 94)');
            createSparkline('commentsSparkline', commentsByMonth, 'rgb(139, 92, 246)');
            createSparkline('savesSparkline', savesByMonth, 'rgb(236, 72, 153)');

            // 2. Gráfico principal de Engajamento (Eventos)
            createChart('engagementChart', {
                type: 'bar',
                data: { 
                    labels: labels, 
                    datasets: [{ 
                        label: 'Interações', 
                        data: eventEngagementValues, 
                        backgroundColor: 'rgba(59, 130, 246, 0.7)', 
                        borderColor: 'rgba(59, 130, 246, 1)', 
                        borderWidth: 1, 
                        borderRadius: 6 
                    }] 
                },
                options: { 
                    responsive: true, 
                    maintainAspectRatio: false, 
                    scales: { 
                        y: { beginAtZero: true } 
                    } 
                }
            });

            // 3. Gráfico de Interações em Posts
            createChart('postInteractionsChart', {
                type: 'line',
                data: { 
                    labels: labels, 
                    datasets: [{ 
                        label: 'Respostas em Posts', 
                        data: postInteractionsValues, 
                        borderColor: 'rgba(245, 158, 11, 1)', 
                        backgroundColor: 'rgba(245, 158, 11, 0.2)',
                        fill: true,
                        tension: 0.4
                    }] 
                },
                options: { 
                    responsive: true, 
                    maintainAspectRatio: false, 
                    scales: { 
                        y: { beginAtZero: true } 
                    } 
                }
            });
            
            // 4. Gráfico de Distribuição (Eventos)
            createChart('distributionChart', {
                type: 'pie',
                data: {
                    labels: ['Curtidas', 'Comentários', 'Salvos'],
                    datasets: [{
                        data: [totalLikes, totalComments, totalSaves],
                        backgroundColor: [
                            'rgba(34, 197, 94, 0.7)', 
                            'rgba(139, 92, 246, 0.7)',
                            'rgba(236, 72, 153, 0.7)'
                        ],
                        borderColor: [
                            'rgba(34, 197, 94, 1)',
                            'rgba(139, 92, 246, 1)',
                            'rgba(236, 72, 153, 1)'
                        ],
                        borderWidth: 1
                    }]
                },
                options: { 
                    responsive: true, 
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'right', 
                            labels: {
                                usePointStyle: true,
                                padding: 20
                            }
                        }
                    }
                }
            });
            
            // 5. Gráfico de Publicações (para o PDF)
            createChart('publicationsChartCanvas', {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [
                        {
                            label: 'Eventos Criados',
                            data: eventsByMonth,
                            borderColor: 'rgb(59, 130, 246)',
                            backgroundColor: 'rgba(59, 130, 246, 0.1)',
                            fill: true,
                            tension: 0.4,
                        },
                        {
                            label: 'Posts Criados',
                            data: postsValues,
                            borderColor: 'rgb(245, 158, 11)',
                            backgroundColor: 'rgba(245, 158, 11, 0.1)',
                            fill: true,
                            tension: 0.4,
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: { beginAtZero: true }
                    }
                }
            });


            // *********************************************************
            // LÓGICA DE EXPORTAÇÃO PDF SIMPLIFICADA (SEM MODAL/FILTRO)
            // *********************************************************
            
            const submitButton = document.getElementById('submitExportButton');
            const exportForm = document.getElementById('exportForm');

            // Função Auxiliar para Capturar Base64
            const captureChartImage = (chartId, hiddenInputId) => {
                const chart = charts[chartId];
                if (chart) {
                    // Garante que o gráfico está com o tamanho correto antes de capturar
                    chart.resize(); 
                    chart.update();
                    const dataURL = chart.toBase64Image('image/png', 1.0); 
                    document.getElementById(hiddenInputId).value = dataURL;
                }
            };

            // Evento no botão Gerar PDF
            submitButton.addEventListener('click', () => {
                
                // 1. Capturar os gráficos (eles já refletem o padrão de 6 meses)
                captureChartImage('engagementChart', 'eventEngagementChartImage');
                captureChartImage('publicationsChartCanvas', 'publicationsChartImage');
                captureChartImage('postInteractionsChart', 'postInteractionsChartImage');
                
                // 2. Submete o formulário. O Controller gera os dados dos 6 meses.
                exportForm.submit();
            });

        });
    </script>
</x-app-layout>