<x-app-layout>
    {{-- INÍCIO: ESTILOS PERSONALIZADOS PARA O FULLCALENDAR --}}
    <style>
        /* Estilos dos botões do FullCalendar */
        .fc-toolbar-chunk .fc-button {
            background-color: transparent;
            border-color: #e5e7eb; /* Cor da borda suave */
            color: #1f2937; /* Cor do texto/ícone: Preto Suave */
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.05), 0 1px 2px 0 rgba(0, 0, 0, 0.03); /* Sombra suave */
            text-transform: capitalize; /* Deixa o texto normalizado */
            font-weight: 500; /* Medium weight */
            transition: all 0.2s;
        }

        /* Hover e Focus dos botões */
        .fc-toolbar-chunk .fc-button:hover,
        .fc-toolbar-chunk .fc-button:focus {
            background-color: #f3f4f6; /* Fundo leve no hover */
            border-color: #d1d5db;
            outline: none;
            box-shadow: 0 2px 4px 0 rgba(0, 0, 0, 0.07);
        }

        /* Botão Ativo */
        .fc-toolbar-chunk .fc-button-primary:not(:disabled).fc-button-active {
            background-color: #1f2937; /* Fundo Preto */
            color: white; /* Texto Branco */
            border-color: #1f2937;
        }

        /* Titulo do Calendário */
        .fc-toolbar-title {
            font-size: 1.5rem;
            color: #1f2937;
            font-weight: 700;
        }

        /* Cabeçalho da Semana (dom, seg, ter...) */
        .fc-col-header-cell {
            background-color: #f9fafb; /* Fundo cinza bem claro */
            padding: 0.5rem 0;
            border-top: 1px solid #e5e7eb;
            border-bottom: 2px solid #e5e7eb;
            font-weight: 600; /* Semibold */
            color: #4b5563; /* Cor mais escura para o texto */
        }

        /* Células do Corpo do Calendário */
        .fc-daygrid-body,
        .fc-daygrid-day {
            border-color: #f3f4f6; /* Bordas mais suaves */
        }
        
        /* Células da tabela do calendário (para o layout fixo) */
        .fc-scrollgrid-sync-table {
            width: 100% !important; /* Garante que a tabela use 100% do container */
            table-layout: fixed; /* Ajuda a distribuir o espaço uniformemente */
        }

        /* Realçar o dia atual em vermelho suave */
        .fc .fc-daygrid-day.fc-day-today {
            background-color: #fee2e2; /* Red 100 - Vermelho muito suave */
            border-left: 3px solid #dc2626; /* Borda esquerda vermelha (Red 600) para ênfase */
        }

        /* Ocultar dias de outros meses (fallback visual e ajuste fino) */
        .fc-day-other .fc-daygrid-day-number {
            opacity: 0; /* Esconde o número do dia */
            pointer-events: none; /* Impede clique */
        }
        /* Ocultar os eventos dos dias de outros meses */
        .fc-day-other .fc-daygrid-event-harness {
            opacity: 0;
            pointer-events: none;
        }
        
        /* Cor dos números dos dias - Mantém o contraste */
        .fc-daygrid-day-number {
            color: #4b5563; /* Cinza escuro */
        }

        /* Estilo para eventos - Melhorias de legibilidade */
        .fc-event {
            border-radius: 0.25rem; /* Bordas arredondadas */
            padding: 2px 4px;
            font-size: 0.8rem;
            white-space: normal;
        }
        .fc-event-title {
            font-weight: 500;
        }
    </style>
    {{-- FIM: ESTILOS PERSONALIZADOS PARA O FULLCALENDAR --}}


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
                    {{-- Botão de Alternância de Visualização --}}
                    <div class="inline-flex rounded-full shadow-md bg-white border border-gray-200">
                        <button id="view-list-btn" data-view="list"
                            class="px-4 py-2.5 text-sm font-medium rounded-l-full transition-colors">
                            <i class="ph-fill ph-list-bullets text-xl align-middle"></i>
                        </button>
                        <button id="view-calendar-btn" data-view="calendar"
                            class="px-4 py-2.5 text-sm font-medium rounded-r-full transition-colors">
                            <i class="ph-fill ph-calendar text-xl align-middle"></i>
                        </button>
                    </div>

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

            {{-- Container do Calendário (Melhoria: Usando flex para garantir que ele se estique se necessário) --}}
            <div id="calendar-view"
                class="p-4 bg-white rounded-xl shadow-2xl hidden transition-all duration-300 ease-in-out">
                {{-- O FullCalendar adiciona a classe .fc que tem a largura definida --}}
                <div id='full-calendar'></div>
            </div>

            {{-- Lista de Eventos --}}
            <div id="list-view"
                class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-10 justify-items-center">

                @forelse ($events as $event)
                    @include('partials.events.event-card', ['event' => $event])
                @empty
                    {{-- Mensagem inicial quando não houver eventos --}}
                    <div id="no-initial-events-message"
                        class="col-span-full flex flex-col items-center justify-center gap-6 text-center w-full my-4 p-6">
                        <img src="{{ asset('imgs/notFound.png') }}" class="w-auto h-40 object-contain"
                            alt="not-found">
                        <div>
                            <p class="text-2xl font-bold text-stone-800">Ops! Nenhum evento disponível no momento</p>
                            <p class="text-gray-500 mt-2 text-md max-w-lg mx-auto">
                                Fique de olho, novos eventos podem aparecer em breve!
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

{{-- NOVO: Modal de Detalhes do Dia --}}
<div id="dayDetailsModal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog"
    aria-modal="true">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"
            onclick="closeModal('dayDetailsModal')">
        </div>

        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

        <div
            class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
            <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                <div class="sm:flex sm:items-start">
                    <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                        <h3 class="text-2xl leading-6 font-medium text-gray-900" id="modal-title">
                            Eventos em <span id="modal-date"></span>
                        </h3>
                        <div class="mt-2 space-y-4" id="modal-events-list">
                            {{-- Lista de eventos será injetada aqui --}}
                        </div>
                    </div>
                </div>
            </div>
            <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                <button type="button"
                    class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto sm:text-sm"
                    onclick="closeModal('dayDetailsModal')">
                    Fechar
                </button>
            </div>
        </div>
    </div>
</div>