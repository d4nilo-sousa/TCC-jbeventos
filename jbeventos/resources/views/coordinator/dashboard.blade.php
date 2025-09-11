<x-app-layout>
    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-4">

            {{-- Boas-vindas --}}
            <div class="p-4 bg-white rounded-2xl shadow flex flex-col sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h2 class="text-2xl font-bold text-gray-800">Olá, {{ $name }}!</h2>
                    <p class="text-gray-600 mt-1">{{ $message }}</p>
                </div>
            </div>

            {{-- Cards de Resumo --}}
            <div class="grid grid-cols-1 md:grid-cols-4 gap-3">
                {{-- Eventos Criados --}}
                <div class="p-4 bg-white rounded-2xl shadow flex flex-col justify-between">
                    <div class="flex items-center space-x-3">
                        <div class="p-2 bg-blue-100 rounded-full flex items-center justify-center">
                            <svg class="w-5 h-5 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-sm text-gray-500">Seus Eventos Criados</h3>
                            <p class="text-xl font-bold text-blue-600 mt-1">{{ $eventsCount }}</p>
                        </div>
                    </div>
                    <div class="mt-2 h-10">
                        <canvas id="eventsSparkline"></canvas>
                    </div>
                </div>

                {{-- Curtidas --}}
                <div class="p-4 bg-white rounded-2xl shadow flex flex-col justify-between">
                    <div class="flex items-center space-x-3">
                        <div class="p-2 bg-green-100 rounded-full flex items-center justify-center">
                            <svg class="h-6 w-6 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-sm text-gray-500">Curtidas em Seus Eventos</h3>
                            <p class="text-xl font-bold text-green-600 mt-1">{{ $likes }}</p>
                        </div>
                    </div>
                    <div class="mt-2 h-10">
                        <canvas id="likesSparkline"></canvas>
                    </div>
                </div>

                {{-- Comentários --}}
                <div class="p-4 bg-white rounded-2xl shadow flex flex-col justify-between">
                    <div class="flex items-center space-x-3">
                        <div class="p-2 bg-purple-100 rounded-full flex items-center justify-center">
                            <svg class="h-6 w-6 text-purple-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M8 10h.01M12 10h.01M16 10h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-sm text-gray-500">Comentários em Seus Eventos</h3>
                            <p class="text-xl font-bold text-purple-600 mt-1">{{ $comments }}</p>
                        </div>
                    </div>
                    <div class="mt-2 h-10">
                        <canvas id="commentsSparkline"></canvas>
                    </div>
                </div>

                {{-- Eventos Salvos --}}
                <div class="p-4 bg-white rounded-2xl shadow flex flex-col justify-between">
                    <div class="flex items-center space-x-3">
                        <div class="p-2 bg-pink-100 rounded-full flex items-center justify-center">
                            <svg class="h-6 w-6 text-pink-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-sm text-gray-500">Seus Eventos Salvos</h3>
                            <p class="text-xl font-bold text-pink-600 mt-1">{{ $saves }}</p>
                        </div>
                    </div>
                    <div class="mt-2 h-10">
                        <canvas id="savesSparkline"></canvas>
                    </div>
                </div>
            </div>

            {{-- Top Eventos --}}
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
                {{-- Engajamento Mês a Mês --}}
                <div class="p-4 bg-white rounded-2xl shadow">
                    <h3 class="text-lg font-semibold text-gray-700 mb-3">Evolução de Engajamento (Mês a Mês)</h3>
                    <div class="h-48">
                        <canvas id="engagementChart"></canvas>
                    </div>
                </div>

                {{-- Distribuição Interações --}}
                <div class="p-4 bg-white rounded-2xl shadow">
                    <h3 class="text-lg font-semibold text-gray-700 mb-3">Distribuição de Interações</h3>
                    <div class="h-48">
                        <canvas id="distributionChart"></canvas>
                    </div>
                </div>
            </div>

        </div>
    </div>

    {{-- Scripts Chart.js --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        function createSparkline(elementId, data, color) {
            new Chart(document.getElementById(elementId), {
                type: 'line',
                data: {
                    labels: @json($labels),
                    datasets: [{ data: data, borderColor: color, borderWidth: 2, fill: false, tension: 0.4, pointRadius: 0 }]
                },
                options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false }, tooltip: { enabled: false } }, scales: { x: { display: false }, y: { display: false } } }
            });
        }

        createSparkline('eventsSparkline', @json($eventsByMonth), 'rgb(59, 130, 246)');
        createSparkline('likesSparkline', @json($likesByMonth), 'rgb(34, 197, 94)');
        createSparkline('commentsSparkline', @json($commentsByMonth), 'rgb(139, 92, 246)');
        createSparkline('savesSparkline', @json($savesByMonth), 'rgb(236, 72, 153)');

        const engagementCtx = document.getElementById('engagementChart').getContext('2d');
        new Chart(engagementCtx, {
            type: 'bar',
            data: { labels: @json($labels), datasets: [{ label: 'Interações', data: @json($values), backgroundColor: 'rgba(54, 162, 235, 0.6)', borderColor: 'rgba(54, 162, 235, 1)', borderWidth: 1, borderRadius: 6 }] },
            options: { responsive: true, maintainAspectRatio: false, scales: { y: { beginAtZero: true } } }
        });

        const distributionCtx = document.getElementById('distributionChart').getContext('2d');
        new Chart(distributionCtx, {
            type: 'pie',
            data: {
                labels: ['Curtidas', 'Comentários', 'Salvos'],
                datasets: [{
                    data: [{{ $likes }}, {{ $comments }}, {{ $saves }}],
                    backgroundColor: ['rgba(59, 130, 246, 0.7)','rgba(34, 197, 94, 0.7)','rgba(139, 92, 246, 0.7)'],
                    borderColor: ['rgba(59, 130, 246, 1)','rgba(34, 197, 94, 1)','rgba(139, 92, 246, 1)'],
                    borderWidth: 1
                }]
            },
            options: { responsive: true, maintainAspectRatio: false }
        });
    </script>
</x-app-layout>