<x-app-layout>
    <div class="py-12">
        <div class="max-w-xl mx-auto sm:px-6 lg:px-8">

            {{-- Mostra mensagem de sucesso se existir na sessão (ESTILO MELHORADO) --}}
            @if (session('success'))
                <div
                    class="mb-6 p-4 bg-green-100 border border-green-300 text-green-700 rounded-lg shadow-sm font-medium flex items-center">
                    <i class="ph-fill ph-check-circle text-xl mr-2"></i>
                    {{ session('success') }}
                </div>
            @endif

            {{-- Container do Formulário (ESTILO MELHORADO) --}}
            <div class="bg-white p-8 rounded-2xl shadow-xl border border-gray-100">

                {{-- NOVO TÍTULO AQUI --}}
                <div class="mb-6 pb-3 border-b border-red-100">
                    <h2 class="text-2xl font-extrabold text-gray-800 flex items-center">
                        <i class="ph-fill ph-lock-key text-2xl mr-3 text-red-500"></i>
                        Atualizar Senha
                    </h2>
                    <p class="text-sm text-gray-500 mt-1">Defina sua nova senha de acesso ao sistema.</p>
                </div>
                {{-- ----------------- --}}

                <x-validation-errors class="mb-6" />

                {{-- Formulário para atualização da senha --}}
                <form method="POST" action="{{ route('coordinator.password.update') }}" class="space-y-6">
                    @csrf
                    @method('PUT')

                    {{-- Nova Senha --}}
                    <div>
                        <x-label for="password" value="{{ __('Senha') }}" />
                        <div class="relative">
                            <x-input id="password" name="password" type="password" class="block mt-1 w-full pr-10"
                                required autocomplete="new-password" />

                            <button type="button"
                                class="absolute inset-y-0 right-3 flex items-center text-gray-500 toggle-password"
                                data-target="#password">
                                <img src="{{ asset('imgs/blind.png') }}" alt="Mostrar senha"
                                    class="w-5 h-5 opacity-75 hover:opacity-100 transition" />
                            </button>
                        </div>

                        <ul id="password-requirements" class="text-sm mt-2 hidden list-disc list-inside">
                            <li id="req-length" class="text-red-500 flex items-center"><i
                                    class="ph ph-x-circle text-base mr-1"></i>Pelo menos 8 caracteres</li>
                            <li id="req-uppercase" class="text-red-500 flex items-center"><i
                                    class="ph ph-x-circle text-base mr-1"></i>Uma letra maiúscula</li>
                            <li id="req-number" class="text-red-500 flex items-center"><i
                                    class="ph ph-x-circle text-base mr-1"></i>Um número</li>
                            <li id="req-special" class="text-red-500 flex items-center"><i
                                    class="ph ph-x-circle text-base mr-1"></i>Um caractere especial (!@#$%&*)</li>
                        </ul>
                    </div>

                    {{-- Confirme a Nova Senha --}}
                    <div>
                        <x-label for="password_confirmation" value="{{ __('Confirmar Senha') }}" />
                        <div class="relative">
                            <x-input id="password_confirmation" name="password_confirmation" type="password"
                                class="block mt-1 w-full pr-10" required autocomplete="new-password" />

                            <button type="button"
                                class="absolute inset-y-0 right-3 flex items-center text-gray-500 toggle-password"
                                data-target="#password_confirmation">
                                <img src="{{ asset('imgs/blind.png') }}" alt="Mostrar senha"
                                    class="w-5 h-5 opacity-75 hover:opacity-100 transition" />
                            </button>
                        </div>

                        <p id="password-mismatch-error" class="text-red-500 text-sm mt-1 hidden">
                            <i class="ph ph-warning text-lg mr-1 translate-y-[2px] inline-block"></i>
                            As senhas são diferentes!
                        </p>
                    </div>

                    {{-- Botão de Envio (ESTILO MELHORADO) --}}
                    <div class="pt-4">
                        <x-button
                            class="w-full justify-center px-4 py-2 bg-red-600 hover:bg-red-700 focus:ring-red-500 active:bg-red-800 font-bold transition duration-150 ease-in-out">
                            <i class="ph-fill ph-lock-key-open text-xl mr-2"></i>
                            Atualizar Senha
                        </x-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>

@vite('resources/js/app.js')
