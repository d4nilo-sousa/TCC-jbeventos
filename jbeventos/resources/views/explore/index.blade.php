<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">

                {{-- Barra de Pesquisa --}}
                <div class="mb-8">
                    <form action="{{ route('explore.index') }}" method="GET">
                        <div class="relative">
                            {{-- Campo oculto para manter a aba ativa após a pesquisa --}}
                            <input type="hidden" name="tab" id="active-tab-input" value="{{ request('tab', 'all') }}"> 
                            
                            <input type="text" name="search" placeholder="Pesquisar por eventos, cursos, posts ou pessoas..."
                                class="w-full pl-10 pr-4 py-2 border rounded-full focus:outline-none focus:ring-2 focus:ring-indigo-500"
                                value="{{ request('search') }}">
                            <div class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400">
                                <i class="fas fa-search"></i>
                            </div>
                        </div>
                    </form>
                </div>

                {{-- Menu de Abas --}}
                <div class="border-b border-gray-200 mb-6">
                    <nav class="-mb-px flex space-x-8" aria-label="Tabs">
                        <button type="button" data-tab="all" class="tab-button whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm focus:outline-none transition-colors duration-200 ease-in-out border-indigo-500 text-indigo-600">
                            Todos
                        </button>
                        <button type="button" data-tab="events" class="tab-button whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm focus:outline-none transition-colors duration-200 ease-in-out border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300">
                            Eventos
                        </button>
                        <button type="button" data-tab="posts" class="tab-button whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm focus:outline-none transition-colors duration-200 ease-in-out border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300">
                            Posts
                        </button>
                        <button type="button" data-tab="courses" class="tab-button whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm focus:outline-none transition-colors duration-200 ease-in-out border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300">
                            Cursos
                        </button>
                        <button type="button" data-tab="coordinators" class="tab-button whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm focus:outline-none transition-colors duration-200 ease-in-out border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300">
                            Coordenadores
                        </button>
                    </nav>
                </div>

                {{-- Conteúdo das Abas --}}

                {{-- =================================== Seção 'Todos' =================================== --}}
                <div id="all-section" class="tab-content">
                    <h2 class="text-3xl font-bold text-gray-800 mb-6">Tudo em Destaque</h2>

                    {{-- Eventos Recentes (Limitado a 5) --}}
                    <h3 class="text-2xl font-bold text-gray-700 mb-4 mt-8 border-b pb-2">Eventos Recentes</h3>
                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                        @forelse ($events->take(5) as $event)
                            <div class="bg-white border border-gray-200 rounded-lg shadow-md overflow-hidden relative transform transition duration-300 hover:scale-105 hover:shadow-lg">
                                <a href="{{ route('events.show', $event->id) }}">
                                    <img src="{{ asset('storage/' . $event->event_image) }}" alt="{{ $event->event_name }}" class="w-full h-48 object-cover">
                                    {{-- Tag de identificação do tipo --}}
                                    <span class="absolute top-2 right-2 bg-pink-500 text-white text-xs font-bold px-2 py-1 rounded-full shadow-md">
                                        EVENTO
                                    </span>
                                    <div class="p-4">
                                        <h3 class="font-bold text-lg text-gray-900 leading-tight">{{ $event->event_name }}</h3>
                                        <p class="text-sm text-gray-600 mt-1">{{ \Illuminate\Support\Str::limit($event->event_description, 50) }}</p>
                                        <p class="text-sm text-gray-500 mt-2">{{ $event->event_scheduled_at->format('d/m/Y H:i') }}</p>
                                    </div>
                                </a>
                            </div>
                        @empty
                            <p class="text-gray-500 text-center col-span-full">Nenhum evento encontrado.</p>
                        @endforelse
                    </div>
                    <div class="mt-4 text-center">
                        <a href="?tab=events" class="text-indigo-600 hover:text-indigo-800 text-sm font-semibold">Ver todos os eventos &rarr;</a>
                    </div>


                    {{-- Posts Recentes (Limitado a 5, com novo layout de rede social) --}}
                    <h3 class="text-2xl font-bold text-gray-700 mb-4 mt-8 border-b pb-2">Posts Recentes</h3>
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                        @forelse ($posts->take(5) as $post)
                            <div class="bg-white border border-gray-200 rounded-lg shadow-sm p-4 flex gap-4 items-center transition duration-300 hover:shadow-lg">
                                <a href="{{ route('courses.show', $post->course->id) }}" class="flex-shrink-0">
                                    @if ($post->images && count($post->images) > 0)
                                        <img src="{{ asset('storage/' . $post->images[0]) }}" alt="Imagem do post" class="w-24 h-24 object-cover rounded-md border border-gray-300">
                                    @else
                                        <div class="w-24 h-24 bg-gray-200 rounded-md flex items-center justify-center text-gray-400">
                                            <i class="fas fa-image text-3xl"></i>
                                        </div>
                                    @endif
                                </a>
                                <div class="flex-grow">
                                    <a href="{{ route('courses.show', $post->course->id) }}">
                                        <h4 class="font-bold text-base text-gray-900 leading-tight">{{ \Illuminate\Support\Str::limit($post->content, 60) }}</h4>
                                        <p class="text-sm text-gray-600 mt-1">
                                            Por <span class="font-semibold">{{ $post->author->name }}</span>
                                        </p>
                                        <p class="text-xs text-gray-500 mt-1">
                                            {{ $post->created_at->format('d/m/Y H:i') }}
                                        </p>
                                    </a>
                                </div>
                            </div>
                        @empty
                            <p class="text-gray-500 col-span-full">Nenhum post encontrado.</p>
                        @endforelse
                    </div>
                    <div class="mt-4 text-center">
                        <a href="?tab=posts" class="text-indigo-600 hover:text-indigo-800 text-sm font-semibold">Ver todos os posts &rarr;</a>
                    </div>


                    {{-- Cursos --}}
                    <h3 class="text-2xl font-bold text-gray-700 mb-4 mt-8 border-b pb-2">Cursos</h3>
                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                        @forelse ($courses as $course)
                            <div class="bg-white border border-gray-200 rounded-lg shadow-md overflow-hidden flex flex-col items-center justify-center p-6 text-center transform transition duration-300 hover:scale-105 hover:shadow-lg">
                                <a href="{{ route('courses.show', $course->id) }}" class="flex flex-col items-center">
                                    <img src="{{ asset('storage/' . $course->course_icon) }}" alt="{{ $course->course_name }}" class="size-24 rounded-full object-cover border-4 border-gray-300 mb-4">
                                    <h3 class="font-bold text-lg text-gray-900 leading-tight mb-1">
                                        {{ $course->course_name }}
                                    </h3>
                                    <p class="text-sm text-gray-600 font-medium">Curso</p>
                                </a>
                            </div>
                        @empty
                            <p class="text-gray-500 text-center col-span-full">Nenhum curso encontrado.</p>
                        @endforelse
                    </div>
                    <div class="mt-4 text-center">
                        <a href="?tab=courses" class="text-indigo-600 hover:text-indigo-800 text-sm font-semibold">Ver todos os Cursos &rarr;</a>
                    </div>


                    {{-- Coordenadores (Limitado a 5) --}}
                    <h3 class="text-2xl font-bold text-gray-700 mb-4 mt-8 border-b pb-2">Coordenadores</h3>
                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                        @forelse ($coordinators->take(5) as $coordinator)
                            <div class="bg-white border border-gray-200 rounded-lg shadow-md overflow-hidden flex flex-col items-center justify-center p-6 text-center transform transition duration-300 hover:scale-105 hover:shadow-lg">
                                <a href="{{ route('profile.view', $coordinator->userAccount->id) }}" class="flex flex-col items-center">
                                    <img src="{{ $coordinator->userAccount->profile_photo_url }}" alt="{{ $coordinator->userAccount->name }}" class="size-24 rounded-full object-cover border-4 border-gray-300 mb-4">
                                    <h3 class="font-bold text-lg text-gray-900 leading-tight mb-1">
                                        {{ $coordinator->userAccount->name }}
                                    </h3>
                                    <p class="text-sm text-gray-600 font-medium">Coordenador</p>
                                    @if($coordinator->course)
                                        <span class="bg-indigo-100 text-indigo-800 text-xs font-semibold px-2 py-1 rounded-full shadow mt-2">
                                            {{ $coordinator->course->course_name }}
                                        </span>
                                    @endif
                                </a>
                            </div>
                        @empty
                            <p class="text-gray-500 text-center col-span-full">Nenhum coordenador encontrado.</p>
                        @endforelse
                    </div>
                    <div class="mt-4 text-center">
                        <a href="?tab=coordinators" class="text-indigo-600 hover:text-indigo-800 text-sm font-semibold">Ver todos os coordenadores &rarr;</a>
                    </div>
                </div>

                {{-- =================================== Seção de Eventos =================================== --}}
                <div id="events-section" class="tab-content hidden">
                    <h2 class="text-3xl font-bold text-gray-800 mb-6">Eventos</h2>
                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                        @forelse ($events as $event)
                            <div class="bg-white border border-gray-200 rounded-lg shadow-md overflow-hidden relative transform transition duration-300 hover:scale-105 hover:shadow-lg">
                                <a href="{{ route('events.show', $event->id) }}">
                                    <img src="{{ asset('storage/' . $event->event_image) }}" alt="{{ $event->event_name }}" class="w-full h-48 object-cover">
                                    {{-- Tag de identificação do tipo --}}
                                    <span class="absolute top-2 right-2 bg-pink-500 text-white text-xs font-bold px-2 py-1 rounded-full shadow-md">
                                        EVENTO
                                    </span>
                                    <div class="p-4">
                                        <h3 class="font-bold text-lg text-gray-900 leading-tight">{{ $event->event_name }}</h3>
                                        <p class="text-sm text-gray-600 mt-1">{{ $event->event_description }}</p>
                                        <p class="text-sm text-gray-500 mt-2">{{ $event->event_scheduled_at->format('d/m/Y H:i') }}</p>
                                    </div>
                                </a>
                            </div>
                        @empty
                            <p class="text-gray-500 text-center col-span-full">Nenhum evento encontrado.</p>
                        @endforelse
                    </div>
                </div>

                {{-- =================================== Seção de Posts =================================== --}}
                <div id="posts-section" class="tab-content hidden">
                    <h2 class="text-3xl font-bold text-gray-800 mb-6">Posts Recentes</h2>
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                        @forelse ($posts as $post)
                            <div class="bg-white border border-gray-200 rounded-lg shadow-sm p-4 flex gap-4 items-center transition duration-300 hover:shadow-lg">
                                <a href="{{ route('courses.show', $post->course->id) }}" class="flex-shrink-0">
                                    @if ($post->images && count($post->images) > 0)
                                        <img src="{{ asset('storage/' . $post->images[0]) }}" alt="Imagem do post" class="w-24 h-24 object-cover rounded-md border border-gray-300">
                                    @else
                                        <div class="w-24 h-24 bg-gray-200 rounded-md flex items-center justify-center text-gray-400">
                                            <i class="fas fa-image text-3xl"></i>
                                        </div>
                                    @endif
                                </a>
                                <div class="flex-grow">
                                    <a href="{{ route('courses.show', $post->course->id) }}">
                                        <h4 class="font-bold text-base text-gray-900 leading-tight">{{ \Illuminate\Support\Str::limit($post->content, 60) }}</h4>
                                        <p class="text-sm text-gray-600 mt-1">
                                            Por <span class="font-semibold">{{ $post->author->name }}</span>
                                        </p>
                                        <p class="text-xs text-gray-500 mt-1">
                                            {{ $post->created_at->format('d/m/Y H:i') }}
                                        </p>
                                    </a>
                                </div>
                            </div>
                        @empty
                            <p class="text-gray-500 col-span-full">Nenhum post encontrado.</p>
                        @endforelse
                    </div>
                </div>

                {{-- =================================== Seção de Cursos =================================== --}}
                <div id="courses-section" class="tab-content hidden">
                    <h2 class="text-3xl font-bold text-gray-800 mb-6">Cursos</h2>
                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                        @forelse ($courses as $course)
                            <div class="bg-white border border-gray-200 rounded-lg shadow-md overflow-hidden flex flex-col items-center justify-center p-6 text-center transform transition duration-300 hover:scale-105 hover:shadow-lg">
                                <a href="{{ route('courses.show', $course->id) }}" class="flex flex-col items-center">
                                    <img src="{{ asset('storage/' . $course->course_icon) }}" alt="{{ $course->course_name }}" class="size-24 rounded-full object-cover border-4 border-gray-300 mb-4">
                                    <h3 class="font-bold text-lg text-gray-900 leading-tight mb-1">
                                        {{ $course->course_name }}
                                    </h3>
                                    <p class="text-sm text-gray-600 font-medium">Curso</p>
                                </a>
                            </div>
                        @empty
                            <p class="text-gray-500 text-center col-span-full">Nenhum curso encontrado.</p>
                        @endforelse
                    </div>
                </div>

                {{-- =================================== Seção de Coordenadores =================================== --}}
                <div id="coordinators-section" class="tab-content hidden">
                    <h2 class="text-3xl font-bold text-gray-800 mb-6">Coordenadores</h2>
                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                        @forelse ($coordinators as $coordinator)
                            <div class="bg-white border border-gray-200 rounded-lg shadow-md overflow-hidden flex flex-col items-center justify-center p-6 text-center transform transition duration-300 hover:scale-105 hover:shadow-lg">
                                <a href="{{ route('profile.view', $coordinator->userAccount->id) }}" class="flex flex-col items-center">
                                    <img src="{{ $coordinator->userAccount->profile_photo_url }}" alt="{{ $coordinator->userAccount->name }}" class="size-24 rounded-full object-cover border-4 border-gray-300 mb-4">
                                    <h3 class="font-bold text-lg text-gray-900 leading-tight mb-1">
                                        {{ $coordinator->userAccount->name }}
                                    </h3>
                                    <p class="text-sm text-gray-600 font-medium">Coordenador</p>
                                    @if($coordinator->course)
                                        <span class="bg-indigo-100 text-indigo-800 text-xs font-semibold px-2 py-1 rounded-full shadow mt-2">
                                            {{ $coordinator->course->course_name }}
                                        </span>
                                    @endif
                                </a>
                            </div>
                        @empty
                            <p class="text-gray-500 text-center col-span-full">Nenhum coordenador encontrado.</p>
                        @endforelse
                    </div>
                </div>

            </div>
        </div>
    </div>

    {{-- Script para a funcionalidade das abas --}}
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const tabs = document.querySelectorAll('.tab-button');
            const contents = document.querySelectorAll('.tab-content');
            const activeTabInput = document.getElementById('active-tab-input');

            function showContent(tabId) {
                // Remove estilos de aba ativa
                tabs.forEach(tab => {
                    tab.classList.remove('border-indigo-500', 'text-indigo-600');
                    tab.classList.add('border-transparent', 'text-gray-500', 'hover:text-gray-700', 'hover:border-gray-300');
                });

                // Oculta todo o conteúdo
                contents.forEach(content => {
                    content.classList.add('hidden');
                });

                // Define a aba ativa e mostra seu conteúdo
                const activeTab = document.querySelector(`[data-tab="${tabId}"]`);
                if (activeTab) {
                    activeTab.classList.remove('border-transparent', 'text-gray-500', 'hover:text-gray-700', 'hover:border-gray-300');
                    activeTab.classList.add('border-indigo-500', 'text-indigo-600');
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
                    // Remove 'search' da URL ao mudar de aba, para iniciar uma nova busca na nova seção
                    newUrl.searchParams.delete('search'); 
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