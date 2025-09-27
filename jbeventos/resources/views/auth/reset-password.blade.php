<x-guest-layout>
    <x-authentication-card>
        <x-slot name="logo">
            <x-authentication-card-logo />
        </x-slot>

        <x-validation-errors class="mb-4" />

        @php
            $user = \App\Models\User::where('email', $request->email)->first();
            $userType = $user?->user_type ?? 'user';
        @endphp

        <form method="POST" action="{{ route('password.update') }}">
            @csrf

            <input type="hidden" name="token" value="{{ $request->route('token') }}">
            <input type="hidden" name="userType" value="{{ $userType }}">


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

                <ul id="password-requirements" class="text-sm mt-2 hidden">
                    <li id="req-length" class="text-red-500">Pelo menos 8 caracteres</li>
                    <li id="req-uppercase" class="text-red-500">Uma letra maiúscula</li>
                    <li id="req-number" class="text-red-500">Um número</li>
                    <li id="req-special" class="text-red-500">Um caractere especial (!@#$%&*)</li>
                </ul>
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

                <p id="password-mismatch-error" class="text-red-500 text-sm mt-1 hidden">
                    As senhas são diferentes!
                </p>
            </div>

            <div class="flex items-center justify-end mt-4">
                <x-button>
                    {{ __('Redefinir Senha') }}
                </x-button>
            </div>
        </form>
    </x-authentication-card>
</x-guest-layout>

@vite('resources/js/app.js')
