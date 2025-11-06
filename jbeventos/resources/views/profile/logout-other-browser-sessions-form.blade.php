<div class="space-y-4">
    <div class="flex items-center mb-4">
        <i class="ph ph-monitor-arrows text-2xl mr-2 text-red-600"></i>
        <div>
            <h4 class="text-lg font-bold text-gray-900">{{ __('Sessões do Navegador') }}</h4>
            <p class="text-sm text-gray-500">{{ __('Gerencie e saia das suas sessões ativas em outros navegadores e dispositivos.') }}</p>
        </div>
    </div>

    <div class="max-w-full text-sm text-gray-600">
        {{ __('Se necessário, você pode sair de todas as suas outras sessões de navegador. Se achar que sua conta foi comprometida, também deve atualizar sua senha.') }}
    </div>

    @if (count($this->sessions) > 0)
        <div class="mt-5 space-y-4 border border-gray-100 p-4 rounded-lg bg-gray-50">
            <h5 class="text-sm font-semibold text-gray-700 flex items-center"><i class="ph ph-list-magnifying-glass text-lg mr-1"></i> Sessões Ativas</h5>
            @foreach ($this->sessions as $session)
                <div class="flex items-center justify-between p-2 rounded-lg {{ $session->is_current_device ? 'bg-green-50/50 border border-green-200' : 'hover:bg-gray-100' }}">
                    <div class="flex items-center">
                        <div>
                            @if ($session->agent->isDesktop())
                                <i class="ph ph-monitor text-2xl text-gray-500"></i>
                            @else
                                <i class="ph ph-device-mobile text-2xl text-gray-500"></i>
                            @endif
                        </div>

                        <div class="ms-3">
                            <div class="text-sm font-medium text-gray-700">
                                {{ $session->agent->platform() ? $session->agent->platform() : __('Desconhecido') }} - {{ $session->agent->browser() ? $session->agent->browser() : __('Desconhecido') }}
                            </div>

                            <div class="flex items-center text-xs text-gray-500 mt-0.5">
                                {{ $session->ip_address }},
                                @if ($session->is_current_device)
                                    <span class="text-green-600 font-semibold ms-1 flex items-center">
                                        <i class="ph ph-check-circle text-sm mr-0.5"></i> {{ __('Este dispositivo') }}
                                    </span>
                                @else
                                    <span class="ms-1">{{ __('Última atividade') }} {{ $session->last_active }}</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif

    <div class="flex items-center justify-end pt-4 border-t border-gray-100">
        <x-action-message class="me-3 text-green-600 font-semibold" on="loggedOut">
            <i class="ph ph-check-circle text-lg mr-1"></i>
            {{ __('Feito!') }}
        </x-action-message>

        <x-button wire:click="confirmLogout" wire:loading.attr="disabled" class="bg-red-600 hover:bg-red-700">
            <i class="ph ph-sign-out text-lg mr-1"></i>
            {{ __('Sair de Outras Sessões') }}
        </x-button>
    </div>

    {{-- Modal de Confirmação --}}
    <x-dialog-modal wire:model.live="confirmingLogout">
        <x-slot name="title">
            {{ __('Sair de Outras Sessões de Navegador') }}
        </x-slot>

        <x-slot name="content">
            {{ __('Por favor, insira sua senha para confirmar que você deseja sair das suas outras sessões de navegador em todos os seus dispositivos.') }}

            <div class="mt-4" x-data="{}" x-on:confirming-logout-other-browser-sessions.window="setTimeout(() => $refs.password.focus(), 250)">
                <x-input type="password" class="mt-1 block w-3/4"
                        autocomplete="current-password"
                        placeholder="{{ __('Senha') }}"
                        x-ref="password"
                        wire:model="password"
                        wire:keydown.enter="logoutOtherBrowserSessions" />

                <x-input-error for="password" class="mt-2" />
            </div>
        </x-slot>

        <x-slot name="footer">
            <x-secondary-button wire:click="$toggle('confirmingLogout')" wire:loading.attr="disabled">
                {{ __('Cancelar') }}
            </x-secondary-button>

            <x-button class="ms-3 bg-red-600 hover:bg-red-700"
                    wire:click="logoutOtherBrowserSessions"
                    wire:loading.attr="disabled">
                {{ __('Sair de Outras Sessões') }}
            </x-button>
        </x-slot>
    </x-dialog-modal>
</div>