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
                    <h1 class="text-3xl font-bold text-gray-800 leading-tight">
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

                                    {{-- Event Type --}}
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Tipo do Evento</label>
                                        <div class="flex items-center space-x-4">
                                            @foreach (['general' => 'Geral', 'course' => 'Curso'] as $value => $label)
                                                <label class="inline-flex items-center">
                                                    <input type="checkbox" name="event_type" value="{{ $value }}" {{ request('event_type') === $value ? 'checked' : '' }} class="form-checkbox text-blue-600 rounded">
                                                    <span class="ml-2 text-sm text-gray-600">{{ $label }}</span>
                                                </label>
                                            @endforeach
                                        </div>
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
                        <a href="{{ route('events.show', $event->id) }}" class="block transform transition-transform duration-300 hover:scale-105 hover:shadow-lg rounded-xl overflow-hidden cursor-pointer">
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