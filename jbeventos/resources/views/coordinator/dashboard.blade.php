<x-app-layout>
    <div class="py-6 max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

        {{-- Boas-vindas e Botão de Exportar PDF --}}
        <div class="p-5 bg-white rounded-2xl shadow-sm flex flex-col sm:flex-row sm:justify-between sm:items-center space-y-4 sm:space-y-0">
            <div>
                <h2 class="text-2xl font-bold font-ubuntu text-gray-800">Olá, {{ $name }}!</h2>
                <p class="text-gray-600 mt-1">{{ $message }}</p>
            </div>
            
            {{-- Botão Exportar para PDF: Agora abre o modal --}}
            <button type="button" id="openExportModalButton" class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-500 active:bg-red-700 focus:outline-none focus:border-red-700 focus:ring focus:ring-red-300 disabled:opacity-25 transition">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                Exportar para PDF
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
                        <h3 class="text-sm font-medium text-gray-600">Qtd. de salvamentos em seus Eventos</h3>
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

    </div>

    {{-- ********************************************************* --}}
    {{-- MODAL DE FILTRO DE DATAS PARA EXPORTAÇÃO PDF --}}
    {{-- ********************************************************* --}}
    <div id="dateFilterModal" class="fixed inset-0 z-50 overflow-y-auto bg-gray-900 bg-opacity-75 hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <form method="POST" action="{{ route('coordinator.dashboard.export.pdf') }}" id="exportForm">
                    @csrf
                    
                    {{-- CAMPOS HIDDEN PARA AS IMAGENS BASE64 DOS GRÁFICOS (mantidos aqui) --}}
                    <input type="hidden" name="eventEngagementChartImage" id="eventEngagementChartImage">
                    <input type="hidden" name="publicationsChartImage" id="publicationsChartImage">
                    <input type="hidden" name="postInteractionsChartImage" id="postInteractionsChartImage">
                    
                    {{-- CAMPOS HIDDEN PARA AS DATAS DE FILTRO --}}
                    <input type="hidden" name="start_date" id="exportStartDate">
                    <input type="hidden" name="end_date" id="exportEndDate">
                    
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start">
                            <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                                <svg class="h-6 w-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h.01M12 11h.01M15 11h.01M7 15h.01M11 15h.01M15 15h.01M4 20h16a2 2 0 002-2V6a2 2 0 00-2-2H4a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                            </div>
                            <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                                <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                                    Filtrar Relatório PDF por Data
                                </h3>
                                <div class="mt-2">
                                    <p class="text-sm text-gray-500">
                                        Selecione o período de início e fim para a captura dos dados no seu relatório de desempenho.
                                    </p>
                                    
                                    <div class="mt-4 grid grid-cols-2 gap-4">
                                        <div>
                                            <label for="modal_start_date" class="block text-sm font-medium text-gray-700">Data Inicial</label>
                                            <input type="date" id="modal_start_date" name="modal_start_date" class="mt-1 focus:ring-red-500 focus:border-red-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" required>
                                        </div>
                                        <div>
                                            <label for="modal_end_date" class="block text-sm font-medium text-gray-700">Data Final</label>
                                            <input type="date" id="modal_end_date" name="modal_end_date" class="mt-1 focus:ring-red-500 focus:border-red-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" required>
                                        </div>
                                    </div>
                                    <p id="dateError" class="text-red-500 text-sm mt-2 hidden">A Data Inicial não pode ser maior que a Data Final.</p>

                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button type="submit" id="submitExportButton" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto sm:text-sm transition ease-in-out duration-150">
                            Gerar PDF
                        </button>
                        <button type="button" id="closeModalButton" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                            Cancelar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>


    {{-- Scripts Chart.js e Lógica do Modal --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            
            // Variáveis de dados do PHP (mantidas para os gráficos do dashboard)
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
            
            // 1. Sparklines dos Cards (apenas visualização no Dashboard)
            createSparkline('eventsSparkline', eventsByMonth, 'rgb(59, 130, 246)');
            createSparkline('postsSparkline', postsValues, 'rgb(245, 158, 11)');
            createSparkline('likesSparkline', likesByMonth, 'rgb(34, 197, 94)');
            createSparkline('commentsSparkline', commentsByMonth, 'rgb(139, 92, 246)');
            createSparkline('savesSparkline', savesByMonth, 'rgb(236, 72, 153)');

            // 2. Gráfico principal de Engajamento (Eventos) - PDF: eventEngagementChartImage
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

            // 3. Gráfico de Interações em Posts (NOVO GRÁFICO) - PDF: postInteractionsChartImage
            createChart('postInteractionsChart', {
                type: 'line',
                data: { 
                    labels: labels, 
                    datasets: [{ 
                        label: 'Respostas em Posts', 
                        data: postInteractionsValues, 
                        borderColor: 'rgba(245, 158, 11, 1)', // Laranja/Amarelo
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
            
            // 4. Gráfico de Distribuição (Eventos) - Não exportado individualmente, mas criado
            createChart('distributionChart', {
                type: 'pie',
                data: {
                    labels: ['Curtidas', 'Comentários', 'Salvos'],
                    datasets: [{
                        data: [totalLikes, totalComments, totalSaves],
                        backgroundColor: [
                            'rgba(34, 197, 94, 0.7)', // Green - Likes
                            'rgba(139, 92, 246, 0.7)', // Purple - Comments
                            'rgba(236, 72, 153, 0.7)' // Pink - Saves
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
            
            // 5. Gráfico de Publicações (Eventos e Posts Criados) - Necessário para o PDF (Sec. 2.2)
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
            // LÓGICA DO MODAL E EXPORTAÇÃO PDF (NOVA)
            // *********************************************************
            
            const modal = document.getElementById('dateFilterModal');
            const openButton = document.getElementById('openExportModalButton');
            const closeButton = document.getElementById('closeModalButton');
            const submitButton = document.getElementById('submitExportButton');
            const exportForm = document.getElementById('exportForm');
            const modalStartDateInput = document.getElementById('modal_start_date');
            const modalEndDateInput = document.getElementById('modal_end_date');
            const exportStartDateHidden = document.getElementById('exportStartDate');
            const exportEndDateHidden = document.getElementById('exportEndDate');
            const dateError = document.getElementById('dateError');

            // 1. Abrir Modal
            openButton.addEventListener('click', () => {
                modal.classList.remove('hidden');
                // Opcional: Pré-preencher as datas com o range padrão atual
                // Não é necessário pois o PDF já tem o range de 6 meses como fallback
            });

            // 2. Fechar Modal
            closeButton.addEventListener('click', () => {
                modal.classList.add('hidden');
            });
            
            // Fechar ao clicar fora
            modal.addEventListener('click', (e) => {
                if (e.target === modal) {
                    modal.classList.add('hidden');
                }
            });


            // 3. Função Auxiliar para Capturar Base64
            const captureChartImage = (chartId, hiddenInputId) => {
                const chart = charts[chartId];
                if (chart) {
                    chart.resize(); 
                    chart.update();
                    const dataURL = chart.toBase64Image('image/png', 1.0); 
                    document.getElementById(hiddenInputId).value = dataURL;
                }
            };

            // 4. Submeter Formulário (dentro do modal)
            exportForm.addEventListener('submit', function(e) {
                e.preventDefault(); 
                
                const startDateValue = modalStartDateInput.value;
                const endDateValue = modalEndDateInput.value;

                if (!startDateValue || !endDateValue) {
                    alert("Por favor, selecione a Data Inicial e a Data Final.");
                    return;
                }

                if (new Date(startDateValue) > new Date(endDateValue)) {
                    dateError.classList.remove('hidden');
                    return;
                } else {
                    dateError.classList.add('hidden');
                }
                
                // 4.1. Inserir as datas no formulário (campos hidden)
                exportStartDateHidden.value = startDateValue;
                exportEndDateHidden.value = endDateValue;

                // 4.2. Capturar os gráficos
                captureChartImage('engagementChart', 'eventEngagementChartImage');
                captureChartImage('publicationsChartCanvas', 'publicationsChartImage');
                captureChartImage('postInteractionsChart', 'postInteractionsChartImage');
                
                // 4.3. Submete o formulário com os dados Base64 e as datas
                this.submit();
            });

        });
    </script>
</x-app-layout>