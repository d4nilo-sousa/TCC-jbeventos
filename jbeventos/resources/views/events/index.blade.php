<x-app-layout>
    <!-- Cabeçalho da página -->
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{-- Uso do if para troca de texto, dependendo do filtro de visibilidade que o coordenador queira usar --}}
            @if (request('status') === 'visible')
                {{ __('Meus Eventos (Visíveis)') }}
            @elseif(request('status') === 'hidden')
                {{ __('Meus Eventos (Ocultos)') }}
            @else
                {{ __('Lista de Eventos') }}
            @endif
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            {{-- Exibe mensagem de sucesso, se houver --}}
            @if (session('success'))
                <div class="mb-4 rounded-lg bg-green-100 px-6 py-4 text-green-800">
                    {{ session('success') }}
                </div>
            @endif

            @php
                $loggedCoordinator = auth()->user()->coordinator;
            @endphp

            <div class="mb-4 flex justify-between items-center">
                {{-- Botão "Novo Evento" à esquerda --}}
                <div>
                    @if ($loggedCoordinator)
                        <a href="{{ route('events.create') }}"
                            class="inline-flex items-center rounded-md bg-blue-600 px-4 py-2 text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                            + Novo Evento
                        </a>
                    @endif
                </div>

                {{-- Botões "Filtrar" e "Ordenar" à direita --}}
                <div class="flex items-center gap-4">

                    {{-- Botão Filtrar --}}
                    <div class="relative">
                        <button id="filterBtn"
                            class="inline-flex items-center rounded-md bg-gray-200 px-4 py-2 text-gray-700 hover:bg-gray-300">
                            Filtrar
                            {{-- Ícone seta --}}
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
                                    {{-- Filtro de visibilidade (apenas coordenador logado) --}}
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Visibilidade</label>
                                    <div class="mb-3 border rounded p-2 flex flex-col items-start space-y-2">
                                        @foreach (['visible' => 'Visível', 'hidden' => 'Oculto'] as $value => $label)
                                            <label class="flex items-center space-x-2 mb-1">
                                                <input type="checkbox" name="status" value="{{ $value }}"
                                                    {{ request('status') === $value ? 'checked' : '' }}
                                                    {{-- Mantém após reload --}} class="rounded border-gray-300"
                                                    onclick="if(this.checked){document.querySelectorAll('input[name=status]').forEach(cb=>{if(cb!==this) cb.checked=false})}">
                                                {{-- Seleção única --}}
                                                <span>{{ $label }}</span>
                                            </label>
                                        @endforeach
                                    </div>
                                @endif

                                {{-- Filtro por tipo de evento --}}
                                <label class="block text-sm font-medium text-gray-700 mb-1">Tipo do Evento</label>
                                <div class="mb-3 border rounded p-2 flex flex-col items-start space-y-2">
                                    @foreach (['general' => 'Geral', 'course' => 'Curso'] as $value => $label)
                                        <label class="flex items-center space-x-2 mb-1">
                                            <input type="checkbox" name="event_type" value="{{ $value }}"
                                                {{ request('event_type') === $value ? 'checked' : '' }}
                                                class="rounded border-gray-300"
                                                onclick="if(this.checked){document.querySelectorAll('input[name=event_type]').forEach(cb=>{if(cb!==this) cb.checked=false})}">
                                            {{-- Seleção única --}}
                                            <span>{{ $label }}</span>
                                        </label>
                                    @endforeach
                                </div>

                                {{-- Filtro por cursos (mostrado apenas se "Curso" for selecionado) --}}
                                <div id="courseSelectWrapper" style="display:none;">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Curso</label>
                                    <div class="mb-3 max-h-40 overflow-y-auto border rounded p-2">
                                        @foreach ($courses as $course)
                                            <label class="flex items-center space-x-2 mb-1">
                                                <input type="checkbox" name="course_id[]" value="{{ $course->id }}"
                                                    {{ is_array(request('course_id')) && in_array($course->id, request('course_id')) ? 'checked' : '' }}
                                                    class="rounded border-gray-300">
                                                <span>{{ $course->course_name }}</span>
                                            </label>
                                        @endforeach
                                    </div>
                                </div>

                                {{-- Filtro por categorias --}}
                                <div id="categorySelectWrapper">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Categoria</label>
                                    <div class="mb-3 max-h-40 overflow-y-auto border rounded p-2">
                                        @foreach ($categories as $category)
                                            <label class="flex items-center space-x-2 mb-1">
                                                <input type="checkbox" name="category_id[]" value="{{ $category->id }}"
                                                    {{ is_array(request('category_id')) && in_array($category->id, request('category_id')) ? 'checked' : '' }}
                                                    class="rounded border-gray-300">
                                                <span>{{ $category->category_name }}</span>
                                            </label>
                                        @endforeach
                                    </div>
                                </div>

                                {{-- Filtro por intervalo de datas --}}
                                <label class="block text-sm font-medium text-gray-700 mb-1">Evento entre</label>
                                <div class="mb-3 border rounded p-2 flex flex-col items-start space-y-2">
                                    <input type="date" name="start_date" value="{{ request('start_date') }}"
                                        class="rounded border-gray-300 px-2 py-1 text-sm w-full">
                                    <span class="text-sm text-gray-600 self-center">até</span>
                                    <input type="date" name="end_date" value="{{ request('end_date') }}"
                                        class="rounded border-gray-300 px-2 py-1 text-sm w-full">
                                </div>

                                {{-- Botões de ação --}}
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
                            class="inline-flex items-center rounded-md bg-gray-200 px-4 py-2 text-gray-700 hover:bg-gray-300">
                            Ordenar
                            {{-- Ícone seta --}}
                            <svg class="ml-2 w-4 h-4" fill="none" stroke="currentColor" stroke-width="2"
                                viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>

                        {{-- Menu dropdown --}}
                        <div id="orderMenu"
                            class="absolute right-0 mt-2 w-64 bg-white border border-gray-200 rounded-md shadow-lg p-3 z-10 hidden">
                            <form method="GET" action="{{ route('events.index') }}">

                                {{-- Título do filtro --}}
                                <label class="block text-sm font-medium text-gray-700 mb-1">Ordenar por curtidas</label>

                                {{-- Opções de ordenação (checkbox com seleção única) --}}
                                <div class="mb-3 border rounded p-2 flex flex-col items-start space-y-2">
                                    @foreach (['most' => 'Mais Curtido', 'least' => 'Menos Curtido'] as $value => $label)
                                        <label class="flex items-center space-x-2 mb-1">
                                            <input type="checkbox" name="likes_order" value="{{ $value }}"
                                                {{ request('likes_order') === $value ? 'checked' : '' }}
                                                {{-- Mantém selecionado após reload --}} class="rounded border-gray-300"
                                                onclick="if(this.checked){document.querySelectorAll('input[name=likes_order]').forEach(cb=>{if(cb!==this) cb.checked=false})}">
                                            {{-- Garante seleção única --}}
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
            </div>

            {{-- Lista de eventos --}}
            @if ($events->count() > 0)
                <div class="grid grid-cols-1 gap-6 md:grid-cols-3">
                    @forelse ($events as $event)
                        @include('partials.event-card', ['event' => $event])
                    @empty
                        <p class="text-gray-500">Nenhum evento cadastrado até o momento.</p>
                    @endforelse
                </div>
            @endif
        </div>
    </div>
</x-app-layout>

@vite('resources/js/filter-menu.js')
@vite('resources/js/order-menu.js')
@vite('resources/js/event-realtime.js')

