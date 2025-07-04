<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Meus Eventos') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            {{-- Mensagens de sucesso --}}
            @if (session('success'))
                <div class="mb-4 rounded-lg bg-green-100 px-6 py-4 text-green-800">
                    {{ session('success') }}
                </div>
            @endif

            {{-- Botão para novo evento --}}
            <div class="mb-4 text-right">
                <a href="{{ route('events.create') }}" class="inline-flex items-center rounded-md bg-blue-600 px-4 py-2 text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                    + Novo Evento
                </a>
            </div>

            {{-- Lista de eventos --}}
            @if($events->count() > 0)
                <div class="grid grid-cols-1 gap-6 md:grid-cols-3">
                    @foreach($events as $event)
                        <div class="overflow-hidden rounded-lg border border-gray-200 shadow-sm flex flex-col"> {{-- Adicionado flex flex-col aqui --}}
                            @if($event->event_image)
                                <img src="{{ asset('storage/' . $event->event_image) }}" alt="Imagem do Evento" class="h-48 w-full object-cover">
                            @else
                                {{-- Adiciona um placeholder para manter a altura consistente se não houver imagem --}}
                                <div class="h-48 w-full bg-gray-200 flex items-center justify-center text-gray-500">
                                    Sem imagem
                                </div>
                            @endif
                            <div class="p-4 flex flex-col flex-grow"> {{-- Alterado h-full para flex-grow --}}
                                <h3 class="mb-2 text-lg font-semibold text-gray-900">{{ $event->event_name }}</h3>
                                <p class="mb-2 text-gray-700 text-sm overflow-hidden text-ellipsis line-clamp-3">{{ Str::limit($event->event_description, 100) }}</p> {{-- Adicionado text-sm e line-clamp --}}
                                <div class="mt-auto"> {{-- Garante que o bloco de localização/data e coordenador fique no final --}}
                                    <p class="mb-1 text-sm text-gray-500">
                                        📍 {{ $event->event_location }}<br>
                                        📅 {{ \Carbon\Carbon::parse($event->event_scheduled_at)->format('d/m/Y H:i') }}
                                    </p>
                                    <p class="mb-4 text-xs text-gray-400">
                                        Coordenador: {{ $event->eventCoordinator?->userAccount?->name ?? 'Não informado' }}<br>
                                        Curso: {{ $event->eventCoordinator?->coordinatedCourse?->course_name ?? 'Evento Geral' }}
                                    </p>
                                </div>
                                <div class="mt-auto flex flex-col space-y-2"> {{-- Mantido mt-auto para os botões --}}
                                    <a href="{{ route('events.show', $event->id) }}" class="rounded-md bg-blue-100 px-3 py-1 text-center text-sm font-medium text-blue-700 hover:bg-blue-200">Ver</a>
                                    <a href="{{ route('events.edit', $event->id) }}" class="rounded-md bg-yellow-100 px-3 py-1 text-center text-sm font-medium text-yellow-700 hover:bg-yellow-200">Editar</a>
                                    <form action="{{ route('events.destroy', $event->id) }}" method="POST" onsubmit="return confirm('Tem certeza que deseja excluir este evento?')" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="w-full rounded-md bg-red-100 px-3 py-1 text-sm font-medium text-red-700 hover:bg-red-200">
                                            Excluir
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-gray-500">Nenhum evento cadastrado até o momento.</p>
            @endif
        </div>
    </div>
</x-app-layout>