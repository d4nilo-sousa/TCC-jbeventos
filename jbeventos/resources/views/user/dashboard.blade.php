<x-app-layout>
    <div class="py-10 bg-gray-50 min-h-screen">
        <div class="max-w-[1400px] mx-auto sm:px-6 lg:px-16 space-y-6">
            @php
                $fromTab = request('fromTab'); 
                $previousUrl = url()->previous();
            @endphp

            @if (Str::contains($previousUrl, '/perfil'))
                <a href="{{ route('profile.show') }}"
                    class="text-red-600 hover:text-red-800 transition-colors flex items-center gap-1 font-medium text-base mb-2">
                    <i class="ph-fill ph-arrow-left text-xl"></i> Voltar ao Perfil
                </a>
            @endif

            <div class="p-5 bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden">
                <div class="flex flex-col md:flex-row items-center justify-between gap-4">

                    <div class="flex-1 max-w-lg md:text-left text-center">
                        <h1 class="text-3xl font-extrabold text-gray-800 leading-snug">
                            👋 Olá, <span class="text-red-600">{{ $name }}</span>!
                        </h1>
                        {{-- Texto de boas-vindas reduzido --}}
                        <p class="mt-1 text-md text-gray-600">
                            Seu dashboard está pronto, confira suas estatísticas e acompanhe suas interações.
                        </p>

                        {{-- Mensagem Dinâmica Original, agora mais compacta --}}
                        <p
                            class="mt-3 text-sm font-semibold text-gray-700 p-2 bg-red-50 rounded-lg border border-red-100 max-w-lg">
                            <span class="text-red-600">Atenção:</span> {{ $message }}
                        </p>
                    </div>

                    <div class="flex-shrink-0 w-full max-w-[150px] sm:max-w-[200px] mt-4 md:mt-0">
                        <img src="{{ asset('imgs/user-dashboard.png') }}"
                            alt="Ilustração de Análise de Dados e Gráficos" class="w-full h-auto object-contain">
                    </div>

                </div>
            </div>

            {{-- Destaque Dinâmico --}}
            <div class="p-3 bg-blue-50 rounded-2xl shadow border border-gray-200 text-center">
                <p class="text-md font-medium text-blue-700">{{ $dynamicHighlight }}</p>
            </div>


            {{-- Estatísticas Principais --}}
            <div class="grid grid-cols-1 md:grid-cols-4 gap-3">
                {{-- Eventos Salvos --}}
                <div class="p-4 bg-white rounded-2xl shadow border border-gray-200 flex items-center space-x-3">
                    <div class="p-3 bg-blue-100 rounded-full flex-shrink-0">
                        <svg class="h-6 w-6 text-blue-600" fill="none" stroke="currentColor" stroke-width="2"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 5v14l7-7 7 7V5H5z" />
                        </svg>
                    </div>
                    <div class="flex flex-col justify-center">
                        <h3 class="text-sm text-gray-500">Eventos Salvos</h3>
                        <p class="text-xl font-bold text-blue-600">{{ $savedEventsCount }}</p>
                    </div>
                </div>

                {{-- Curtidas Dadas --}}
                <div class="p-4 bg-white rounded-2xl shadow border border-gray-200 flex items-center space-x-3">
                    <div class="p-3 bg-green-100 rounded-full flex-shrink-0">
                        <svg class="h-6 w-6 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z"
                                clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="flex flex-col justify-center">
                        <h3 class="text-sm text-gray-500">Curtidas Dadas</h3>
                        <p class="text-xl font-bold text-green-600">{{ $likesCount }}</p>
                    </div>
                </div>

                {{-- Comentários Feitos --}}
                <div class="p-4 bg-white rounded-2xl shadow border border-gray-200 flex items-center space-x-3">
                    <div class="p-3 bg-purple-100 rounded-full flex-shrink-0">
                        <svg class="h-6 w-6 text-purple-600" fill="none" stroke="currentColor" stroke-width="2"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M8 10h.01M12 10h.01M16 10h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                        </svg>
                    </div>
                    <div class="flex flex-col justify-center">
                        <h3 class="text-sm text-gray-500">Comentários Feitos</h3>
                        <p class="text-xl font-bold text-purple-600">{{ $commentsCount }}</p>
                    </div>
                </div>

                {{-- Notificações Ativas --}}
                <div class="p-4 bg-white rounded-2xl shadow border border-gray-200 flex items-center space-x-3">
                    <div class="p-3 bg-pink-100 rounded-full flex-shrink-0">
                        <svg class="h-6 w-6 text-pink-600" fill="none" stroke="currentColor" stroke-width="2"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6 6 0 00-5-5.917V5a1 1 0 10-2 0v.083A6 6 0 006 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1h6z" />
                        </svg>
                    </div>
                    <div class="flex flex-col justify-center">
                        <h3 class="text-sm text-gray-500">Notificações Ativas</h3>
                        <p class="text-xl font-bold text-pink-600">{{ $notifiedEventsCount }}</p>
                    </div>
                </div>
            </div>

            {{-- Conteúdo lado a lado --}}
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 mt-4">

                {{-- Atividade Recente --}}
                <div>
                    <h3 class="text-lg font-semibold text-gray-700 mb-2">Sua Atividade Recente</h3>
                    @if ($recentActivities->isEmpty())
                        <p class="text-gray-500 bg-white p-4 rounded-2xl shadow border border-gray-200">
                            Nenhuma interação recente.
                        </p>
                    @else
                        <ul class="bg-white rounded-2xl shadow border border-gray-200 divide-y divide-gray-200">
                            @foreach ($recentActivities as $activity)
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

                {{-- Gráfico de Distribuição --}}
                <div class="p-3 bg-white rounded-2xl shadow border border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-700 mb-3">Distribuição das Minhas Interações</h3>
                    <div class="relative h-64">
                        <canvas id="userDistributionChart"></canvas>
                    </div>
                </div>
            </div>


            {{-- Chart.js --}}
            <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    const ctx = document.getElementById('userDistributionChart').getContext('2d');
                    const data = @json($distributionData);

                    new Chart(ctx, {
                        type: 'pie',
                        data: {
                            labels: ['Salvos', 'Curtidas', 'Comentários'],
                            datasets: [{
                                data: [data.saves, data.likes, data.comments],
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
                        options: {
                            responsive: true,
                            maintainAspectRatio: false
                        }
                    });
                });
            </script>
</x-app-layout>
