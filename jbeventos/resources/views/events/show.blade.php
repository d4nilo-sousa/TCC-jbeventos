<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ $event->event_name }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="overflow-hidden rounded-lg bg-white shadow">

                @if($event->event_image)
                    <img src="{{ asset('storage/' . $event->event_image) }}" alt="Imagem do Evento" class="w-full object-cover max-h-96">
                @endif

                <div class="p-6">
                    <p class="mb-4 text-gray-700">{{ $event->event_description }}</p>

                    <div class="grid grid-cols-1 gap-4 md:grid-cols-2 mb-4 text-gray-600">
                        <div>
                            <strong>📍 Local:</strong> {{ $event->event_location }}
                        </div>
                        <div>
                            <strong>📅 Início:</strong> {{ \Carbon\Carbon::parse($event->event_scheduled_at)->format('d/m/Y H:i') }}
                            @if($event->event_expired_at)
                                <br><strong>⏱ Fim:</strong> {{ \Carbon\Carbon::parse($event->event_expired_at)->format('d/m/Y H:i') }}
                            @endif
                        </div>
                    </div>

                    <div class="mb-4 text-gray-600">
                        <strong>👤 Coordenador:</strong> {{ $event->eventCoordinator?->userAccount?->name ?? 'Nenhum coordenador definido' }}<br>
                        <strong>🎓 Curso:</strong> {{ $event->eventCoordinator?->coordinatedCourse?->course_name ?? 'Evento Geral' }}
                    </div>

                    <div class="mb-4">
                        <strong>🏷 Categorias:</strong>
                        @forelse($event->eventCategories as $category)
                            <span class="inline-block rounded bg-blue-100 px-3 py-1 text-sm font-semibold text-blue-800 mr-2 mb-2">
                                {{ $category->category_name }}
                            </span>
                        @empty
                            <span class="text-gray-400">Nenhuma categoria atribuída.</span>
                        @endforelse
                    </div>

                    <div class="flex justify-between">
                        <a href="{{ route('events.index') }}" class="inline-flex items-center rounded-md bg-gray-200 px-4 py-2 text-gray-700 hover:bg-gray-300">
                            ← Voltar
                        </a>

                        <div class="flex space-x-2">
                            <a href="{{ route('events.edit', $event->id) }}" class="inline-flex items-center rounded-md bg-yellow-300 px-4 py-2 text-yellow-900 hover:bg-yellow-400">
                                Editar
                            </a>

                            <form action="{{ route('events.destroy', $event->id) }}" method="POST" onsubmit="return confirm('Deseja realmente excluir este evento?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="inline-flex items-center rounded-md bg-red-300 px-4 py-2 text-red-900 hover:bg-red-400">
                                    Excluir
                                </button>
                            </form>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>