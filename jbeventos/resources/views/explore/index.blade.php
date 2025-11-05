<x-app-layout>
    <div class="py-10 bg-gray-50 min-h-screen">
        <div class="max-w-[1400px] mx-auto sm:px-6 lg:px-16 space-y-8"> {{-- Aumentei o espaçamento vertical aqui --}}

            <div class="grid grid-cols-1 lg:grid-cols-4 gap-10"> {{-- Aumentei o espaçamento do grid --}}

                {{-- Coluna 1: Barra de Pesquisa e Filtros (Filtro Lateral Sticky) --}}
                <div class="lg:col-span-1">
                    <div class="sticky top-6 space-y-6"> {{-- Mantive sticky e ajustei o topo para melhor visualização --}}

                        {{-- Menu de Abas/Filtros com Estilo de Navegação --}}
                        <div class="bg-white overflow-hidden shadow-2xl sm:rounded-2xl p-6 border border-gray-100">
                            <h3 class="text-lg font-bold text-gray-700 mb-4 border-b pb-2 flex items-center">
                                <i class="ph ph-funnel-simple mr-2 text-red-600"></i>
                                Filtrar por Categoria
                            </h3>
                            <nav class="space-y-1" aria-label="Tabs">
                                @php
                                    // Abas disponíveis (sem a antiga 'Destaques')
                                    $tabs = [
                                        'events' => ['label' => 'Eventos', 'icon' => 'ph ph-calendar-blank'],
                                        'posts' => ['label' => 'Posts', 'icon' => 'ph ph-feather'],
                                        'courses' => ['label' => 'Cursos', 'icon' => 'ph ph-book-open'],
                                        'coordinators' => ['label' => 'Coordenadores', 'icon' => 'ph ph-user-circle'],
                                    ];

                                    // Define aba padrão ao carregar a página (já que 'all' foi removida)
                                    $activeTab = request('tab', 'events');
                                @endphp

                                @foreach ($tabs as $key => $tab)
                                    <button type="button" data-tab="{{ $key }}"
                                        class="tab-button w-full flex items-center px-4 py-3 rounded-xl text-left transition-all duration-300 group
                                            {{ $activeTab === $key
                                                ? 'bg-red-600 text-white shadow-lg shadow-red-500/30 font-semibold'
                                                : 'text-gray-600 hover:bg-red-50 hover:text-red-700 font-medium' }}">
                                        <i
                                            class="{{ $tab['icon'] }} mr-3 text-xl transition-colors duration-300 {{ $activeTab === $key ? 'text-white' : 'text-red-500 group-hover:text-red-700' }}"></i>
                                        <span class="text-base">{{ $tab['label'] }}</span>
                                    </button>
                                @endforeach
                            </nav>
                        </div>
                    </div>
                </div>

                {{-- Coluna 2: Conteúdo Principal e Resultados --}}
                <div class="lg:col-span-3">

                    <div id="results-container" class="space-y-12"> {{-- Aumentei o espaçamento entre as seções --}}

                        {{--
                        |===================================|
                        | Seção 'Destaques' (Padrão)        |
                        |===================================|
                        --}}
                        {{-- Seção fixa de Destaques da Comunidade (sempre visível) --}}
                        <div id="community-highlights" class="tab-content">
                            <h2
                                class="text-4xl font-extrabold text-gray-900 mb-8 border-b-4 border-red-500/70 pb-3 flex items-center">
                                <i class="ph ph-star-fill text-red-500 mr-3 text-3xl"></i>
                                Destaques da Comunidade
                            </h2>

                            {{-- Destaque: Eventos Populares (Melhoria no Design e UX de Rolagem) --}}
                            {{-- ADICIONEI id="events-section" para o scroll funcionar --}}
                            <div id="events-section"
                                class="bg-white p-6 rounded-2xl shadow-2xl border border-gray-100 mb-10">
                                <h3 class="text-2xl font-bold text-gray-700 mb-6 flex justify-between items-center">
                                    Eventos Populares (Top 5) 🔥
                                    <a href="{{ route('events.index') }}" {{-- ALTERADO: para a aba de explorar --}}
                                        class="text-base text-red-600 hover:text-red-800 font-semibold transition duration-200 flex items-center">
                                        Ver todos <i class="ph ph-arrow-right ml-1 text-lg"></i>
                                    </a>
                                </h3>

                                <div class="relative group">
                                    {{-- Botões de Seta (Melhor visibilidade) --}}
                                    <button id="left-arrow" onclick="scrollSection('event-highlight-container', -320)"
                                        class="absolute left-0 z-20 top-1/2 -translate-y-1/2 size-10 p-1 bg-white border border-gray-300 rounded-full shadow-xl hidden lg:group-hover:flex items-center justify-center hover:bg-red-50 transition duration-200">
                                        <i class="ph-bold ph-caret-left text-xl text-red-600"></i>
                                    </button>

                                    <div id="event-highlight-container"
                                        class="flex overflow-x-scroll snap-x snap-mandatory space-x-6 pb-4 scrollbar-hide">

                                        {{-- Usamos ->take(5) para garantir apenas os 5 primeiros eventos da lista --}}
                                        @forelse ($events->take(5) as $event)
                                            {{-- INÍCIO: COMPONENTE CARD DE EVENTO --}}
                                            <div class="flex-shrink-0 w-80 snap-center">
                                                <a href="{{ route('events.show', $event->id) }}"
                                                    class="block bg-white border border-gray-200 rounded-xl shadow-lg overflow-hidden relative transform transition duration-300 hover:scale-[1.01] hover:shadow-2xl">

                                                    {{-- IMAGEM DO EVENTO COM PLACEHOLDER MELHORADO --}}
                                                    <div class="relative w-full h-44 bg-gray-200">
                                                        @if ($event->event_image)
                                                            <img src="{{ asset('storage/' . $event->event_image) }}"
                                                                alt="{{ $event->event_name }}"
                                                                class="w-full h-full object-cover">
                                                        @else
                                                            <div
                                                                class="flex flex-col items-center justify-center w-full h-full text-red-500">
                                                                <i class="ph-bold ph-calendar-blank text-6xl"></i>
                                                                <p class="mt-2 text-sm">Sem Imagem de Capa</p>
                                                            </div>
                                                        @endif

                                                        {{-- NOVO: Badge de Curtidas (Likes) no canto superior direito --}}
                                                        @if (isset($event->likes_count) && $event->likes_count > 0)
                                                            <span
                                                                class="absolute top-3 right-3 flex items-center bg-red-600 text-white text-xs font-bold px-2 py-1 rounded-full shadow-lg">
                                                                <i class="ph-fill ph-heart mr-1 text-base"></i>
                                                                {{ $event->likes_count }} Curtidas
                                                            </span>
                                                        @endif
                                                        {{-- FIM: Badge de Curtidas --}}
                                                    </div>

                                                    {{-- Nome do evento --}}
                                                    <div class="px-6 pt-6 pb-0">
                                                        <p
                                                            class="font-bold text-gray-900 text-lg line-clamp-2 break-words mb-0">
                                                            {{ $event->event_name }}
                                                        </p>
                                                    </div>

                                                    {{-- Data e hora --}}
                                                    <div class="px-6 pb-6 mt-0.5">
                                                        @if ($event->event_scheduled_at)
                                                            <p
                                                                class="flex items-center gap-1 text-gray-500 mt-2 text-base">
                                                                <i
                                                                    class="ph-fill ph-clock-clockwise text-red-600 text-lg"></i>
                                                                {{ \Carbon\Carbon::parse($event->event_scheduled_at)->isoFormat('D [de] MMMM [de] YYYY, [às] HH:mm') }}
                                                            </p>
                                                        @endif
                                                    </div>
                                                </a>
                                            </div>
                                            {{-- FIM: COMPONENTE CARD DE EVENTO --}}
                                        @empty
                                            <p class="text-gray-500 text-center py-8 w-full">Nenhum evento em destaque
                                                no momento. ✨</p>
                                        @endforelse
                                    </div>

                                    <button id="right-arrow" onclick="scrollSection('event-highlight-container', 320)"
                                        class="absolute right-0 z-20 top-1/2 -translate-y-1/2 size-10 p-1 bg-white border border-gray-300 rounded-full shadow-xl hidden lg:group-hover:flex items-center justify-center hover:bg-red-50 transition duration-300">
                                        <i class="ph-bold ph-caret-right text-xl text-red-600"></i>
                                    </button>
                                </div>
                            </div>

                            {{-- Destaque: Posts (Principais Discussões) --}}
                            {{-- ADICIONEI id="posts-section" --}}
                            <div id="posts-section"
                                class="bg-white p-6 rounded-2xl shadow-2xl border border-gray-100 mb-10">
                                <h3 class="text-2xl font-bold text-gray-700 mb-6 flex justify-between items-center">
                                    Principais Discussões (Top 3) 🗣️
                                </h3>
                                <div class="grid grid-cols-1 gap-4">
                                    {{-- Aqui iteramos sobre os posts JÁ ordenados e limitados a 3 --}}
                                    @forelse ($posts->take(3) as $post)
                                        {{-- Exemplo do wrapper do post (adapte se a estrutura for diferente) --}}
                                        <a href="{{ route('courses.show', $post->course->id) }}#post-{{ $post->id }}"
                                            id="post-{{ $post->id }}"
                                            class="post-card flex bg-white p-4 rounded-xl border border-gray-200 shadow-lg overflow-hidden relative transform transition duration-300 hover:scale-[1.01] hover:shadow-2xl">
                                            <div class="flex-shrink-0 mr-4">
                                                @if ($post->images && count($post->images) > 0)
                                                    <img src="{{ asset('storage/' . $post->images[0]) }}"
                                                        alt="Imagem do post"
                                                        class="size-20 object-cover rounded-lg border border-red-400/50 shadow">
                                                @else
                                                    <div
                                                        class="size-20 bg-gray-100 rounded-lg flex items-center justify-center text-red-500 border border-red-300/50 shadow">
                                                        <i class="ph ph-image-square text-3xl"></i>
                                                    </div>
                                                @endif
                                            </div>

                                            <div class="flex-grow">
                                                <p
                                                    class="text-xs font-semibold text-gray-500 mb-1 uppercase tracking-wide">
                                                    POST em <span
                                                        class="text-red-600 font-bold">{{ $post->course->course_name ?? 'Curso Desconhecido' }}</span>
                                                </p>
                                                <h4
                                                    class="font-extrabold text-lg text-gray-900 leading-snug line-clamp-2">
                                                    {{ $post->content }}
                                                </h4>
                                                <p class="text-sm text-gray-600 mt-2 flex items-center">
                                                    Por <span
                                                        class="font-bold text-red-600 ml-1">{{ $post->author->name }}</span>
                                                    <span class="text-xs text-gray-400 ml-3 flex items-center">
                                                        <i class="ph ph-clock-counter-clockwise mr-1"></i>
                                                        {{ $post->created_at->diffForHumans() }}
                                                    </span>
                                                    <span class="text-xs text-red-600 ml-3 flex items-center font-bold">
                                                        <i class="ph ph-chat-circle-dots mr-1 text-lg"></i>
                                                        {{ $post->replies_count ?? $post->replies->count() }}
                                                    </span>
                                                </p>
                                            </div>
                                        </a>
                                    @empty
                                        <p class="text-gray-500 col-span-full py-4 text-center">Nenhuma discussão em
                                            destaque no momento. 😔</p>
                                    @endforelse
                                </div>
                            </div>


                            {{-- Destaque: Cursos Populares (Top 5) --}}
                            {{-- ADICIONEI id="courses-section" --}}
                            <div id="courses-section"
                                class="bg-white p-6 rounded-2xl shadow-2xl border border-gray-100 mb-10">
                                <h3 class="text-2xl font-bold text-gray-700 mb-6 flex justify-between items-center">
                                    Cursos Populares (Top 5) ⭐
                                    <a href="{{ route('courses.index') }}" {{-- ALTERADO: para a aba de explorar --}}
                                        class="text-base text-red-600 hover:text-red-800 font-semibold transition duration-200 flex items-center">
                                        Ver todos <i class="ph ph-arrow-right ml-1 text-lg"></i>
                                    </a>
                                </h3>

                                <div class="relative group">
                                    {{-- Botão Esquerdo --}}
                                    <button id="left-arrow-courses"
                                        onclick="scrollSection('course-highlight-container', -320)"
                                        class="absolute left-0 z-20 top-1/2 -translate-y-1/2 size-10 p-1 bg-white border border-gray-300 rounded-full shadow-xl hidden lg:group-hover:flex items-center justify-center hover:bg-red-50 transition duration-300 opacity-0 pointer-events-none">
                                        <i class="ph-bold ph-caret-left text-xl text-red-600"></i>
                                    </button>

                                    {{-- Container de cursos com scroll horizontal --}}
                                    <div id="course-highlight-container"
                                        class="flex overflow-x-scroll snap-x snap-mandatory space-x-6 pb-4 scrollbar-hide">

                                        {{-- Usamos ->take(5) para garantir que apenas os 5 trazidos pelo Controller sejam iterados --}}
                                        @forelse ($courses as $course)
                                            {{-- CARD DE CURSO (com badge de seguidores) --}}
                                            <a href="{{ route('courses.show', $course->id) }}"
                                                class="flex-shrink-0 w-80 flex flex-col items-center text-center group relative snap-center
            bg-gray-50 p-4 rounded-xl border border-gray-200 shadow-lg overflow-hidden transform transition duration-300
            hover:scale-[1.01] hover:shadow-2xl">

                                                @if ($course->course_icon)
                                                    <img src="{{ asset('storage/' . $course->course_icon) }}"
                                                        alt="{{ $course->course_name }}"
                                                        class="size-16 rounded-full object-cover border-4 border-red-500/50 mb-3 shadow-lg group-hover:scale-105 transition duration-300">
                                                @else
                                                    <div
                                                        class="size-16 flex items-center justify-center rounded-full border-4 border-red-500/50 mb-3 shadow-lg bg-gray-100 group-hover:scale-105 transition duration-300">
                                                        <i class="ph ph-book-open text-2xl text-red-600"></i>
                                                    </div>
                                                @endif



                                                {{-- NOVO: Badge de Contagem de Seguidores --}}
                                                @if (isset($course->followers_count) && $course->followers_count > 0)
                                                    <span
                                                        class="absolute top-2 right-2 flex items-center bg-red-600 text-white text-xs font-bold px-2 py-1 rounded-full shadow-lg">
                                                        <i class="ph-fill ph-user-plus mr-1 text-base"></i>
                                                        {{ $course->followers_count }}
                                                    </span>
                                                @endif

                                                <p
                                                    class="font-extrabold text-base text-gray-900 leading-tight line-clamp-2">
                                                    {{ $course->course_name }}
                                                </p>
                                                <p class="text-xs font-bold text-red-600 mt-1 flex items-center">
                                                    <i class="ph ph-chalkboard-teacher mr-1 text-sm"></i> Curso
                                                </p>
                                            </a>
                                        @empty
                                            <p class="text-gray-500 text-center py-8 w-full">Nenhum curso em destaque.
                                            </p>
                                        @endforelse
                                    </div>

                                    {{-- Botão Direito --}}
                                    <button id="right-arrow-courses"
                                        onclick="scrollSection('course-highlight-container', 320)"
                                        class="absolute right-0 z-20 top-1/2 -translate-y-1/2 size-10 p-1 bg-white border border-gray-300 rounded-full shadow-xl hidden lg:group-hover:flex items-center justify-center hover:bg-red-50 transition duration-300">
                                        <i class="ph-bold ph-caret-right text-xl text-red-600"></i>
                                    </button>
                                </div>
                            </div>

                            {{-- Destaque: Coordenadores Mais Ativos (Top 5 Eventos) --}}
                            <div class="bg-white p-6 rounded-2xl shadow-2xl border border-gray-100 mb-10">
                                <h3 class="text-2xl font-bold text-gray-700 mb-6 flex justify-between items-center">
                                    Coordenadores Mais Ativos (Top 5) 🎯
                                    <a href="{{ route('explore.index', ['tab' => 'coordinators']) }}"
                                        id="ver-todos-coordinators"
                                        class="text-base text-red-600 hover:text-red-800 font-semibold transition duration-200 flex items-center">
                                        Ver todos <i class="ph ph-arrow-right ml-1 text-lg"></i>
                                    </a>
                                </h3>

                                <div class="relative group">
                                    {{-- Botão Esquerdo --}}
                                    <button id="left-arrow-coordinators"
                                        onclick="scrollSection('coordinator-highlight-container', -320)"
                                        class="absolute left-0 z-20 top-1/2 -translate-y-1/2 size-10 p-1 bg-white border border-gray-300 rounded-full shadow-xl hidden lg:group-hover:flex items-center justify-center hover:bg-red-50 transition duration-300 opacity-0 pointer-events-none">
                                        <i class="ph-bold ph-caret-left text-xl text-red-600"></i>
                                    </button>

                                    {{-- Container de coordenadores com scroll horizontal --}}
                                    <div id="coordinator-highlight-container"
                                        class="flex overflow-x-scroll snap-x snap-mandatory space-x-6 pb-4 scrollbar-hide">

                                        @forelse ($coordinators->take(5) as $coordinator)
                                            {{-- ADICIONADO take(5) para limitar os destaques --}}
                                            {{-- CARD DE COORDENADOR --}}
                                            <a href="{{ route('profile.view', $coordinator->userAccount->id) }}"
                                                class="flex-shrink-0 w-80 flex flex-col items-center text-center group relative snap-center
            bg-gray-50 p-4 rounded-xl border border-gray-200 shadow-lg overflow-hidden transform transition duration-300
            hover:scale-[1.01] hover:shadow-2xl">

                                                <img src="{{ $coordinator->userAccount->user_icon_url }}"
                                                    alt="{{ $coordinator->userAccount->name }}"
                                                    class="size-16 rounded-full object-cover border-4 border-red-500/50 mb-3 shadow-lg group-hover:scale-105 transition duration-300">

                                                {{-- NOVO: Badge de contagem de eventos --}}
                                                @if (isset($coordinator->managed_events_count) && $coordinator->managed_events_count > 0)
                                                    <span
                                                        class="absolute top-2 right-2 flex items-center bg-red-600 text-white text-xs font-bold px-2 py-1 rounded-full shadow-lg">
                                                        <i class="ph-fill ph-calendar-check mr-1 text-base"></i>
                                                        {{ $coordinator->managed_events_count }}
                                                    </span>
                                                @endif

                                                <p
                                                    class="font-extrabold text-base text-gray-900 leading-tight line-clamp-2">
                                                    {{ $coordinator->userAccount->name }}
                                                </p>

                                                <p class="text-xs font-bold text-red-600 mt-1 flex items-center">
                                                    <i class="ph ph-chalkboard-teacher mr-1 text-sm"></i>
                                                    @if ($coordinator->coordinator_type === 'general')
                                                        Coordenador Geral
                                                    @elseif ($coordinator->coordinator_type === 'course')
                                                        Coordenador de Curso
                                                    @endif
                                                </p>
                                            </a>
                                        @empty
                                            <p class="text-gray-500 text-center py-8 w-full">Nenhum coordenador em
                                                destaque.</p>
                                        @endforelse
                                    </div>

                                    <button id="right-arrow-coordinators"
                                        onclick="scrollSection('coordinator-highlight-container', 320)"
                                        class="absolute right-0 z-20 top-1/2 -translate-y-1/2 size-10 p-1 bg-white border border-gray-300 rounded-full shadow-xl hidden lg:group-hover:flex items-center justify-center hover:bg-red-50 transition duration-300">
                                        <i class="ph-bold ph-caret-right text-xl text-red-600"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{--
                        |===================================|
                        | NOVA Seção 'Coordenadores' (Lista Completa) |
                        |===================================|
                        --}}
                    <div id="coordinators-section"
                        class="tab-content {{ $activeTab !== 'coordinators' ? 'hidden' : '' }} bg-white p-6 rounded-2xl shadow-2xl border border-gray-100">

                        <h2
                            class="text-4xl font-extrabold text-gray-900 mb-8 border-b-4 border-red-500/70 pb-3 flex items-center">
                            <i class="ph ph-user-circle text-red-500 mr-3 text-3xl"></i>
                            Lista Completa de Coordenadores
                        </h2>

                        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6">
                            @forelse ($coordinators as $coordinator)
                                <a href="{{ route('profile.view', ['user' => $coordinator->userAccount->id, 'fromTab' => 'coordinators']) }}"
                                    class="flex flex-col items-center text-center group relative snap-center
              bg-gray-50 p-4 rounded-xl border border-gray-200 shadow-lg overflow-hidden transform transition duration-300
              hover:scale-[1.01] hover:shadow-2xl w-full">

                                    <img src="{{ $coordinator->userAccount->user_icon_url }}"
                                        alt="{{ $coordinator->userAccount->name }}"
                                        class="size-16 rounded-full object-cover border-4 border-red-500/50 mb-3 shadow-lg group-hover:scale-105 transition duration-300">

                                    <p class="font-extrabold text-lg text-gray-900 leading-tight line-clamp-2 mb-1">
                                        {{ $coordinator->userAccount->name }}
                                    </p>

                                    <p class="text-xs font-bold text-red-600 mt-1 flex items-center">
                                        <i class="ph ph-chalkboard-teacher mr-1 text-sm"></i>
                                        @if ($coordinator->coordinator_type === 'general')
                                            Coordenador Geral
                                        @elseif ($coordinator->coordinator_type === 'course')
                                            Coordenador de Curso
                                        @endif
                                    </p>
                                </a>
                            @empty
                                <div class="col-span-full">
                                    <p class="text-gray-500 text-center py-12 bg-white rounded-xl shadow-xl">
                                        Nenhum coordenador encontrado. 😥
                                    </p>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const tabButtons = document.querySelectorAll('.tab-button');
            const activeTabInput = document.getElementById('active-tab-input');
            const communityHighlights = document.getElementById('community-highlights');
            const coordinatorsSection = document.getElementById('coordinators-section');
            const verTodosLink = document.querySelector('a[href*="?tab=coordinators"]');

            function updateActiveButton(tabKey) {
                tabButtons.forEach(button => {
                    const icon = button.querySelector('i');

                    if (button.getAttribute('data-tab') === tabKey) {
                        button.classList.add(
                            'bg-red-600',
                            'text-white',
                            'shadow-lg',
                            'shadow-red-500/30',
                            'font-semibold'
                        );
                        button.classList.remove(
                            'text-gray-600',
                            'hover:bg-red-50',
                            'hover:text-red-700',
                            'font-medium'
                        );

                        if (icon) {
                            icon.classList.add('text-white');
                            icon.classList.remove('text-red-500', 'group-hover:text-red-700');
                        }
                    } else {
                        button.classList.remove(
                            'bg-red-600',
                            'text-white',
                            'shadow-lg',
                            'shadow-red-500/30',
                            'font-semibold'
                        );
                        button.classList.add(
                            'text-gray-600',
                            'hover:bg-red-50',
                            'hover:text-red-700',
                            'font-medium'
                        );

                        if (icon) {
                            icon.classList.remove('text-white');
                            icon.classList.add('text-red-500', 'group-hover:text-red-700');
                        }
                    }
                });

                if (activeTabInput) activeTabInput.value = tabKey;
            }

            function scrollToSection(tabKey, fromLink = false) {
                if (tabKey === 'coordinators' && fromLink) {
                    if (communityHighlights) communityHighlights.classList.add('hidden');
                    if (coordinatorsSection) coordinatorsSection.classList.remove('hidden');

                    const offset = 80;
                    const top = coordinatorsSection.getBoundingClientRect().top + window.scrollY - offset;
                    window.scrollTo({
                        top,
                        behavior: 'smooth'
                    });
                } else {
                    if (communityHighlights) communityHighlights.classList.remove('hidden');
                    if (coordinatorsSection) coordinatorsSection.classList.add('hidden');

                    let sectionId = tabKey === 'coordinators' ? 'coordinator-highlight-container' : tabKey +
                        '-section';
                    const section = document.getElementById(sectionId);
                    if (section) {
                        const offset = 80;
                        const top = section.getBoundingClientRect().top + window.scrollY - offset;
                        window.scrollTo({
                            top,
                            behavior: 'smooth'
                        });
                    }
                }

                const urlParams = new URLSearchParams(window.location.search);
                if (urlParams.has('tab')) {
                    urlParams.delete('tab');
                    window.history.replaceState({}, '', window.location.pathname + (urlParams.toString() ? '?' +
                        urlParams.toString() : ''));
                }
            }

            tabButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const tabKey = this.getAttribute('data-tab');
                    updateActiveButton(tabKey);
                    scrollToSection(tabKey);
                });
            });

            if (verTodosLink) {
                verTodosLink.addEventListener('click', function(e) {
                    e.preventDefault();
                    const tabKey = 'coordinators';
                    updateActiveButton(tabKey);
                    scrollToSection(tabKey, true);
                });
            }

            const urlParams = new URLSearchParams(window.location.search);
            const initialTab = activeTabInput ? activeTabInput.value : urlParams.get('tab');
            if (initialTab) {
                updateActiveButton(initialTab);
                scrollToSection(initialTab, initialTab === 'coordinators');
            }

            function scrollSection(containerId, amount) {
                const container = document.getElementById(containerId);
                if (container) {
                    container.scrollBy({
                        left: amount,
                        behavior: 'smooth'
                    });
                }
            }

            const scrollGroups = [{
                    container: 'event-highlight-container',
                    left: 'left-arrow',
                    right: 'right-arrow'
                },
                {
                    container: 'course-highlight-container',
                    left: 'left-arrow-courses',
                    right: 'right-arrow-courses'
                },
                {
                    container: 'coordinator-highlight-container',
                    left: 'left-arrow-coordinators',
                    right: 'right-arrow-coordinators'
                }
            ];

            scrollGroups.forEach(group => {
                const container = document.getElementById(group.container);
                const leftArrow = document.getElementById(group.left);
                const rightArrow = document.getElementById(group.right);

                if (!container || !leftArrow || !rightArrow) return;

                function updateArrows() {
                    const scrollLeft = container.scrollLeft;
                    const scrollWidth = container.scrollWidth;
                    const clientWidth = container.clientWidth;
                    const scrollEnd = scrollWidth - clientWidth;

                    const hasItems = container.children.length > 0;

                    if (!hasItems || scrollWidth <= clientWidth) {
                        leftArrow.classList.add('opacity-0', 'pointer-events-none');
                        rightArrow.classList.add('opacity-0', 'pointer-events-none');
                        return;
                    }

                    if (scrollLeft > 10) leftArrow.classList.remove('opacity-0', 'pointer-events-none');
                    else leftArrow.classList.add('opacity-0', 'pointer-events-none');

                    if (scrollLeft >= scrollEnd - 10) rightArrow.classList.add('opacity-0',
                        'pointer-events-none');
                    else rightArrow.classList.remove('opacity-0', 'pointer-events-none');
                }

                container.addEventListener('scroll', updateArrows);
                updateArrows();

                leftArrow.addEventListener('click', () => scrollSection(group.container, -300));
                rightArrow.addEventListener('click', () => scrollSection(group.container, 300));
            });
        });
    </script>

</x-app-layout>

<style>
    .scrollbar-hide::-webkit-scrollbar {
        display: none;
    }

    .scrollbar-hide {
        -ms-overflow-style: none;
        scrollbar-width: none;
    }
</style>
