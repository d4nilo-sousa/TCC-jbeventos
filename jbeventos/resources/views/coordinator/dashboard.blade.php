<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard Coordenador') }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-4">

            {{-- Definição da variável $totalInteractions --}}
            @php
                $totalInteractions = ($likes ?? 0) + ($comments ?? 0) + ($saves ?? 0);
            @endphp

            {{-- Cards Resumo --}}
            <div class="grid grid-cols-1 md:grid-cols-4 gap-3">
                <div class="p-3 bg-white rounded-2xl shadow">
                    <h3 class="text-sm text-gray-500">Eventos Criados</h3>
                    <p class="text-xl font-bold text-blue-600 mt-2">{{ $eventsCount }}</p>
                </div>
                <div class="p-3 bg-white rounded-2xl shadow">
                    <h3 class="text-sm text-gray-500">Curtidas Recebidas</h3>
                    <p class="text-xl font-bold text-green-600 mt-2">{{ $likes }}</p>
                </div>
                <div class="p-3 bg-white rounded-2xl shadow">
                    <h3 class="text-sm text-gray-500">Comentários</h3>
                    <p class="text-xl font-bold text-purple-600 mt-2">{{ $comments }}</p>
                </div>
                <div class="p-3 bg-white rounded-2xl shadow">
                    <h3 class="text-sm text-gray-500">Eventos Salvos</h3>
                    <p class="text-xl font-bold text-pink-600 mt-2">{{ $saves }}</p>
                </div>
            </div>

            {{-- Top 3 eventos mais engajados --}}
            <div class="mt-4">
                <h3 class="text-lg font-semibold text-gray-700 mb-2">Top 3 Eventos Mais Engajados</h3>

                @if($topEvents->isEmpty())
                    <p class="text-gray-500">Nenhum evento com interações ainda.</p>
                @else
                    <ul class="bg-white rounded-2xl shadow divide-y divide-gray-200">
                        @foreach($topEvents as $event)
                            <li class="flex justify-between items-center px-4 py-3">
                                <a href="{{ route('events.show', $event) }}" class="font-medium text-gray-700 hover:text-blue-500 transition duration-150 ease-in-out">
                                    {{ $event->event_name }}
                                </a>
                                <span class="text-gray-500">{{ $event->total_engagement }} interações</span>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>

            {{-- Gráficos --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                {{-- Gráfico de engajamento por mês --}}
                <div class="p-3 bg-white rounded-2xl shadow">
                    <h3 class="text-lg font-semibold text-gray-700 mb-0">Evolução do Engajamento (Mês a Mês)</h3>
                    <canvas id="engagementChart" class="{{ $totalInteractions > 0 ? 'h-32' : 'h-28' }} w-full"></canvas>
                </div>

                {{-- Gráfico de distribuição de interações --}}
                <div class="p-3 bg-white rounded-2xl shadow">
                    <h3 class="text-lg font-semibold text-gray-700 mb-3">Distribuição das Interações</h3>
                    {{-- AJUSTADO: Aumentada a altura do contêiner para 'h-48' ou 'h-56' --}}
                    <div class="relative h-75"> {{-- Você pode experimentar com h-56 ou h-64 se quiser maior --}}
                        <canvas id="distributionChart"></canvas>
                    </div>
                </div>
            </div>

        </div>
    </div>

    {{-- Chart.js --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // O código JavaScript para os gráficos permanece o mesmo
        const engagementCtx = document.getElementById('engagementChart').getContext('2d');
        new Chart(engagementCtx, {
            type: 'bar',
            data: {
                labels: @json($labels),
                datasets: [{
                    label: 'Interações',
                    data: @json($values),
                    backgroundColor: 'rgba(54, 162, 235, 0.6)',
                    borderColor: 'rgba(54, 162, 235, 1)',
                    borderWidth: 1,
                    borderRadius: 6
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: { beginAtZero: true }
                }
            }
        });

        const distributionCtx = document.getElementById('distributionChart').getContext('2d');
        new Chart(distributionCtx, {
            type: 'pie',
            data: {
                labels: ['Curtidas', 'Comentários', 'Salvos'],
                datasets: [{
                    data: [{{ $likes }}, {{ $comments }}, {{ $saves }}],
                    backgroundColor: [
                        'rgba(59, 130, 246, 0.7)',
                        'rgba(34, 197, 94, 0.7)',
                        'rgba(139, 92, 246, 0.7)'
                    ],
                    borderColor: [
                        'rgba(59, 130, 246, 1)',
                        'rgba(34, 197, 94, 1)',
                        'rgba(139, 92, 246, 1)'
                    ],
                    borderWidth: 1
                }]
            },
            options: { responsive: true, maintainAspectRatio: false }
        });
    </script>
</x-app-layout>