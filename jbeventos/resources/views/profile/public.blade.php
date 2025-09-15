<x-app-layout>
    <div class="relative bg-white shadow-xl rounded-lg overflow-hidden max-w-4xl mx-auto my-8">
        <x-slot name="header">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Perfil de {{ $user->name }}
            </h2>
        </x-slot>

        {{-- Banner --}}
        <div class="relative h-56 bg-gray-200"
            style="{{ preg_match('/^#[a-f0-9]{6}$/i', $user->user_banner_url) ? 'background-color: ' . $user->user_banner_url : '' }}">

            {{-- Se for uma imagem, exibe-a --}}
            @if(!preg_match('/^#[a-f0-9]{6}$/i', $user->user_banner_url))
                <img src="{{ $user->user_banner_url }}" alt="Banner do Usuário" class="object-cover w-full h-full">
            @endif

        </div>

        {{-- Avatar e Nome --}}
        <div class="px-6 -mt-16 flex items-end space-x-6 pb-6 border-b border-gray-200">
            <div class="w-32 h-32 rounded-full border-6 border-white bg-gray-300 shadow-lg">
                <img src="{{ $user->user_icon_url }}" alt="Avatar" class="w-full h-full rounded-full object-cover">
            </div>

            <div class="flex-1 mt-6">
                <h2 class="text-3xl font-bold text-gray-900">{{ $user->name }}</h2>
                <p class="text-sm text-gray-500 mt-1">
                    @php
                        $userTypes = ['coordinator' => 'Coordenador', 'user' => 'Usuário', 'admin' => 'Administrador'];
                    @endphp
                    {{ $userTypes[$user->user_type] ?? ucfirst($user->user_type) }}
                </p>
                {{-- Botão de Conversar (apenas se for outro usuário) --}}
                @if(auth()->check() && auth()->id() !== $user->id)
                    <a href="{{ route('chat.show', ['user' => $user->id]) }}"
                       class="inline-flex items-center mt-3 px-5 py-2 bg-indigo-600 text-white font-semibold rounded-lg shadow-md
                              hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-400 focus:ring-opacity-75 transition">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M8 10h.01M12 10h.01M16 10h.01M21 16v-5a2 2 0 00-2-2H7a2 2 0 00-2 2v5a2 2 0 002 2h12a2 2 0 002-2zM7 16v3a1 1 0 001 1h8a1 1 0 001-1v-3" />
                        </svg>
                        Conversar
                    </a>
                @endif
            </div>
        </div>

        {{-- Biografia (apenas leitura) --}}
        <div class="px-6 py-4">
            <h3 class="text-sm font-semibold mb-2">Biografia</h3>
            <div class="text-sm text-gray-700 min-h-[3rem] whitespace-pre-line bg-gray-50 p-4 rounded-lg">
                {{ $user->bio ?? 'Este usuário ainda não escreveu uma biografia.' }}
            </div>
        </div>

        {{-- Seção de Eventos Criados (apenas se o usuário visualizado for um coordenador) --}}
        @if($user->user_type === 'coordinator' && $eventsCreated->isNotEmpty())
            <div class="px-6 py-4 border-t border-gray-200">
                <h3 class="text-lg font-semibold mb-4">Eventos Criados por {{ $user->name }}</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($eventsCreated as $event)
                        <a href="{{ route('events.show', $event) }}" class="block bg-white rounded-lg shadow hover:shadow-lg transition-shadow duration-200 overflow-hidden">
                            <div class="h-32 bg-gray-200 flex items-center justify-center overflow-hidden">
                                {{-- Exibe a imagem de capa do evento --}}
                                <img src="{{ $event->event_image ? asset('storage/' . $event->event_image) : asset('default-event-image.jpg') }}"
                                     alt="{{ $event->event_name }}"
                                     class="object-cover w-full h-full">
                            </div>
                            <div class="p-4">
                                <p class="font-bold text-gray-800">{{ $event->event_name }}</p>
                                <p class="text-sm text-gray-600 mt-1">{{ $event->event_scheduled_at->format('d/m/Y H:i') }}</p>
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>
        @elseif ($user->user_type === 'coordinator' && $eventsCreated->isEmpty())
            <div class="px-6 py-4 border-t border-gray-200 text-center text-gray-500">
                <p>Este coordenador ainda não criou nenhum evento.</p>
            </div>
        @endif

        {{-- Informações adicionais --}}
        <div class="px-6 py-4 text-sm text-gray-600 border-t border-gray-200">
            <p class="mt-1"><strong>Membro desde:</strong> {{ $user->created_at->format('d/m/Y') }}</p>
        </div>
    </div>
</x-app-layout>