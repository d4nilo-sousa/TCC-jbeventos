<x-app-layout>
    <!-- Cabeçalho da página -->
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{-- Uso do if para troca de texto, dependendo do filtro de visibilidade que o coordenador queira usar --}}
            @if(request('status') === 'visible')
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
                    @foreach ($events as $event)
                        <div class="overflow-hidden rounded-lg border border-gray-200 shadow-sm flex flex-col">
                            {{-- Imagem do evento ou mensagem "Sem imagem" --}}
                            @if ($event->event_image)
                                <img src="{{ asset('storage/' . $event->event_image) }}" alt="Imagem do Evento"
                                    class="h-48 w-full object-cover">
                            @else
                                <div class="h-48 w-full bg-gray-200 flex items-center justify-center text-gray-500">
                                    Sem imagem
                                </div>
                            @endif

                            <div class="p-4 flex flex-col flex-grow">
                                {{-- Nome do evento --}}
                                <h3 class="mb-2 text-lg font-semibold text-gray-900">{{ $event->event_name }}</h3>

                                {{-- Descrição limitada a 100 caracteres --}}
                                <p class="mb-2 text-gray-700 text-sm overflow-hidden text-ellipsis line-clamp-3">
                                    {{ Str::limit($event->event_description, 100) }}
                                </p>

                                <div class="mt-auto">
                                    {{-- Local e data/hora do evento --}}
                                    <p class="mb-1 text-sm text-gray-500">
                                        📍 {{ $event->event_location }}<br>
                                        📅
                                        {{ \Carbon\Carbon::parse($event->event_scheduled_at)->format('d/m/Y H:i') }}
                                    </p>

                                </div>

                                {{-- Botões de ação: Ver, Editar, Ocultar e Excluir --}}
                                <div class="mt-auto flex flex-col space-y-2">

                                    {{-- Botão para visualizar o evento --}}
                                    <a href="{{ route('events.show', $event->id) }}"
                                        class="rounded-md bg-blue-100 px-3 py-1 text-center text-sm font-medium text-blue-700 hover:bg-blue-200">
                                        Ver
                                    </a>

                                    @if (auth()->check() && auth()->user()->user_type === 'coordinator')
                                        {{-- Só exibe as opções abaixo se o usuário estiver logado e for coordenador --}}

                                        @php
                                            $loggedCoordinator = auth()->user()->coordinator;
                                            // Obtém o coordenador vinculado ao usuário logado
                                        @endphp

                                        @if ($loggedCoordinator && $loggedCoordinator->id === $event->coordinator_id)
                                            {{-- Garante que o coordenador logado é o responsável pelo evento --}}

                                            {{-- Botão para editar o evento --}}
                                            <a href="{{ route('events.edit', $event->id) }}"
                                                class="rounded-md bg-yellow-100 px-3 py-1 text-center text-sm font-medium text-yellow-700 hover:bg-yellow-200">
                                                Editar
                                            </a>

                                            {{-- Botão para ocultar ou exibir o evento --}}
                                            <form action="{{ route('events.updateEvent', $event->id) }}"
                                                method="POST" {{-- Uso do operador ternário ? : do PHP para fazer a troca de mensagem dependendo do estado de visible_event --}}
                                                onsubmit="return confirm('{{ $event->visible_event ? 'Deseja ocultar este evento?' : 'Deseja mostrar este evento?' }}')"
                                                class="inline">
                                                @csrf {{-- Proteção contra CSRF --}}
                                                @method('PATCH') {{-- Requisição do tipo PATCH --}}
                                                @if ($event->visible_event)
                                                    <button type="submit"
                                                        class="w-full rounded-md bg-green-100 px-3 py-1 text-sm font-medium text-green-700 hover:bg-green-200">
                                                        Ocultar
                                                    </button>
                                                @endif
                                                @if (!$event->visible_event)
                                                    <button type="submit"
                                                        class="w-full rounded-md bg-green-100 px-3 py-1 text-sm font-medium text-green-700 hover:bg-green-200">
                                                        Mostrar
                                                    </button>
                                                @endif
                                            </form>

                                            {{-- Botão para excluir o evento --}}
                                            <form action="{{ route('events.destroy', $event->id) }}" method="POST"
                                                onsubmit="return confirm('Tem certeza que deseja excluir este evento?')"
                                                class="inline">
                                                @csrf {{-- Proteção contra CSRF --}}
                                                @method('DELETE') {{-- Requisição do tipo DELETE --}}
                                                <button type="submit"
                                                    class="w-full rounded-md bg-red-100 px-3 py-1 text-sm font-medium text-red-700 hover:bg-red-200">
                                                    Excluir
                                                </button>
                                            </form>
                                        @endif
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                @else
                    {{-- Caso não existam eventos --}}
                    <p class="text-gray-500">Nenhum evento cadastrado até o momento.</p>
            @endif
        </div>
    </div>
    </div>
</x-app-layout>

@vite('resources/js/filter-menu.js')
@vite('resources/js/order-menu.js')
