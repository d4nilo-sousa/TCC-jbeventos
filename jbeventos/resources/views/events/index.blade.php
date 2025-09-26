<x-app-layout>
    <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
        <div class="bg-white shadow-xl rounded-2xl p-6 sm:p-9">

            {{-- Header com Pesquisa, Filtros e Ordenação --}}
            <div class="mb-10 flex flex-col md:flex-row justify-between items-start md:items-center space-y-4 md:space-y-0">
                <h2 class="text-2xl font-bold text-gray-800">Todos os Eventos</h2>

                {{-- O contêiner pai deve ter 'flex' e a pesquisa deve ter 'flex-grow' --}}
                <div class="flex flex-col sm:flex-row items-center space-y-3 sm:space-y-0 sm:space-x-3 w-full md:w-auto">
                    
                    {{-- Pesquisa - Corrigido para ocupar o máximo de espaço --}}
                    <form method="GET" action="{{ route('events.index') }}" class="w-full flex-grow">
                        <div class="relative flex items-center w-full">
                            <input type="search" name="search" id="search-input" placeholder="Pesquisar eventos..."
                                value="{{ request('search') }}"
                                class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 text-sm">
                            <i class="fas fa-search absolute left-3 text-gray-400"></i>
                        </div>
                    </form>

                    {{-- Botão e Menu Dropdown de Filtros --}}
                    <div class="relative inline-block text-left w-full sm:w-auto">
                        <button id="filterBtn" type="button"
                            class="inline-flex items-center justify-center w-full rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none">
                            Filtros
                            <svg class="-mr-1 ml-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"
                                fill="currentColor" aria-hidden="true">
                                <path fill-rule="evenodd"
                                    d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                    clip-rule="evenodd" />
                            </svg>
                        </button>
                        <div id="filterMenu"
                            class="absolute right-0 z-20 mt-2 w-64 origin-top-right rounded-md bg-white shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none hidden">
                            <form method="GET" action="{{ route('events.index') }}" class="p-4 space-y-4">

                                {{-- Passa a Pesquisa atual para o formulário de filtros (necessário para filtros manuais) --}}
                                @if (request('search'))
                                    <input type="hidden" name="search" value="{{ request('search') }}">
                                @endif

                                {{-- Visibilidade (Coordenador) --}}
                                @php $loggedCoordinator = auth()->user()->coordinator ?? null; @endphp
                                @if ($loggedCoordinator)
                                    <div>
                                        <label
                                            class="block text-sm font-medium text-gray-700 mb-1">Visibilidade</label>
                                        <div class="flex items-center space-x-4">
                                            @foreach (['visible' => 'Visível', 'hidden' => 'Oculto'] as $value => $label)
                                                <label class="inline-flex items-center">
                                                    <input type="checkbox" name="status"
                                                        value="{{ $value }}"
                                                        {{ request('status') === $value ? 'checked' : '' }}
                                                        class="form-checkbox text-blue-600 rounded">
                                                    <span
                                                        class="ml-2 text-sm text-gray-600">{{ $label }}</span>
                                                </label>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif

                                {{-- Tipo de Evento --}}
                                <div>
                                    <label
                                        class="block text-sm font-medium text-gray-700 mb-1">Tipo</label>
                                    <div class="flex items-center space-x-4">
                                        @foreach (['event' => 'Evento', 'course' => 'Curso'] as $value => $label)
                                            <label class="inline-flex items-center">
                                                <input type="checkbox" name="event_type"
                                                    value="{{ $value }}"
                                                    {{ request('event_type') === $value ? 'checked' : '' }}
                                                    class="form-checkbox text-blue-600 rounded">
                                                <span
                                                    class="ml-2 text-sm text-gray-600">{{ $label }}</span>
                                            </label>
                                        @endforeach
                                    </div>
                                </div>

                                {{-- Categories --}}
                                <div>
                                    <label
                                        class="block text-sm font-medium text-gray-700 mb-1">Categorias</label>
                                    <div
                                        class="flex flex-wrap gap-2 max-h-40 overflow-y-auto p-2 border rounded-md">
                                        {{-- Certifique-se que $categories é passado pela controller --}}
                                        @if (isset($categories))
                                            @foreach ($categories as $category)
                                                <label class="inline-flex items-center">
                                                    <input type="checkbox" name="category_id[]"
                                                        value="{{ $category->id }}"
                                                        {{ is_array(request('category_id')) && in_array($category->id, request('category_id')) ? 'checked' : '' }}
                                                        class="form-checkbox text-blue-600 rounded">
                                                    <span
                                                        class="ml-2 text-sm text-gray-600">{{ $category->category_name }}</span>
                                                </label>
                                            @endforeach
                                        @endif
                                    </div>
                                </div>

                                {{-- Date Range --}}
                                <div>
                                    <label
                                        class="block text-sm font-medium text-gray-700 mb-1">Intervalo
                                        de Datas</label>
                                    <input type="date" name="start_date"
                                        value="{{ request('start_date') }}"
                                        class="w-full rounded-md border-gray-300 text-sm mb-2">
                                    <input type="date" name="end_date"
                                        value="{{ request('end_date') }}"
                                        class="w-full rounded-md border-gray-300 text-sm">
                                </div>

                                {{-- Order By --}}
                                <div>
                                    <label
                                        class="block text-sm font-medium text-gray-700 mb-1">Ordenar
                                        por</label>
                                    <div class="flex flex-col space-y-2">
                                        <label class="inline-flex items-center">
                                            <input type="checkbox" name="schedule_order"
                                                value="soonest"
                                                {{ request('schedule_order') === 'soonest' ? 'checked' : '' }}
                                                class="form-checkbox text-blue-600 rounded">
                                            <span class="ml-2 text-sm text-gray-600">Mais
                                                Próximo</span>
                                        </label>
                                        <label class="inline-flex items-center">
                                            <input type="checkbox" name="schedule_order"
                                                value="latest"
                                                {{ request('schedule_order') === 'latest' ? 'checked' : '' }}
                                                class="form-checkbox text-blue-600 rounded">
                                            <span class="ml-2 text-sm text-gray-600">Mais
                                                Distante</span>
                                        </label>
                                        <label class="inline-flex items-center">
                                            <input type="checkbox" name="likes_order"
                                                value="most"
                                                {{ request('likes_order') === 'most' ? 'checked' : '' }}
                                                class="form-checkbox text-blue-600 rounded">
                                            <span class="ml-2 text-sm text-gray-600">Mais
                                                Curtidos</span>
                                        </label>
                                        <label class="inline-flex items-center">
                                            <input type="checkbox" name="likes_order"
                                                value="least"
                                                {{ request('likes_order') === 'least' ? 'checked' : '' }}
                                                class="form-checkbox text-blue-600 rounded">
                                            <span class="ml-2 text-sm text-gray-600">Menos
                                                Curtidos</span>
                                        </label>
                                    </div>
                                </div>

                                <button type="submit"
                                    class="w-full rounded-md bg-blue-600 py-2 text-white font-semibold hover:bg-blue-700 transition-colors">Aplicar
                                    Filtros</button>
                                <button type="button"
                                    onclick="window.location.href='{{ route('events.index') }}'"
                                    class="w-full mt-2 rounded-md bg-gray-200 py-2 text-gray-700 font-semibold hover:bg-gray-300 transition-colors">Limpar
                                    Filtros</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Events List (Container de AJAX) --}}
            <div id="events-container"
                class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                {{-- Use APENAS o loop, sem o @empty, pois a mensagem é tratada no Controller/AJAX --}}
                @foreach ($events as $event)
                    @include('partials.events.event-card', ['event' => $event])
                @endforeach

                {{-- Se for a primeira carga e a coleção estiver vazia, adicione a mensagem AQUI manualmente --}}
                @if ($events->isEmpty() && !request()->ajax())
                    <div id="no-events-message"
                        class="col-span-full flex flex-col items-center justify-center p-12">
                        <img src="{{ asset('imgs/notFound.png') }}"
                            alt="Nenhum evento encontrado"
                            class="w-32 h-32 mb-4 text-gray-400">
                        <p class="text-xl font-semibold text-gray-500">Nenhum evento encontrado...</p>
                        <p class="text-sm text-gray-400 mt-2">Tente ajustar os filtros ou a pesquisa.</p>
                    </div>
                @endif
            </div>

            {{-- Pagination (Container de AJAX) --}}
            <div id="pagination-links" class="mt-8">
                {{ $events->links() }}
            </div>
        </div>
    </div>
</x-app-layout>

{{-- Scripts para Dropdown e Checkbox (Deixamos aqui para não precisar mexer em outros arquivos) --}}
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const filterBtn = document.getElementById('filterBtn');
        const filterMenu = document.getElementById('filterMenu');

        // Toggle filter dropdown
        filterBtn.addEventListener('click', function() {
            filterMenu.classList.toggle('hidden');
        });

        // Hide dropdown on outside click
        document.addEventListener('click', function(event) {
            if (!filterBtn.contains(event.target) && !filterMenu.contains(event.target)) {
                filterMenu.classList.add('hidden');
            }
        });

        // Stop propogation on filter form to keep dropdown open
        filterMenu.addEventListener('click', function(event) {
            event.stopPropagation();
        });

        // Ensure only one checkbox is checked for single-select filters
        document.querySelectorAll(
            'input[name="status"], input[name="event_type"], input[name="schedule_order"], input[name="likes_order"]'
        ).forEach(checkbox => {
            checkbox.addEventListener('change', function() {
                // Desmarca outros checkboxes com o mesmo 'name'
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

{{-- Scripts compilados, incluindo search-highlight.js --}}
@vite('resources/js/app.js')