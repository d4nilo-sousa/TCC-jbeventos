<div class="space-y-4">
    <div class="flex items-center mb-4">
        <i class="ph ph-device-mobile-camera text-2xl mr-2 text-red-600"></i>
        <div>
            <h4 class="text-lg font-bold text-gray-900">{{ __('Autenticação de Dois Fatores (2FA)') }}</h4>
            <p class="text-sm text-gray-500">{{ __('Adicione segurança extra à sua conta com autenticação de dois fatores.') }}</p>
        </div>
    </div>

    <div class="max-w-full text-sm text-gray-600">
        <p>
            {{ __('Com a 2FA ativada, você precisará de um código de segurança aleatório (token) do seu app Google Authenticator no login.') }}
        </p>
    </div>

    @if ($this->enabled)
        <h5 class="text-base font-semibold text-green-600 flex items-center">
            @if ($showingConfirmation)
                <i class="ph ph-check-circle text-lg mr-1"></i> {{ __('Finalize a ativação da 2FA.') }}
            @else
                <i class="ph ph-shield-check text-lg mr-1"></i> {{ __('A Autenticação de Dois Fatores está ativada.') }}
            @endif
        </h5>

        @if ($showingQrCode)
            <div class="mt-4 max-w-full text-sm text-gray-600 border border-gray-100 p-4 rounded-lg bg-gray-50 space-y-3">
                <p class="font-semibold">
                    @if ($showingConfirmation)
                        {{ __('Para finalizar, escaneie o QR Code no seu app autenticador ou insira a chave de configuração abaixo e informe o código gerado.') }}
                    @else
                        {{ __('A 2FA está ativa. Escaneie o QR Code ou insira a chave de configuração para adicionar ao seu app.') }}
                    @endif
                </p>

                <div class="p-2 inline-block bg-white shadow-inner rounded-md">
                    {!! $this->user->twoFactorQrCodeSvg() !!}
                </div>

                <p class="font-semibold break-all">
                    {{ __('Chave de Configuração') }}: <code class="bg-gray-200 px-1 py-0.5 rounded text-xs">{{ decrypt($this->user->two_factor_secret) }}</code>
                </p>
            </div>

            @if ($showingConfirmation)
                <div class="mt-4">
                    <x-label for="code" value="{{ __('Código de Confirmação') }}" />

                    <x-input id="code" type="text" name="code" class="block mt-1 w-full sm:w-1/2" inputmode="numeric" autofocus autocomplete="one-time-code"
                        wire:model="code"
                        wire:keydown.enter="confirmTwoFactorAuthentication" />

                    <x-input-error for="code" class="mt-2" />
                </div>
            @endif
        @endif

        @if ($showingRecoveryCodes)
            <div class="mt-4 max-w-full text-sm text-gray-600 border border-red-200 p-4 rounded-lg bg-red-50/50">
                <p class="font-semibold text-red-700 flex items-center">
                    <i class="ph ph-warning-circle text-xl mr-2"></i>
                    {{ __('Guarde estes códigos de recuperação em um local seguro. Eles podem ser usados para recuperar o acesso à sua conta caso você perca seu dispositivo de autenticação.') }}
                </p>
            </div>

            <div class="grid gap-2 max-w-xl mt-4 px-4 py-3 font-mono text-sm bg-gray-100 rounded-lg grid-cols-2">
                @foreach (json_decode(decrypt($this->user->two_factor_recovery_codes), true) as $code)
                    <div class="bg-white p-1 rounded text-center shadow-sm">{{ $code }}</div>
                @endforeach
            </div>
        @endif
    @else
        <h5 class="text-base font-semibold text-red-600 flex items-center">
            <i class="ph ph-shield-slash text-lg mr-1"></i> {{ __('A Autenticação de Dois Fatores NÃO está ativada.') }}
        </h5>
    @endif

    <div class="flex items-center justify-end pt-4 border-t border-gray-100">
        {{-- Botões de Ação --}}
        @if (! $this->enabled)
            <x-confirms-password wire:then="enableTwoFactorAuthentication">
                <x-button type="button" wire:loading.attr="disabled" class="bg-red-600 hover:bg-red-700">
                    <i class="ph ph-lock-key-open text-lg mr-1"></i>
                    {{ __('Ativar 2FA') }}
                </x-button>
            </x-confirms-password>
        @else
            @if ($showingRecoveryCodes)
                <x-confirms-password wire:then="regenerateRecoveryCodes">
                    <x-secondary-button class="me-3 !text-red-600 hover:!bg-red-50">
                        <i class="ph ph-repeat text-lg mr-1"></i>
                        {{ __('Regerar Códigos') }}
                    </x-secondary-button>
                </x-confirms-password>
            @elseif ($showingConfirmation)
                <x-confirms-password wire:then="confirmTwoFactorAuthentication">
                    <x-button type="button" class="me-3 bg-red-600 hover:bg-red-700" wire:loading.attr="disabled">
                        <i class="ph ph-check text-lg mr-1"></i>
                        {{ __('Confirmar') }}
                    </x-button>
                </x-confirms-password>
            @else
                <x-confirms-password wire:then="showRecoveryCodes">
                    <x-secondary-button class="me-3 !text-red-600 hover:!bg-red-50">
                        <i class="ph ph-key text-lg mr-1"></i>
                        {{ __('Mostrar Códigos') }}
                    </x-secondary-button>
                </x-confirms-password>
            @endif

            @if ($showingConfirmation)
                <x-confirms-password wire:then="disableTwoFactorAuthentication">
                    <x-secondary-button wire:loading.attr="disabled">
                        <i class="ph ph-x text-lg mr-1"></i>
                        {{ __('Cancelar') }}
                    </x-secondary-button>
                </x-confirms-password>
            @else
                <x-confirms-password wire:then="disableTwoFactorAuthentication">
                    <x-danger-button wire:loading.attr="disabled">
                        <i class="ph ph-shield-slash text-lg mr-1"></i>
                        {{ __('Desativar 2FA') }}
                    </x-danger-button>
                </x-confirms-password>
            @endif
        @endif
    </div>
</div>