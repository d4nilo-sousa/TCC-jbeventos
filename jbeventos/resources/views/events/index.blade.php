
<x-app-layout>
    <div class="py-12">
        <div class="w-full max-w-7xl mx-auto sm:px-6 lg:px-8 flex justify-center">
            <div class="w-full bg-white shadow-md rounded-2xl p-4 sm:p-6 lg:p-9 mx-auto min-h-[70vh]">

                <!-- Título -->
                <div class="w-full grid place-items-center mb-5 text-center">
                    <p class="text-2xl sm:text-3xl text-stone-900 font-semibold">Eventos</p>
                </div>

                <!-- Mensagem de sucesso -->
                @if (session('success'))
                    <div class="mb-4 rounded-lg bg-green-100 px-4 sm:px-6 py-3 text-green-800 text-sm sm:text-base">
                        {{ session('success') }}
                    </div>
                @endif

                <!-- Botão Novo Evento -->
                @if (auth()->check() && auth()->user()->user_type === 'coordinator')
                    <div class="mb-4 text-right">
                        <a href="{{ route('events.create') }}"
                            class="inline-flex items-center rounded-md bg-blue-600 px-4 py-2 text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 text-sm sm:text-base">
                            + Novo Evento
                        </a>
                    </div>
                @endif

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
                                    <div class="h-48 w-full bg-gray-200 flex items-center justify-center text-gray-500 text-sm sm:text-base">
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
                                        📅 {{ \Carbon\Carbon::parse($event->event_scheduled_at)->format('d/m/Y H:i') }}
                                    </div>

                                    <!-- Ações -->
                                    <div class="mt-4 flex flex-col sm:flex-row sm:space-x-2 space-y-2 sm:space-y-0">
                                        {{-- Botão para visualizar o evento --}}
                                        <a href="{{ route('events.show', $event->id) }}"
                                            class="flex-1 rounded-md bg-blue-100 px-3 py-1 text-center text-sm font-medium text-blue-700 hover:bg-blue-200">
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
                                                <form action="{{ route('events.updateEvent', $event->id) }}" method="POST"
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
