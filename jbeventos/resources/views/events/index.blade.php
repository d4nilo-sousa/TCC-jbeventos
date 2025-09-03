<x-app-layout backgroundClass="bg-gradient-to-br from-red-500 via-red-200 to-red-100">
    <div class="py-[5rem] min-h-screen">
        <div class="w-full max-w-[100rem] mx-auto sm:px-6 lg:px-5 flex justify-center">
            <div class="w-full bg-white shadow-md rounded-2xl p-4 sm:p-6 lg:p-9 mx-auto min-h-[70vh]">

                {{-- Exibe mensagem de sucesso, se houver --}}
                @if (session('success'))
                    <div class="mb-4 rounded-lg bg-green-100 px-6 py-4 text-green-800 mt-6">
                        {{ session('success') }}
                    </div>
                @endif

                @php
                    $loggedCoordinator = auth()->user()->coordinator;
                @endphp

                <div
                    class="flex flex-col sm:flex-row justify-between items-start sm:items-center px-3 gap-5 w-full flex-wrap mb-10 mt-3">

                    {{-- Título e hr personalizado --}}
                    <div>
                        {{-- Título da view 'Lista de Eventos' --}}
                        <p
                            class="text-center bg-gradient-to-r from-stone-900 to-stone-400 bg-clip-text text-transparent 
                        font-extrabold text-3xl sm:text-5xl tracking-wide drop-shadow-md">
                            {{ request('status') === 'visible' ? __('Meus Eventos (Visíveis)') : (request('status') === 'hidden' ? __('Meus Eventos (Ocultos)') : __('Lista de Eventos')) }}
                        </p>
                        {{-- "hr" personalizado --}}
                        <div class="w-[5rem] h-1 bg-red-400 rounded-full mt-3 shadow-xl"></div>
                    </div>


                    <div class="flex flex-wrap gap-5">
                        {{-- Botão "Novo Evento" --}}
                        <div>
                            @if ($loggedCoordinator)
                                <a href="{{ route('events.create') }}"
                                    class="inline-flex items-center rounded-md bg-blue-600 px-4 py-2 text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                                    + Novo Evento
                                </a>
                            @endif
                        </div>

                        {{-- Botões Filtrar e Ordenar --}}
                        <div class="flex items-center gap-4">

                            {{-- Botão Filtrar --}}
                            <div class="relative">
                                <button id="filterBtn"
                                    class="inline-flex items-center rounded-md bg-red-200 px-4 py-2 text-gray-700 hover:bg-red-300 transition ease-in-out">
                                    Filtrar
                                    <svg class="ml-2 w-4 h-4" fill="none" stroke="currentColor" stroke-width="2"
                                        viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"></path>
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
                                <input type="text" name="search" value="{{ request('search') }}"
                                    placeholder="Pesquisar cursos..." autocomplete="off"
                                    class="px-6 flex-1 min-w-[200px] sm:min-w-[300px] lg:min-w-[350px] text-gray-800 placeholder-gray-500 border-none outline-none focus:ring-0 bg-white">
                                <button type="submit"
                                    class="flex items-center justify-center bg-stone-800 hover:bg-stone-900 transition-colors px-6 py-3">
                                    <img src="{{ asset('imgs/lupaBranca.svg') }}" class="w-7 h-7">
                                </button>
                            </div>
                        </form>
                    </div>

                </div>

                <div>
                    <div
                        class="bg-white shadow-xl rounded-2xl p-4 sm:p-6 lg:p-9 mx-auto border-2 border-stone-100 min-h-[72%]">
                        <div class="mb-10">
                            {{-- Lista de eventos --}}
                            @if ($events->count() > 0)
                                <div class="grid grid-cols-1 gap-6 md:grid-cols-3 mb-10 mt-10">
                                    @foreach ($events as $event)
                                        @include('partials.event-card', ['event' => $event])
                                    @endforeach
                                </div>
                            @else
                                <div class="flex flex-col items-center gap-5">
                                    <img src="{{ asset('imgs/notFound.png') }}" class="w-[19%] flex mx-auto"
                                        alt="not-found">
                                    <p class="text-gray-500">Nenhum evento encontrado...</p>
                                </div>




                            @endif
                        </div>


                    </div>
                </div>
            </div>
        </div>
</x-app-layout>

{{-- Scripts compilados --}}
@vite('resources/js/app.js')
