<x-app-layout>
    <div class="py-10 bg-gray-50 min-h-screen">
        <div class="max-w-[1400px] mx-auto sm:px-6 lg:px-16 space-y-6">
            <div
                class="mb-8 flex flex-col md:flex-row justify-between items-start md:items-center space-y-4 md:space-y-0">
                <div class="mt-1">
                    <p class="text-3xl sm:text-4xl font-extrabold text-stone-800 mt-3 tracking-tight drop-shadow-sm">
                        Todos os Eventos
                    </p>
                    <div class="w-16 h-1 bg-red-500 rounded-full mt-2 shadow-lg"></div>
                </div>

                <div class="flex flex-col sm:flex-row items-center space-y-3 sm:space-y-0 sm:space-x-3 w-full md:w-auto">
                    {{-- Formulário de Pesquisa de Eventos --}}
                    <form method="GET" action="{{ route('events.index') }}" class="w-full flex-grow max-w-sm">
                        <div class="relative flex items-center w-full shadow-md rounded-full bg-white ">
                            <svg class="absolute left-4 w-5 h-5 text-gray-500" fill="none" stroke="currentColor"
                                stroke-width="2" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"
                                aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M21 21l-4.35-4.35m0 0A7.5 7.5 0 1110.5 3a7.5 7.5 0 016.15 13.65z"></path>
                            </svg>

                            <input type="search" name="search" id="search-input" placeholder="Pesquisar eventos..."
                                value="{{ request('search') }}"
                                class="w-full pl-11 pr-5 py-2.5 border border-gray-200 rounded-full focus:ring-red-500 focus:border-red-500 text-sm placeholder-gray-500 bg-transparent">

                        </div>
                    </form>

                    {{-- Botão e Menu Dropdown de Filtros --}}
                    <div class="relative inline-block text-left w-full sm:w-auto">
                        <button id="filterBtn" type="button"
                            class="inline-flex items-center justify-center w-full rounded-full border border-gray-200 bg-white px-5 py-2.5 text-sm font-medium text-gray-700 shadow-md hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-red-500">
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

                                @if (request('search'))
                                    <input type="hidden" name="search" value="{{ request('search') }}">
                                @endif

                                {{-- Tipo de Evento --}}
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Tipo</label>
                                    <div class="flex items-center space-x-4">
                                        @foreach (['general' => 'Geral', 'course' => 'Curso'] as $value => $label)
                                            <label class="inline-flex items-center">
                                                <input type="checkbox" name="event_type" value="{{ $value }}"
                                                    {{ request('event_type') === $value ? 'checked' : '' }}
                                                    class="form-checkbox text-red-600 rounded">
                                                <span class="ml-2 text-sm text-gray-600">{{ $label }}</span>
                                            </label>
                                        @endforeach
                                    </div>
                                </div>

                                @php
                                    $selectedCourses = array_map('intval', (array) request('course_id'));
                                    $selectedCategories = array_map('intval', (array) request('category_id'));
                                @endphp

                                <div id="courseSelectWrapper"
                                    class="{{ request('event_type') === 'course' || count($selectedCourses) > 0 ? '' : 'hidden' }} mt-4 p-4 border rounded bg-gray-50">
                                    <label class="block mb-1 text-sm font-medium text-gray-700">Selecione o(s)
                                        Curso(s)</label>
                                    <div class="flex flex-wrap gap-4 max-h-48 overflow-y-auto">
                                        @foreach ($courses as $course)
                                            <label class="inline-flex items-center">
                                                <input type="checkbox" name="course_id[]" value="{{ $course->id }}"
                                                    {{ in_array($course->id, $selectedCourses) ? 'checked' : '' }}
                                                    class="form-checkbox text-red-600 rounded">
                                                <span
                                                    class="ml-2 text-sm text-gray-600">{{ $course->course_name }}</span>
                                            </label>
                                        @endforeach
                                    </div>
                                </div>

                                {{-- Categorias --}}
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Categorias</label>
                                    <div class="flex flex-wrap gap-2 max-h-40 overflow-y-auto p-2 border rounded-md">
                                        @if (isset($categories))
                                            @foreach ($categories as $category)
                                                <label class="inline-flex items-center">
                                                    <input type="checkbox" name="category_id[]"
                                                        value="{{ $category->id }}"
                                                        {{ in_array($category->id, $selectedCategories) ? 'checked' : '' }}
                                                        class="form-checkbox text-red-600 rounded">
                                                    <span
                                                        class="ml-2 text-sm text-gray-600">{{ $category->category_name }}</span>
                                                </label>
                                            @endforeach
                                        @endif
                                    </div>
                                </div>

                                {{-- Intervalo de Datas --}}
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Intervalo de
                                        Datas</label>

                                    <input type="date" name="start_date"
                                        value="{{ old('start_date', request('start_date')) }}"
                                        class="w-full rounded-md border-gray-300 text-sm mb-2">
                                    @error('start_date')
                                        <div class="text-red-600 text-sm mt-1">{{ $message }}</div>
                                    @enderror

                                    <input type="date" name="end_date"
                                        value="{{ old('end_date', request('end_date')) }}"
                                        class="w-full rounded-md border-gray-300 text-sm">
                                    @error('end_date')
                                        <div class="text-red-600 text-sm mt-1">{{ $message }}</div>
                                    @enderror
                                </div>


                                {{-- Ordenar por - usando radios para seleção única --}}
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Ordenar por</label>
                                    <div class="flex flex-col space-y-2">
                                        <label class="inline-flex items-center">
                                            <input type="radio" name="schedule_order" value="soonest"
                                                {{ request('schedule_order') === 'soonest' ? 'checked' : '' }}
                                                class="form-radio text-red-600 rounded">
                                            <span class="ml-2 text-sm text-gray-600">Mais Próximo</span>
                                        </label>
                                        <label class="inline-flex items-center">
                                            <input type="radio" name="schedule_order" value="latest"
                                                {{ request('schedule_order') === 'latest' ? 'checked' : '' }}
                                                class="form-radio text-red-600 rounded">
                                            <span class="ml-2 text-sm text-gray-600">Mais Distante</span>
                                        </label>
                                        <label class="inline-flex items-center">
                                            <input type="radio" name="likes_order" value="most"
                                                {{ request('likes_order') === 'most' ? 'checked' : '' }}
                                                class="form-radio text-red-600 rounded">
                                            <span class="ml-2 text-sm text-gray-600">Mais Curtidos</span>
                                        </label>
                                        <label class="inline-flex items-center">
                                            <input type="radio" name="likes_order" value="least"
                                                {{ request('likes_order') === 'least' ? 'checked' : '' }}
                                                class="form-radio text-red-600 rounded">
                                            <span class="ml-2 text-sm text-gray-600">Menos Curtidos</span>
                                        </label>
                                    </div>
                                </div>

                                <button type="submit"
                                    class="w-full rounded-md bg-red-600 py-2 text-white font-semibold hover:bg-red-700 transition-colors">Aplicar
                                    Filtros</button>
                                <button type="button" id="resetFiltres"
                                    class="w-full mt-2 rounded-md bg-gray-200 py-2 text-gray-700 font-semibold hover:bg-gray-300 transition-colors">Limpar
                                    Filtros</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Events List (Container de AJAX) --}}
            <div id="events-container"
                class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-10 justify-items-center">

                @forelse ($events as $event)
                    @include('partials.events.event-card', ['event' => $event])
                @empty
                    {{-- Mensagem de Vazio/Não Encontrado (Classes de espaçamento ajustadas) --}}
                    <div id="no-events-message"
                        class="col-span-full flex flex-col items-center justify-center gap-6 text-center w-full my-4 p-6">
                        <img src="{{ asset('imgs/notFound.png') }}" class="w-auto h-40 object-contain"
                            alt="not-found">
                        <div>
                            <p class="text-2xl font-bold text-stone-800">Ops! Nada foi encontrado...</p>
                            <p class="text-gray-500 mt-2 text-md max-w-lg mx-auto">
                                Não encontramos nenhum evento com os termos de busca. Tente refazer a pesquisa!
                            </p>
                        </div>
                    </div>
                @endforelse
            </div>

            {{-- Pagination (Container de AJAX) --}}
            <div id="pagination-links" class="mt-8">
                {{ $events->links() }}
            </div>
        </div>
    </div>
</x-app-layout>

@vite('resources/js/app.js')

<script src="https://unpkg.com/@phosphor-icons/web"></script>
