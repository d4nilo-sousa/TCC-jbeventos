<x-guest-layout>
    <x-authentication-card>
        <x-slot name="logo">
            <a href="/">
                <img src="{{ asset('imgs/logoJb.png') }}" alt="Logo" class="w-1/6 h-auto mx-auto">
            </a>
        </x-slot>

        <!-- Título -->
        <div class="text-center mb-5">
            <h1 class="text-3xl font-thin text-stone-500 font-poppins">Esqueci minha senha</h1>
            <p class="mt-2 text-sm text-stone-400">Informe seu e-mail e enviaremos<br> um link para redefinição.</p>
            <hr class="w-1/4 mx-auto">
        </div>

        @if (session('status'))
            <div class="p-5 bg-green-100 rounded-xl mb-4 font-medium text-sm text-green-600">
                {{ session('status') }}
            </div>
        @endif

        <x-validation-errors class="mb-3 p-3 bg-red-50 rounded-lg shadow-md" />

        {{-- Formulário de envio --}}
        <form method="POST" action="{{ route('password.email') }}">
            @csrf

            <div class="block">
                <x-label for="email" value="{{ __('Email') }}" />
                <x-input id="email" class="block mt-1 w-full placeholder-gray-300" 
                         type="email"
                         name="email" 
                         :value="old('email')" 
                         required 
                         autofocus 
                         autocomplete="username"
                          />
            </div>

            <div class="flex items-center justify-center mt-7">
                <x-button class="bg-red-600 hover:bg-red-700 w-full h-10">
                    {{ __('Redefinir senha') }}
                </x-button>
            </div>
        </form>

        <div class="flex items-center justify-center mt-5 gap-1">
            @if (Route::has('login'))
                <p class="ml-2 text-sm text-stone-500">Lembrou sua senha? </p>
                <a class="underline hover:no-underline text-sm text-gray-500" href="{{ route('login') }}">
                    {{ __('Login') }}
                </a>
            @endif
        </div>

    </x-authentication-card>
</x-guest-layout>
