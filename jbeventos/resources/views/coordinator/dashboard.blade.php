<x-app-layout>
    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-4">
            
            {{-- Componente de boas-vindas --}}
            <x-welcome-message :name="$name" :role="$role" />

            {{-- Definição da variável $totalInteractions --}}
            @php
                $totalInteractions = ($likes ?? 0) + ($comments ?? 0) + ($saves ?? 0);
            @endphp

            {{-- Cards Resumo com Mini Gráficos --}}
            <div class="grid grid-cols-1 md:grid-cols-4 gap-3">
                {{-- Card Eventos Criados --}}
                <div class="p-3 bg-white rounded-2xl shadow">
                    <h3 class="text-sm text-gray-500">Seus Eventos Criados</h3>
                    <p class="text-xl font-bold text-blue-600 mt-2">{{ $eventsCount }}</p>
                    <div class="h-10 mt-2">
                        <canvas id="eventsSparkline"></canvas>
                    </div>
                </div>
                {{-- Card Curtidas Recebidas --}}
                <div class="p-3 bg-white rounded-2xl shadow">
                    <h3 class="text-sm text-gray-500">Curtidas em Seus Eventos</h3>
                    <p class="text-xl font-bold text-green-600 mt-2">{{ $likes }}</p>
                    <div class="h-10 mt-2">
                        <canvas id="likesSparkline"></canvas>
                    </div>
                </div>
                {{-- Card Comentários --}}
                <div class="p-3 bg-white rounded-2xl shadow">
                    <h3 class="text-sm text-gray-500">Comentários em Seus Eventos</h3>
                    <p class="text-xl font-bold text-purple-600 mt-2">{{ $comments }}</p>
                    <div class="h-10 mt-2">
                        <canvas id="commentsSparkline"></canvas>
                    </div>
                </div>
                {{-- Card Eventos Salvos --}}
                <div class="p-3 bg-white rounded-2xl shadow">
                    <h3 class="text-sm text-gray-500">Seus Eventos Salvos</h3>
                    <p class="text-xl font-bold text-pink-600 mt-2">{{ $saves }}</p>
                    <div class="h-10 mt-2">
                        <canvas id="savesSparkline"></canvas>
                    </div>
                </div>
            </div>

            {{-- Top 3 eventos mais engajados do coordenador --}}
            <div class="mt-4">
                <h3 class="text-lg font-semibold text-gray-700 mb-2">Seus Eventos Mais Engajados</h3>

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
                    <h3 class="text-lg font-semibold text-gray-700 mb-0">Evolução de Engajamento em Seus Eventos (Mês a Mês)</h3>
                    <canvas id="engagementChart" style="height: 180px;" class="w-full"></canvas>
                </div>

                {{-- Gráfico de distribuição de interações --}}
                <div class="p-3 bg-white rounded-2xl shadow">
                    <h3 class="text-lg font-semibold text-gray-700 mb-3">Distribuição das Interações em Seus Eventos</h3>
                    <div class="relative" style="height: 180px;">
                        <canvas id="distributionChart"></canvas>
                    </div>
                </div>
            </div>

        </div>
    </div>

    {{-- Chart.js --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Função utilitária para criar mini gráfico
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
                        pointRadius: 0 // Remove os pontos
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

        // Cria os mini gráficos com os dados do controller
        createSparkline('eventsSparkline', @json($eventsByMonth), 'rgb(59, 130, 246)');
        createSparkline('likesSparkline', @json($likesByMonth), 'rgb(34, 197, 94)');
        createSparkline('commentsSparkline', @json($commentsByMonth), 'rgb(139, 92, 246)');
        createSparkline('savesSparkline', @json($savesByMonth), 'rgb(236, 72, 153)');

        // Gráfico de engajamento principal (mantido)
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

        // Gráfico de distribuição principal (mantido)
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