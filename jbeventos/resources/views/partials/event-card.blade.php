<div id="event-card-{{ $event->id }}" class="overflow-hidden rounded-lg border border-gray-200 shadow-sm flex flex-col">
    {{-- Imagem do evento ou placeholder --}}
    @if ($event->event_image)
        <div class="w-full aspect-[2/1] overflow-hidden">
            <img src="{{ asset('storage/' . $event->event_image) }}" alt="Imagem do Evento"
                 class="w-full h-full object-cover">
        </div>
    @else
        <div class="w-full aspect-[2/1] flex items-center justify-center bg-gray-200 text-gray-500">
            📷 Nenhuma imagem enviada
        </div>
    @endif

    <div class="p-4 flex flex-col flex-grow">
        {{-- Nome do evento --}}
        <h3 class="event-title mb-2 text-lg font-semibold text-gray-900">{{ $event->event_name }}</h3>

            {{-- Descrição limitada a 100 caracteres --}}
            <p class="mb-2 text-gray-700 text-sm overflow-hidden text-ellipsis line-clamp-3">
                {{ Str::limit($event->event_description, 100) }}
            </p>

            <div class="mt-auto">
                {{-- Local e data/hora do evento --}}
                <p class="mb-1 text-sm text-gray-500">
                    📍 {{ $event->event_location }}<br>
                    📅 {{ \Carbon\Carbon::parse($event->event_scheduled_at)->format('d/m/Y H:i') }}
                </p>
            </div>

            {{-- Botões de ação --}}
            <div class="mt-auto flex flex-col space-y-2">

                <a href="{{ route('events.show', $event->id) }}"
                    class="rounded-md bg-blue-100 px-3 py-1 text-center text-sm font-medium text-blue-700 hover:bg-blue-200 transition ease-in-out">
                    Ver
                </a>

                @if (auth()->check() && auth()->user()->user_type === 'coordinator')
                    @php
                        $loggedCoordinator = auth()->user()->coordinator;
                    @endphp
                    @if ($loggedCoordinator && $loggedCoordinator->id === $event->coordinator_id)
                        <a href="{{ route('events.edit', $event->id) }}"
                            class="rounded-md bg-yellow-100 px-3 py-1 text-center text-sm font-medium text-yellow-700 hover:bg-yellow-200">
                            Editar
                        </a>

                        <form action="{{ route('events.updateEvent', $event->id) }}" method="POST"
                            onsubmit="return confirm('{{ $event->visible_event ? 'Deseja ocultar este evento?' : 'Deseja mostrar este evento?' }}')"
                            class="inline">
                            @csrf
                            @method('PATCH')
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

                        <form action="{{ route('events.destroy', $event->id) }}" method="POST"
                            onsubmit="return confirm('Tem certeza que deseja excluir este evento?')" class="inline">
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
    </a>
</div>
