<x-app-layout>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-4">

            {{-- Mensagem de Boas-Vindas Centralizada --}}
            <div class="flex justify-center">
                <div class="bg-blue-50 border-l-4 border-blue-400 text-blue-800 p-4 mb-6 w-full md:w-3/4 lg:w-2/3" role="alert">
                    <div class="flex">
                        <div class="py-1">
                            <svg class="h-6 w-6 text-blue-400 mr-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div>
                            <p class="font-bold">Olá, {{ Auth::user()->name }}!</p>
                            <p class="text-sm">Bem-vindo(a) ao seu painel de controle. Aqui você pode acompanhar suas interações e atividades recentes nos eventos da nossa escola.</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Destaque Dinâmico --}}
            <div class="p-3 bg-white rounded-2xl shadow-md text-center">
                <p class="text-md font-medium text-gray-700">{{ $dynamicHighlight }}</p>
            </div>

            {{-- Cards de Resumo --}}
            <div class="grid grid-cols-1 md:grid-cols-4 gap-3">
                <div class="p-3 bg-white rounded-2xl shadow">
                    <h3 class="text-sm text-gray-500">Eventos Salvos</h3>
                    <p class="text-xl font-bold text-blue-600 mt-2">{{ $savedEventsCount }}</p>
                </div>
                <div class="p-3 bg-white rounded-2xl shadow">
                    <h3 class="text-sm text-gray-500">Curtidas Dadas</h3>
                    <p class="text-xl font-bold text-green-600 mt-2">{{ $likesCount }}</p>
                </div>
                <div class="p-3 bg-white rounded-2xl shadow">
                    <h3 class="text-sm text-gray-500">Comentários Feitos</h3>
                    <p class="text-xl font-bold text-purple-600 mt-2">{{ $commentsCount }}</p>
                </div>
                <div class="p-3 bg-white rounded-2xl shadow">
                    <h3 class="text-sm text-gray-500">Notificações Ativas</h3>
                    <p class="text-xl font-bold text-pink-600 mt-2">{{ $notifiedEventsCount }}</p>
                </div>
            </div>

            {{-- Conteúdo Lado a Lado para Dispositivos Maiores --}}
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">

                {{-- Sua Atividade Recente --}}
                <div class="mt-4 lg:mt-0">
                    <h3 class="text-lg font-semibold text-gray-700 mb-2">Sua Atividade Recente</h3>
    
                    @if($recentActivities->isEmpty())
                        <p class="text-gray-500 bg-white p-4 rounded-2xl shadow">Nenhuma interação recente para exibir.</p>
                    @else
                        <ul class="bg-white rounded-2xl shadow divide-y divide-gray-200">
                            @foreach($recentActivities as $activity)
                                <li class="px-4 py-3 flex justify-between items-center">
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-medium text-gray-900 truncate">
                                            {{ $activity->message }}
                                        </p>
                                    </div>
                                    <span class="ml-2 text-sm text-gray-500 flex-shrink-0">
                                        {{ $activity->created_at->diffForHumans() }}
                                    </span>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </div>

                {{-- Gráfico de Distribuição das Interações --}}
                <div class="p-3 bg-white rounded-2xl shadow h-full flex flex-col justify-between">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-700 mb-3">Distribuição das Minhas Interações</h3>
                        <div class="relative h-64">
                            <canvas id="userDistributionChart"></canvas>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    {{-- Chart.js --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const distributionCtx = document.getElementById('userDistributionChart').getContext('2d');
            
            // Dados vindos do Laravel
            const distributionData = @json($distributionData);

            new Chart(distributionCtx, {
                type: 'pie',
                data: {
                    labels: ['Salvos', 'Curtidas', 'Comentários'],
                    datasets: [{
                        data: [
                            distributionData.saves,
                            distributionData.likes,
                            distributionData.comments
                        ],
                        backgroundColor: [
                            'rgba(59, 130, 246, 0.7)', // Azul para Salvos
                            'rgba(34, 197, 94, 0.7)', // Verde para Curtidas
                            'rgba(139, 92, 246, 0.7)'  // Roxo para Comentários
                        ],
                        borderColor: [
                            'rgba(59, 130, 246, 1)',
                            'rgba(34, 197, 94, 1)',
                            'rgba(139, 92, 246, 1)'
                        ],
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true, // Habilita a responsividade
                    maintainAspectRatio: false // Desabilita o ajuste automático do tamanho do gráfico
                }
            });
        });
    </script>
</x-app-layout>