<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Atualizar Telefone
        </h2>
    </x-slot>

    <div class="py-10">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            {{-- Mensagem de sucesso --}}
            @if (session('success'))
                <div class="mb-4 text-green-600 font-semibold">
                    {{ session('success') }}
                </div>
            @endif

            {{-- Formulário para atualizar telefone --}}
            <form method="POST" action="{{ route('user.phone.update') }}" class="bg-white p-6 rounded shadow-md space-y-6">
                @csrf
                @method('PUT')

                <div>
                    {{-- Label para o campo telefone --}}
                    <x-label for="phone_number" value="Telefone" />
                    {{-- Input para telefone, com valor antigo ou atual --}}
                    <x-input id="phone_number" name="phone_number" type="tel" class="mt-1 block w-full" value="{{ old('phone_number', $user->phone_number ?? '') }}" required autocomplete="tel" />
                    {{-- Exibe erro de validação para telefone --}}
                    @error('phone_number')
                        <span class="text-sm text-red-600">{{ $message }}</span>
                    @enderror
                </div>

                <div class="flex items-center justify-end">
                    {{-- Botão para enviar o formulário --}}
                    <x-button class="ml-4">
                        Atualizar Telefone
                    </x-button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>

// Importa o script responsável por aplicar a máscara no formato de telefone, exemplo: (19) 99999-9999
@vite('resources/js/phone-mask.js')
