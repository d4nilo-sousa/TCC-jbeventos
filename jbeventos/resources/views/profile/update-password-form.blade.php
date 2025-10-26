<form wire:submit.prevent="updatePassword" class="space-y-4">
    <div class="flex items-center mb-4">
        <i class="ph ph-lock-key text-2xl mr-2 text-red-600"></i>
        <div>
            <h4 class="text-lg font-bold text-gray-900">{{ __('Atualizar Senha') }}</h4>
            <p class="text-sm text-gray-500">
                {{ __('Garanta que sua conta esteja usando uma senha longa e aleatória para se manter segura.') }}</p>
        </div>
    </div>

    <div class="grid grid-cols-1 gap-y-4">
        {{-- Campo oculto com o tipo do usuário --}}
        <input type="hidden" name="userType" value="{{ auth()->user()->user_type }}">

        {{-- Senha atual --}}
        <div class="col-span-1">
            <x-label for="current_password" value="{{ __('Senha Atual') }}" />
            <div class="relative">
                <x-input id="current_password" type="password" class="mt-1 block w-full pr-10"
                    wire:model="state.current_password" autocomplete="current-password" required />
                <button type="button"
                    class="absolute top-1/2 right-3 transform -translate-y-1/2 text-gray-500 toggle-password hover:text-red-500"
                    data-target="#current_password">
                    <img src="/imgs/blind.png" alt="Mostrar senha" class="w-5 h-5">
                </button>
            </div>
            <x-input-error for="current_password" class="mt-2" />
        </div>

        {{-- Nova senha --}}
        <div class="col-span-1">
            <x-label for="password" value="{{ __('Nova Senha') }}" />
            <div class="relative">
                <x-input id="password" type="password" class="mt-1 block w-full pr-10" wire:model="state.password"
                    autocomplete="new-password" required />
                <button type="button"
                    class="absolute top-1/2 right-3 transform -translate-y-1/2 text-gray-500 toggle-password hover:text-red-500"
                    data-target="#password">
                    <img src="/imgs/blind.png" alt="Mostrar senha" class="w-5 h-5">
                </button>
            </div>
            <x-input-error for="password" class="mt-2" />

            {{-- Requisitos de senha --}}
            <ul id="password-requirements" class="text-sm mt-2 hidden list-disc list-inside">
                <li id="req-length" class="text-red-500 flex items-center"><i
                        class="ph ph-x-circle text-base mr-1"></i>Pelo menos 8 caracteres</li>
                <li id="req-uppercase" class="text-red-500 flex items-center"><i
                        class="ph ph-x-circle text-base mr-1"></i>Uma letra maiúscula</li>
                <li id="req-number" class="text-red-500 flex items-center"><i
                        class="ph ph-x-circle text-base mr-1"></i>Um número</li>
                <li id="req-special" class="text-red-500 flex items-center"><i
                        class="ph ph-x-circle text-base mr-1"></i>Um caractere especial (!@#$%&*)</li>
            </ul>
        </div>

        {{-- Confirmação --}}
        <div class="col-span-1">
            <x-label for="password_confirmation" value="{{ __('Confirmar Senha') }}" />
            <div class="relative">
                <x-input id="password_confirmation" type="password" class="mt-1 block w-full pr-10"
                    wire:model="state.password_confirmation" autocomplete="new-password" required />
                <button type="button"
                    class="absolute top-1/2 right-3 transform -translate-y-1/2 text-gray-500 toggle-password hover:text-red-500"
                    data-target="#password_confirmation">
                    <img src="/imgs/blind.png" alt="Mostrar senha" class="w-5 h-5">
                </button>
            </div>
            <x-input-error for="password_confirmation" class="mt-2" />

            {{-- Erro de mismatch --}}
            <p id="password-mismatch-error" class="text-red-500 text-sm mt-1 hidden flex items-center">
                <i class="ph ph-warning text-lg mr-1"></i> As senhas são diferentes!
            </p>
        </div>
    </div>

    {{-- Ações (Botão Salvar) --}}
    <div class="flex items-center justify-end pt-4 border-t border-gray-100">
        <x-action-message class="me-3 text-green-600 font-semibold" on="saved">
            <i class="ph ph-check-circle text-lg mr-1"></i>
            {{ __('Salvo!') }}
        </x-action-message>

        <x-button class="bg-red-600 hover:bg-red-700">
            <i class="ph ph-floppy-disk text-lg mr-1"></i>
            {{ __('Salvar') }}
        </x-button>
    </div>
</form>
