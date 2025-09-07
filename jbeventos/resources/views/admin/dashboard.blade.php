<x-app-layout>
    <x-welcome-message :name="Auth::user()->name" role="Admin Master" />

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-4">
            
            {{-- Cards de Resumo --}}
            <div class="grid grid-cols-1 md:grid-cols-4 gap-3">
                <div class="p-3 bg-white rounded-2xl shadow">
                    <h3 class="text-sm text-gray-500">Total de Eventos</h3>
                    <p class="text-xl font-bold text-blue-600 mt-2">{{ $eventsCount }}</p>
                </div>
                <div class="p-3 bg-white rounded-2xl shadow">
                    <h3 class="text-sm text-gray-500">Curtidas Totais</h3>
                    <p class="text-xl font-bold text-green-600 mt-2">{{ $likesCount }}</p>
                </div>
                <div class="p-3 bg-white rounded-2xl shadow">
                    <h3 class="text-sm text-gray-500">Comentários Feitos</h3>
                    <p class="text-xl font-bold text-purple-600 mt-2">{{ $commentsCount }}</p>
                </div>
                <div class="p-3 bg-white rounded-2xl shadow">
                    <h3 class="text-sm text-gray-500">Qtd. Eventos Salvos</h3>
                    <p class="text-xl font-bold text-pink-600 mt-2">{{ $savedEventsCount }}</p>
                </div>
            </div>

            {{-- Conteúdo Principal em Grid --}}
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">

                {{-- Ranking de Coordenadores --}}
                <div class="p-4 bg-white rounded-2xl shadow">
                    <h3 class="text-lg font-semibold text-gray-700 mb-2">Coordenadores com mais Eventos</h3>
                    <ul class="space-y-3">
                        @forelse ($topCoordinators as $coordinator)
                            @if($coordinator->eventCoordinator && $coordinator->eventCoordinator->userAccount)
                                <li class="flex items-center space-x-4 p-2 bg-gray-50 rounded-lg">
                                    <img class="h-12 w-12 rounded-full object-cover" src="{{ $coordinator->eventCoordinator->userAccount->profile_photo_url }}" alt="{{ $coordinator->eventCoordinator->userAccount->name }}" />
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
                                        <img class="h-12 w-12 rounded-full object-cover" src="{{ $coordinator->eventCoordinator->userAccount->profile_photo_url }}" alt="{{ $coordinator->eventCoordinator->userAccount->name }}" />
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
                <div class="p-4 bg-white rounded-2xl shadow">
                    <h3 class="text-lg font-semibold text-gray-700 mb-2">Top Eventos do Mês</h3>
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

                {{-- Gráfico de Ranking de Cursos --}}
                <div class="p-4 bg-white rounded-2xl shadow">
                    <h3 class="text-lg font-semibold text-gray-700 mb-3">Ranking de Cursos por Eventos</h3>
                    <div class="relative h-64">
                        <canvas id="coursesChart"></canvas>
                    </div>
                </div>

                {{-- Gráfico de Evolução no Tempo --}}
                <div class="p-4 bg-white rounded-2xl shadow">
                    <h3 class="text-lg font-semibold text-gray-700 mb-3">Evolução de Interações (Últimos 6 Meses)</h3>
                    <div class="relative h-64">
                        <canvas id="interactionsChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Scripts para os Gráficos --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Gráfico de Ranking de Cursos (Barras Horizontais)
            const coursesCtx = document.getElementById('coursesChart').getContext('2d');
            new Chart(coursesCtx, {
                type: 'bar',
                data: {
                    labels: @json($coursesLabels),
                    datasets: [{
                        label: 'Eventos Criados',
                        data: @json($coursesData),
                        backgroundColor: 'rgba(59, 130, 246, 0.8)',
                        borderColor: 'rgba(59, 130, 246, 1)',
                        borderWidth: 1
                    }]
                },
                options: {
                    indexAxis: 'y',
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        x: { beginAtZero: true }
                    }
                }
            });

            // Gráfico de Evolução no Tempo (Linha)
            const interactionsCtx = document.getElementById('interactionsChart').getContext('2d');
            new Chart(interactionsCtx, {
                type: 'line',
                data: {
                    labels: @json($interactionsLabels),
                    datasets: [{
                        label: 'Total de Interações',
                        data: @json($interactionsData),
                        borderColor: 'rgba(34, 197, 94, 1)',
                        backgroundColor: 'rgba(34, 197, 94, 0.2)',
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
        });
    </script>
</x-app-layout>