<form wire:submit.prevent="updateProfileInformation" class="space-y-4">
    <div class="flex items-center mb-4">
        <i class="ph ph-user-circle text-2xl mr-2 text-red-600"></i>
        <div>
            <h4 class="text-lg font-bold text-gray-900">{{ __('Informações do Perfil') }}</h4>
            <p class="text-sm text-gray-500">{{ __('Atualize as informações do perfil da sua conta e o endereço de email.') }}</p>
        </div>
    </div>

    <div class="grid grid-cols-1 gap-y-4"> 
        @if (Laravel\Jetstream\Jetstream::managesProfilePhotos())
            <div x-data="{photoName: null, photoPreview: null}" class="col-span-1">
                <input type="file" id="photo" class="hidden"
                    wire:model.live="photo"
                    x-ref="photo"
                    x-on:change="
                            photoName = $refs.photo.files[0].name;
                            const reader = new FileReader();
                            reader.onload = (e) => {
                                photoPreview = e.target.result;
                            };
                            reader.readAsDataURL($refs.photo.files[0]);
                    " />

                <x-label for="photo" value="{{ __('Foto de Perfil') }}" />
                <div class="flex items-center space-x-4 mt-2">
                    <div x-show="! photoPreview">
                        <img src="{{ $this->user->profile_photo_url }}" alt="{{ $this->user->name }}" class="rounded-full size-16 object-cover shadow-md">
                    </div>
                    <div x-show="photoPreview" style="display: none;">
                        <span class="block rounded-full size-16 bg-cover bg-no-repeat bg-center shadow-md"
                              x-bind:style="'background-image: url(\'' + photoPreview + '\');'">
                        </span>
                    </div>
                    <div class="flex flex-col space-y-1">
                        <x-secondary-button type="button" x-on:click.prevent="$refs.photo.click()" class="!py-1.5 !px-3 text-xs">
                            <i class="ph ph-image text-sm mr-1"></i>
                            {{ __('Nova Foto') }}
                        </x-secondary-button>
                        @if ($this->user->profile_photo_path)
                            <x-secondary-button type="button" wire:click="deleteProfilePhoto" class="!py-1.5 !px-3 text-xs text-red-500 hover:text-red-700">
                                <i class="ph ph-trash text-sm mr-1"></i>
                                {{ __('Remover') }}
                            </x-secondary-button>
                        @endif
                    </div>
                </div>
                <x-input-error for="photo" class="mt-2" />
            </div>
        @endif

        <div class="col-span-1"> 
            <x-label for="name" value="{{ __('Nome') }}" />
            <x-input id="name" type="text" class="mt-1 block w-full" wire:model="state.name" required autocomplete="name" />
            <x-input-error for="name" class="mt-2" />
        </div>

        <div class="col-span-1"> 
            <div class="flex items-end space-x-4">
                {{-- Campo Email --}}
                <div class="flex-grow">
                    <x-label for="email" value="{{ __('Email') }}" />
                    <x-input id="email" type="email" class="mt-1 block w-full" wire:model="state.email" required autocomplete="username" />
                    <x-input-error for="email" class="mt-2" />
                </div>
                
                {{-- Botão Salvar --}}
                <div class="pb-2"> 
                    <x-action-message class="me-3 text-green-600 font-semibold" on="saved">
                        <i class="ph ph-check-circle text-lg mr-1"></i>
                        {{ __('Salvo!') }}
                    </x-action-message>

                    <x-button wire:loading.attr="disabled" wire:target="photo" class="bg-red-600 hover:bg-red-700 h-10">
                        <i class="ph ph-floppy-disk text-lg mr-1"></i>
                        {{ __('Salvar') }}
                    </x-button>
                </div>
            </div>

            @if (Laravel\Fortify\Features::enabled(Laravel\Fortify\Features::emailVerification()) && ! $this->user->hasVerifiedEmail())
                <p class="text-sm mt-2">
                    {{ __('Seu endereço de e-mail não está verificado.') }}

                    <button type="button" class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" wire:click.prevent="sendEmailVerification">
                        {{ __('Clique aqui para reenviar o e-mail de verificação.') }}
                    </button>
                </p>

                @if ($this->verificationLinkSent)
                    <p class="mt-2 font-medium text-sm text-green-600">
                        {{ __('Um novo link de verificação foi enviado para seu endereço de e-mail.') }}
                    </p>
                @endif
            @endif
        </div>
    </div>
</form>