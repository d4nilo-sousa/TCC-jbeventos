<x-app-layout backgroundClass="bg-gradient-to-br from-red-400 via-orange-100 to-red-100">
    <div class="py-[5rem] min-h-screen">
        <div class="w-full max-w-[100rem] mx-auto sm:px-6 lg:px-5 flex justify-center">
            <div class="w-full bg-white shadow-md rounded-2xl p-4 sm:p-6 lg:p-9 mx-auto min-h-[70vh]">

                <div
                    class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 px-3 gap-5 w-full flex-wrap">
                    <div class="mt-1">
                        <p
                            class="text-center bg-gradient-to-r from-stone-900 to-stone-400 bg-clip-text text-transparent font-extrabold text-3xl sm:text-5xl tracking-wide drop-shadow-md">
                            Lista de Eventos
                        </p>
                        <div class="w-24 h-1 bg-red-500 mx-auto rounded-full mt-3 shadow-xl"></div>
                    </div>

                    <div
                        class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3 sm:gap-5 w-full sm:w-auto flex-wrap">
                        <!-- Botão Criar Curso -->

                        <!-- Mensagem de sucesso -->
                        @if (session('success'))
                            <div class="mb-4 rounded-lg bg-green-100 px-4 sm:px-6 py-3 text-green-800 text-sm sm:text-base">
                                {{ session('success') }}
                            </div>
                        @endif

                        <!-- Botão Novo Evento ORIGINAL -->
                        @if (auth()->check() && auth()->user()->user_type === 'coordinator')
                            <div
                                class="flex justify-center gap-1 border-2 rounded-full overflow-hidden shadow-md transition-colors duration-200">
                                    <a href="{{ route('events.create') }}"
                                    class="text-red-700 gap-2 px-5 py-2 rounded-lg flex items-center justify-center
                                    hover:bg-[#ff3131] hover:text-white transition-colors duration-200">
                                        <img src="{{ asset('imgs/addred.png') }}" class="w-8">
                                        Criar Evento
                                    </a>
                            </div>
                         @endif

                        

                        <!-- Barra de pesquisa -->
                        <form action="{{ route('events.index') }}" method="GET"
                            class="flex items-center w-full sm:w-auto">
                            <div
                                class="flex items-center bg-white rounded-full overflow-hidden shadow-md border-2 w-full sm:w-auto">
                                <input type="text" name="search" value="{{ request('search') }}"
                                    placeholder="Pesquisar cursos..." autocomplete="off"
                                    class="px-6 py-3 flex-1 min-w-[200px] sm:min-w-[300px] lg:min-w-[400px] text-gray-800 placeholder-gray-500 border-none outline-none focus:ring-0 bg-white">
                                <button type="submit"
                                    class="flex items-center justify-center bg-stone-900 hover:bg-stone-800 transition-colors px-6 py-3">
                                    <img src="{{ asset('imgs/lupaBranca.svg') }}" class="w-7 h-7">
                                </button>
                            </div>
                        </form>

                    </div>
                </div>

                <br><br>

                

                <!-- Lista de eventos -->
                @if ($events->count() > 0)
                    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
                        @foreach ($events as $event)
                            <div class="overflow-hidden rounded-lg border border-gray-200 shadow-sm flex flex-col">
                                <!-- Imagem -->
                                @if ($event->event_image)
                                    <img src="{{ asset('storage/' . $event->event_image) }}" alt="Imagem do Evento"
                                        class="h-48 w-full object-cover">
                                @else
                                    <div
                                        class="h-48 w-full bg-gray-200 flex items-center justify-center text-gray-500 text-sm sm:text-base">
                                        Sem imagem
                                    </div>
                                @endif

                                <!-- Conteúdo -->
                                <div class="p-4 flex flex-col flex-grow">
                                    <h3 class="mb-2 text-lg font-semibold text-gray-900 break-words">
                                        {{ $event->event_name }}
                                    </h3>

                                    <p class="mb-2 text-gray-700 text-sm sm:text-base overflow-hidden line-clamp-3">
                                        {{ Str::limit($event->event_description, 100) }}
                                    </p>

                                    <div class="mt-auto text-sm text-gray-500">
                                        📍 {{ $event->event_location }}<br>
                                        📅
                                        {{ \Carbon\Carbon::parse($event->event_scheduled_at)->format('d/m/Y H:i') }}
                                    </div>

                                    <!-- Ações -->
                                    <div class="mt-4 flex flex-col sm:flex-row sm:space-x-2 space-y-2 sm:space-y-0">
                                        {{-- Botão para visualizar o evento --}}
                                        <a href="{{ route('events.show', $event->id) }}"
                                            class="flex-1 rounded-md bg-red-100 px-3 py-1 text-center text-sm font-medium text-red-700 hover:bg-red-200">
                                            Ver
                                        </a>

                                        @if (auth()->check() && auth()->user()->user_type === 'coordinator')
                                            @php
                                                $loggedCoordinator = auth()->user()->coordinator;
                                            @endphp

                                            @if ($loggedCoordinator && $loggedCoordinator->id === $event->coordinator_id)
                                                {{-- Botão para editar --}}
                                                <a href="{{ route('events.edit', $event->id) }}"
                                                    class="flex-1 rounded-md bg-yellow-100 px-3 py-1 text-center text-sm font-medium text-yellow-700 hover:bg-yellow-200">
                                                    Editar
                                                </a>

                                                {{-- Botão para ocultar/exibir evento --}}
                                                <form action="{{ route('events.updateEvent', $event->id) }}"
                                                    method="POST"
                                                    onsubmit="return confirm('Tem certeza que deseja ocultar este evento?')"
                                                    class="flex-1">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="submit"
                                                        class="w-full rounded-md bg-green-100 px-3 py-1 text-sm font-medium text-green-700 hover:bg-green-200">
                                                        Ocultar
                                                    </button>
                                                </form>

                                                {{-- Botão para excluir --}}
                                                <form action="{{ route('events.destroy', $event->id) }}" method="POST"
                                                    onsubmit="return confirm('Tem certeza que deseja excluir este evento?')"
                                                    class="flex-1">
                                                    @csrf
                                                    @method('DELETE')
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
                    </div>
                @else
                    <div class="w-full flex flex-col items-center mt-16">
                        <p class="text-gray-500 mt-5 text-base sm:text-lg">Nenhum evento cadastrado . . .</p>
                        <img src="{{ asset('imgs/notFound3.png') }}" class="w-2/3 sm:w-1/3 lg:w-1/5 mt-6">
                    </div>
                @endif

            </div>
        </div>
    </div>
</x-app-layout>
