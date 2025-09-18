<x-guest-layout>
    <x-authentication-card>
        <x-slot name="logo">
            <a href="/">
                <img src="{{ asset('imgs/logoJb.png') }}" alt="Logo" class="w-1/6 h-auto mx-auto">
            </a>
        </x-slot>

        <x-validation-errors class="mb-4" />

        <form method="POST" action="{{ route('register') }}">
            @csrf

            <!-- Título -->
            <div class="text-center mb-5">
                <h1 class="text-3xl font-bold text-stone-500 font-poppins">Olá, seja bem-vindo!</h1>
                <p class="mt-2 text-sm text-stone-400">Cadastre-se para continuar</p>
                <hr class="mx-auto w-1/4">
            </div>

            <div>
                <x-label for="name" value="{{ __('Nome') }}" />
                <x-input id="name" class="block mt-1 w-full placeholder-gray-300 placeholder" type="text" name="name" :value="old('name')"
                    required autofocus autocomplete="name" placeholder="Digite seu nome"/>
            </div>

            <div class="mt-4">
                <x-label for="email" value="{{ __('Email') }}" />
                <x-input id="email" class="block mt-1 w-full placeholder-gray-300" type="email" name="email" :value="old('email')"
                    required autocomplete="username" placeholder="exemplo@gmail.com" />
            </div>

            <!-- Campo de Senha  -->
            <div class="flex mt-4 gap-3">
                <div class="flex-1">
                    <x-label for="password" value="{{ __('Senha') }}" />
                    <div class="relative">
                        <x-input id="password" class="block mt-1 w-full pr-10" type="password" name="password" required
                            autocomplete="new-password" />
                    </div>
                </div>

                <!-- Campo de Confirmação  -->
                <div class="flex-1">
                    <x-label for="password_confirmation" value="{{ __('Confirmar Senha') }}" />
                    <div class="relative">
                        <x-input id="password_confirmation" class="block mt-1 w-full pr-10" type="password"
                            name="password_confirmation" required autocomplete="new-password" />
                    </div>
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

            <div class="flex items-center justify-center mt-6">
                <x-button class="bg-red-600 hover:bg-red-700 w-full h-11 text-white">
                    {{ __('Login') }}
                </x-button>
            </div>
        </form>

    </x-authentication-card>
</x-guest-layout>

@vite('resources/js/app.js')
