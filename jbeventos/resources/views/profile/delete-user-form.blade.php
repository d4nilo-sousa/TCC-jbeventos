<div class="space-y-4">
    <div class="flex items-center mb-4">
        <i class="ph ph-user-x text-2xl mr-2 text-red-600"></i>
        <div>
            <h4 class="text-lg font-bold text-gray-900">{{ __('Excluir Conta') }}</h4>
            <p class="text-sm text-gray-500">{{ __('Excluir sua conta permanentemente.') }}</p>
        </div>
    </div>

    <div class="max-w-full text-sm text-gray-600 border border-red-200 p-4 rounded-lg bg-red-50/50">
        <p class="font-semibold text-red-700 flex items-center">
            <i class="ph ph-warning-octagon text-xl mr-2"></i>
            {{ __('Após a exclusão da sua conta, todos os seus recursos e dados serão permanentemente excluídos. Antes de excluir sua conta, faça o download de qualquer dado ou informação que deseja reter.') }}
        </p>
    </div>

    <div class="flex items-center justify-end pt-4 border-t border-gray-100">
        <x-danger-button wire:click="confirmUserDeletion" wire:loading.attr="disabled">
            <i class="ph ph-trash text-lg mr-1"></i>
            {{ __('Excluir Conta') }}
        </x-danger-button>
    </div>

    {{-- Modal de Confirmação --}}
    <x-dialog-modal wire:model.live="confirmingUserDeletion">
        <x-slot name="title">
            {{ __('Excluir Conta') }}
        </x-slot>

        <x-slot name="content">
            {{ __('Você tem certeza de que deseja excluir sua conta? Após a exclusão da sua conta, todos os seus recursos e dados serão permanentemente excluídos. Por favor, insira sua senha para confirmar que deseja excluir permanentemente sua conta.') }}

            <div class="mt-4" x-data="{}" x-on:confirming-delete-user.window="setTimeout(() => $refs.password.focus(), 250)">
                <x-input type="password" class="mt-1 block w-3/4"
                        autocomplete="current-password"
                        placeholder="{{ __('Senha') }}"
                        x-ref="password"
                        wire:model="password"
                        wire:keydown.enter="deleteUser" />

                <x-input-error for="password" class="mt-2" />
            </div>
        </x-slot>

        <x-slot name="footer">
            <x-secondary-button wire:click="$toggle('confirmingUserDeletion')" wire:loading.attr="disabled">
                {{ __('Cancelar') }}
            </x-secondary-button>

            <x-danger-button class="ms-3" wire:click="deleteUser" wire:loading.attr="disabled">
                {{ __('Excluir Conta') }}
            </x-danger-button>
        </x-slot>
    </x-dialog-modal>
</div>