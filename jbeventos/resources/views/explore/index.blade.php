<x-app-layout>
    <div class="py-12 bg-gray-50">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">

                {{-- Coluna 1: Barra de Pesquisa e Filtros --}}
                <div class="lg:col-span-1">
                    <div class="sticky top-4 space-y-6">

                        {{-- Barra de Pesquisa --}}
                        <div class="bg-white overflow-hidden shadow-xl sm:rounded-xl p-6">
                            <h2 class="text-xl font-extrabold text-gray-800 mb-4 flex items-center">
                                <i class="ph ph-magnifying-glass mr-3 text-red-600 text-2xl font-bold"></i>
                                Explorar
                            </h2>
                            <form action="{{ route('explore.index') }}" method="GET">
                                <div class="relative">
                                    {{-- Campo oculto para manter a aba ativa após a pesquisa --}}
                                    <input type="hidden" name="tab" id="active-tab-input" value="{{ request('tab', 'all') }}">
                                    
                                    <input type="text" name="search" placeholder="Buscar por eventos, cursos, posts..."
                                        class="w-full pl-12 pr-4 py-3 border border-gray-300 rounded-full focus:outline-none focus:ring-4 focus:ring-red-200 focus:border-red-500 transition-all duration-300"
                                        value="{{ request('search') }}">
                                    
                                    <div class="absolute left-4 top-1/2 transform -translate-y-1/2 text-red-500">
                                        <i class="ph ph-magnifying-glass text-lg font-bold"></i> 
                                    </div>
                                    
                                    @if (request('search'))
                                        <a href="{{ route('explore.index', ['tab' => request('tab', 'all')]) }}" class="absolute right-4 top-1/2 transform -translate-y-1/2 text-gray-500 hover:text-red-600 transition-colors duration-200" title="Limpar Busca">
                                            <i class="ph ph-x-circle-fill text-xl"></i>
                                        </a>
                                    @endif
                                </div>
                            </form>
                        </div>

                        {{-- Menu de Abas/Filtros --}}
                        <div class="bg-white overflow-hidden shadow-xl sm:rounded-xl p-6">
                            <h3 class="text-lg font-bold text-gray-700 mb-4 border-b pb-2">Filtrar por Categoria</h3>
                            <nav class="space-y-2" aria-label="Tabs">
                                @php
                                    $tabs = [
                                        'all' => ['label' => 'Todos', 'icon' => 'ph ph-globe-hemisphere-east'],
                                        'events' => ['label' => 'Eventos', 'icon' => 'ph ph-calendar-blank'],
                                        'posts' => ['label' => 'Posts', 'icon' => 'ph ph-feather'],
                                        'courses' => ['label' => 'Cursos', 'icon' => 'ph ph-book-open'],
                                        'coordinators' => ['label' => 'Coordenadores', 'icon' => 'ph ph-user-circle'],
                                    ];
                                    $activeTab = request('tab', 'all');
                                @endphp

                                @foreach ($tabs as $key => $tab)
                                    <button type="button" data-tab="{{ $key }}" 
                                        class="tab-button w-full flex items-center px-4 py-3 rounded-xl text-left transition-all duration-300
                                            {{ $activeTab === $key 
                                                ? 'bg-red-600 text-white shadow-lg hover:bg-red-700 font-semibold' 
                                                : 'text-gray-600 hover:bg-gray-100 hover:text-red-600 font-medium' }}">
                                        <i class="{{ $tab['icon'] }} mr-3 text-lg"></i>
                                        <span class="text-sm">{{ $tab['label'] }}</span>
                                    </button>
                                @endforeach
                            </nav>
                        </div>
                    </div>
                </div>

                {{-- Coluna 2: Conteúdo Principal e Resultados --}}
                <div class="lg:col-span-3">

                    <div id="results-container" class="space-y-10">

                        {{-- =================================== Seção 'Todos' (Destaques) =================================== --}}
                        <div id="all-section" class="tab-content">
                            <h2 class="text-4xl font-extrabold text-gray-900 mb-8 border-b-4 border-red-500/50 pb-2">
                                <i class="ph ph-star-fill text-red-500 mr-3"></i>
                                Destaques da Comunidade
                            </h2>

                            {{-- Eventos (Primeiro a aparecer) --}}
                            <div class="bg-white p-6 rounded-xl shadow-lg border border-gray-100 mb-8">
                                <h3 class="text-2xl font-bold text-gray-700 mb-6 flex justify-between items-center">
                                    Próximos Eventos (Top 4)
                                    <a href="?tab=events" class="text-sm text-red-600 hover:text-red-800 font-semibold transition duration-200">
                                        Ver todos <i class="ph ph-arrow-right ml-1"></i>
                                    </a>
                                </h3>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    @forelse ($events->take(4) as $event)
                                        <div class="bg-white border border-gray-200 rounded-lg shadow-md overflow-hidden relative transform transition duration-300 hover:scale-[1.02] hover:shadow-xl">
                                            <a href="{{ route('events.show', $event->id) }}">
                                                <img src="{{ asset('storage/' . $event->event_image) }}" alt="{{ $event->event_name }}" class="w-full h-40 object-cover">
                                                <span class="absolute top-2 left-2 bg-red-600 text-white text-xs font-extrabold px-3 py-1 rounded-full shadow-md flex items-center">
                                                    <i class="ph ph-calendar-check mr-1"></i> EVENTO
                                                </span>
                                                <div class="p-4">
                                                    <h3 class="font-extrabold text-lg text-gray-900 leading-snug truncate">{{ $event->event_name }}</h3>
                                                    <p class="text-sm text-gray-500 mt-1 line-clamp-2">{{ \Illuminate\Support\Str::limit($event->event_description, 60) }}</p>
                                                    <div class="flex items-center text-sm text-red-600 mt-3 font-semibold">
                                                        <i class="ph ph-clock mr-2"></i>
                                                        <span>{{ $event->event_scheduled_at->format('d/m/Y H:i') }}</span>
                                                    </div>
                                                </div>
                                            </a>
                                        </div>
                                    @empty
                                        <p class="text-gray-500 text-center col-span-full py-4">Nenhum evento encontrado no momento.</p>
                                    @endforelse
                                </div>
                            </div>
                            
                            {{-- Posts (Principais Discussões) --}}
                            <div class="bg-white p-6 rounded-xl shadow-lg border border-gray-100 mb-8">
                                <h3 class="text-2xl font-bold text-gray-700 mb-6 flex justify-between items-center">
                                    Principais Discussões (Top 3)
                                    <a href="?tab=posts" class="text-sm text-red-600 hover:text-red-800 font-semibold transition duration-200">
                                        Ver todos <i class="ph ph-arrow-right ml-1"></i>
                                    </a>
                                </h3>
                                <div class="grid grid-cols-1 gap-4">
                                    @forelse ($posts->take(3) as $post)
                                        <div class="flex bg-gray-50 p-4 rounded-lg border border-gray-200 transition duration-300 hover:shadow-md hover:border-red-300">
                                            <a href="{{ route('courses.show', $post->course->id) }}" class="flex-shrink-0 mr-4">
                                                @if ($post->images && count($post->images) > 0)
                                                    <img src="{{ asset('storage/' . $post->images[0]) }}" alt="Imagem do post" class="size-20 object-cover rounded-lg border-2 border-red-400/50">
                                                @else
                                                    <div class="size-20 bg-red-100 rounded-lg flex items-center justify-center text-red-500">
                                                        <i class="ph ph-chat-text-fill text-3xl"></i>
                                                    </div>
                                                @endif
                                            </a>
                                            <div class="flex-grow">
                                                <a href="{{ route('courses.show', $post->course->id) }}">
                                                    <p class="text-xs font-semibold text-gray-500 mb-1">POST em {{ $post->course->course_name ?? 'Curso Desconhecido' }}</p>
                                                    <h4 class="font-extrabold text-base text-gray-900 leading-snug line-clamp-2">{{ $post->content }}</h4>
                                                    <p class="text-sm text-gray-600 mt-2">
                                                        Por <span class="font-bold text-red-600">{{ $post->author->name }}</span>
                                                        <span class="text-xs text-gray-400 ml-2">
                                                            <i class="ph ph-clock-counter-clockwise"></i> {{ $post->created_at->diffForHumans() }}
                                                        </span>
                                                    </p>
                                                </a>
                                            </div>
                                        </div>
                                    @empty
                                        <p class="text-gray-500 col-span-full py-4 text-center">Nenhuma discussão em destaque no momento.</p>
                                    @endforelse
                                </div>
                            </div>


                            {{-- Cursos (Último a aparecer) --}}
                            <div class="bg-white p-6 rounded-xl shadow-lg border border-gray-100">
                                <h3 class="text-2xl font-bold text-gray-700 mb-6 flex items-center">
                                    Cursos
                                </h3>
                                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                                    @forelse ($courses->take(3) as $course)
                                        <div class="flex items-center p-3 bg-gray-50 rounded-lg shadow-sm hover:bg-red-50 transition duration-200">
                                            <a href="{{ route('courses.show', $course->id) }}" class="flex items-center w-full">
                                                <img src="{{ asset('storage/' . $course->course_icon) }}" alt="{{ $course->course_name }}" class="size-12 rounded-full object-cover border-2 border-red-400">
                                                <div class="ml-4 flex-grow">
                                                    <p class="font-bold text-gray-800 leading-tight">{{ $course->course_name }}</p>
                                                    <p class="text-xs text-gray-500 font-medium flex items-center">
                                                        <i class="ph ph-graduation-cap-fill mr-1"></i> Curso
                                                    </p>
                                                </div>
                                                <i class="ph ph-arrow-right text-red-400 text-sm"></i>
                                            </a>
                                        </div>
                                    @empty
                                        <p class="text-gray-500 text-center col-span-full">Nenhum curso em destaque.</p>
                                    @endforelse
                                </div>
                                <div class="mt-4 text-center">
                                    <a href="?tab=courses" class="text-red-600 hover:text-red-800 text-sm font-semibold">Ver todos os Cursos &rarr;</a>
                                </div>
                            </div>
                        </div>


                        {{-- =================================== Seção de Eventos (Conteúdo da Aba) =================================== --}}
                        <div id="events-section" class="tab-content hidden">
                            <h2 class="text-4xl font-extrabold text-gray-900 mb-8 border-b-4 border-red-500/50 pb-2">
                                <i class="ph ph-calendar-blank text-red-600 mr-3"></i>
                                Todos os Eventos
                            </h2>
                            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6">
                                @forelse ($events as $event)
                                    <div class="bg-white border border-gray-200 rounded-xl shadow-lg overflow-hidden relative transform transition duration-300 hover:shadow-xl">
                                        <a href="{{ route('events.show', $event->id) }}">
                                            <img src="{{ asset('storage/' . $event->event_image) }}" alt="{{ $event->event_name }}" class="w-full h-48 object-cover">
                                            <div class="p-4">
                                                <h3 class="font-extrabold text-xl text-gray-900 leading-tight truncate">{{ $event->event_name }}</h3>
                                                <p class="text-sm text-gray-600 mt-1 line-clamp-2">{{ \Illuminate\Support\Str::limit($event->event_description, 80) }}</p>
                                                <div class="flex items-center text-sm text-red-600 mt-3 font-semibold">
                                                    <i class="ph ph-clock mr-2"></i>
                                                    <span>{{ $event->event_scheduled_at->format('d/m/Y H:i') }}</span>
                                                </div>
                                            </div>
                                        </a>
                                    </div>
                                @empty
                                    <p class="text-gray-500 text-center col-span-full py-10">Nenhum evento encontrado para sua busca. <i class="ph ph-face-frowning text-lg ml-1"></i></p>
                                @endforelse
                            </div>
                        </div>

                        {{-- =================================== Seção de Posts (Conteúdo da Aba) =================================== --}}
                        <div id="posts-section" class="tab-content hidden">
                            <h2 class="text-4xl font-extrabold text-gray-900 mb-8 border-b-4 border-red-500/50 pb-2">
                                <i class="ph ph-feather text-red-600 mr-3"></i>
                                Todos os Posts
                            </h2>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                                @forelse ($posts as $post)
                                    <div class="bg-white border border-gray-200 rounded-xl shadow-lg p-5 flex gap-4 items-start transition duration-300 hover:shadow-xl">
                                        <a href="{{ route('courses.show', $post->course->id) }}" class="flex-shrink-0">
                                            @if ($post->images && count($post->images) > 0)
                                                <img src="{{ asset('storage/' . $post->images[0]) }}" alt="Imagem do post" class="size-20 object-cover rounded-lg border-2 border-red-300">
                                            @else
                                                <div class="size-20 bg-gray-100 rounded-lg flex items-center justify-center text-gray-400">
                                                    <i class="ph ph-image-square text-3xl"></i>
                                                </div>
                                            @endif
                                        </a>
                                        <div class="flex-grow">
                                            <a href="{{ route('courses.show', $post->course->id) }}">
                                                <p class="text-xs font-semibold text-red-600 mb-1">POST em {{ $post->course->course_name ?? 'Curso' }}</p>
                                                <h4 class="font-extrabold text-lg text-gray-900 leading-snug line-clamp-2">{{ $post->content }}</h4>
                                                <p class="text-sm text-gray-600 mt-2">
                                                    Por <span class="font-bold">{{ $post->author->name }}</span>
                                                </p>
                                                <p class="text-xs text-gray-500 mt-1">
                                                    <i class="ph ph-calendar-check mr-1"></i> {{ $post->created_at->format('d/m/Y H:i') }}
                                                </p>
                                            </a>
                                        </div>
                                    </div>
                                @empty
                                    <p class="text-gray-500 text-center col-span-full py-10">Nenhum post encontrado para sua busca. <i class="ph ph-face-frowning text-lg ml-1"></i></p>
                                @endforelse
                            </div>
                        </div>

                        {{-- =================================== Seção de Cursos (Conteúdo da Aba) =================================== --}}
                        <div id="courses-section" class="tab-content hidden">
                            <h2 class="text-4xl font-extrabold text-gray-900 mb-8 border-b-4 border-red-500/50 pb-2">
                                <i class="ph ph-book-open text-red-600 mr-3"></i>
                                Catálogo de Cursos
                            </h2>
                            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                                @forelse ($courses as $course)
                                    <div class="bg-white border border-gray-200 rounded-xl shadow-lg overflow-hidden flex flex-col items-center justify-center p-6 text-center transform transition duration-300 hover:scale-[1.05] hover:shadow-xl">
                                        <a href="{{ route('courses.show', $course->id) }}" class="flex flex-col items-center">
                                            <img src="{{ asset('storage/' . $course->course_icon) }}" alt="{{ $course->course_name }}" class="size-28 rounded-full object-cover border-4 border-red-500/50 mb-4 shadow-lg">
                                            <h3 class="font-extrabold text-lg text-gray-900 leading-tight mb-1">
                                                {{ $course->course_name }}
                                            </h3>
                                            <p class="text-sm font-bold text-red-600 mt-1 flex items-center">
                                                <i class="ph ph-chalkboard-teacher mr-1"></i> CURSO
                                            </p>
                                        </a>
                                    </div>
                                @empty
                                    <p class="text-gray-500 text-center col-span-full py-10">Nenhum curso encontrado para sua busca. <i class="ph ph-face-frowning text-lg ml-1"></i></p>
                                @endforelse
                            </div>
                        </div>

                        {{-- =================================== Seção de Coordenadores (Conteúdo da Aba) =================================== --}}
                        <div id="coordinators-section" class="tab-content hidden">
                            <h2 class="text-4xl font-extrabold text-gray-900 mb-8 border-b-4 border-red-500/50 pb-2">
                                <i class="ph ph-user-circle text-red-600 mr-3"></i>
                                Nossos Coordenadores
                            </h2>
                            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                                @forelse ($coordinators as $coordinator)
                                    <div class="bg-white border border-gray-200 rounded-xl shadow-lg overflow-hidden flex flex-col items-center justify-center p-6 text-center transform transition duration-300 hover:scale-[1.05] hover:shadow-xl">
                                        <a href="{{ route('profile.view', $coordinator->userAccount->id) }}" class="flex flex-col items-center">
                                            <img src="{{ $coordinator->userAccount->profile_photo_url }}" alt="{{ $coordinator->userAccount->name }}" class="size-28 rounded-full object-cover border-4 border-red-500/50 mb-4 shadow-lg">
                                            <h3 class="font-extrabold text-lg text-gray-900 leading-tight mb-1">
                                                {{ $coordinator->userAccount->name }}
                                            </h3>
                                            <p class="text-sm text-gray-600 font-medium flex items-center">
                                                <i class="ph ph-briefcase mr-1 text-red-600"></i> Coordenador
                                            </p>
                                            @if($coordinator->course)
                                                <span class="bg-red-100 text-red-800 text-xs font-semibold px-3 py-1 rounded-full shadow mt-2 truncate max-w-full">
                                                    {{ $coordinator->course->course_name }}
                                                </span>
                                            @endif
                                        </a>
                                    </div>
                                @empty
                                    <p class="text-gray-500 text-center col-span-full py-10">Nenhum coordenador encontrado para sua busca. <i class="ph ph-face-frowning text-lg ml-1"></i></p>
                                @endforelse
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Script para a funcionalidade das abas/filtros--}}
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const tabs = document.querySelectorAll('.tab-button');
            const contents = document.querySelectorAll('.tab-content');
            const activeTabInput = document.getElementById('active-tab-input');

            function showContent(tabId) {
                // Remove estilos de aba ativa e define o estilo inativo
                tabs.forEach(tab => {
                    tab.classList.remove('bg-red-600', 'text-white', 'shadow-lg', 'hover:bg-red-700', 'font-semibold');
                    tab.classList.add('text-gray-600', 'hover:bg-gray-100', 'hover:text-red-600', 'font-medium');
                });

                // Oculta todo o conteúdo
                contents.forEach(content => {
                    content.classList.add('hidden');
                });

                // Define a aba ativa e mostra seu conteúdo
                const activeTab = document.querySelector(`[data-tab="${tabId}"]`);
                if (activeTab) {
                    activeTab.classList.remove('text-gray-600', 'hover:bg-gray-100', 'hover:text-red-600', 'font-medium');
                    activeTab.classList.add('bg-red-600', 'text-white', 'shadow-lg', 'hover:bg-red-700', 'font-semibold');
                }

                const activeContent = document.getElementById(`${tabId}-section`);
                if (activeContent) {
                    activeContent.classList.remove('hidden');
                }
                
                // ATUALIZA CAMPO OCULTO para a busca
                if (activeTabInput) {
                    activeTabInput.value = tabId;
                }
            }

            // Lógica para definir a aba inicial (prioriza URL, senão 'all')
            const urlParams = new URLSearchParams(window.location.search);
            const activeTabParam = urlParams.get('tab');
            if (activeTabParam && document.querySelector(`[data-tab="${activeTabParam}"]`)) {
                showContent(activeTabParam);
            } else {
                showContent('all'); // Aba 'Todos' como padrão
            }

            // Listener de clique para as abas
            tabs.forEach(tab => {
                tab.addEventListener('click', () => {
                    const tabId = tab.getAttribute('data-tab');
                    showContent(tabId);

                    // Atualiza a URL sem recarregar
                    const newUrl = new URL(window.location.href);
                    newUrl.searchParams.set('tab', tabId);
                    
                    // Mantém a lógica de busca (search) ativa
                    
                    window.history.pushState({ path: newUrl.href }, '', newUrl.href);
                });
            });

            // Permite a navegação com o botão "voltar"
            window.addEventListener('popstate', function(event) {
                const urlParams = new URLSearchParams(window.location.search);
                const activeTabParam = urlParams.get('tab');
                if (activeTabParam) {
                    showContent(activeTabParam);
                } else {
                    showContent('all');
                }
            });
        });
    </script>
</x-app-layout>