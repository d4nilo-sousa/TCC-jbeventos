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

            <x-validation-errors class="mb-4" />
            {{-- Formulário para atualização da senha --}}
            <form method="POST" action="{{ route('coordinator.password.update') }}" class="bg-white p-6 rounded shadow-md space-y-6">
                @csrf
                @method('PUT')

                <div>
                    <x-label for="password" value="Nova Senha" />
                    <div class="relative">
                        <x-input id="password" name="password" type="password" class="mt-1 block w-full pr-10" required autocomplete="new-password" />
                        <button type="button" 
                                class="absolute top-1/2 right-2 transform -translate-y-1/2 text-gray-500 toggle-password" 
                                data-target="#password" 
                                aria-label="Mostrar/ocultar senha">
                            👁️
                        </button>
                    </div>

                    <ul id="password-requirements" class="text-sm mt-2">
                        <li id="req-length" class="text-red-500">Pelo menos 8 caracteres</li>
                        <li id="req-uppercase" class="text-red-500">Uma letra maiúscula</li>
                        <li id="req-number" class="text-red-500">Um número</li>
                        <li id="req-special" class="text-red-500">Um caractere especial (!@#$%&*)</li>
                    </ul>
                </div>

                <div>
                    <x-label for="password_confirmation" value="Confirme a Nova Senha" />
                    <div class="relative">
                        <x-input id="password_confirmation" name="password_confirmation" type="password" class="mt-1 block w-full pr-10" required autocomplete="new-password" />
                        <button type="button" 
                                class="absolute top-1/2 right-2 transform -translate-y-1/2 text-gray-500 toggle-password" 
                                data-target="#password_confirmation" 
                                aria-label="Mostrar/ocultar senha">
                            👁️
                        </button>
                    </div>
                    @error('password_confirmation')
                        <span class="text-sm text-red-600">{{ $message }}</span>
                    @enderror

                    <p id="password-mismatch-error" class="text-red-500 text-sm mt-1 hidden">
                        As senhas são diferentes!
                    </p>
                </div>

                <div class="flex items-center justify-end">
                    <x-button class="ml-4">
                        Atualizar Senha
                    </x-button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const passwordInput = document.getElementById('password'); // Campo de senha
        const requirements = document.getElementById('password-requirements'); // Elemento com os requisitos da senha

        // Garante que a lista de requisitos comece oculta
        if (requirements) {
            requirements.classList.add('hidden');
        }

        if (passwordInput) {
            passwordInput.addEventListener('input', function () {
                // Exibe os requisitos se houver texto digitado
                if (passwordInput.value.length > 0) {
                    equirements.classList.remove('hidden');
                } else {
                    // Oculta novamente se o campo estiver vazio
                    requirements.classList.add('hidden');
                }
            });
        }
    });
</script>

@vite('resources/js/app.js')