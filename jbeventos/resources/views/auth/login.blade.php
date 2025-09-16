<x-guest-layout>
    <x-authentication-card>
        <x-slot name="logo">
            <a href="/">
                <img src="{{ asset('imgs/logoJb.png') }}" alt="Logo" class="w-1/3 h-auto mx-auto">
            </a>
        </x-slot>

        <x-validation-errors class="mb-4" />

        @session('status')
            <div class="mb-4 font-medium text-sm text-green-600">
                {{ $value }}
            </div>
        @endsession

        <form method="POST" action="{{ route('login') }}">
            @csrf

            <!-- Título -->
            <div class="text-center mb-5">
                <h1 class="text-4xl font-bold text-stone-500 font-poppins">Bem-vindo de volta!</h1>
                <p class="mt-2 text-sm text-stone-400">Faça login para continuar</p>
                <hr class="mx-auto w-1/4">
            </div>

            <div class="mt-7">
                <x-label for="email" value="{{ __('Email') }}" />
                <x-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')"
                    required autofocus autocomplete="off" />
            </div>

            <div class="mt-4">
                <x-label for="password" value="{{ __('Senha') }}" />
                <div class="relative">
                    <x-input id="password" class="block mt-1 w-full pr-10" type="password" name="password" required
                        autocomplete="current-password" />
                    <button type="button"
                        class="absolute top-1/2 right-3 transform -translate-y-1/2 text-gray-500 toggle-password"
                        data-target="#password">
                        <img src="{{ asset('imgs/blind.png') }}" alt="Mostrar senha"
                            class="w-5 h-5 opacity-75 hover:opacity-100 transition" />
                    </button>
                </div>
            </div>

            <div class="block mt-4">
                <label for="remember_me" class="flex items-center">
                    <x-checkbox id="remember_me" name="remember" />
                    <span class="ml-2 text-sm text-gray-700">{{ __('Lembre-me') }}</span>
                </label>
            </div>

            <div class="flex items-center justify-center mt-6">
                @if (Route::has('password.request'))
                    <a class="underline text-sm text-stone-600" href="{{ route('password.request') }}">
                        {{ __('Esqueceu sua senha?') }}
                    </a>
                @endif
            </div>

            <div class="flex items-center justify-center mt-6">
                <x-button class="bg-red-600 hover:bg-red-700 w-full h-11 text-white">
                    {{ __('Login') }}
                </x-button>
            </div>
        </form>

        <div class="mt-8 text-center">
            <p class="text-sm text-gray-700">
                Não tem uma conta?
                <a href="{{ route('register') }}" class="underline text-stone-600 hover:text-stone-600 transition">
                    Crie uma agora
                </a>
            </p>
        </div>
    </x-authentication-card>
</x-guest-layout>

@vite('resources/js/app.js')
