<x-app-layout title="Feed Principal">
    @push('styles')
        <style>
            /* Estilos básicos para o feed */
            .reaction-button {
                transition: color 0.15s ease-in-out, transform 0.1s ease-in-out;
                padding: 0.5rem;
                border-radius: 9999px;
                /* Arredondado completo */
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

            /* O CSS do modal foi movido para o Tailwind (classes 'opacity-0' e 'scale-95') para melhor performance */
        </style>
    @endpush

    <div class="py-10 bg-gray-50 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-16 space-y-6">

            <div class="bg-white shadow rounded-xl p-4 sm:p-6 border border-gray-200">
                <h1 class="text-2xl font-extrabold text-gray-900 mb-2">Olá, {{ $user->name }}!</h1>
                <p class="text-gray-600">
                    Descubra eventos importantes e posts das áreas que você segue.
                </p>
            </div>

            {{-- FILTRAGEM E SEPARAÇÃO DE EVENTOS E POSTS --}}
            @php
                // Separa os itens do feed em duas coleções distintas para o layout de colunas
                $events = $feedItems->filter(fn($item) => $item->type === 'event');
            @endphp

            @if ($events->isNotEmpty() || true)
                {{-- LAYOUT PRINCIPAL DE DUAS COLUNAS --}}
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-12">

                    {{-- COLUNA ESQUERDA: EVENTOS --}}
                    <div class="space-y-6">
                        <h2 class="text-2xl font-bold text-gray-800 border-b border-red-200 pb-2 flex items-center">
                            <i class="ph ph-calendar-blank bg-red-600 text-white rounded-full p-1 mr-2 text-xl"></i>
                            Eventos
                        </h2>
                        @forelse ($events as $item)
                            <div id="event-{{ $item->id }}"
                                class="feed-card w-full bg-white rounded-xl overflow-hidden transform transition duration-300 hover:shadow-2xl border border-red-200 flex flex-col">

                                {{-- Imagem --}}
                                <a href="{{ route('events.show', $item) }}" class="block">
                                    <div class="relative w-full aspect-video bg-gray-200">
                                        @if ($item->event_image)
                                            <img class="w-full h-full object-cover"
                                                src="{{ asset('storage/' . $item->event_image) }}"
                                                alt="{{ $item->event_name }}" loading="lazy">
                                        @else
                                            <div
                                                class="flex flex-col items-center justify-center w-full h-full text-red-500">
                                                <i class="ph-bold ph-calendar-blank text-6xl"></i>
                                                <p class="mt-2 text-sm">Evento Sem Imagem</p>
                                            </div>
                                        @endif
                                        <span
                                            class="absolute top-4 right-4 bg-red-600 text-white text-xs font-bold px-3 py-1.5 rounded-full shadow-lg">
                                            {{ $item->event_type === 'course' ? 'CURSO' : ($item->event_type === 'general' ? 'GERAL' : '') }}
                                        </span>
                                    </div>
                                </a>

                                {{-- Conteúdo --}}
                                <div class="p-6 flex flex-col gap-2"> <!-- gap-4 → gap-2 -->
                                    <div class="flex items-center mb-1 text-sm text-gray-500"> <!-- mb-3 → mb-1 -->
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
                                        <h2 class="text-2xl font-bold text-gray-900 hover:text-red-600 transition">
                                            {{ $item->event_name }}
                                        </h2>
                                    </a>

                                <div class="flex flex-wrap gap-2 mt-1  text-sm"> <!-- text-xs → text-sm -->
                                    @forelse ($item->eventCategories as $category)
                                        <span
                                            class="bg-gray-100 text-gray-700 px-2 py-1 rounded-full border border-gray-200 mb-3">
                                            <!-- px-2 py-1 → px-3 py-2 -->
                                            {{ $category->category_name }}
                                        </span>
                                    @empty
                                        <span
                                            class="bg-gray-100 text-gray-700 px-3 py-2 rounded-full border border-gray-200">
                                            Sem Categoria
                                        </span>
                                    @endforelse
                                </div>

                                {{-- Datas e Local --}}
                                <div
                                    class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm text-gray-600 border-t border-gray-100 pt-4">
                                    <!-- Local e Data -->
                                    <div class="flex items-center gap-2">
                                        <i class="ph-fill ph-map-pin text-red-600 text-lg"></i>
                                        <span>{{ $item->event_location }}</span>
                                    </div>
                                    <br>
                                    <div class="flex items-center gap-2">
                                        <i class="ph-fill ph-calendar-check text-red-600 text-lg"></i>
                                        <span>{{ \Carbon\Carbon::parse($item->event_scheduled_at)->isoFormat('D [de] MMMM [de] YYYY, [às] HH:mm') }}</span>
                                    </div>
                                </div>
                            </div>
                    </div>
                @empty
                    <div class="p-4 bg-white rounded-xl shadow border border-gray-100 text-center text-gray-500">
                        Nenhum evento para exibir.
                    </div>
            @endforelse
        </div>

        {{-- COLUNA DIREITA: POSTS (Gerenciada pelo Livewire) --}}
        <div class="space-y-6">
            <h2 class="text-2xl font-bold text-gray-800 border-b border-gray-200 pb-2 flex items-center">
                <i class="ph ph-article bg-red-600 text-white rounded-full p-1 mr-2 text-xl"></i> Posts
            </h2>

            {{-- INTEGRAÇÃO LIVEWIRE: O componente Livewire FeedPosts fará o formulário, loop e a paginação dos posts --}}
            @livewire('feed-posts')
        </div>

    </div>
@else
    {{-- MENSAGEM QUANDO NÃO HOUVER ITENS (Fallback: só há posts ou nada) --}}
    <div class="max-w-2xl mx-auto space-y-6">
        <div class="text-center py-10 bg-white rounded-xl shadow-lg border border-gray-200">
            <i class="ph-bold ph-magnifying-glass text-5xl text-gray-400 mb-4"></i>
            <p class="text-xl font-semibold text-gray-700">Nenhum conteúdo no feed.</p>
            <p class="text-gray-500 mt-2">Parece que ainda não há eventos recentes. Tente explorar novos
                cursos!</p>

            {{-- Chamada do Livewire para garantir que pelo menos o post form e posts apareçam --}}
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
        <div id="welcome-modal"
            class="fixed inset-0 bg-gray-900 bg-opacity-75 z-50 flex items-center justify-center transition-opacity duration-300 ease-out opacity-0 pointer-events-none">
            <div id="welcome-modal-content"
                class="bg-white rounded-xl shadow-2xl max-w-lg w-full p-8 m-4 transform transition duration-300 ease-out scale-95"
                role="dialog" aria-modal="true" aria-labelledby="modal-title">

                <div class="text-center">
                    {{-- PLACEHOLDER PARA O LOGO DO SISTEMA --}}
                    <img src="{{ asset('imgs/logoJb.png') }}" alt="Logo do Sistema de Eventos"
                        class="h-16 mx-auto mb-4 object-contain"
                        onerror="this.onerror=null;this.src='https://placehold.co/64x64/990000/ffffff?text=LOGO'">

                    <h3 id="modal-title" class="text-3xl font-extrabold text-gray-900 mb-3">
                        Bem-vindo(a) ao feed do JB Eventos!
                    </h3>
                    <p class="text-gray-600 text-lg mb-8">
                        Fique por dentro dos principais eventos e posts da nossa escola!
                        Encontre todos os eventos, notícias e posts em um só lugar, independente do seu papel na escola.
                    </p>

                    <button onclick="closeWelcomeModal()"
                        class="w-full inline-flex justify-center rounded-lg border border-transparent shadow-md px-6 py-3 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition duration-150 sm:text-lg">
                        Começar a Explorar o Feed
                    </button>
                </div>
            </div>
        </div>
    @endif

    <script>
        // Função unificada para fechar o modal com transição suave
        function closeWelcomeModal() {
            const modal = document.getElementById('welcome-modal');
            if (modal) {
                // Inicia a animação de saída: opacidade para 0 e escala para 95%
                modal.classList.remove('opacity-100', 'pointer-events-auto');
                modal.classList.add('opacity-0');
                document.getElementById('welcome-modal-content').classList.remove('scale-100');
                document.getElementById('welcome-modal-content').classList.add('scale-95');
                document.body.style.overflow = ''; // Habilita o scroll do body

                // Remove o modal do DOM após a transição para limpar o código
                setTimeout(() => {
                    modal.remove();
                }, 300); // 300ms é a duração da transição
            }
        }

        // Função para abrir o modal, usada apenas no DOMContentLoaded
        function openWelcomeModal() {
            const modal = document.getElementById('welcome-modal');
            if (modal) {
                // Define as classes para o estado final da transição (visível)
                modal.classList.add('opacity-100', 'pointer-events-auto');
                modal.classList.remove('opacity-0');
                document.getElementById('welcome-modal-content').classList.add('scale-100');
                document.getElementById('welcome-modal-content').classList.remove('scale-95');
                document.body.style.overflow = 'hidden';
            }
        }

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
            const oldIsActive = isActive; // Salva o estado antigo para reversão
            isActive = !isActive;
            button.setAttribute('data-is-active', isActive);

            if (reactionType === 'like') {
                if (isActive) {
                    // Adiciona ph-fill, remove hover
                    icon.classList.remove('ph', 'hover:text-red-500');
                    icon.classList.add('ph-fill',
                        'text-red-500'); // ph-bold já deve estar no HTML, mantive a lógica de JS mais simples
                    if (countElement) countElement.textContent = currentCount + 1;
                } else {
                    // Remove ph-fill, adiciona hover
                    icon.classList.remove('ph-fill', 'text-red-500');
                    icon.classList.add('ph', 'hover:text-red-500');
                    if (countElement) countElement.textContent = currentCount - 1;
                }
            } else if (reactionType === 'save') {
                if (isActive) {
                    // Adiciona ph-fill, remove hover
                    icon.classList.remove('ph', 'hover:text-yellow-500');
                    icon.classList.add('ph-fill', 'text-yellow-500');
                    button.setAttribute('aria-label', 'Remover dos salvos');
                } else {
                    // Remove ph-fill, adiciona hover
                    icon.classList.remove('ph-fill', 'text-yellow-500');
                    icon.classList.add('ph', 'hover:text-yellow-500');
                    button.setAttribute('aria-label', 'Salvar evento');
                }
            }

            // Reverte a UI em caso de falha na requisição
            const undoUiChanges = () => {
                button.setAttribute('data-is-active', oldIsActive);

                if (reactionType === 'like') {
                    if (oldIsActive) { // Reverte para o estado original (ativado)
                        icon.classList.remove('ph', 'hover:text-red-500');
                        icon.classList.add('ph-fill', 'text-red-500');
                        if (countElement) countElement.textContent = currentCount;
                    } else { // Reverte para o estado original (desativado)
                        icon.classList.remove('ph-fill', 'text-red-500');
                        icon.classList.add('ph', 'hover:text-red-500');
                        if (countElement) countElement.textContent = currentCount;
                    }
                } else if (reactionType === 'save') {
                    if (oldIsActive) {
                        icon.classList.remove('ph', 'hover:text-yellow-500');
                        icon.classList.add('ph-fill', 'text-yellow-500');
                        button.setAttribute('aria-label', 'Remover dos salvos');
                    } else {
                        icon.classList.remove('ph-fill', 'text-yellow-500');
                        icon.classList.add('ph', 'hover:text-yellow-500');
                        button.setAttribute('aria-label', 'Salvar evento');
                    }
                }
            }

            // 2. Requisição AJAX (fetch) para o backend
            fetch(`/events/${eventId}/react`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        reaction_type: reactionType
                    })
                })
                .then(response => {
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
                    undoUiChanges();
                })
                .finally(() => {
                    button.disabled = false; // Desbloqueia o botão
                });
        }

        document.addEventListener('DOMContentLoaded', function() {
            // Inicialização e Animação do Modal (Chamado se o blade o renderizar)
            const modal = document.getElementById('welcome-modal');
            if (modal) {
                // Inicia a animação de entrada do modal 
                openWelcomeModal();
            }

            // Adiciona listener aos botões de reação
            document.querySelectorAll('.reaction-button').forEach(button => {
                button.addEventListener('click', function() {
                    toggleReaction(this);
                });
            });
        });
    </script>

</x-app-layout>
