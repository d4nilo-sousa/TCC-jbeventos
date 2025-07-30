<x-guest-layout>
    <x-authentication-card>
        <x-slot name="logo">
            <a href="/">
                <img src="{{ asset('imgs/logoJb.png') }}" alt="Logo" class="w-60 h-auto mx-auto">
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

            <div>
                <x-label for="email" value="{{ __('Email') }}" />
                <x-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autofocus autocomplete="off" />
            </div>

            <div class="mt-4">
                <x-label for="password" value="{{ __('Senha') }}" />
                <x-input id="password" class="block mt-1 w-full" type="password" name="password" required autocomplete="current-password" />
            </div>

            <div class="block mt-4">
                <label for="remember_me" class="flex items-center">
                    <x-checkbox id="remember_me" name="remember" />
                    <span class="ms-2 text-sm text-gray-600">{{ __('Lembre-me') }}</span>
                </label>
            </div>

            <div class="flex items-center justify-center mt-4 space-x-4">
                @if (Route::has('password.request'))
                <a class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" href="{{ route('password.request') }}">
                    {{ __('Esqueceu sua senha?') }}
                </a>
                @endif

            </div>

            <div class="flex items-center justify-center mt-3">
                <x-button class="bg-red-600 hover:bg-red-700 w-60 h-10">
                    {{ __('Login') }}
                </x-button>
            </div>
        </form>

        {{-- Link para registrar, separado e centralizado --}}
        <div class="mt-6 text-center">
            <p class="text-sm text-gray-600">
                Não tem uma conta?
                <a href="{{ route('register') }}" class="underline text-sm text-gray-600 hover:text-gray-900">
                    Crie uma agora
                </a>
            </p>
        </div>
    </x-authentication-card>
</x-guest-layout>