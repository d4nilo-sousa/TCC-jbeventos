<x-app-layout>
    {{-- Cabeçalho --}}
    <x-slot name="header">
        <h2 class="font-semibold text-2xl text-gray-800 leading-tight flex items-center gap-2">
            📢 {{ $event->event_name }}
        </h2>
    </x-slot>

    {{-- Conteúdo principal --}}
    <div class="py-12">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- Card do evento --}}
            <div class="overflow-hidden rounded-lg bg-white shadow-lg border">

                {{-- Imagem do evento --}}
                @if($event->event_image)
                    <img src="{{ asset('storage/' . $event->event_image) }}" alt="Imagem do Evento"
                             class="w-full object-cover max-h-96">
                @endif

                {{-- Informações do evento --}}
                <div class="p-6 space-y-6">

                    {{-- Descrição --}}
                    <p class="text-gray-700 text-lg leading-relaxed">
                        {{ $event->event_description }}
                    </p>

                    {{-- Local e Data/Hora --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-gray-700">
                        <div class="flex items-center gap-2">
                            📍 <span class="font-semibold">Local:</span> {{ $event->event_location }}
                        </div>
                        <div>
                            <strong>📅 Irá ocorrer em:</strong> {{ \Carbon\Carbon::parse($event->event_scheduled_at)->format('d/m/Y H:i') }}
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

                    {{-- Exclusão automática (visível apenas para o coordenador responsável) --}}
                    @if(auth()->check() && auth()->user()->user_type === 'coordinator')
                        @php
                            $loggedCoordinator = auth()->user()->coordinator;
                        @endphp
                        @if($loggedCoordinator && $loggedCoordinator->id === $event->coordinator_id)
                            <p class="text-sm text-red-600 font-semibold">
                                ⏱ Exclusão Automática: 
                                {{ \Carbon\Carbon::parse($event->event_expired_at)->format('d/m/Y H:i') }}
                            </p>
                        @endif
                    @endif

                    {{-- Coordenador e tipo do evento --}}
                    <div class="space-y-1 text-gray-700">
                        <p>
                            👤 <strong>Coordenador:</strong> 
                            {{ $event->eventCoordinator?->userAccount?->name ?? 'Nenhum coordenador definido' }}
                        </p>
                        @if ($event->eventCoordinator?->coordinator_type === 'general')
                            <p>📌 <strong>Tipo:</strong> Evento Geral</p>
                        @elseif ($event->eventCoordinator?->coordinator_type === 'course')
                            <p>📌 <strong>Tipo:</strong> Evento de Curso</p>
                            <p>🎓 <strong>Curso:</strong> 
                                {{ $event->eventCoordinator?->coordinatedCourse?->course_name ?? 'Sem Curso' }}
                            </p>
                        @endif
                    </div>

                    {{-- Categorias --}}
                    <div>
                        <strong>🏷 Categorias:</strong>
                        <div class="mt-2 flex flex-wrap gap-2">
                            @forelse($event->eventCategories as $category)
                                <span class="inline-block rounded-full bg-blue-100 px-3 py-1 text-sm 
                                             font-semibold text-blue-800 shadow-sm">
                                    {{ $category->category_name }}
                                </span>
                            @empty
                                <span class="text-gray-400">Nenhuma categoria atribuída.</span>
                            @endforelse
                        </div>
                    </div>

                    {{-- Reações ao evento --}}
                    <div class="mb-6">
                        <strong>💬 Reações:</strong>
                        <div id="reactions" class="mt-2 space-x-2 flex flex-wrap">
                            @foreach (['like' => '👍 Curtir', 'dislike' => '👎 Não Gostei', 'save' => '💾 Salvar', 'notify' => '🔔 Notificar'] as $type => $label)
                                @php
                                    $isActive = in_array($type, $userReactions);
                                    $baseClasses = 'reaction-btn px-3 py-1 rounded border border-blue-500';
                                    $activeClasses = 'bg-blue-600 text-white';
                                    $inactiveClasses = 'bg-white text-blue-600 hover:bg-blue-100';
                                @endphp
                                <form action="{{ route('events.react', ['event' => $event->id]) }}" method="POST" class="inline-block reaction-form">
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
                        <a href="{{ route('events.index') }}" class="inline-flex items-center rounded-md bg-gray-200 px-4 py-2 text-gray-700 hover:bg-gray-300">
                            ← Voltar
                        </a>

                        @if(auth()->user()->id === ($event->eventCoordinator?->userAccount?->id ?? 0))
                            <div class="flex space-x-2">
                                <a href="{{ route('events.edit', $event->id) }}" class="inline-flex items-center rounded-md bg-yellow-300 px-4 py-2 text-yellow-900 hover:bg-yellow-400">
                                    Editar
                                </a>
                                <form action="{{ route('events.destroy', $event->id) }}" method="POST" onsubmit="return confirm('Deseja realmente excluir este evento?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                                 class="inline-flex items-center rounded-md bg-red-300 px-4 py-2 
                                                        text-red-900 hover:bg-red-400">
                                        🗑 Excluir
                                    </button>
                                </form>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Comentários --}}
            <div class="bg-white shadow rounded-lg p-6">
                <h2 class="text-xl font-bold mb-4 text-gray-800 flex items-center gap-2">
                    💬 Comentários
                </h2>
                @livewire('event-comments', ['event' => $event])
            </div>

        </div>
    </div>
</x-app-layout>

{{-- Modal para cadastrar telefone --}}
<div id="phoneModal" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 hidden z-50">
  <div class="bg-white rounded-lg shadow-lg max-w-md w-full p-6 relative">
    <h3 class="text-xl font-semibold mb-4">Cadastre seu número de celular</h3>
    <form id="phoneForm" method="POST" action="{{ route('user.phone.update') }}" class="space-y-4">
      @csrf
      @method('PUT')
      <input type="text" name="phone_number" id="phone_number" placeholder="(99) 99999-9999" pattern="\([0-9]{2}\) [0-9]{5}-[0-9]{4}" class="w-full border border-gray-300 rounded px-3 py-2" required>
      <div class="flex justify-end space-x-2">
        <button type="button" id="cancelPhoneModal" class="px-4 py-2 bg-gray-300 rounded hover:bg-gray-400">Cancelar</button>
        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">Salvar</button>
      </div>
    </form>
  </div>
</div>

{{-- Toast simples --}}
<div id="toast" class="fixed bottom-5 right-5 bg-blue-600 text-white px-4 py-2 rounded shadow hidden z-50">
  <span id="toast-message"></span>
</div>


{{-- Scripts --}}
@vite('resources/js/event-reactions.js')