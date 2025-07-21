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
