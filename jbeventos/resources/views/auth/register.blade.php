<x-guest-layout>
    <x-authentication-card>
        <x-slot name="logo">
            <x-authentication-card-logo />
        </x-slot>

        <x-validation-errors class="mb-4" />

        <form method="POST" action="{{ route('register') }}">
            @csrf

            <div>
                <x-label for="name" value="{{ __('Nome') }}" />
                <x-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" />
            </div>

            <div class="mt-4">
                <x-label for="email" value="{{ __('Email') }}" />
                <x-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autocomplete="username" />
            </div>

            <!-- Campo de Senha com botão olho -->
            <div class="mt-4">
                <x-label for="password" value="{{ __('Senha') }}" />
                <div class="relative">
                    <x-input id="password" class="block mt-1 w-full pr-10" type="password" name="password" required autocomplete="new-password" />
                    <button type="button" class="absolute top-1/2 right-2 transform -translate-y-1/2 text-gray-500 toggle-password" data-target="#password">
                        👁️
                    </button>
                </div>

                <ul id="password-requirements" class="text-sm mt-2 hidden">
                    <li id="req-length" class="text-red-500">Pelo menos 8 caracteres</li>
                    <li id="req-uppercase" class="text-red-500">Uma letra maiúscula</li>
                    <li id="req-number" class="text-red-500">Um número</li>
                    <li id="req-special" class="text-red-500">Um caractere especial (!@#$%&*)</li>
                </ul>
            </div>

            <!-- Campo de Confirmação com botão olho -->
            <div class="mt-4">
                <x-label for="password_confirmation" value="{{ __('Confirmar Senha') }}" />
                <div class="relative">
                    <x-input id="password_confirmation" class="block mt-1 w-full pr-10" type="password" name="password_confirmation" required autocomplete="new-password" />
                    <button type="button" class="absolute top-1/2 right-2 transform -translate-y-1/2 text-gray-500 toggle-password" data-target="#password_confirmation">
                        👁️
                    </button>
                </div>

                <p id="password-mismatch-error" class="text-red-500 text-sm mt-1 hidden">
                    As senhas são diferentes!
                </p>
            </div>

            <!-- Número de Telefone -->
            <div class="mt-4">
                <x-label for="phone_number" value="{{ __('Telefone (Opcional)') }}" />
                <x-input id="phone_number" class="block mt-1 w-full" type="tel" name="phone_number" :value="old('phone_number')" autocomplete="tel" />
            </div>

            @if (Laravel\Jetstream\Jetstream::hasTermsAndPrivacyPolicyFeature())
                <div class="mt-4">
                    <x-label for="terms">
                        <div class="flex items-center">
                            <x-checkbox name="terms" id="terms" required />
                            <div class="ms-2">
                                {!! __('I agree to the :terms_of_service and :privacy_policy', [
                                    'terms_of_service' => '<a target="_blank" href="'.route('terms.show').'" class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">'.__('Terms of Service').'</a>',
                                    'privacy_policy' => '<a target="_blank" href="'.route('policy.show').'" class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">'.__('Privacy Policy').'</a>',
                                ]) !!}
                            </div>
                        </div>
                    </x-label>
                </div>
            @endif

            <div class="flex items-center justify-end mt-4">
                <a class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" href="{{ route('login') }}">
                    {{ __('Já possui conta?') }}
                </a>

                <x-button class="ms-4">
                    {{ __('Criar Conta') }}
                </x-button>
            </div>
        </form>
    </x-authentication-card>
</x-guest-layout>

// Importa o script responsável por validar a senha (ex: requisitos mínimos e etc.)
@vite('resources/js/password-validator.js')

// Importa o script responsável por aplicar a máscara no formato de telefone, exemplo: (19) 99999-9999
@vite('resources/js/phone-mask.js')
