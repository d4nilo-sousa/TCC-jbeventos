<x-app-layout>
    {{-- Mensagem de boas-vindas --}}
    <div class="py-6 max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="p-5 bg-white rounded-2xl shadow">
            <h2 class="text-2xl font-bold text-gray-800">Olá, {{ $name }}!</h2>
            <p class="text-gray-600 mt-1">{{ $message }}</p>
        </div>
    </div>

    <div class="py-6 max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

        {{-- Cards de Resumo --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-6">

            {{-- Eventos Totais --}}
            <div class="p-5 rounded-2xl shadow-lg bg-gradient-to-r from-blue-400 to-blue-600 text-white transform hover:-translate-y-1 transition-transform duration-300 relative">
                <div class="flex items-center space-x-4">
                    <div class="p-3 bg-white bg-opacity-20 rounded-full">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-6 14h6m-6 0v-4m0 4h-6m6 0V9m0 5h6" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-sm font-medium">Eventos Totais</h3>
                        <p class="text-2xl font-bold mt-1">{{ $eventsCount }}</p>
                    </div>
                </div>
                <div class="absolute top-3 right-3 px-2 py-1 rounded-full text-xs font-semibold flex items-center space-x-1
                    {{ $eventsTrend >= 0 ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        @if($eventsTrend >= 0)
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7" />
                        @else
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        @endif
                    </svg>
                    <span>{{ abs($eventsTrend) }}%</span>
                </div>
            </div>

            {{-- Curtidas --}}
            <div class="p-5 rounded-2xl shadow-lg bg-gradient-to-r from-green-400 to-green-600 text-white transform hover:-translate-y-1 transition-transform duration-300 relative">
                <div class="flex items-center space-x-4">
                    <div class="p-3 bg-white bg-opacity-20 rounded-full">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 9l-3 3m0 0l-3-3m3 3V4" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-sm font-medium">Curtidas no Sistema</h3>
                        <p class="text-2xl font-bold mt-1">{{ $likesCount }}</p>
                    </div>
                </div>
                <div class="absolute top-3 right-3 px-2 py-1 rounded-full text-xs font-semibold flex items-center space-x-1
                    {{ $likesTrend >= 0 ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        @if($likesTrend >= 0)
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7" />
                        @else
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        @endif
                    </svg>
                    <span>{{ abs($likesTrend) }}%</span>
                </div>
            </div>

            {{-- Comentários --}}
            <div class="p-5 rounded-2xl shadow-lg bg-gradient-to-r from-purple-400 to-purple-600 text-white transform hover:-translate-y-1 transition-transform duration-300 relative">
                <div class="flex items-center space-x-4">
                    <div class="p-3 bg-white bg-opacity-20 rounded-full">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8h2a2 2 0 002-2v-2a2 2 0 00-2-2h-2m-4 0H5a2 2 0 00-2 2v2a2 2 0 002 2h2m0-4v4" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-sm font-medium">Comentários Feitos</h3>
                        <p class="text-2xl font-bold mt-1">{{ $commentsCount }}</p>
                    </div>
                </div>
                <div class="absolute top-3 right-3 px-2 py-1 rounded-full text-xs font-semibold flex items-center space-x-1
                    {{ $commentsTrend >= 0 ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        @if($commentsTrend >= 0)
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7" />
                        @else
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        @endif
                    </svg>
                    <span>{{ abs($commentsTrend) }}%</span>
                </div>
            </div>

            {{-- Eventos Salvos --}}
            <div class="p-5 rounded-2xl shadow-lg bg-gradient-to-r from-pink-400 to-pink-600 text-white transform hover:-translate-y-1 transition-transform duration-300 relative">
                <div class="flex items-center space-x-4">
                    <div class="p-3 bg-white bg-opacity-20 rounded-full">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-sm font-medium">Eventos Salvos</h3>
                        <p class="text-2xl font-bold mt-1">{{ $savedEventsCount }}</p>
                    </div>
                </div>
                <div class="absolute top-3 right-3 px-2 py-1 rounded-full text-xs font-semibold flex items-center space-x-1
                    {{ $savedEventsTrend >= 0 ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        @if($savedEventsTrend >= 0)
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7" />
                        @else
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        @endif
                    </svg>
                    <span>{{ abs($savedEventsTrend) }}%</span>
                </div>
            </div>
        </div>

        {{-- Conteúdo Principal: Ranking e Gráficos --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

            {{-- Ranking de Coordenadores --}}
            <div class="p-5 bg-white rounded-2xl shadow">
                <h3 class="text-lg font-semibold text-gray-700 mb-3">Coordenadores com mais Eventos</h3>
                <ul class="space-y-3">
                    @forelse ($topCoordinators as $coordinator)
                        @if($coordinator->eventCoordinator && $coordinator->eventCoordinator->userAccount)
                            <li class="flex items-center space-x-4 p-2 bg-gray-50 rounded-lg">
                                <img class="h-12 w-12 rounded-full object-cover" src="{{ $coordinator->eventCoordinator->userAccount->user_icon_url }}" alt="{{ $coordinator->eventCoordinator->userAccount->name }}" />
                                <div class="flex-1">
                                    <h4 class="font-medium text-gray-900">{{ $coordinator->eventCoordinator->userAccount->name }}</h4>
                                    <p class="text-sm text-gray-500">{{ $coordinator->events_count }} eventos criados</p>
                                </div>
                            </li>
                        @endif
                    @empty
                        <p class="text-gray-500">Nenhum coordenador para exibir.</p>
                    @endforelse
                </ul>

                @if($otherCoordinators->count() > 0)
                    <div x-data="{ open: false }">
                        <button @click="open = !open" class="mt-4 w-full text-center text-blue-600 font-medium hover:text-blue-500 transition-colors duration-200">
                            <span x-show="!open">Ver mais ({{ $otherCoordinators->count() }})</span>
                            <span x-show="open">Ver menos</span>
                        </button>
                        <ul x-show="open" x-collapse.duration.500ms class="mt-3 space-y-3">
                            @foreach ($otherCoordinators as $coordinator)
                                @if($coordinator->eventCoordinator && $coordinator->eventCoordinator->userAccount)
                                    <li class="flex items-center space-x-4 p-2 bg-gray-50 rounded-lg">
                                        <img class="h-12 w-12 rounded-full object-cover" src="{{ $coordinator->eventCoordinator->userAccount->user_icon_url }}" alt="{{ $coordinator->eventCoordinator->userAccount->name }}" />
                                        <div class="flex-1">
                                            <h4 class="font-medium text-gray-900">{{ $coordinator->eventCoordinator->userAccount->name }}</h4>
                                            <p class="text-sm text-gray-500">{{ $coordinator->events_count }} eventos criados</p>
                                        </div>
                                    </li>
                                @endif
                            @endforeach
                        </ul>
                    </div>
                @endif
            </div>

            {{-- Top Eventos do Mês --}}
            <div class="p-5 bg-white rounded-2xl shadow">
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

            {{-- Gráfico Ranking de Cursos --}}
            <div class="p-5 bg-white rounded-2xl shadow">
                <h3 class="text-lg font-semibold text-gray-700 mb-3">Ranking de Cursos por Eventos</h3>
                <div class="relative h-64">
                    <canvas id="coursesChart"></canvas>
                </div>
            </div>

            {{-- Gráfico Evolução de Interações --}}
            <div class="p-5 bg-white rounded-2xl shadow">
                <h3 class="text-lg font-semibold text-gray-700 mb-3">Evolução de Interações (Últimos 6 Meses)</h3>
                <div class="relative h-64">
                    <canvas id="interactionsChart"></canvas>
                </div>
            </div>

        </div>

    </div>

    {{-- Chart.js --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const coursesCtx = document.getElementById('coursesChart').getContext('2d');
            new Chart(coursesCtx, {
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
                options: { indexAxis: 'y', responsive: true, maintainAspectRatio: false, scales: { x: { beginAtZero: true } } }
            });

            const interactionsCtx = document.getElementById('interactionsChart').getContext('2d');
            new Chart(interactionsCtx, {
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
                options: { responsive: true, maintainAspectRatio: false, scales: { y: { beginAtZero: true } } }
            });
        });
    </script>

</x-app-layout>
