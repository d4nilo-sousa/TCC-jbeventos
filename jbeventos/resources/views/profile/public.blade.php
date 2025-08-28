<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ $user->name }}
        </h2>
    </x-slot>

    <div class="relative bg-white shadow rounded-lg overflow-hidden">
        {{-- Banner (somente visualização) --}}
        <div class="relative h-48 bg-gray-200">
            <img src="{{ $user->user_banner_url }}" alt="Banner" class="object-cover w-full h-full">
        </div>

        {{-- Avatar e Nome --}}
        <div class="px-6 -mt-12 flex items-end space-x-4">
            <div class="relative">
                <img src="{{ $user->user_icon_url }}" alt="Avatar"
                     class="w-24 h-24 rounded-full border-4 border-white object-cover bg-gray-300">
            </div>

            <div>
                <h2 class="text-xl font-bold">{{ $user->name }}</h2>

                @if(auth()->check() && auth()->id() !== $user->id)
        <a href="{{ route('chat.show', ['user' => $user->id]) }}"
           class="inline-flex items-center mt-3 px-5 py-2 bg-blue-600 text-white font-semibold rounded-lg shadow-md
                  hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-400 focus:ring-opacity-75 transition">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24"
                 stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round"
                      d="M8 10h.01M12 10h.01M16 10h.01M21 16v-5a2 2 0 00-2-2H7a2 2 0 00-2 2v5a2 2 0 002 2h12a2 2 0 002-2zM7 16v3a1 1 0 001 1h8a1 1 0 001-1v-3" />
            </svg>
            Conversar
        </a>
            @endif

                <p class="text-sm text-gray-500">
                    @php
                        $userTypes = [
                            'coordinator' => 'Coordenador',
                            'user' => 'Usuário',
                            'admin' => 'Administrador',
                        ];
                    @endphp
                    {{ $userTypes[$user->user_type] ?? ucfirst($user->user_type) }}
                </p>
            </div>
        </div>

        {{-- Biografia (apenas leitura) --}}
        <div class="px-6 py-4">
            <h3 class="text-sm font-semibold mb-1">Biografia</h3>
            <div class="text-sm text-gray-700 min-h-[3rem] whitespace-pre-line">
                {{ $user->bio ?? 'Este usuário ainda não escreveu uma biografia.' }}
            </div>
        </div>

        {{-- Informações adicionais --}}
        <div class="px-6 py-2 text-sm text-gray-600 border-t">
            <p><strong>Email:</strong> {{ $user->email }}</p>
            <p><strong>Telefone:</strong> {{ $user->phone_number ?? 'Não informado' }}</p>
            <p><strong>Membro desde:</strong> {{ $user->created_at->format('d/m/Y') }}</p>
        </div>
    </div>
</x-app-layout>
