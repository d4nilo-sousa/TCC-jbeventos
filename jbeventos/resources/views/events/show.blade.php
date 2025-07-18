<x-app-layout>
    {{-- Slot do cabeçalho da página --}}
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ $event->event_name }}
        </h2>
    </x-slot>

    {{-- Corpo principal da página --}}
    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="overflow-hidden rounded-lg bg-white shadow">

                {{-- Exibe a imagem do evento, se houver --}}
                @if($event->event_image)
                    <img src="{{ asset('storage/' . $event->event_image) }}" alt="Imagem do Evento" class="w-full object-cover max-h-96">
                @endif

                {{-- Informações detalhadas do evento --}}
                <div class="p-6">
                    {{-- Descrição do evento --}}
                    <p class="mb-4 text-gray-700">{{ $event->event_description }}</p>

                    {{-- Local e data/hora do evento --}}
                    <div class="grid grid-cols-1 gap-4 md:grid-cols-2 mb-4 text-gray-600">
                        <div>
                            <strong>📍 Local:</strong> {{ $event->event_location }}
                        </div>
                        <div>
                            <strong>📅 Irá ocorrer em:</strong> {{ \Carbon\Carbon::parse($event->event_scheduled_at)->format('d/m/Y H:i') }}
                            {{-- Mostra a data de término, se estiver definida (Apenas para o coordenador que criou) --}}
                            @if(auth()->check() && auth()->user()->user_type === 'coordinator')
                                @php
                                    $loggedCoordinator = auth()->user()->coordinator;
                                @endphp

                                @if($loggedCoordinator && $loggedCoordinator->id === $event->coordinator_id)
                                    <br><strong>⏱ Exclusão Automática:</strong> {{ \Carbon\Carbon::parse($event->event_expired_at)->format('d/m/Y H:i') }}
                                @endif
                            @endif
                        </div>
                    </div>

                    {{-- Coordenador responsável, tipo e curso relacionado --}}
                    <div class="mb-4 text-gray-600">
                        <strong>👤 Coordenador:</strong> {{ $event->eventCoordinator?->userAccount?->name ?? 'Nenhum coordenador definido' }}<br>
                        @if ($event->eventCoordinator?->coordinator_type === 'general')
                            <strong>📌 Tipo do evento:</strong> Evento Geral
                        @elseif ($event->eventCoordinator?->coordinator_type === 'course')
                            <strong>📌 Tipo do evento:</strong> Evento de Curso<br>
                            <strong>🎓 Curso:</strong> {{ $event->eventCoordinator?->coordinatedCourse?->course_name ?? 'Sem Curso' }}
                        @endif
                    </div>

                    {{-- Categorias associadas ao evento --}}
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

                    {{-- Reações ao evento --}}
                    <div class="mb-6">
                        <strong>💬 Reações:</strong>
                        <div id="reactions" class="mt-2 space-x-2 flex flex-wrap">
                            @foreach (['like' => '👍 Curtir', 'dislike' => '👎 Não Gostei', 'save' => '💾 Salvar', 'notify' => '🔔 Notificar'] as $type => $label)
                                @php
                                    $isActive = in_array($type, $userReactions);  // Verifica se o usuário já reagiu com esse tipo
                                    $baseClasses = 'reaction-btn px-3 py-1 rounded border border-blue-500';  // Classes base para os botões
                                    $activeClasses = 'bg-blue-600 text-white'; // Classes para botão ativo
                                    $inactiveClasses = 'bg-white text-blue-600 hover:bg-blue-100'; // Classes para botão inativo
                                @endphp
                                <form action="{{ route('events.react', $event->id) }}" method="POST" class="inline-block reaction-form">
                                    @csrf
                                    <input type="hidden" name="reaction_type" value="{{ $type }}">
                                    <button type="submit" data-type="{{ $type }}" class="{{ $baseClasses }} {{ $isActive ? $activeClasses : $inactiveClasses }}">
                                        {{ $label }}
                                    </button>
                                </form>
                            @endforeach
                        </div>
                    </div>

                    {{-- Botões de navegação e ações do coordenador --}}
                    <div class="flex justify-between">
                        {{-- Botão para voltar à lista de eventos --}}
                        <a href="{{ route('events.index') }}" class="inline-flex items-center rounded-md bg-gray-200 px-4 py-2 text-gray-700 hover:bg-gray-300">
                            ← Voltar
                        </a>

                        {{-- Botões de editar e excluir, visíveis apenas para o coordenador responsável --}}
                        @if(auth()->user()->id === ($event->eventCoordinator?->userAccount?->id ?? 0))
                            <div class="flex space-x-2">
                                {{-- Editar evento --}}
                                <a href="{{ route('events.edit', $event->id) }}" class="inline-flex items-center rounded-md bg-yellow-300 px-4 py-2 text-yellow-900 hover:bg-yellow-400">
                                    Editar
                                </a>

                                {{-- Formulário para excluir o evento com confirmação --}}
                                <form action="{{ route('events.destroy', $event->id) }}" method="POST" onsubmit="return confirm('Deseja realmente excluir este evento?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="inline-flex items-center rounded-md bg-red-300 px-4 py-2 text-red-900 hover:bg-red-400">
                                        Excluir
                                    </button>
                                </form>
                            </div>
                        @endif
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>

{{-- Importa o script JavaScript responsável pelo controle das reações ao evento usando Vite --}}
@vite('resources/js/event-reactions.js')
