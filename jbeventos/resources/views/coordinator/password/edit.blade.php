<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Atualizar Senha
        </h2>
    </x-slot>

    <div class="py-10">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            {{-- Mostra mensagem de sucesso se existir na sessão --}}
            @if (session('success'))
                <div class="mb-4 text-green-600 font-semibold">
                    {{ session('success') }}
                </div>
            @endif

            {{-- Formulário para atualização da senha --}}
            <form method="POST" action="{{ route('coordinator.password.update') }}" class="bg-white p-6 rounded shadow-md space-y-6">
                @csrf
                @method('PUT') {{-- Usa método PUT para a rota --}}

                <div>
                    {{-- Label e campo para a nova senha --}}
                    <x-label for="password" value="Nova Senha" />
                    <x-input id="password" name="password" type="password" class="mt-1 block w-full" required autocomplete="new-password" />
                    {{-- Exibe erro de validação para senha --}}
                    @error('password')
                        <span class="text-sm text-red-600">{{ $message }}</span>
                    @enderror
                </div>

                <div>
                    {{-- Label e campo para confirmar a nova senha --}}
                    <x-label for="password_confirmation" value="Confirme a Nova Senha" />
                    <x-input id="password_confirmation" name="password_confirmation" type="password" class="mt-1 block w-full" required autocomplete="new-password" />
                </div>

                <div class="flex items-center justify-end">
                    {{-- Botão para enviar o formulário --}}
                    <x-button class="ml-4">
                        Atualizar Senha
                    </x-button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
