<x-app-layout>
    {{-- Mensagem de boas-vindas --}}
<div class="py-10 bg-gray-50 min-h-screen">
    <div class="max-w-[1400px] mx-auto sm:px-6 lg:px-16 space-y-6">
        {{-- Card de Saudação com layout Flexbox e BG suave para destacar --}}
        <div class="p-6 bg-red-50 rounded-xl shadow-lg flex items-center justify-between border border-red-300 overflow-hidden">
            <div class="max-w-xl pr-4">

                {{-- Título --}}
                <h2 class="text-3xl font-extrabold text-red-800 mb-2 leading-snug">
                    Bem-vindo(a) de volta, {{ $name }}! 
                </h2>

                {{-- Descrição do dashboard --}}
                <p class="text-red-700 mt-1 text-base">
                    Este é o seu Painel de Controle de Administrador . Aqui, você acompanha o desempenho geral do sistema e monitora as principais métricas de interações e eventos.
                </p>

                {{-- Botão para exportação --}}
                <form method="POST" action="{{ route('admin.dashboard.export.pdf') }}" id="exportForm" class="mt-4">
                    @csrf
                    {{-- CAMPOS OCULTOS PARA AS IMAGENS DOS GRÁFICOS --}}
                    <input type="hidden" name="interactionsChartImage" id="interactionsChartImage">
                    <input type="hidden" name="postInteractionsChartImage" id="postInteractionsChartImage">
                    <input type="hidden" name="coursesChartImage" id="coursesChartImage">

                    <button type="submit" id="pdfSubmitButton"
                        class="inline-flex items-center px-4 py-2 bg-red-600 hover:bg-red-700 border border-transparent rounded-lg text-sm font-medium text-white uppercase tracking-widest transition shadow-md hover:shadow-lg focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 disabled:opacity-50">

                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                            xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                        </svg>
                        Exportar Relatório em PDF
                    </button>
                </form>
            </div>

            {{-- Imagem de Ilustração para o Dashboard --}}
            <div class="hidden md:block flex-shrink-0">
                <img src="{{ asset('imgs/admin-dashboard.png') }}" alt="Ilustração de Dashboard e Análise de Dados" 
                    class="h-40 w-auto object-contain" /> 
            </div>
        </div>
    </div>

        <div class="max-w-[1400px] mx-auto sm:px-6 lg:px-16 space-y-6 pt-6">

            {{-- Seção de Gráficos (3 GRÁFICOS NA MESMA LINHA) --}}
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">

                {{-- Gráfico Evolução de Posts e Respostas --}}
                <div class="p-5 bg-white rounded-2xl shadow-sm border border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-700 mb-3 truncate">Evolução de Posts e Respostas (6M)
                    </h3>
                    <div class="relative h-64"> {{-- Ajustado para altura mais compacta --}}
                        <canvas id="postInteractionsChart"></canvas>
                    </div>
                </div>

                {{-- Gráfico Evolução de Interações (Likes/Comentários Eventos) --}}
                <div class="p-5 bg-white rounded-2xl shadow-sm border border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-700 mb-3 truncate">Evolução de Interações de Eventos (6M)
                    </h3>
                    <div class="relative h-64"> {{-- Ajustado para altura mais compacta --}}
                        <canvas id="interactionsChart"></canvas>
                    </div>
                </div>

                {{-- Gráfico Ranking de Cursos --}}
                <div class="p-5 bg-white rounded-2xl shadow-sm border border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-700 mb-3 truncate">Ranking de Cursos por Eventos</h3>
                    <div class="relative h-64"> {{-- Ajustado para altura mais compacta --}}
                        <canvas id="coursesChart"></canvas>
                    </div>
                </div>

            </div>

            {{-- Seção de Cards de Resumo (Design Compacto) --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-6">

                {{-- Card: Eventos Totais --}}
                <div
                    class="bg-white p-4 rounded-xl shadow-sm border border-gray-200 relative transform hover:scale-[1.02] transition-transform duration-200">
                    <div class="flex items-center justify-between mb-2">
                        <div class="flex items-center space-x-2">
                            <div class="p-2 rounded-full bg-blue-100 text-blue-600">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M8 7V3m8 4V3m-9 8h.01M12 11h.01M15 11h.01M7 15h.01M11 15h.01M15 15h.01M4 20h16a2 2 0 002-2V6a2 2 0 00-2-2H4a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                            </div>
                            <span class="text-sm font-medium text-gray-600">Eventos Totais</span>
                        </div>
                        <div
                            class="px-2 py-0.5 rounded-full text-xs font-semibold flex items-center
                        {{ $eventsTrend >= 0 ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                @if ($eventsTrend >= 0)
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M5 15l7-7 7 7" />
                                @else
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 9l-7 7-7-7" />
                                @endif
                            </svg>
                            <span>{{ abs($eventsTrend) }}%</span>
                        </div>
                    </div>
                    <p class="text-3xl font-bold text-gray-800 mt-2 ml-2">{{ $eventsCount }}</p>
                </div>

                {{-- Card: Curtidas no Sistema --}}
                <div
                    class="bg-white p-4 rounded-xl shadow-sm border border-gray-200 relative transform hover:scale-[1.02] transition-transform duration-200">
                    <div class="flex items-center justify-between mb-2">
                        <div class="flex items-center space-x-2">
                            <div class="p-2 rounded-full bg-green-100 text-green-600">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="currentColor"
                                    viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z"
                                        clip-rule="evenodd" />
                                </svg>
                            </div>
                            <span class="text-sm font-medium text-gray-600">Curtidas no Sistema</span>
                        </div>
                        <div
                            class="px-2 py-0.5 rounded-full text-xs font-semibold flex items-center
                        {{ $likesTrend >= 0 ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                @if ($likesTrend >= 0)
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M5 15l7-7 7 7" />
                                @else
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 9l-7 7-7-7" />
                                @endif
                            </svg>
                            <span>{{ abs($likesTrend) }}%</span>
                        </div>
                    </div>
                    <p class="text-3xl font-bold text-gray-800 mt-2 ml-2">{{ $likesCount }}</p>
                </div>

                {{-- Card: Comentários Feitos --}}
                <div
                    class="bg-white p-4 rounded-xl shadow-sm border border-gray-200 relative transform hover:scale-[1.02] transition-transform duration-200">
                    <div class="flex items-center justify-between mb-2">
                        <div class="flex items-center space-x-2">
                            <div class="p-2 rounded-full bg-purple-100 text-purple-600">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                                </svg>
                            </div>
                            <span class="text-sm font-medium text-gray-600">Comentários Feitos</span>
                        </div>
                        <div
                            class="px-2 py-0.5 rounded-full text-xs font-semibold flex items-center
                        {{ $commentsTrend >= 0 ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                @if ($commentsTrend >= 0)
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M5 15l7-7 7 7" />
                                @else
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 9l-7 7-7-7" />
                                @endif
                            </svg>
                            <span>{{ abs($commentsTrend) }}%</span>
                        </div>
                    </div>
                    <p class="text-3xl font-bold text-gray-800 mt-2 ml-2">{{ $commentsCount }}</p>
                </div>

                {{-- Card: Eventos Salvos --}}
                <div
                    class="bg-white p-4 rounded-xl shadow-sm border border-gray-200 relative transform hover:scale-[1.02] transition-transform duration-200">
                    <div class="flex items-center justify-between mb-2">
                        <div class="flex items-center space-x-2">
                            <div class="p-2 rounded-full bg-pink-100 text-pink-600">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z" />
                                </svg>
                            </div>
                            <span class="text-sm font-medium text-gray-600">Eventos Salvos</span>
                        </div>
                        <div
                            class="px-2 py-0.5 rounded-full text-xs font-semibold flex items-center
                        {{ $savedEventsTrend >= 0 ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                @if ($savedEventsTrend >= 0)
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M5 15l7-7 7 7" />
                                @else
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 9l-7 7-7-7" />
                                @endif
                            </svg>
                            <span>{{ abs($savedEventsTrend) }}%</span>
                        </div>
                    </div>
                    <p class="text-3xl font-bold text-gray-800 mt-2 ml-2">{{ $savedEventsCount }}</p>
                </div>

                {{-- Card: Posts de Cursos --}}
                <div
                    class="bg-white p-4 rounded-xl shadow-sm border border-gray-200 relative transform hover:scale-[1.02] transition-transform duration-200">
                    <div class="flex items-center justify-between mb-2">
                        <div class="flex items-center space-x-2">
                            <div class="p-2 rounded-full bg-yellow-100 text-yellow-600">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                </svg>
                            </div>
                            <span class="text-sm font-medium text-gray-600">Posts de Cursos</span>
                        </div>
                        <div
                            class="px-2 py-0.5 rounded-full text-xs font-semibold flex items-center
                        {{ $postsTrend >= 0 ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                @if ($postsTrend >= 0)
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M5 15l7-7 7 7" />
                                @else
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 9l-7 7-7-7" />
                                @endif
                            </svg>
                            <span>{{ abs($postsTrend) }}%</span>
                        </div>
                    </div>
                    <p class="text-3xl font-bold text-gray-800 mt-2 ml-2">{{ $postsCount }}</p>
                </div>
            </div>

            {{-- Ranking de Coordenadores e Top Eventos --}}
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                {{-- Ranking de Coordenadores --}}
                <div class="p-5 bg-white rounded-2xl shadow-sm border border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-700 mb-3">Coordenadores com mais Eventos</h3>
                    <ul class="space-y-3">
                        @forelse ($topCoordinators as $coordinator)
                            @if ($coordinator->eventCoordinator && $coordinator->eventCoordinator->userAccount)
                                <li class="flex items-center space-x-4 p-2 bg-gray-50 rounded-lg">
                                    <img class="h-12 w-12 rounded-full object-cover"
                                        src="{{ $coordinator->eventCoordinator->userAccount->user_icon_url }}"
                                        alt="{{ $coordinator->eventCoordinator->userAccount->name }}" />
                                    <div class="flex-1">
                                        <h4 class="font-medium text-gray-900">
                                            {{ $coordinator->eventCoordinator->userAccount->name }}</h4>
                                        <p class="text-sm text-gray-500">Eventos Criados:
                                            {{ $coordinator->events_count }}</p>
                                    </div>
                                </li>
                            @endif
                        @empty
                            <p class="text-gray-500">Nenhum coordenador para exibir.</p>
                        @endforelse
                    </ul>

                    @if ($otherCoordinators->count() > 0)
                        <div x-data="{ open: false }">
                            <button @click="open = !open"
                                class="mt-4 w-full text-center text-blue-600 font-medium hover:text-blue-500 transition-colors duration-200">
                                <span x-show="!open">Ver mais ({{ $otherCoordinators->count() }})</span>
                                <span x-show="open">Ver menos</span>
                            </button>
                            <ul x-show="open" x-collapse.duration.500ms class="mt-3 space-y-3">
                                @foreach ($otherCoordinators as $coordinator)
                                    @if ($coordinator->eventCoordinator && $coordinator->eventCoordinator->userAccount)
                                        <li class="flex items-center space-x-4 p-2 bg-gray-50 rounded-lg">
                                            <img class="h-12 w-12 rounded-full object-cover"
                                                src="{{ $coordinator->eventCoordinator->userAccount->user_icon_url }}"
                                                alt="{{ $coordinator->eventCoordinator->userAccount->name }}" />
                                            <div class="flex-1">
                                                <h4 class="font-medium text-gray-900">
                                                    {{ $coordinator->eventCoordinator->userAccount->name }}</h4>
                                                <p class="text-sm text-gray-500">{{ $coordinator->events_count }}
                                                    eventos criados</p>
                                            </div>
                                        </li>
                                    @endif
                                @endforeach
                            </ul>
                        </div>
                    @endif
                </div>

                {{-- Top Eventos do Mês --}}
                <div class="p-5 bg-white rounded-2xl shadow-sm border border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-700 mb-3">Top Eventos do Mês</h3>
                    <ul class="divide-y divide-gray-200">
                        @forelse ($topEventsOfTheMonth as $event)
                            <li class="py-3 flex justify-between items-center">
                                <span class="text-gray-900 font-medium">{{ $event->event_name }}</span>
                                <span class="text-sm text-gray-500">{{ $event->total_interactions }} interações</span>
                            </li>
                        @empty
                            <p class="text-gray-500">Nenhum evento com interações neste mês.</p>
                        @endforelse
                    </ul>
                </div>
            </div>
        </div>
    </div>

    {{-- Chart.js --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Variável global para armazenar as instâncias dos gráficos
        let interactionsChartInstance;
        let postInteractionsChartInstance;
        let coursesChartInstance;

        document.addEventListener('DOMContentLoaded', function() {

            // --- 1. Gráfico Ranking de Cursos ---
            const coursesCtx = document.getElementById('coursesChart').getContext('2d');
            coursesChartInstance = new Chart(coursesCtx, {
                type: 'bar',
                data: {
                    labels: @json($coursesLabels),
                    datasets: [{
                        label: 'Eventos Criados',
                        data: @json($coursesData),
                        backgroundColor: 'rgba(99, 102, 241, 0.8)',
                        borderColor: 'rgba(99, 102, 241, 1)',
                        borderWidth: 1
                    }]
                },
                options: {
                    indexAxis: 'y',
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        x: {
                            beginAtZero: true
                        }
                    }
                }
            });

            // --- 2. Gráfico Evolução de Interações (Eventos) ---
            const interactionsCtx = document.getElementById('interactionsChart').getContext('2d');
            interactionsChartInstance = new Chart(interactionsCtx, {
                type: 'line',
                data: {
                    labels: @json($interactionsLabels),
                    datasets: [{
                        label: 'Total de Interações',
                        data: @json($interactionsData),
                        borderColor: 'rgba(16, 185, 129, 1)',
                        backgroundColor: 'rgba(16, 185, 129, 0.2)',
                        fill: true,
                        tension: 0.3
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });

            // --- 3. Gráfico Evolução de Posts e Respostas ---
            const postInteractionsCtx = document.getElementById('postInteractionsChart').getContext('2d');
            postInteractionsChartInstance = new Chart(postInteractionsCtx, {
                type: 'line',
                data: {
                    labels: @json($postInteractionsLabels),
                    datasets: [{
                            label: 'Posts Criados',
                            data: @json($postsData),
                            borderColor: 'rgba(245, 158, 11, 1)',
                            backgroundColor: 'rgba(245, 158, 11, 0.2)',
                            fill: true,
                            tension: 0.3
                        },
                        {
                            label: 'Respostas Recebidas',
                            data: @json($repliesData),
                            borderColor: 'rgba(59, 130, 246, 1)',
                            backgroundColor: 'rgba(59, 130, 246, 0.2)',
                            fill: true,
                            tension: 0.3
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });

            // --- LÓGICA DE CAPTURA DE GRÁFICOS PARA O PDF ---
            const exportForm = document.getElementById('exportForm');
            if (exportForm) {
                exportForm.addEventListener('submit', function(e) {
                    // Previne o envio padrão do formulário
                    e.preventDefault();

                    // Converte cada canvas do Chart.js para Base64 e insere no campo oculto
                    // NOTA: chart.toBase64Image() é o método preferido do Chart.js, mas 
                    // toDataURL() da canvas funciona de forma semelhante e é mais genérico.
                    document.getElementById('interactionsChartImage').value = interactionsChartInstance
                        .toBase64Image('image/png', 1.0);
                    document.getElementById('postInteractionsChartImage').value =
                        postInteractionsChartInstance.toBase64Image('image/png', 1.0);
                    document.getElementById('coursesChartImage').value = coursesChartInstance.toBase64Image(
                        'image/png', 1.0);

                    // Re-envia o formulário com os dados Base64
                    exportForm.submit();
                });
            }
        });
    </script>

</x-app-layout>
