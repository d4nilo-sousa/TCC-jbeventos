<x-app-layout title="Feed Principal">
    @push('styles')
    <!-- Incluir ícones Phosphor para um toque moderno -->
    <script src="https://www.google.com/search?q=https://unpkg.com/%40phosphor-icons/web"></script>
    @endpush
    
    <div class="py-10 bg-gray-50 dark:bg-gray-900 min-h-screen">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8 space-y-6">
    
            <!-- CABEÇALHO DO FEED - MENSAGEM DE BOAS-VINDAS RÁPIDA -->
            <div class="bg-white dark:bg-gray-800 shadow rounded-xl p-4 sm:p-6 mb-8 border border-gray-200 dark:border-gray-700">
                <h1 class="text-3xl font-extrabold text-gray-900 dark:text-white mb-2">Olá, {{ $user->name }}!</h1>
                <p class="text-gray-600 dark:text-gray-400">
                    Neste feed você encontra uma combinação dos eventos mais importantes e dos posts das áreas que você segue.
                </p>
            </div>
    
            <!-- LOOP PRINCIPAL DO FEED -->
            @forelse ($feedItems as $item)
    
                @if ($item->type === 'event')
                    {{-- CARTÃO DE EVENTO --}}
                    <div class="bg-white dark:bg-gray-800 shadow-lg rounded-xl overflow-hidden transform transition duration-300 hover:shadow-xl border border-gray-200 dark:border-gray-700">
                        <!-- Imagem/Placeholder do Evento -->
                        <a href="{{ route('events.show', $item) }}">
                            <div class="relative h-64 w-full bg-gray-200 dark:bg-gray-700">
                                @if ($item->images->first())
                                    <img class="w-full h-full object-cover" src="{{ asset('storage/' . $item->images->first()->image_path) }}" alt="{{ $item->event_name }}" loading="lazy">
                                @else
                                    <!-- Placeholder sem imagem -->
                                    <div class="flex flex-col items-center justify-center w-full h-full text-gray-500 dark:text-gray-400">
                                        <i class="ph-fill ph-calendar-blank text-6xl"></i>
                                        <p class="mt-2 text-sm">Sem Imagem</p>
                                    </div>
                                @endif
                                <!-- Tag de Evento -->
                                <span class="absolute top-4 right-4 bg-red-600 text-white text-xs font-bold px-3 py-1.5 rounded-full shadow-md">EVENTO</span>
                            </div>
                        </a>
    
                        <div class="p-6">
                            <!-- Metadado do Autor/Curso -->
                            <div class="flex items-center mb-3 text-sm text-gray-500 dark:text-gray-400">
                                <i class="ph-fill ph-graduation-cap mr-2"></i>
                                Postado por: <span class="font-semibold text-indigo-600 dark:text-indigo-400 ml-1">
                                    {{ $item->eventCourse->courseCoordinator->userAccount->name ?? $item->eventCourse->course_name ?? 'Curso não definido' }}
                                </span>
                            </div>
    
                            <a href="{{ route('events.show', $item) }}" class="block">
                                <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-2 hover:text-indigo-600 dark:hover:text-indigo-400 transition">
                                    {{ $item->event_name }}
                                </h2>
                            </a>
    
                            <p class="text-gray-700 dark:text-gray-300 line-clamp-2 mb-4">
                                {{ $item->event_description }}
                            </p>
    
                            <!-- Data e Local -->
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-sm text-gray-600 dark:text-gray-400 border-t border-gray-100 dark:border-gray-700 pt-4">
                                <div class="flex items-center">
                                    <i class="ph ph-calendar-check mr-2 text-indigo-500"></i>
                                    <span class="font-medium">Data:</span> {{ $item->event_scheduled_at->format('d/m/Y H:i') }}
                                </div>
                                <div class="flex items-center">
                                    <i class="ph ph-map-pin mr-2 text-indigo-500"></i>
                                    <span class="font-medium">Local:</span> {{ $item->event_location }}
                                </div>
                            </div>
    
                            <!-- Ações (Interação) -->
                            <div class="mt-5 flex justify-between items-center border-t border-gray-100 dark:border-gray-700 pt-4">
                                <div class="space-x-4 flex">
                                    {{-- Botão de Reação/Curtir (Exemplo de interação) --}}
                                    <button class="flex items-center text-gray-600 dark:text-gray-400 hover:text-red-500 transition">
                                        <i class="ph ph-heart text-xl mr-1"></i>
                                        {{ $item->reactions->where('reaction_type', 'like')->count() }} Curtidas
                                    </button>
                                    {{-- Botão de Salvar/Bookmark --}}
                                    <button class="flex items-center text-gray-600 dark:text-gray-400 hover:text-yellow-500 transition">
                                        <i class="ph ph-bookmark-simple text-xl mr-1"></i>
                                        Salvar
                                    </button>
                                </div>
                                <a href="{{ route('events.show', $item) }}" class="text-indigo-600 dark:text-indigo-400 font-semibold hover:underline flex items-center">
                                    Ver Detalhes <i class="ph ph-arrow-right ml-1"></i>
                                </a>
                            </div>
                        </div>
                    </div>
    
                @elseif ($item->type === 'post')
                    {{-- CARTÃO DE POST --}}
                    <div class="bg-white dark:bg-gray-800 shadow rounded-xl p-6 border border-gray-200 dark:border-gray-700">
                        
                        <!-- Header do Post: Autor e Data -->
                        <div class="flex items-start mb-4">
                            <div class="w-10 h-10 bg-indigo-100 dark:bg-indigo-900 rounded-full flex items-center justify-center mr-3 flex-shrink-0">
                                <i class="ph-fill ph-chalkboard-teacher text-indigo-600 dark:text-indigo-400 text-xl"></i>
                            </div>
                            <div>
                                <p class="font-bold text-gray-900 dark:text-white">
                                    Post de {{ $item->course->course_name ?? 'Comunidade' }}
                                </p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">
                                    Publicado por {{ $item->author->name ?? 'Usuário Desconhecido' }} &middot; {{ $item->created_at->diffForHumans() }}
                                </p>
                            </div>
                        </div>
    
                        <!-- Conteúdo do Post -->
                        <p class="text-gray-800 dark:text-gray-200 mb-4 whitespace-pre-wrap">
                            {{ $item->content }}
                        </p>
    
                        <!-- Imagens do Post (Simulação de Array de Imagens do Post) -->
                        @if (property_exists($item, 'images') && is_array($item->images) && count($item->images) > 0)
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 mt-4">
                                @foreach ($item->images as $imagePath)
                                    <img src="{{ asset('storage/' . $imagePath) }}" alt="Imagem do Post" class="rounded-lg object-cover w-full h-40 shadow">
                                @endforeach
                            </div>
                        @endif
    
                        <!-- Footer do Post: Respostas -->
                        <div class="mt-4 border-t border-gray-100 dark:border-gray-700 pt-4 flex items-center justify-between text-sm text-gray-500 dark:text-gray-400">
                            <div class="flex items-center">
                                <i class="ph ph-chat-circle text-lg mr-2"></i>
                                {{ $item->replies->count() }} Respostas
                            </div>
                            <button class="text-indigo-600 dark:text-indigo-400 hover:underline font-medium">
                                Comentar
                            </button>
                        </div>
                    </div>
    
                @endif
    
            @empty
                {{-- MENSAGEM QUANDO NÃO HOUVER ITENS --}}
                <div class="text-center py-10 bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700">
                    <i class="ph-bold ph-magnifying-glass text-5xl text-gray-400 dark:text-gray-500 mb-4"></i>
                    <p class="text-xl font-semibold text-gray-700 dark:text-gray-300">Nenhum conteúdo no feed.</p>
                    <p class="text-gray-500 dark:text-gray-400 mt-2">Parece que ainda não há eventos ou posts recentes. Tente explorar novos cursos!</p>
                </div>
            @endforelse
    
            <!-- Final do Feed -->
            <div class="text-center py-6 text-gray-500 dark:text-gray-600">
                <p>Você chegou ao final do feed.</p>
            </div>
        </div>
    </div>
    
    
    <!-- MODAL DE BOAS-VINDAS (Condicional para o Primeiro Login) -->
    @if ($isFirstLogin)
        <div id="welcome-modal" class="fixed inset-0 bg-gray-900 bg-opacity-75 z-50 flex items-center justify-center transition-opacity duration-300">
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-2xl max-w-lg w-full p-8 m-4 transform transition-transform duration-300 scale-100"
                 role="dialog" aria-modal="true" aria-labelledby="modal-title">
    
                <div class="text-center">
                    {{-- PLACEHOLDER PARA O LOGO DO SISTEMA --}}
                    <!-- Substitua o src pelo caminho real do seu logo -->
                    <img src="{{ asset('imgs/logo_do_sistema.png') }}" 
                         alt="Logo do Sistema de Eventos" 
                         class="h-16 mx-auto mb-4 object-contain"
                         onerror="this.onerror=null;this.src='https://placehold.co/64x64/004d99/ffffff?text=LOGO'">
    
                    <h3 id="modal-title" class="text-3xl font-extrabold text-gray-900 dark:text-white mb-3">
                        Bem-vindo(a) à Comunidade!
                    </h3>
                    <p class="text-gray-600 dark:text-gray-400 text-lg mb-8">
                        Seu portal de eventos e comunicações agora é a tela principal! 
                        Encontre todos os eventos, notícias e posts em um só lugar, independente do seu papel na escola.
                    </p>
                    
                    <button onclick="document.getElementById('welcome-modal').classList.add('hidden')"
                            class="w-full inline-flex justify-center rounded-lg border border-transparent shadow-md px-6 py-3 bg-indigo-600 text-base font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition duration-150 sm:text-lg">
                        Começar a Explorar o Feed
                    </button>
                </div>
            </div>
        </div>
    @endif
    
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Se o modal estiver visível (ou seja, se for o primeiro login), não permite o scroll do fundo
            const modal = document.getElementById('welcome-modal');
            if (modal && !modal.classList.contains('hidden')) {
                document.body.style.overflow = 'hidden';
            }
            
            // Adiciona a função para remover o bloqueio de scroll ao fechar o modal
            modal?.querySelector('button').addEventListener('click', () => {
                document.body.style.overflow = '';
            });
        });
    </script>
    
    </x-app-layout>