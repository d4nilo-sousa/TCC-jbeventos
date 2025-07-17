<x-app-layout>
    {{-- Cabeçalho --}}
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ $event->event_name }}
        </h2>
    </x-slot>

    {{-- Conteúdo principal --}}
    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">

            <div class="overflow-hidden rounded-lg bg-white shadow">

                {{-- Imagem do evento --}}
                @if($event->event_image)
                    <img src="{{ asset('storage/' . $event->event_image) }}" alt="Imagem do Evento" 
                         class="w-full object-cover max-h-96">
                @endif

                {{-- Info do evento --}}
                <div class="p-6 space-y-6">

                    {{-- Descrição --}}
                    <p class="text-gray-700">{{ $event->event_description }}</p>

                    {{-- Local e Data/Hora --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-gray-600 font-medium">
                        <div>📍 <span class="font-semibold">Local:</span> {{ $event->event_location }}</div>
                        <div>📅 <span class="font-semibold">Quando:</span> {{ \Carbon\Carbon::parse($event->event_scheduled_at)->format('d/m/Y H:i') }}</div>
                    </div>

                    {{-- Exclusão automática (coordenador) --}}
                    @if(auth()->check() && auth()->user()->user_type === 'coordinator')
                        @php
                            $loggedCoordinator = auth()->user()->coordinator;
                        @endphp
                        @if($loggedCoordinator && $loggedCoordinator->id === $event->coordinator_id)
                            <p class="text-sm text-red-600 font-semibold">
                                ⏱ Exclusão Automática: {{ \Carbon\Carbon::parse($event->event_expired_at)->format('d/m/Y H:i') }}
                            </p>
                        @endif
                    @endif

                    {{-- Coordenador e tipo do evento --}}
                    <div class="text-gray-600">
                        <p>👤 <strong>Coordenador:</strong> {{ $event->eventCoordinator?->userAccount?->name ?? 'Nenhum coordenador definido' }}</p>

                        @if ($event->eventCoordinator?->coordinator_type === 'general')
                            <p>📌 <strong>Tipo:</strong> Evento Geral</p>
                        @elseif ($event->eventCoordinator?->coordinator_type === 'course')
                            <p>📌 <strong>Tipo:</strong> Evento de Curso</p>
                            <p>🎓 <strong>Curso:</strong> {{ $event->eventCoordinator?->coordinatedCourse?->course_name ?? 'Sem Curso' }}</p>
                        @endif
                    </div>

                    {{-- Categorias --}}
                    <div>
                        <strong>🏷 Categorias:</strong>
                        @forelse($event->eventCategories as $category)
                            <span class="inline-block rounded bg-blue-100 px-3 py-1 text-sm font-semibold text-blue-800 mr-2 mb-2">
                                {{ $category->category_name }}
                            </span>
                        @empty
                            <span class="text-gray-400">Nenhuma categoria atribuída.</span>
                        @endforelse
                    </div>

                    {{-- Ações --}}
                    <div class="flex justify-between items-center">
                        <a href="{{ route('events.index') }}"
                           class="inline-flex items-center rounded-md bg-gray-200 px-4 py-2 text-gray-700 hover:bg-gray-300">
                            ← Voltar
                        </a>

                        @if(auth()->user()->id === ($event->eventCoordinator?->userAccount?->id ?? 0))
                            <div class="flex space-x-2">
                                <a href="{{ route('events.edit', $event->id) }}" 
                                   class="inline-flex items-center rounded-md bg-yellow-300 px-4 py-2 text-yellow-900 hover:bg-yellow-400">
                                    Editar
                                </a>

                                <form action="{{ route('events.destroy', $event->id) }}" method="POST" 
                                      onsubmit="return confirm('Deseja realmente excluir este evento?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" 
                                            class="inline-flex items-center rounded-md bg-red-300 px-4 py-2 text-red-900 hover:bg-red-400">
                                        Excluir
                                    </button>
                                </form>
                            </div>
                        @endif
                    </div>

                </div>
            </div>

            {{-- Comentários --}}
            <div class="mt-8">
                <h2 class="text-xl font-semibold mb-4 text-gray-800">Comentários</h2>
                @livewire('event-comments', ['event' => $event])
            </div>

        </div>
    </div>
</x-app-layout>
