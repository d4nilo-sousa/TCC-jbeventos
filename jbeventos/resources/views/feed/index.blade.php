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
    
    <div class="py-10 bg-gray-50 min-h-screen"> 
        {{-- Aumentado o max-w para suportar 2 colunas e mx-auto para centralizar --}}
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <div class="bg-white shadow rounded-xl p-4 sm:p-6 border border-gray-200">
                <h1 class="text-3xl font-extrabold text-gray-900 mb-2">Olá, {{ $user->name }}!</h1>
                <p class="text-gray-600">
                    Descubra eventos importantes e posts das áreas que você segue.
                </p>
            </div>
    
            {{-- FILTRAGEM E SEPARAÇÃO DE EVENTOS E POSTS --}}
            @php
                // Separa os itens do feed em duas coleções distintas para o layout de colunas
                // O FeedController já filtrou os itens; aqui só os separamos
                $events = $feedItems->filter(fn($item) => $item->type === 'event');
            @endphp

            @if ($events->isNotEmpty() || true) {{-- Mantemos o layout mesmo se só houver posts (gerenciados pelo Livewire) --}}
                {{-- LAYOUT PRINCIPAL DE DUAS COLUNAS --}}
                {{-- Aplicado um grid de 2 colunas para telas maiores que 'md' --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    
                    {{-- COLUNA ESQUERDA: EVENTOS --}}
                    <div class="space-y-6">
                        <h2 class="text-2xl font-bold text-gray-800 border-b border-red-200 pb-2"><i class="ph ph-calendar-blank bg-red-600 text-white rounded-full p-1"></i> Eventos</h2>
                        @forelse ($events as $item)
                            {{-- CARTÃO DE EVENTO --}}
                            @php
                                $isLiked = $item->reactions->contains('user_id', $user->id) && $item->reactions->contains('reaction_type', 'like');
                                $isSaved = $item->reactions->contains('user_id', $user->id) && $item->reactions->contains('reaction_type', 'save');
                                $likeCount = $item->reactions->where('reaction_type', 'like')->count();
                            @endphp

                            <div id="event-{{ $item->id }}" class="feed-card bg-white rounded-xl overflow-hidden transform transition duration-300 hover:shadow-2xl border border-red-200">
                                
                                <a href="{{ route('events.show', $item) }}" class="block">
                                    <div class="relative h-64 w-full bg-gray-200"> 
                                        @if ($item->event_image)
                                            <img class="w-full h-full object-cover" 
                                                src="{{ asset('storage/' . $item->event_image) }}" 
                                                alt="{{ $item->event_name }}" 
                                                loading="lazy">
                                        @else
                                            <div class="flex flex-col items-center justify-center w-full h-full text-red-500">
                                                <i class="ph-bold ph-calendar-blank text-6xl"></i>
                                                <p class="mt-2 text-sm">Evento Sem Imagem</p>
                                            </div>
                                        @endif
                                        <span class="absolute top-4 right-4 bg-red-600 text-white text-xs font-bold px-3 py-1.5 rounded-full shadow-lg">EVENTO</span>
                                    </div>
                                </a>
                                                                
                                <div class="p-6">
                                    <div class="flex items-center mb-3 text-sm text-gray-500">
                                        <i class="ph-fill ph-graduation-cap mr-2 text-red-500"></i>
                                        Coordenador: <span class="font-semibold text-gray-800 ml-1">
                                            @php
                                                // 1. Pega o nome do Coordenador que criou o evento (eventCoordinator)
                                                $coordenadorNome = optional(optional($item->eventCoordinator)->userAccount)->name;
                                                
                                                // 2. Pega o nome do Curso associado ao evento (eventCourse)
                                                $cursoNome = optional($item->eventCourse)->course_name;
                                            @endphp
                                            
                                            {{-- Exibe o nome do Coordenador Criador, se não, o nome do Curso, se não, o fallback --}}
                                            {{ $coordenadorNome ?? $cursoNome ?? 'Curso não definido' }}
                                        </span>
                                    </div>
                                    
                                    <a href="{{ route('events.show', $item) }}" class="block">
                                        <h2 class="text-2xl font-bold text-gray-900 mb-2 hover:text-red-600 transition">
                                            {{ $item->event_name }}
                                        </h2>
                                    </a>
                                    
                                    <p class="text-gray-700 line-clamp-3 mb-4 text-base">
                                        {{ $item->event_description }}
                                    </p>
                                    
                                    {{-- REMOVIDO: dark:text-gray-400, dark:border-gray-700 --}}
                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-sm text-gray-600 border-t border-gray-100 pt-4">
                                        <div class="flex items-center">
                                            <i class="ph-fill ph-calendar-check mr-2 text-red-600"></i>
                                            <span class="font-medium">Data:</span> {{ $item->event_scheduled_at->format('d/m/Y H:i') }}
                                        </div>
                                        <div class="flex items-center">
                                            <i class="ph-fill ph-map-pin mr-2 text-red-600"></i>
                                            <span class="font-medium">Local:</span> {{ $item->event_location }}
                                        </div>
                                    </div>
                                    
                                    {{-- REMOVIDO: dark:border-gray-700 --}}
                                    <div class="mt-5 flex items-center border-t border-gray-100 pt-4 space-x-6">
                                        
                                        {{-- Botão de Curtir --}}
                                        <button 
                                            class="reaction-button flex items-center text-gray-600 transition"
                                            data-event-id="{{ $item->id }}" 
                                            data-reaction-type="like"
                                            data-is-active="{{ $isLiked ? 'true' : 'false' }}"
                                        >
                                            <i class="ph-bold ph-heart text-2xl mr-1 {{ $isLiked ? 'text-red-500 ph-fill' : 'hover:text-red-500' }}" id="icon-like-{{ $item->id }}"></i>
                                            <span class="text-sm font-medium" id="count-like-{{ $item->id }}">{{ $likeCount }}</span>
                                            <span class="text-sm font-medium ml-1 hidden sm:inline">Curtidas</span>
                                        </button>

                                        {{-- Botão de Salvar --}}
                                        <button 
                                            class="reaction-button flex items-center text-gray-600 transition"
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
                        @empty
                            <div class="p-4 bg-white rounded-xl shadow border border-gray-100 text-center text-gray-500">Nenhum evento para exibir.</div>
                        @endforelse
                    </div>

                    {{-- COLUNA DIREITA: POSTS (Gerenciada pelo Livewire) --}}
                    <div class="space-y-6">
                        <h2 class="text-2xl font-bold text-gray-800 border-b border-gray-200 pb-2"><i class="ph ph-article bg-red-600 text-white rounded-full p-1"></i> Posts</h2>
                        
                        {{-- INTEGRAÇÃO LIVEWIRE: O componente Livewire FeedPosts fará o loop e a paginação dos posts --}}
                        @livewire('feed-posts')
                        
                        {{-- Caso o Livewire não consiga renderizar, mantemos um fallback básico (embora o Livewire deva renderizar o seu próprio estado vazio) --}}
                        @if ($events->isEmpty() && $posts->isEmpty())
                             <div class="p-4 bg-white rounded-xl shadow border border-gray-100 text-center text-gray-500">Nenhum post para exibir.</div>
                        @endif
                    </div>

                </div>
            @else
                {{-- MENSAGEM QUANDO NÃO HOUVER ITENS (Fallback para Livewire em caso de erro no FeedController) --}}
                <div class="max-w-2xl mx-auto space-y-6">
                    <div class="text-center py-10 bg-white rounded-xl shadow-lg border border-gray-200">
                        <i class="ph-bold ph-magnifying-glass text-5xl text-gray-400 mb-4"></i>
                        <p class="text-xl font-semibold text-gray-700">Nenhum conteúdo no feed.</p>
                        <p class="text-gray-500 mt-2">Parece que ainda não há eventos ou posts recentes. Tente explorar novos cursos!</p>
                        
                        {{-- Chamada do Livewire para garantir que pelo menos o post form apareça, se for o caso --}}
                         <div class="mt-6">@livewire('feed-posts')</div>
                    </div>
                </div>
            @endif
    
            <div class="text-center py-6 text-gray-500">
                <p>Você chegou ao final do feed.</p>
            </div>
        </div>
    </div>
    
    
    @if (isset($isFirstLogin) && $isFirstLogin)
        <div id="welcome-modal" class="fixed inset-0 bg-gray-900 bg-opacity-75 z-50 flex items-center justify-center transition-opacity duration-300">
            <div class="bg-white rounded-xl shadow-2xl max-w-lg w-full p-8 m-4 transform transition-transform duration-300 scale-100"
                role="dialog" aria-modal="true" aria-labelledby="modal-title">
    
                <div class="text-center">
                    {{-- PLACEHOLDER PARA O LOGO DO SISTEMA --}}
                    <img src="{{ asset('imgs/logoJb.png') }}" 
                        alt="Logo do Sistema de Eventos" 
                        class="h-16 mx-auto mb-4 object-contain"
                        onerror="this.onerror=null;this.src='https://placehold.co/64x64/990000/ffffff?text=LOGO'">
    
                    {{-- REMOVIDO: dark:text-white --}}
                    <h3 id="modal-title" class="text-3xl font-extrabold text-gray-900 mb-3">
                        Bem-vindo(a) à Comunidade!
                    </h3>
                    {{-- REMOVIDO: dark:text-gray-400 --}}
                    <p class="text-gray-600 text-lg mb-8">
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
            
            // 2. Requisição AJAX (fetch) para o backend
            fetch(`/events/${eventId}/react`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}' // Obrigatório 
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