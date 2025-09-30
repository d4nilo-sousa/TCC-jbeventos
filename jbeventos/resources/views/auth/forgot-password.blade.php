<x-guest-layout>
    <x-authentication-card>
        <x-slot name="logo">
            <a href="/">
                <img src="{{ asset('imgs/logoJb.png') }}" alt="Logo" class="w-1/6 h-auto mx-auto">
            </a>
        </x-slot>

        <div class="mb-5 text-center">
            <h1 class="text-3xl font-medium text-red-700 font-ubuntu">Esqueceu sua senha?</h1>
        </div>
        <div class="mb-4 text-sm text-center text-gray-600 px-5">
            {{ __('Informe seu e-mail e enviaremos um link para redefinição.') }}
            
        </div>

        @session('status')
            <div class="mb-4 font-medium text-sm text-green-600">
                {{ $value }}
            </div>
        @endsession

        <x-validation-errors class="mb-4" />

        <form method="POST" action="{{ route('password.email') }}">
            @csrf

            <div class="block">
                <x-label for="email" value="{{ __('Email') }}" />
                <x-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />
            </div>

            <div class="flex items-center justify-end mt-4">
                <x-button>
                    {{ __('Redefina sua Senha') }}
                </x-button>
            </div>
        </form>
    </x-authentication-card>
</x-guest-layout>
