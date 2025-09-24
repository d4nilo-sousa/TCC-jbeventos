<x-app-layout>
    {{-- Main Content Container com sombra e cantos arredondados --}}
    <div class="py-6 min-h-screen mt-8">
        <div class="w-full max-w-[100rem] mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-md rounded-2xl p-4 sm:p-6 lg:p-9 mx-auto min-h-[70vh]">

                {{-- Success Message --}}
                @if (session('success'))
                    <div class="mb-6 rounded-lg bg-green-100 px-6 py-4 text-green-800 animate-fade-in" role="alert">
                        {{ session('success') }}
                    </div>
                @endif

                {{-- Page Header and Filters --}}
                <div class="flex flex-col md:flex-row justify-between items-center mb-8 gap-4 md:gap-0">
                    <h1 class="text-2xl font-bold text-gray-800 leading-tight">
                        {{ request('status') === 'visible' ? 'Meus Eventos (Visíveis)' : (request('status') === 'hidden' ? 'Meus Eventos (Ocultos)' : 'Todos os Eventos') }}
                    </h1>
                    
                    <div class="flex flex-col sm:flex-row items-center gap-4">
                        {{-- Search Bar --}}
                        <form action="{{ route('events.index') }}" method="GET" class="flex items-center w-full">
                            <div class="relative w-full">
                                <input type="text" name="search" value="{{ request('search') }}" placeholder="Pesquisar eventos..." class="w-full rounded-full border-2 border-gray-300 bg-white py-2 pl-5 pr-12 text-gray-700 focus:border-blue-500 focus:ring-blue-500 transition-colors">
                                <button type="submit" class="absolute right-0 top-0 mt-1 mr-2 px-3 py-2">
                                    <svg class="w-6 h-6 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                    </svg>
                                </button>

                                {{-- Menu dropdown --}}
                                <div id="filterMenu"
                                    class="absolute right-0 mt-2 w-64 bg-white border border-gray-200 rounded-md shadow-lg p-3 z-10 hidden">
                                    <form method="GET" action="{{ route('events.index') }}">
                                        @if ($loggedCoordinator)
                                            {{-- Filtro de visibilidade --}}
                                            <label
                                                class="block text-sm font-medium text-gray-700 mb-1">Visibilidade</label>
                                            <div class="mb-3 border rounded p-2 flex flex-col items-start space-y-2">
                                                @foreach (['visible' => 'Visível', 'hidden' => 'Oculto'] as $value => $label)
                                                    <label class="flex items-center space-x-2 mb-1">
                                                        <input type="checkbox" name="status"
                                                            value="{{ $value }}"
                                                            {{ request('status') === $value ? 'checked' : '' }}
                                                            class="rounded border-gray-300"
                                                            onclick="if(this.checked){document.querySelectorAll('input[name=status]').forEach(cb=>{if(cb!==this) cb.checked=false})}">
                                                        <span>{{ $label }}</span>
                                                    </label>
                                                @endforeach
                                            </div>
                                        @endif

                                        {{-- Tipo de evento --}}
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Tipo do
                                            Evento</label>
                                        <div class="mb-3 border rounded p-2 flex flex-col items-start space-y-2">
                                            @foreach (['general' => 'Geral', 'course' => 'Curso'] as $value => $label)
                                                <label class="flex items-center space-x-2 mb-1">
                                                    <input type="checkbox" name="event_type" value="{{ $value }}"
                                                        {{ request('event_type') === $value ? 'checked' : '' }}
                                                        class="rounded border-gray-300"
                                                        onclick="if(this.checked){document.querySelectorAll('input[name=event_type]').forEach(cb=>{if(cb!==this) cb.checked=false})}">
                                                    <span>{{ $label }}</span>
                                                </label>
                                            @endforeach
                                        </div>

                                        {{-- Cursos --}}
                                        <div id="courseSelectWrapper" style="display:none;">
                                            <label class="block text-sm font-medium text-gray-700 mb-1">Curso</label>
                                            <div class="mb-3 max-h-40 overflow-y-auto border rounded p-2">
                                                @foreach ($courses as $course)
                                                    <label class="flex items-center space-x-2 mb-1">
                                                        <input type="checkbox" name="course_id[]"
                                                            value="{{ $course->id }}"
                                                            {{ is_array(request('course_id')) && in_array($course->id, request('course_id')) ? 'checked' : '' }}
                                                            class="rounded border-gray-300">
                                                        <span>{{ $course->course_name }}</span>
                                                    </label>
                                                @endforeach
                                            </div>
                                        </div>

                                        {{-- Categorias --}}
                                        <div id="categorySelectWrapper">
                                            <label
                                                class="block text-sm font-medium text-gray-700 mb-1">Categoria</label>
                                            <div class="mb-3 max-h-40 overflow-y-auto border rounded p-2">
                                                @foreach ($categories as $category)
                                                    <label class="flex items-center space-x-2 mb-1">
                                                        <input type="checkbox" name="category_id[]"
                                                            value="{{ $category->id }}"
                                                            {{ is_array(request('category_id')) && in_array($category->id, request('category_id')) ? 'checked' : '' }}
                                                            class="rounded border-gray-300">
                                                        <span>{{ $category->category_name }}</span>
                                                    </label>
                                                @endforeach
                                            </div>
                                        </div>

                                        {{-- Intervalo de datas --}}
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Evento entre</label>
                                        <div class="mb-3 border rounded p-2 flex flex-col items-start space-y-2">
                                            <input type="date" name="start_date" value="{{ request('start_date') }}"
                                                class="rounded border-gray-300 px-2 py-1 text-sm w-full">
                                            <span class="text-sm text-gray-600 self-center">até</span>
                                            <input type="date" name="end_date" value="{{ request('end_date') }}"
                                                class="rounded border-gray-300 px-2 py-1 text-sm w-full">
                                        </div>

                                        {{-- Ações --}}
                                        <button type="submit"
                                            class="w-full bg-blue-600 text-white px-3 py-1 rounded-md hover:bg-blue-700 text-sm">
                                            Aplicar Filtros
                                        </button>
                                        <button id="resetFiltres" type="button"
                                            class="w-full bg-gray-500 text-white px-3 py-1 mt-1 rounded-md hover:bg-gray-600 text-sm">
                                            Resetar Filtros
                                        </button>
                                    </form>
                                </div>
                            </div>

                            {{-- Botão Ordenar --}}
                            <div class="relative">
                                <button id="orderBtn"
                                    class="inline-flex items-center rounded-md bg-gray-200 px-4 py-2 text-gray-700 hover:bg-gray-300 transition ease-in-out">
                                    Ordenar
                                    <svg class="ml-2 w-4 h-4" fill="none" stroke="currentColor" stroke-width="2"
                                        viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"></path>
                                    </svg>
                                </button>

                                {{-- Menu dropdown --}}
                                <div id="orderMenu"
                                    class="absolute right-0 mt-2 w-64 bg-white border border-gray-200 rounded-md shadow-lg p-3 z-10 hidden">
                                    <form method="GET" action="{{ route('events.index') }}">
                                        {{-- Curtidas --}}
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Ordenar por
                                            curtidas</label>
                                        <div class="mb-3 border rounded p-2 flex flex-col items-start space-y-2">
                                            @foreach (['most' => 'Mais Curtido', 'least' => 'Menos Curtido'] as $value => $label)
                                                <label class="flex items-center space-x-2 mb-1">
                                                    <input type="checkbox" name="likes_order"
                                                        value="{{ $value }}"
                                                        {{ request('likes_order') === $value ? 'checked' : '' }}
                                                        class="rounded border-gray-300"
                                                        onclick="if(this.checked){document.querySelectorAll('input[name=likes_order]').forEach(cb=>{if(cb!==this) cb.checked=false})}">
                                                    <span>{{ $label }}</span>
                                                </label>
                                            @endforeach
                                        </div>

                                        {{-- Agendamento --}}
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Ordenar por
                                            agendamento</label>
                                        <div class="mb-3 border rounded p-2 flex flex-col items-start space-y-2">
                                            @foreach (['soonest' => 'Mais Próximo', 'latest' => 'Mais Distante'] as $value => $label)
                                                <label class="flex items-center space-x-2 mb-1">
                                                    <input type="checkbox" name="schedule_order"
                                                        value="{{ $value }}"
                                                        {{ request('schedule_order') === $value ? 'checked' : '' }}
                                                        class="rounded border-gray-300"
                                                        onclick="if(this.checked){document.querySelectorAll('input[name=schedule_order]').forEach(cb=>{if(cb!==this) cb.checked=false})}">
                                                    <span>{{ $label }}</span>
                                                </label>
                                            @endforeach
                                        </div>

                                        {{-- Botão aplicar --}}
                                        <button type="submit"
                                            class="w-full bg-blue-600 text-white px-3 py-1 rounded-md hover:bg-blue-700 text-sm">
                                            Aplicar Ordenação
                                        </button>
                                    </form>
                                </div>
                            </div>

                        </div>

                        {{-- Barra de pesquisa --}}
                        <form action="{{ route('events.index') }}" method="GET"
                            class="items-center w-full sm:w-auto"> {{-- Adicionei mt-6 para espaçamento --}}
                            <div
                                class="flex items-center bg-white rounded-full overflow-hidden border-2 w-full sm:w-auto">
                                <input id="searchInput" name="search" value="{{ request('search') }}"
                                    placeholder="Pesquisar eventos..." autocomplete="off"
                                    class="px-6 flex-1 min-w-[200px] sm:min-w-[300px] lg:min-w-[350px] text-gray-800 placeholder-gray-500 border-none outline-none focus:ring-0 bg-white">
                                <button type="submit"
                                    class="flex items-center justify-center bg-stone-800 hover:bg-stone-900 transition-colors px-6 py-3">
                                    <img src="{{ asset('imgs/lupaBranca.svg') }}" class="w-7 h-7">
                                </button>
                            </div>
                        </form>

                        {{-- Filter Dropdown --}}
                        <div class="relative inline-block text-left w-full sm:w-auto">
                            <button id="filterBtn" type="button" class="inline-flex items-center justify-center w-full rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none">
                                Filtros
                                <svg class="-mr-1 ml-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </button>
                            <div id="filterMenu" class="absolute right-0 z-20 mt-2 w-72 origin-top-right rounded-md bg-white shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none hidden">
                                <form method="GET" action="{{ route('events.index') }}" class="p-4 space-y-4">
                                    {{-- Visibilidade (Coordenador) --}}
                                    @php $loggedCoordinator = auth()->user()->coordinator; @endphp
                                    @if ($loggedCoordinator)
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1">Visibilidade</label>
                                            <div class="flex items-center space-x-4">
                                                @foreach (['visible' => 'Visível', 'hidden' => 'Oculto'] as $value => $label)
                                                    <label class="inline-flex items-center">
                                                        <input type="checkbox" name="status" value="{{ $value }}" {{ request('status') === $value ? 'checked' : '' }} class="form-checkbox text-blue-600 rounded">
                                                        <span class="ml-2 text-sm text-gray-600">{{ $label }}</span>
                                                    </label>
                                                @endforeach
                                            </div>
                                        </div>
                                    @endif

                <div>
                    <div
                        class="bg-white shadow-xl rounded-2xl p-4 sm:p-6 lg:p-9 mx-auto border-2 border-stone-100 min-h-[72%]">
                        <div class="mb-10">
                            <div id="eventsList" data-url="{{ route('events.index') }}"
                                class="grid grid-cols-1 gap-6 md:grid-cols-3 mb-10 mt-10">
                                @forelse ($events as $event)
                                    @include('partials.events.event-card', ['event' => $event])
                                @empty
                                    <div id="noEventsMessage"
                                        class="col-span-full flex flex-col items-center justify-center gap-5 p-10">
                                        <img src="{{ asset('imgs/notFound.png') }}" class="w-[19%]" alt="not-found">
                                        <p class="text-gray-500 text-center">Nenhum evento encontrado...</p>
                                    </div>
                                    
                                    {{-- Categories --}}
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Categorias</label>
                                        <div class="flex flex-wrap gap-2 max-h-40 overflow-y-auto p-2 border rounded-md">
                                            @foreach ($categories as $category)
                                                <label class="inline-flex items-center">
                                                    <input type="checkbox" name="category_id[]" value="{{ $category->id }}" {{ is_array(request('category_id')) && in_array($category->id, request('category_id')) ? 'checked' : '' }} class="form-checkbox text-blue-600 rounded">
                                                    <span class="ml-2 text-sm text-gray-600">{{ $category->category_name }}</span>
                                                </label>
                                            @endforeach
                                        </div>
                                    </div>

                                    {{-- Date Range --}}
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Intervalo de Datas</label>
                                        <input type="date" name="start_date" value="{{ request('start_date') }}" class="w-full rounded-md border-gray-300 text-sm mb-2">
                                        <input type="date" name="end_date" value="{{ request('end_date') }}" class="w-full rounded-md border-gray-300 text-sm">
                                    </div>
                                    
                                    {{-- Order By --}}
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Ordenar por</label>
                                        <div class="flex flex-col space-y-2">
                                            <label class="inline-flex items-center">
                                                <input type="checkbox" name="schedule_order" value="soonest" {{ request('schedule_order') === 'soonest' ? 'checked' : '' }} class="form-checkbox text-blue-600 rounded">
                                                <span class="ml-2 text-sm text-gray-600">Mais Próximo</span>
                                            </label>
                                            <label class="inline-flex items-center">
                                                <input type="checkbox" name="schedule_order" value="latest" {{ request('schedule_order') === 'latest' ? 'checked' : '' }} class="form-checkbox text-blue-600 rounded">
                                                <span class="ml-2 text-sm text-gray-600">Mais Distante</span>
                                            </label>
                                            <label class="inline-flex items-center">
                                                <input type="checkbox" name="likes_order" value="most" {{ request('likes_order') === 'most' ? 'checked' : '' }} class="form-checkbox text-blue-600 rounded">
                                                <span class="ml-2 text-sm text-gray-600">Mais Curtidos</span>
                                            </label>
                                            <label class="inline-flex items-center">
                                                <input type="checkbox" name="likes_order" value="least" {{ request('likes_order') === 'least' ? 'checked' : '' }} class="form-checkbox text-blue-600 rounded">
                                                <span class="ml-2 text-sm text-gray-600">Menos Curtidos</span>
                                            </label>
                                        </div>
                                    </div>

                                    <button type="submit" class="w-full rounded-md bg-blue-600 py-2 text-white font-semibold hover:bg-blue-700 transition-colors">Aplicar Filtros</button>
                                    <button type="button" onclick="window.location.href='{{ route('events.index') }}'" class="w-full mt-2 rounded-md bg-gray-200 py-2 text-gray-700 font-semibold hover:bg-gray-300 transition-colors">Limpar Filtros</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Events List --}}
                <div id="events-container" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                    @forelse ($events as $event)
                        <a href="{{ route('events.show', $event->id) }}" class="block shadow-lg transform transition-transform duration-300 hover:scale-105 rounded-xl overflow-hidden cursor-pointer">
                            {{-- Event Card --}}
                            <div class="relative bg-white border border-gray-200 rounded-xl shadow-md p-4 flex flex-col h-full">
                                {{-- Image --}}
                                <div class="relative w-full h-48 rounded-md overflow-hidden mb-4">
                                    <img src="{{ $event->event_image ? asset('storage/' . $event->event_image) : asset('imgs/placeholder.png') }}" alt="{{ $event->event_name }}" class="object-cover w-full h-full">
                                    
                                    {{-- Tag para tipo de evento --}}
                                    <span class="absolute top-2 right-2 bg-blue-600 text-white text-xs font-semibold px-2 py-1 rounded-full shadow">
                                        {{ $event->event_type === 'course' ? 'Curso' : 'Evento' }}
                                    </span>
                                    
                                    {{-- Tag para visibilidade (somente se o usuário é o coordenador e o evento é dele) --}}
                                    @if ($loggedCoordinator && $loggedCoordinator->id === $event->eventCoordinator->user_account_id)
                                        @if ($event->is_visible)
                                            <span class="absolute top-2 left-2 text-xs font-semibold px-2 py-1 rounded-full shadow bg-green-500 text-white">
                                                Visível
                                            </span>
                                        @else
                                            <span class="absolute top-2 left-2 text-xs font-semibold px-2 py-1 rounded-full shadow bg-red-500 text-white">
                                                Oculto
                                            </span>
                                        @endif
                                    @endif
                                </div>
                                
                                {{-- Content --}}
                                <div class="flex-1 flex flex-col justify-between">
                                    <div>
                                        <h3 class="text-lg font-bold text-gray-900 mb-1 leading-tight line-clamp-2">
                                            {{ $event->event_name }}
                                        </h3>
                                        <p class="text-sm text-gray-600 mb-2 line-clamp-2">
                                            {{ $event->event_description }}
                                        </p>
                                        
                                        <div class="flex flex-wrap gap-2 text-xs mb-2">
                                            @forelse ($event->eventCategories as $category)
                                                <span class="bg-gray-200 text-gray-700 px-2 py-1 rounded-full">
                                                    {{ $category->category_name }}
                                                </span>
                                            @empty
                                                <span class="bg-gray-200 text-gray-700 px-2 py-1 rounded-full">
                                                    Sem Categoria
                                                </span>
                                            @endforelse
                                        </div>
                                    </div>
                                    
                                    <div class="mt-auto">
                                        <p class="text-sm text-gray-800 font-medium mt-2">
                                            <i class="fas fa-map-marker-alt text-gray-500 mr-1"></i>
                                            {{ $event->event_location }}
                                        </p>
                                        <p class="text-sm text-gray-800 font-medium mt-1">
                                            <i class="far fa-calendar-alt text-gray-500 mr-1"></i>
                                            {{ \Carbon\Carbon::parse($event->event_scheduled_at)->isoFormat('D MMMM YYYY, HH:mm') }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </a>
                    @empty
                        {{-- Este bloco é exibido quando a lista de eventos está vazia --}}
                        <div class="col-span-full flex flex-col items-center justify-center p-12">
                            <img src="{{ asset('imgs/placeholder.png') }}" alt="Nenhum evento encontrado" class="w-32 h-32 mb-4 text-gray-400">
                            <p class="text-xl font-semibold text-gray-500">Nenhum evento encontrado...</p>
                            <p class="text-sm text-gray-400 mt-2">Tente ajustar os filtros ou a pesquisa.</p>
                        </div>
                    @endforelse
                </div>
                
                {{-- Pagination --}}
                <div class="mt-8">
                    {{ $events->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

{{-- Scripts for Interactivity --}}
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const filterBtn = document.getElementById('filterBtn');
        const filterMenu = document.getElementById('filterMenu');
        
        // Toggle filter dropdown
        filterBtn.addEventListener('click', function () {
            filterMenu.classList.toggle('hidden');
        });
        
        // Hide dropdown on outside click
        document.addEventListener('click', function (event) {
            if (!filterBtn.contains(event.target) && !filterMenu.contains(event.target)) {
                filterMenu.classList.add('hidden');
            }
        });
        
        // Stop propogation on filter form to keep dropdown open
        filterMenu.addEventListener('click', function (event) {
            event.stopPropagation();
        });

        // Ensure only one checkbox is checked for single-select filters
        document.querySelectorAll('input[name="status"], input[name="event_type"], input[name="schedule_order"], input[name="likes_order"]').forEach(checkbox => {
            checkbox.addEventListener('change', function () {
                if (this.checked) {
                    document.querySelectorAll(`input[name="${this.name}"]`).forEach(cb => {
                        if (cb !== this) {
                            cb.checked = false;
                        }
                    });
                }
            });
        });
    });
</script>
{{-- Scripts compilados --}}
@vite('resources/js/app.js')
