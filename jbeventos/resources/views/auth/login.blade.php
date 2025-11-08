<x-guest-layout>
    <x-authentication-card>
        <x-slot name="logo">
            <a href="/">
                <img src="{{ asset('imgs/logoJb.png') }}" alt="Logo" class="w-[15%] h-auto mx-auto">
            </a>
        </x-slot>

        @session('status')
            <div class="mb-4 font-medium text-sm text-green-600">
                {{ $value }}
            </div>
        @endsession

        <form method="POST" action="{{ route('login') }}">
            @csrf

            <!-- Título -->
            <div class="text-center mb-5">
                <h1 class="text-3xl font-thin text-stone-600 font-ubuntu">Bem-vindo de volta!</h1>
                <p class="mt-2 text-sm text-stone-400">Faça login para continuar</p>
                <hr class="w-1/5 mx-auto">
            </div>

            <x-validation-errors class="mb-3 p-3 bg-red-50 rounded-lg shadow-md" />

            <!-- Email -->
            <div>
                <x-label for="email" value="{{ __('Email') }}" />
                <x-input id="email" class="block mt-1 w-full placeholder-gray-300 placeholder:text-base text-base"
                    type="email" name="email" :value="old('email')" required autofocus autocomplete="off" />
            </div>

            <!-- Senha -->
            <div class="mt-4">
                <x-label for="password" value="{{ __('Senha') }}" />
                <div class="relative">
                    <x-input id="password"
                        class="block mt-1 w-full pr-10 placeholder-gray-300 placeholder:text-base text-base"
                        type="password" name="password" required autocomplete="current-password" />

                    <button type="button"
                        class="absolute top-1/2 right-3 transform -translate-y-1/2 text-gray-500 toggle-password"
                        data-target="#password">
                        <img src="{{ asset('imgs/blind.png') }}" alt="Mostrar senha"
                            class="w-5 h-5 opacity-75 hover:opacity-100 transition">
                    </button>
                </div>
            </div>

            <!-- Campos de lembre-me e esqueceu senha -->
            <div class="flex justify-between items-center mb-6 mt-4 px-1">
                <div class="block">
                    <label for="remember_me" class="flex items-center w-fit cursor-pointer">
                        <x-checkbox id="remember_me" name="remember" class="transition" />
                        <span class="ml-2 text-sm text-gray-700">{{ __('Lembre-me') }}</span>
                    </label>
                </div>

                <div class="flex items-center justify-center">
                    @if (Route::has('password.request'))
                        <a class="underline hover:no-underline text-sm text-gray-500"
                            href="{{ route('password.request') }}">
                            {{ __('Esqueci minha senha') }}
                        </a>
                    @endif
                </div>
            </div>

            <!-- Botão Entrar -->
            <div class="flex items-center justify-center mt-3">
                <x-button class="bg-red-600 hover:bg-red-700 w-full h-10">
                    {{ __('Entrar') }}
                </x-button>
            </div>
        </form>

    </x-authentication-card>
</x-guest-layout>

@vite('resources/js/app.js')
