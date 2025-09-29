<x-app-layout>
    <div class="py-6 max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

        {{-- Boas-vindas --}}
        <div class="p-5 bg-white rounded-2xl shadow-sm">
            <h2 class="text-2xl font-bold font-ubuntu text-gray-800">Olá, {{ $name }}!</h2>
            <p class="text-gray-600 mt-1">{{ $message }}</p>
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
                        <h3 class="text-sm font-medium text-gray-600">Eventos Criados por Você</h3>
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
                        <h3 class="text-sm font-medium text-gray-600">Posts Criados por Você</h3>
                        <p class="text-3xl font-bold text-gray-800 mt-1">{{ $postsCount }}</p>
                    </div>
                </div>
                {{-- REMOVIDO: Tendência de Posts (Comparativo Mês Anterior) --}}
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

    {{-- Scripts Chart.js --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Função para criar Sparklines
        function createSparkline(elementId, data, color) {
            new Chart(document.getElementById(elementId), {
                type: 'line',
                data: {
                    labels: @json($labels),
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

        // 1. Sparklines dos Cards
        createSparkline('eventsSparkline', @json($eventsByMonth), 'rgb(59, 130, 246)');
        createSparkline('postsSparkline', @json($postsValues), 'rgb(245, 158, 11)'); // Sparkline para Posts
        createSparkline('likesSparkline', @json($likesByMonth), 'rgb(34, 197, 94)');
        createSparkline('commentsSparkline', @json($commentsByMonth), 'rgb(139, 92, 246)');
        createSparkline('savesSparkline', @json($savesByMonth), 'rgb(236, 72, 153)');

        // 2. Gráfico principal de Engajamento (Eventos)
        const engagementCtx = document.getElementById('engagementChart').getContext('2d');
        new Chart(engagementCtx, {
            type: 'bar',
            data: { 
                labels: @json($labels), 
                datasets: [{ 
                    label: 'Interações', 
                    data: @json($eventEngagementValues), 
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

        // 3. Gráfico de Interações em Posts (NOVO GRÁFICO)
        const postInteractionsCtx = document.getElementById('postInteractionsChart').getContext('2d');
        new Chart(postInteractionsCtx, {
            type: 'line',
            data: { 
                labels: @json($labels), 
                datasets: [{ 
                    label: 'Respostas em Posts', 
                    data: @json($postInteractionsValues), 
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

        // 4. Gráfico de Distribuição (Eventos)
        const distributionCtx = document.getElementById('distributionChart').getContext('2d');
        new Chart(distributionCtx, {
            type: 'pie',
            data: {
                labels: ['Curtidas', 'Comentários', 'Salvos'],
                datasets: [{
                    data: [{{ $likes }}, {{ $comments }}, {{ $saves }}],
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
    </script>
</x-app-layout>
