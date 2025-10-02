<x-app-layout title="Feed Principal">
    @push('styles')
    
    <style>
        /* Estilos básicos para o feed */
        .reaction-button {
            transition: color 0.15s ease-in-out, transform 0.1s ease-in-out;
            padding: 0.5rem;
            border-radius: 9999px; /* Arredondado completo */
        }
        .reaction-button:hover {
            background-color: rgba(0, 0, 0, 0.05);
        }
        .reaction-button.active {
            transform: scale(1.05);
        }
        .feed-card {
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        }
    </style>
    @endpush
    
    <div class="py-10 bg-gray-50 dark:bg-gray-900 min-h-screen">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <!-- CABEÇALHO DO FEED -->
            <div class="bg-white dark:bg-gray-800 shadow rounded-xl p-4 sm:p-6 border border-gray-200 dark:border-gray-700">
                <h1 class="text-3xl font-extrabold text-gray-900 dark:text-white mb-2">Olá, {{ $user->name }}!</h1>
                <p class="text-gray-600 dark:text-gray-400">
                    Descubra eventos importantes e posts das áreas que você segue.
                </p>
            </div>
    
            <!-- LOOP PRINCIPAL DO FEED -->
            @forelse ($feedItems as $item)
    
                @if ($item->type === 'event')
                    {{-- CARTÃO DE EVENTO - Destaque em VERMELHO --}}
                    @php
                        // Assumindo que $item->userReactions é uma coleção de EventUserReaction 
                        // carregada com eager loading (Event::with('userReactions'))
                        $isLiked = $item->reactions->contains('user_id', $user->id) && $item->reactions->contains('reaction_type', 'like');
                        $isSaved = $item->reactions->contains('user_id', $user->id) && $item->reactions->contains('reaction_type', 'save');
                        $likeCount = $item->reactions->where('reaction_type', 'like')->count();
                    @endphp

                    <div id="event-{{ $item->id }}" class="feed-card bg-white dark:bg-gray-800 rounded-xl overflow-hidden transform transition duration-300 hover:shadow-2xl border border-red-200 dark:border-red-700">
                        
                        <!-- Imagem/Placeholder do Evento (Clicável) -->
                        <a href="{{ route('events.show', $item) }}" class="block">
                            <div class="relative h-64 w-full bg-gray-200 dark:bg-gray-700">
                                @if ($item->images->first())
                                    <img class="w-full h-full object-cover" src="{{ asset('storage/' . $item->images->first()->image_path) }}" alt="{{ $item->event_name }}" loading="lazy">
                                @else
                                    <!-- Placeholder sem imagem -->
                                    <div class="flex flex-col items-center justify-center w-full h-full text-red-500 dark:text-red-400">
                                        <i class="ph-bold ph-calendar-blank text-6xl"></i>
                                        <p class="mt-2 text-sm">Evento Sem Imagem</p>
                                    </div>
                                @endif
                                <!-- Tag de Evento -->
                                <span class="absolute top-4 right-4 bg-red-600 text-white text-xs font-bold px-3 py-1.5 rounded-full shadow-lg">EVENTO</span>
                            </div>
                        </a>
    
                        <div class="p-6">
                            <!-- Metadado do Autor/Curso -->
                            <div class="flex items-center mb-3 text-sm text-gray-500 dark:text-gray-400">
                                <i class="ph-fill ph-graduation-cap mr-2 text-red-500"></i>
                                Coordenador: <span class="font-semibold text-gray-800 dark:text-white ml-1">
                                    {{ $item->eventCourse->courseCoordinator->userAccount->name ?? $item->eventCourse->course_name ?? 'Curso não definido' }}
                                </span>
                            </div>
    
                            <a href="{{ route('events.show', $item) }}" class="block">
                                <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-2 hover:text-red-600 dark:hover:text-red-400 transition">
                                    {{ $item->event_name }}
                                </h2>
                            </a>
    
                            <p class="text-gray-700 dark:text-gray-300 line-clamp-3 mb-4 text-base">
                                {{ $item->event_description }}
                            </p>
    
                            <!-- Data e Local -->
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-sm text-gray-600 dark:text-gray-400 border-t border-gray-100 dark:border-gray-700 pt-4">
                                <div class="flex items-center">
                                    <i class="ph-fill ph-calendar-check mr-2 text-red-600"></i>
                                    <span class="font-medium">Data:</span> {{ $item->event_scheduled_at->format('d/m/Y H:i') }}
                                </div>
                                <div class="flex items-center">
                                    <i class="ph-fill ph-map-pin mr-2 text-red-600"></i>
                                    <span class="font-medium">Local:</span> {{ $item->event_location }}
                                </div>
                            </div>
    
                            <!-- Ações (Interação) -->
                            <div class="mt-5 flex items-center border-t border-gray-100 dark:border-gray-700 pt-4 space-x-6">
                                
                                {{-- Botão de Curtir --}}
                                <button 
                                    class="reaction-button flex items-center text-gray-600 dark:text-gray-400 transition"
                                    data-event-id="{{ $item->id }}" 
                                    data-reaction-type="like"
                                    data-is-active="{{ $isLiked ? 'true' : 'false' }}"
                                >
                                    <i class="ph-bold ph-heart text-2xl mr-1 {{ $isLiked ? 'text-red-500 ph-fill' : 'hover:text-red-500' }}" id="icon-like-{{ $item->id }}"></i>
                                    <span class="text-sm font-medium" id="count-like-{{ $item->id }}">{{ $likeCount }}</span>
                                    <span class="text-sm font-medium ml-1 hidden sm:inline">Curtidas</span>
                                </button>

                                {{-- Botão de Salvar (Mantido com destaque amarelo) --}}
                                <button 
                                    class="reaction-button flex items-center text-gray-600 dark:text-gray-400 transition"
                                    data-event-id="{{ $item->id }}" 
                                    data-reaction-type="save"
                                    data-is-active="{{ $isSaved ? 'true' : 'false' }}"
                                >
                                    <i class="ph-bold ph-bookmark-simple text-2xl {{ $isSaved ? 'text-yellow-500 ph-fill' : 'hover:text-yellow-500' }}" id="icon-save-{{ $item->id }}"></i>
                                    <span class="text-sm font-medium ml-2 hidden sm:inline">Salvar</span>
                                </button>
                            </div>
                        </div>
                    </div>
    
                @elseif ($item->type === 'post')
                    {{-- CARTÃO DE POST - Destaque Suave --}}
                    <div class="bg-white dark:bg-gray-800 shadow rounded-xl p-6 border border-gray-200 dark:border-gray-700 transition duration-300 hover:shadow-lg">
                        
                        <!-- Header do Post: Autor e Data -->
                        <div class="flex items-start mb-4">
                            <div class="w-10 h-10 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center mr-3 flex-shrink-0">
                                <i class="ph-fill ph-chalkboard-teacher text-gray-600 dark:text-gray-400 text-xl"></i>
                            </div>
                            <div>
                                <p class="font-bold text-gray-900 dark:text-white">
                                    Post de <span class="text-red-600 dark:text-red-400">{{ $item->course->course_name ?? 'Comunidade' }}</span>
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
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 mt-4">
                                @foreach ($item->images as $imagePath)
                                    <img src="{{ asset('storage/' . $imagePath) }}" alt="Imagem do Post" class="rounded-lg object-cover w-full h-40 shadow-md">
                                @endforeach
                            </div>
                        @endif
    
                        <!-- Footer do Post: Respostas -->
                        <div class="mt-4 border-t border-gray-100 dark:border-gray-700 pt-4 flex items-center justify-between text-sm text-gray-500 dark:text-gray-400">
                            <div class="flex items-center">
                                <i class="ph-fill ph-chat-circle text-lg mr-2 text-red-500"></i>
                                {{ $item->replies->count() }} Respostas
                            </div>
                            <span class="text-gray-500 font-medium flex items-center">
                                Veja na página do curso <i class="ph ph-arrow-right ml-1"></i>
                            </span>
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
    
    
    <!-- MODAL DE BOAS-VINDAS (Mantido sem alteração) -->
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
                         onerror="this.onerror=null;this.src='https://placehold.co/64x64/990000/ffffff?text=LOGO'">
    
                    <h3 id="modal-title" class="text-3xl font-extrabold text-gray-900 dark:text-white mb-3">
                        Bem-vindo(a) à Comunidade!
                    </h3>
                    <p class="text-gray-600 dark:text-gray-400 text-lg mb-8">
                        Seu portal de eventos e comunicações agora é a tela principal! 
                        Encontre todos os eventos, notícias e posts em um só lugar, independente do seu papel na escola.
                    </p>
                    
                    <button onclick="document.getElementById('welcome-modal').classList.add('hidden')"
                            class="w-full inline-flex justify-center rounded-lg border border-transparent shadow-md px-6 py-3 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition duration-150 sm:text-lg">
                        Começar a Explorar o Feed
                    </button>
                </div>
            </div>
        </div>
    @endif
    
    <!-- IMPORTANTE: Incluir Ícones Phosphor aqui para garantir o carregamento antes do script de reação -->
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
    
    <script>
        // Lógica de Reações (Curtir e Salvar)
        function toggleReaction(button) {
            const eventId = button.getAttribute('data-event-id');
            const reactionType = button.getAttribute('data-reaction-type');
            let isActive = button.getAttribute('data-is-active') === 'true';

            // Elementos para atualização
            const icon = button.querySelector(`#icon-${reactionType}-${eventId}`);
            const countElement = button.querySelector(`#count-${reactionType}-${eventId}`);
            const currentCount = countElement ? parseInt(countElement.textContent) : 0;

            // Bloqueia cliques durante a requisição para evitar spam
            button.disabled = true;

            // 1. Simulação de Otimismo (Atualização imediata na UI)
            // Atualiza o estado visual
            isActive = !isActive;
            button.setAttribute('data-is-active', isActive);

            if (reactionType === 'like') {
                if (isActive) {
                    icon.classList.remove('ph');
                    icon.classList.add('ph-fill', 'text-red-500');
                    countElement.textContent = currentCount + 1;
                } else {
                    icon.classList.remove('ph-fill', 'text-red-500');
                    icon.classList.add('ph');
                    countElement.textContent = currentCount - 1;
                }
            } else if (reactionType === 'save') {
                // Manter o destaque amarelo para Salvar
                if (isActive) {
                    icon.classList.remove('ph');
                    icon.classList.add('ph-fill', 'text-yellow-500');
                } else {
                    icon.classList.remove('ph-fill', 'text-yellow-500');
                    icon.classList.add('ph');
                }
            }
            
            // 2. Requisição AJAX (fetch) para o seu Laravel Controller
            fetch(`/events/${eventId}/react`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}' // Obrigatório para Laravel
                },
                body: JSON.stringify({ reaction_type: reactionType })
            })
            .then(response => {
                // Se a resposta não for OK, reverte a UI
                if (!response.ok) {
                    throw new Error('Server response not OK');
                }
                return response.json();
            })
            .then(data => {
                console.log(`Reação de ${reactionType} ${data.status} com sucesso.`, data);
            })
            .catch(error => {
                console.error(`Erro ao reagir ao evento ${eventId}:`, error);
                
                // 3. Reversão de Otimismo (Undo UI update)
                // Se falhar, reverte o estado visual e a contagem
                isActive = !isActive;
                button.setAttribute('data-is-active', isActive);
                
                if (reactionType === 'like') {
                    if (!isActive) { // Reverte para o estado original (desativado)
                        icon.classList.remove('ph-fill', 'text-red-500');
                        icon.classList.add('ph');
                        countElement.textContent = currentCount; // Volta a contagem original
                    } else { // Reverte para o estado original (ativado)
                        icon.classList.remove('ph');
                        icon.classList.add('ph-fill', 'text-red-500');
                        countElement.textContent = currentCount + 1; // Volta a contagem original
                    }
                } else if (reactionType === 'save') {
                    // Reversão do destaque amarelo
                    if (!isActive) {
                        icon.classList.remove('ph-fill', 'text-yellow-500');
                        icon.classList.add('ph');
                    } else {
                        icon.classList.remove('ph');
                        icon.classList.add('ph-fill', 'text-yellow-500');
                    }
                }

                // Em um app real, você mostraria uma mensagem de erro aqui
            })
            .finally(() => {
                button.disabled = false; // Desbloqueia o botão
            });
        }

        document.addEventListener('DOMContentLoaded', function () {
            // Inicialização do Modal (mantida)
            const modal = document.getElementById('welcome-modal');
            if (modal && !modal.classList.contains('hidden')) {
                document.body.style.overflow = 'hidden';
            }
            modal?.querySelector('button').addEventListener('click', () => {
                document.body.style.overflow = '';
            });

            // Adiciona listener aos botões de reação
            document.querySelectorAll('.reaction-button').forEach(button => {
                button.addEventListener('click', function() {
                    toggleReaction(this);
                });
            });
        });
    </script>
    
</x-app-layout>
