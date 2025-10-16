<x-app-layout>
    <div class="relative bg-white shadow-2xl rounded-xl overflow-hidden max-w-4xl mx-auto my-8 border border-gray-100">

        {{-- Banner --}}
        <div class="relative h-56 {{ preg_match('/^#[a-f0-9]{6}$/i', $user->user_banner_url) ? '' : 'bg-gradient-to-r from-red-500/10 to-purple-500/10' }}"
            style="{{ preg_match('/^#[a-f0-9]{6}$/i', $user->user_banner_url) ? 'background-color: ' . $user->user_banner_url : '' }}">
            
            @if(!preg_match('/^#[a-f0-9]{6}$/i', $user->user_banner_url))
                <img src="{{ $user->user_banner_url }}" alt="Banner do Usuário"
                    class="object-cover w-full h-full">
            @endif
            
            {{-- Botões de Edição do Banner --}}
            @if(auth()->id() === $user->id)
                {{-- Upload de Imagem --}}
                <label for="bannerUpload"
                    class="absolute top-4 right-4 bg-white/70 backdrop-blur-sm text-sm px-4 py-2 rounded-full shadow-lg cursor-pointer hover:bg-white transition-colors duration-200 flex items-center space-x-1">
                    <i class="ph ph-image text-lg"></i>
                    <span>Trocar Imagem</span>
                </label>
                <form id="bannerForm" method="POST" action="{{ route('profile.updateBanner') }}"
                    enctype="multipart/form-data" class="hidden">
                    @csrf
                    <input type="file" name="user_banner" id="bannerUpload" onchange="document.getElementById('bannerForm').submit()">
                </form>

                {{-- Seletor de Cor --}}
                <form method="POST" action="{{ route('profile.updateBannerColor') }}" class="absolute top-4 right-44">
                    @csrf
                    <label for="bannerColor" class="sr-only">Escolher Cor de Banner</label>
                    <input type="color" name="user_banner" id="bannerColor" value="{{ preg_match('/^#[a-f0-9]{6}$/i', $user->user_banner) ? $user->user_banner : '#a0a0a0' }}"
                        onchange="this.form.submit()" class="cursor-pointer h-10 w-10 p-1 rounded-full border-2 border-white shadow-lg transition-all duration-200 hover:scale-105">
                </form>
            @endif
        </div>

        {{-- Avatar, Nome e Tipo do Usuário --}}
        <div class="px-6 -mt-16 flex items-center space-x-6 pb-6 border-b border-gray-200">
            {{-- Bloco do Avatar --}}
            <div class="flex flex-col items-center -mb-4">
                {{-- Avatar --}}
                <div class="relative w-36 h-36 rounded-full border-6 border-white bg-gray-300 shadow-xl">
                    <img src="{{ $user->user_icon_url }}" alt="Avatar"
                        class="w-full h-full rounded-full object-cover">
                    
                    @if(auth()->id() === $user->id)
                        {{-- Botão de upload de imagem --}}
                        <label for="photoUpload"
                            class="absolute bottom-0 right-0 bg-white p-2 rounded-full shadow-xl border border-gray-200 cursor-pointer hover:bg-gray-100 transition-colors duration-200">
                            <i class="ph ph-camera text-base text-gray-700"></i>
                        </label>
                        <form id="photoForm" method="POST" action="{{ route('profile.updatePhoto') }}"
                            enctype="multipart/form-data" class="hidden">
                            @csrf
                            <input type="file" name="user_icon" id="photoUpload" onchange="document.getElementById('photoForm').submit()">
                        </form>
                    @endif
                </div>
                
                {{-- Botão e Dropdown de Avatares Padrão --}}
                @if(auth()->id() === $user->id)
                    <div class="relative inline-block text-left mt-4" x-data="{ open: false }" @click.away="open = false">
                        <div>
                            <button type="button" @click="open = !open" class="inline-flex items-center rounded-full border border-gray-300 shadow-sm px-3 py-1 bg-white text-xs font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-white focus:ring-red-500 transition-all duration-200">
                                <i class="ph ph-crown-simple text-sm mr-1"></i>
                                Avatares Padrão
                                <i class="ph ph-caret-down text-sm ml-1" :class="{'rotate-180': open}"></i>
                            </button>
                        </div>
                        
                        <div x-show="open" x-transition:enter="transition ease-out duration-100" x-transition:enter-start="transform opacity-0 scale-95" x-transition:enter-end="transform opacity-100 scale-100" x-transition:leave="transition ease-in duration-75" x-transition:leave-start="transform opacity-100 scale-100" x-transition:leave-end="transform opacity-0 scale-95" class="origin-top-right absolute left-1/2 -translate-x-1/2 mt-2 w-56 rounded-lg shadow-xl bg-white ring-1 ring-black ring-opacity-5 focus:outline-none z-10 p-2">
                            <div class="flex flex-wrap gap-2 justify-center">
                                @php
                                    $defaultAvatars = [
                                        'avatar_default_1.svg',
                                        'avatar_default_2.svg',
                                        'avatar_default_3.png',
                                        'avatar_default_4.png'
                                    ];
                                @endphp
                                @foreach($defaultAvatars as $avatar)
                                    <form action="{{ route('profile.updateDefaultPhoto') }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="user_icon_default" value="{{ $avatar }}">
                                        <button type="submit" class="w-10 h-10 rounded-full border-2 transition-all duration-200 {{ $user->user_icon_default === $avatar && !$user->user_icon ? 'border-red-500 shadow-md scale-110' : 'border-transparent hover:border-red-300' }}">
                                            <img src="{{ asset('imgs/' . $avatar) }}" alt="Ícone Padrão" class="w-full h-full rounded-full">
                                        </button>
                                    </form>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endif
            </div>

            {{-- Nome e Tipo do Usuário --}}
            <div class="flex-1 mt-8">
                <h2 class="text-4xl font-extrabold text-gray-900">{{ $user->name }}</h2>
                <div class="mt-1">
                    @php
                        $userTypeData = [
                            'coordinator' => ['label' => 'Coordenador', 'color' => 'bg-red-500', 'icon' => 'ph-chalkboard-teacher'],
                            'user' => ['label' => 'Usuário Comum', 'color' => 'bg-gray-500', 'icon' => 'ph-user'],
                            'admin' => ['label' => 'Administrador', 'color' => 'bg-red-500', 'icon' => 'ph-crown'],
                        ];
                        $type = $userTypeData[$user->user_type] ?? ['label' => ucfirst($user->user_type), 'color' => 'bg-red-500', 'icon' => 'ph-person'];
                    @endphp
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold text-white {{ $type['color'] }}">
                        <i class="ph {{ $type['icon'] }} text-sm mr-1"></i>
                        {{ $type['label'] }}
                    </span>
                </div>
            </div>
        </div>

        {{-- Seções do Perfil com Abas --}}
        <div class="px-6 py-4" x-data="{ activeTab: 'biography' }">
            <div class="border-b border-gray-200">
                <nav class="-mb-px flex space-x-8" aria-label="Tabs">
                    <button @click="activeTab = 'biography'" :class="{'border-red-500 text-red-600': activeTab === 'biography', 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': activeTab !== 'biography'}" class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition-colors duration-200 flex items-center">
                        <i class="ph ph-notepad text-lg mr-2"></i>
                        Biografia
                    </button>
                    <button @click="activeTab = 'savedEvents'" :class="{'border-red-500 text-red-600': activeTab === 'savedEvents', 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': activeTab !== 'savedEvents'}" class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition-colors duration-200 flex items-center">
                        <i class="ph ph-bookmark-simple text-lg mr-2"></i>
                        Eventos Salvos ({{ $savedEvents->count() }})
                    </button>
                    @if($user->user_type === 'coordinator')
                        <button @click="activeTab = 'createdEvents'" :class="{'border-red-500 text-red-600': activeTab === 'createdEvents', 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': activeTab !== 'createdEvents'}" class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition-colors duration-200 flex items-center">
                            <i class="ph ph-calendar-plus text-lg mr-2"></i>
                            Meus Eventos ({{ $createdEvents->count() }})
                        </button>
                    @endif
                </nav>
            </div>

            <div class="mt-4">
                {{-- Biografia --}}
                <div x-show="activeTab === 'biography'">
                    <div class="p-2 py-4" x-data="{ editing: false, bio: @js(old('bio', $user->bio ?? '')), original: @js($user->bio ?? '') }" @keydown.escape="editing = false; bio = original">
                        <h3 class="text-base font-bold mb-3 flex items-center text-gray-800">
                             <i class="ph ph-info text-xl mr-2"></i> Sobre Mim
                        </h3>
                        
                        {{-- Visualização --}}
                        <div x-show="!editing" @click="editing = true" class="cursor-pointer text-sm text-gray-700 min-h-[5rem] whitespace-pre-line p-4 rounded-lg bg-gray-50 hover:bg-gray-100 transition-colors duration-200 border border-transparent hover:border-red-200 shadow-sm">
                            <span x-text="bio || 'Clique aqui ou no botão para adicionar uma biografia atraente.' "></span>
                        </div>
                        
                        {{-- Form de Edição --}}
                        <form method="POST" action="{{ route('profile.updateBio') }}" x-show="editing" x-transition>
                            @csrf
                            <textarea name="bio" rows="5" x-model="bio"
                                class="w-full border-gray-300 rounded-lg p-3 text-sm focus:ring-red-500 focus:border-red-500 shadow-inner"
                                placeholder="Escreva sua biografia aqui (máx. 500 caracteres)..."></textarea>
                            @error('bio') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            <div class="mt-3 text-right space-x-2">
                                <button type="button" @click="editing = false; bio = original"
                                    class="text-gray-500 hover:text-gray-700 px-4 py-2 text-sm rounded-lg transition-colors duration-200">
                                    Cancelar
                                </button>
                                <button type="submit"
                                    class="bg-red-600 hover:bg-red-700 text-white px-5 py-2 text-sm rounded-lg shadow-lg transition-colors duration-200 flex-inline items-center">
                                    <i class="ph ph-floppy-disk text-lg mr-1"></i>
                                    Salvar Biografia
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                {{-- Eventos Salvos --}}
                <div x-show="activeTab === 'savedEvents'">
                    <h3 class="text-lg font-bold mb-4 text-gray-800 flex items-center">
                        <i class="ph ph-heart-straight text-xl mr-2 text-red-500"></i> Eventos que você salvou
                    </h3>
                    @if($savedEvents->isEmpty())
                        <div class="text-center py-10 border border-dashed rounded-lg bg-gray-50">
                            <i class="ph ph-magnifying-glass text-4xl text-gray-400"></i>
                            <p class="text-gray-500 text-sm mt-2">Você ainda não salvou nenhum evento interessante.</p>
                            <a href="{{ route('events.index') }}" class="mt-4 inline-block text-red-600 font-medium hover:text-red-800 transition-colors">
                                <i class="ph ph-arrow-right text-sm mr-1"></i> Explore eventos agora!
                            </a>
                        </div>
                    @else
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                            @foreach($savedEvents as $event)
                                <div class="bg-white rounded-xl shadow-md border border-gray-100 hover:shadow-xl transition-all duration-300 overflow-hidden relative">
                                    <a href="{{ route('events.show', $event) }}" class="block">
                                        <div class="h-36 bg-gray-200 flex items-center justify-center overflow-hidden">
                                            <img src="{{ $event->event_image ? asset('storage/' . $event->event_image) : asset('default-event-image.jpg') }}"
                                                alt="{{ $event->event_name }}"
                                                class="object-cover w-full h-full">
                                        </div>
                                        <div class="p-4">
                                            <p class="font-bold text-gray-900 line-clamp-2">{{ $event->event_name }}</p>
                                            <p class="text-xs text-red-600 mt-2 flex items-center">
                                                <i class="ph ph-calendar text-sm mr-1"></i>
                                                {{ $event->event_scheduled_at->format('d/m/Y') }} às {{ $event->event_scheduled_at->format('H:i') }}
                                            </p>
                                        </div>
                                    </a>
                                    {{-- Botão para "des-salvar" o evento --}}
                                    <form action="{{ route('events.unsave', $event) }}" method="POST" class="absolute top-2 right-2">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="bg-white/80 backdrop-blur-sm p-1.5 rounded-full text-red-500 hover:text-red-700 shadow-md transition-colors duration-200" title="Remover dos Salvos">
                                            <i class="ph ph-x-circle text-xl"></i>
                                        </button>
                                    </form>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>

                {{-- Eventos Criados (apenas para coordenadores) --}}
                @if($user->user_type === 'coordinator')
                    <div x-show="activeTab === 'createdEvents'">
                        <h3 class="text-lg font-bold mb-4 text-gray-800 flex items-center">
                             <i class="ph ph-rocket-launch text-xl mr-2 text-red-500"></i> Eventos que você publicou
                        </h3>
                        @if($createdEvents->isEmpty())
                            <div class="text-center py-10 border border-dashed rounded-lg bg-gray-50">
                                <i class="ph ph-package text-4xl text-gray-400"></i>
                                <p class="text-gray-500 text-sm mt-2">Você ainda não criou nenhum evento. Está na hora de começar!</p>
                                <a href="{{ route('coordinator.events.create') }}" class="mt-4 inline-block text-red-600 font-medium hover:text-red-800 transition-colors">
                                    <i class="ph ph-plus-circle text-sm mr-1"></i> Crie seu primeiro evento!
                                </a>
                            </div>
                        @else
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                                @foreach($createdEvents as $event)
                                    <a href="{{ route('events.show', $event) }}" class="block bg-white rounded-xl shadow-md border border-gray-100 hover:shadow-xl transition-all duration-300 overflow-hidden">
                                        <div class="h-36 bg-gray-200 flex items-center justify-center overflow-hidden">
                                            <img src="{{ $event->event_image ? asset('storage/' . $event->event_image) : asset('default-event-image.jpg') }}"
                                                 alt="{{ $event->event_name }}"
                                                 class="object-cover w-full h-full">
                                        </div>
                                        <div class="p-4">
                                            <p class="font-bold text-gray-900 line-clamp-2">{{ $event->event_name }}</p>
                                            <p class="text-xs text-red-600 mt-2 flex items-center">
                                                <i class="ph ph-calendar text-sm mr-1"></i>
                                                {{ $event->event_scheduled_at->format('d/m/Y') }} às {{ $event->event_scheduled_at->format('H:i') }}
                                            </p>
                                        </div>
                                    </a>
                                @endforeach
                            </div>
                        @endif
                    </div>
                @endif
            </div>
        </div>

        {{-- Informações adicionais --}}
        <div class="px-6 py-4 text-sm text-gray-600 border-t border-gray-200 bg-gray-50 flex items-center justify-between">
            <p class="flex items-center">
                <i class="ph ph-clock-counter-clockwise text-lg mr-2"></i>
                <strong>Membro desde:</strong> {{ $user->created_at->format('d/m/Y') }}
            </p>
        </div>
    </div>
</x-app-layout>