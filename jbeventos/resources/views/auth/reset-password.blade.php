<x-guest-layout>
    <x-validation-errors class="mb-4" />

    @php
        $user = \App\Models\User::where('email', $request->email)->first();
        $userType = $user?->user_type ?? 'user';
    @endphp

    <!-- 🔹 Fundo da página -->
    <div class="absolute inset-0 bg-cover bg-center bg-no-repeat"
        style="background-image: url('{{ asset('imgs/etecPhoto.png') }}'); 
                filter: grayscale(0%) brightness(90%);">
    </div>

    <!-- 🔹 Conteúdo principal -->
    <div class="relative flex items-center justify-center h-screen">
        <div
            class="w-1/4 py-10 text-gray-800 shadow-xl rounded-xl border-2 border-gray-100 p-14 bg-white bg-opacity-100">
            <form method="POST" action="{{ route('password.update') }}">
                @csrf
                <input type="hidden" name="token" value="{{ $request->route('token') }}">
                <input type="hidden" name="userType" value="{{ $userType }}">

                <div class="mb-7">
                    <a href="/">
                        <img src="{{ asset('imgs/logoJb.png') }}" alt="Logo" class="w-[35%] h-auto mx-auto">
                    </a>
                </div>

                <div class="text-center mb-5">
                    <h1 class="text-3xl font-thin text-stone-600 font-ubuntu">Redefinir minha senha</h1>
                    <p class="mt-2 text-sm text-stone-400">Insira sua nova senha para continuar</p>
                    <hr class="w-1/5 mx-auto">
                </div>

                <!-- Inputs -->
                <div class="block">
                    <x-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email', $request->email)"
                        required autofocus autocomplete="username" style="display: none;" />
                </div>

                <div class="mt-4">
                    <x-label for="password" value="{{ __('Senha') }}" />
                    <div class="relative">
                        <x-input id="password" name="password" type="password" class="block mt-1 w-full pr-10" required
                            autocomplete="new-password" />
                        <button type="button"
                            class="absolute inset-y-0 right-3 flex items-center text-gray-500 toggle-password"
                            data-target="#password">
                            <img src="{{ asset('imgs/blind.png') }}" alt="Mostrar senha"
                                class="w-5 h-5 opacity-75 hover:opacity-100 transition" />
                        </button>
                    </div>
                </div>

                <div class="mt-4">
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
                </div>

                <div class="flex items-center justify-center mt-6">
                    <x-button class="bg-red-600 hover:bg-red-700 w-full h-10">
                        {{ __('Redefinir') }}
                    </x-button>
                </div>

                <div class="flex items-center justify-center mt-5 gap-1">
                    <p class="text-sm text-stone-500">Voltar a tela anterior</p>
                    <a href="{{ route('password.request') }}"
                        class="underline hover:no-underline text-sm text-gray-500"> {{ __('Voltar') }}</a>
                </div>
            </form>
        </div>
    </div>
</x-guest-layout>

@vite('resources/js/app.js')
