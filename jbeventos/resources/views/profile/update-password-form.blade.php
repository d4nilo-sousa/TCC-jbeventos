<x-form-section submit="updatePassword">
    <x-slot name="title">
        {{ __('Atualizar Senha') }}
    </x-slot>

    <x-slot name="description">
        {{ __('Garanta que sua conta esteja usando uma senha longa e aleatória para se manter segura.') }}
    </x-slot>

    <x-slot name="form">
        {{-- Campo oculto com o tipo do usuário --}}
        <input type="hidden" name="userType" value="{{ auth()->user()->user_type }}">

        {{-- Senha atual --}}
        <div class="col-span-6 sm:col-span-4">
            <x-label for="current_password" value="{{ __('Senha Atual') }}" />
            <div class="relative">
                <x-input id="current_password" type="password" class="mt-1 block w-full pr-10"
                    wire:model="state.current_password" autocomplete="current-password" />
                <button type="button"
                    class="absolute top-1/2 right-3 transform -translate-y-1/2 text-gray-500 toggle-password"
                    data-target="#current_password">
                    <img src="{{ asset('imgs/blind.png') }}" alt="Mostrar senha"
                        class="w-5 h-5 opacity-75 hover:opacity-100 transition">
                </button>
            </div>
            <x-input-error for="current_password" class="mt-2" />
        </div>

        {{-- Nova senha --}}
        <div class="col-span-6 sm:col-span-4">
            <x-label for="password" value="{{ __('Nova Senha') }}" />
            <div class="relative">
                <x-input id="password" type="password" class="mt-1 block w-full pr-10"
                    wire:model="state.password" autocomplete="new-password" />
                <button type="button"
                    class="absolute top-1/2 right-3 transform -translate-y-1/2 text-gray-500 toggle-password"
                    data-target="#password">
                    <img src="{{ asset('imgs/blind.png') }}" alt="Mostrar senha"
                        class="w-5 h-5 opacity-75 hover:opacity-100 transition">
                </button>
            </div>
            <x-input-error for="password" class="mt-2" />

            {{-- Requisitos de senha (JS já vai manipular) --}}
            <ul id="password-requirements" class="text-sm mt-2 hidden">
                <li id="req-length" class="text-red-500">Pelo menos 8 caracteres</li>
                <li id="req-uppercase" class="text-red-500">Uma letra maiúscula</li>
                <li id="req-number" class="text-red-500">Um número</li>
                <li id="req-special" class="text-red-500">Um caractere especial (!@#$%&*)</li>
            </ul>
        </div>

        {{-- Confirmação --}}
        <div class="col-span-6 sm:col-span-4">
            <x-label for="password_confirmation" value="{{ __('Confirmar Senha') }}" />
            <div class="relative">
                <x-input id="password_confirmation" type="password" class="mt-1 block w-full pr-10"
                    wire:model="state.password_confirmation" autocomplete="new-password" />
                <button type="button"
                    class="absolute top-1/2 right-3 transform -translate-y-1/2 text-gray-500 toggle-password"
                    data-target="#password_confirmation">
                    <img src="{{ asset('imgs/blind.png') }}" alt="Mostrar senha"
                        class="w-5 h-5 opacity-75 hover:opacity-100 transition">
                </button>
            </div>
            <x-input-error for="password_confirmation" class="mt-2" />

            {{-- Erro de mismatch --}}
            <p id="password-mismatch-error" class="text-red-500 text-sm mt-1 hidden">
                As senhas são diferentes!
            </p>
        </div>
    </x-slot>

    <x-slot name="actions">
        <x-action-message class="me-3" on="saved">
            {{ __('Salvo.') }}
        </x-action-message>

        <x-button>
            {{ __('Salvar') }}
        </x-button>
    </x-slot>
</x-form-section>
