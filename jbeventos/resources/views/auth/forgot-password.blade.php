<x-guest-layout>
    <!-- 🔹 Fundo da página -->
    <div class="absolute inset-0 bg-cover bg-center bg-no-repeat"
        style="background-image: url('{{ asset('imgs/etecPhoto.png') }}'); 
                filter: grayscale(0%) brightness(50%);">
    </div>

    <!-- 🔹 Conteúdo principal responsivo -->
    <div class="relative flex items-center justify-center min-h-screen px-4 sm:px-6 lg:px-8">
        <div
            class="w-full max-w-md sm:max-w-lg md:max-w-xl lg:max-w-md xl:max-w-lg 2xl:max-w-lg py-10 px-8 sm:px-10 md:px-14 bg-white rounded-xl bg-opacity-100 border-2 border-gray-100 shadow-xl text-gray-800">

            <!-- Logo -->
            <div class="mb-7">
                <a href="/">
                    <img src="{{ asset('imgs/logoJb.png') }}" alt="Logo"
                        class="w-1/3 sm:w-1/4 md:w-1/5 lg:w-[35%] h-auto mx-auto">
                </a>
            </div>

            <!-- Título -->
            <div class="text-center mb-5">
                <h1 class="text-2xl sm:text-3xl font-thin text-stone-600 font-ubuntu">Esqueci minha senha</h1>
                <p class="mt-2 text-xs sm:text-sm text-stone-400">
                    Informe seu e-mail e enviaremos<br class="hidden sm:block">
                    um link para redefinição.
                </p>
                <hr class="w-1/5 mx-auto mt-2">
            </div>

            <!-- Mensagem de sucesso -->
            @if (session('status'))
            <div class="p-2 sm:p-3 bg-green-100 rounded-xl mb-4 font-medium text-xs sm:text-sm text-green-600 text-center">
                {{ session('status') }}
            </div>
            @endif

            <!-- Erros de validação -->
            <x-validation-errors class="mb-3 p-3 bg-red-50 rounded-lg shadow-md text-sm" />

            {{-- Formulário de envio --}}
            <form method="POST" action="{{ route('password.email') }}">
                @csrf

                <div class="block">
                    <x-label for="email" value="{{ __('Email') }}" />
                    <x-input id="email"
                        class="block mt-1 w-full placeholder-gray-300"
                        type="email"
                        name="email"
                        :value="old('email')"
                        required
                        autofocus
                        autocomplete="username" />
                </div>

                <div class="flex items-center justify-center mt-7">
                    <x-button class="bg-red-600 hover:bg-red-700 w-full h-10 sm:h-11 md:h-12 text-sm sm:text-base">
                        {{ __('Enviar') }}
                    </x-button>
                </div>
            </form>

            <!-- Link para login -->
            <div class="flex flex-col sm:flex-row items-center justify-center mt-5 gap-1 text-center">
                @if (Route::has('login'))
                <p class="text-xs sm:text-sm text-stone-500">Lembrou sua senha?</p>
                <a class="underline hover:no-underline text-xs sm:text-sm text-gray-500" href="{{ route('login') }}">
                    {{ __('Login') }}
                </a>
                @endif
            </div>
        </div>
    </div>
</x-guest-layout>