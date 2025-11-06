<x-guest-layout>
    <x-authentication-card>
        <x-slot name="logo">
            <a href="/">
                <img src="{{ asset('imgs/logoJb.png') }}" alt="Logo" class="w-1/6 h-auto mx-auto">
            </a>
        </x-slot>

        <form method="POST" action="{{ route('register') }}">
            @csrf

            <!-- Título -->
            <div class="text-center mb-5">
                <h1 class="text-3xl font-thin text-stone-600 font-ubuntu">Olá, seja bem-vindo!</h1>
                <p class="mt-2 text-sm text-stone-400">Cadastre-se para continuar</p>
                <hr class="w-1/5 mx-auto">
            </div>

            <x-validation-errors class="mb-3 p-3 bg-red-50 rounded-lg shadow-md" />

            <!-- Nome -->
            <div>
                <x-label for="name" value="{{ __('Nome') }}" />
                <x-input id="name" class="block mt-1 w-full placeholder-gray-300 placeholder" type="text"
                    name="name" :value="old('name')" required autofocus autocomplete="name"
                    placeholder="Digite seu nome" />
            </div>

            <!-- Gmail -->
            <div class="mt-4">
                <x-label for="email" value="{{ __('Email') }}" />
                <x-input id="email" class="block mt-1 w-full placeholder-gray-300" type="email" name="email"
                    :value="old('email')" placeholder="exemplo@gmail.com" required autocomplete="username" />
            </div>

            <!-- Campo de senha e confirmar senha -->
            <div class="mt-4 mb-6 space-y-4">
                <!-- Campo de Senha com botão olho -->
                <div class="w-full">
                    <x-label for="password" value="{{ __('Senha') }}" />
                    <div class="relative">
                        <x-input id="password" class="block mt-1 w-full pr-10" type="password" name="password" required
                            autocomplete="new-password" />

                        <button type="button"
                            class="absolute top-1/2 right-3 transform -translate-y-1/2 text-gray-500 toggle-password"
                            data-target="#password">
                            <img src="{{ asset('imgs/blind.png') }}" alt="Mostrar senha"
                                class="w-5 h-5 opacity-75 hover:opacity-100 transition">
                        </button>
                    </div>

                    <ul id="password-requirements" class="text-sm mt-2 hidden list-disc list-inside">
                        <li id="req-length" class="text-red-500 flex items-center"><i
                                class="ph ph-x-circle text-base mr-1"></i>Pelo menos 8 caracteres</li>
                    </ul>
                </div>

                <!-- Campo de Confirmação com botão olho -->
                <div class="w-full">
                    <x-label for="password_confirmation" value="{{ __('Confirmar Senha') }}" />
                    <div class="relative">
                        <x-input id="password_confirmation" class="block mt-1 w-full pr-10" type="password"
                            name="password_confirmation" required autocomplete="new-password" />
                        <button type="button"
                            class="absolute top-1/2 right-2 transform -translate-y-1/2 text-gray-500 toggle-password"
                            data-target="#password_confirmation">
                            <img src="{{ asset('imgs/blind.png') }}" alt="Mostrar senha"
                                class="w-5 h-5 opacity-75 hover:opacity-100 transition">
                        </button>
                    </div>

                    <p id="password-mismatch-error" class="text-red-500 text-sm mt-1 hidden flex items-center">
                        <i class="ph ph-warning text-lg mr-1"></i> As senhas são diferentes!
                    </p>
                </div>
            </div>


            @if (Laravel\Jetstream\Jetstream::hasTermsAndPrivacyPolicyFeature())
                <div class="mt-4">
                    <x-label for="terms">
                        <div class="flex items-center">
                            <x-checkbox name="terms" id="terms" required />
                            <div class="ms-2">
                                {!! __('I agree to the :terms_of_service and :privacy_policy', [
                                    'terms_of_service' =>
                                        '<a target="_blank" href="' .
                                        route('terms.show') .
                                        '" class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">' .
                                        __('Terms of Service') .
                                        '</a>',
                                    'privacy_policy' =>
                                        '<a target="_blank" href="' .
                                        route('policy.show') .
                                        '" class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">' .
                                        __('Privacy Policy') .
                                        '</a>',
                                ]) !!}
                            </div>
                        </div>
                    </x-label>
                </div>
            @endif
            <div class="flex items-center justify-center mt-3">
                <x-button class="bg-red-600 hover:bg-red-700 w-full h-10">
                    {{ __('Criar Conta') }}
                </x-button>
            </div>

        </form>

    </x-authentication-card>
</x-guest-layout>

@vite('resources/js/app.js')

<script src="https://unpkg.com/@phosphor-icons/web"></script>