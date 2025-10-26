<x-app-layout>
    {{-- Variável Alpine para controlar o Modal de Configurações e o Dropdown de Ícones Padrão --}}
    <div x-data="{ settingsModalOpen: false, defaultIconsOpen: false }">

        {{-- Container Principal --}}
        <div class="py-10 bg-gray-50 min-h-screen">
            <div class="max-w-[1400px] mx-auto sm:px-6 lg:px-16 space-y-6">
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8" x-data="{ activeTab: '{{ request('tab', 'biography') }}' }">

                    {{-- Coluna PRINCIPAL (Conteúdo das Abas e Formulários) --}}
                    <div class="lg:col-span-2 bg-white shadow-2xl rounded-xl overflow-hidden border border-gray-100">

                        <div class="relative">
                            {{-- Div do banner com id para JS --}}
                            <div id="userBanner" class="relative h-56 w-full rounded-lg overflow-hidden"
                                style="
        background-color: {{ preg_match('/^#[a-f0-9]{6}$/i', $user->user_banner) ? $user->user_banner : 'transparent' }};
        background-image: {{ !preg_match('/^#[a-f0-9]{6}$/i', $user->user_banner) ? "url('{$user->user_banner_url}')" : 'none' }};
        background-size: cover;
        background-position: center;
        background-repeat: no-repeat;
     ">
                                {{-- Imagem apenas para acessibilidade --}}
                                @if (!preg_match('/^#[a-f0-9]{6}$/i', $user->user_banner))
                                    <img src="{{ $user->user_banner_url }}" alt="Banner do Usuário" class="sr-only">
                                @endif
                                {{-- Botões de edição do banner --}}
                                @if (auth()->id() === $user->id)
                                    {{-- Upload de Imagem --}}
                                    <label for="bannerUpload"
                                        class="absolute top-4 right-4 bg-white/70 backdrop-blur-sm text-sm px-4 py-2 rounded-full shadow-lg cursor-pointer hover:bg-white transition-colors duration-200 flex items-center space-x-1">
                                        <i class="ph ph-image text-lg"></i>
                                        <span>Trocar Imagem</span>
                                    </label>
                                    <form id="bannerForm" method="POST" action="{{ route('profile.updateBanner') }}"
                                        enctype="multipart/form-data" class="hidden">
                                        @csrf
                                        <input type="file" name="user_banner" id="bannerUpload">
                                    </form>

                                    {{-- Seletor de Cor --}}
                                    <form id="bannerColorForm" method="POST"
                                        action="{{ route('profile.updateBannerColor') }}"
                                        class="absolute top-4 right-44">
                                        @csrf
                                        <label for="bannerColor" class="sr-only">Escolher Cor de Banner</label>
                                        <input type="color" name="user_banner" id="bannerColor"
                                            value="{{ preg_match('/^#[a-f0-9]{6}$/i', $user->user_banner) ? $user->user_banner : '#a0a0a0' }}"
                                            class="cursor-pointer h-10 w-10 p-1 rounded-full border-2 border-white shadow-lg transition-all duration-200 hover:scale-105">
                                    </form>
                                @endif
                            </div>

                            {{-- Avatar, Nome e Tipo do Usuário --}}
                            <div class="px-6 -mt-16 flex items-end space-x-6 pb-6 border-b border-gray-200">
                                {{-- Bloco do Avatar e Botões de Edição --}}
                                <div class="flex flex-col items-center">
                                    <div
                                        class="relative w-36 h-36 rounded-full border-6 border-white bg-gray-300 shadow-xl">
                                        <img src="{{ $user->user_icon_url }}" alt="Avatar"
                                            class="w-full h-full rounded-full object-cover">

                                        @if (auth()->id() === $user->id)
                                            <label for="photoUpload"
                                                class="absolute bottom-0 right-0 bg-white p-2 rounded-full shadow-xl border border-gray-200 cursor-pointer hover:bg-gray-100 transition-colors duration-200">
                                                <i class="ph ph-camera text-base text-gray-700"></i>
                                            </label>

                                            <form id="photoForm" method="POST"
                                                action="{{ route('profile.updatePhoto') }}"
                                                enctype="multipart/form-data" class="hidden">
                                                @csrf
                                                <input type="file" name="user_icon" id="photoUpload">
                                            </form>
                                        @endif
                                    </div>

                                    {{-- BOTÃO E POP-UP PARA AVATARES PADRÃO --}}
                                    @if (auth()->id() === $user->id)
                                        <div class="relative mt-2" @click.away="defaultIconsOpen = false">
                                            <button @click="defaultIconsOpen = !defaultIconsOpen"
                                                class="flex items-center text-xs text-gray-600 hover:text-red-500 transition-colors duration-200 px-3 py-1 rounded-full bg-gray-50 hover:bg-red-50 border border-gray-200">
                                                <i class="ph ph-users-three text-sm mr-1"></i>
                                                Avatares Padrão
                                            </button>

                                            {{-- Pop-up de Seleção de Avatares --}}
                                            <div x-cloak x-show="defaultIconsOpen"
                                                x-transition:enter="transition ease-out duration-100"
                                                x-transition:enter-start="transform opacity-0 scale-95"
                                                x-transition:enter-end="transform opacity-100 scale-100"
                                                x-transition:leave="transition ease-in duration-75"
                                                x-transition:leave-start="transform opacity-100 scale-100"
                                                x-transition:leave-end="transform opacity-0 scale-95"
                                                class="absolute left-1/2 transform -translate-x-1/2 mt-2 w-52 origin-top-right bg-white rounded-lg shadow-xl ring-1 ring-black ring-opacity-5 p-4 z-10">
                                                <p class="text-xs font-semibold text-gray-700 mb-2 border-b pb-1">
                                                    Selecione um ícone padrão:</p>
                                                <div class="grid grid-cols-4 gap-2">
                                                    @php
                                                        $defaultIcons = [
                                                            'avatar_default_1.svg',
                                                            'avatar_default_2.svg',
                                                            'avatar_default_3.png',
                                                            'avatar_default_4.png',
                                                        ];
                                                    @endphp

                                                    @foreach ($defaultIcons as $icon)
                                                        <form method="POST"
                                                            action="{{ route('profile.updateDefaultPhoto') }}"
                                                            class="inline-block default-avatar-form">
                                                            @csrf
                                                            <input type="hidden" name="user_icon_default"
                                                                value="{{ $icon }}">
                                                            <button type="submit"
                                                                class="w-full h-full rounded-full border-2 p-1 transition-all duration-150 {{ $user->user_icon_default === $icon ? 'border-red-500 ring-4 ring-red-100' : 'border-gray-200 hover:border-red-300' }}"
                                                                title="Usar {{ $icon }}">
                                                                <img src="{{ asset('imgs/' . $icon) }}"
                                                                    alt="{{ $icon }}"
                                                                    class="w-full h-full rounded-full object-cover">
                                                            </button>
                                                        </form>
                                                    @endforeach
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                </div>

                                {{-- Nome e Tipo do Usuário --}}
                                <div class="flex-1 pb-2">
                                    <h2 class="text-4xl font-extrabold text-gray-900">{{ $user->name }}</h2>
                                    <div class="mt-1">
                                        @php
                                            $userTypeData = [
                                                'coordinator' => [
                                                    'label' => 'Coordenador',
                                                    'color' => 'bg-red-500',
                                                    'icon' => 'ph-chalkboard-teacher',
                                                ],
                                                'user' => [
                                                    'label' => 'Usuário Comum',
                                                    'color' => 'bg-gray-500',
                                                    'icon' => 'ph-user',
                                                ],
                                                'admin' => [
                                                    'label' => 'Administrador',
                                                    'color' => 'bg-red-500',
                                                    'icon' => 'ph-crown',
                                                ],
                                            ];
                                            $type = $userTypeData[$user->user_type] ?? [
                                                'label' => ucfirst($user->user_type),
                                                'color' => 'bg-red-500',
                                                'icon' => 'ph-person',
                                            ];
                                        @endphp
                                        <span
                                            class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold text-white {{ $type['color'] }}">
                                            <i class="ph {{ $type['icon'] }} text-sm mr-1"></i>
                                            {{ $type['label'] }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Seções do Perfil com Abas --}}
                        <div class="px-6 py-4">
                            <div class="border-b border-gray-200">
                                <nav class="-mb-px flex space-x-8" aria-label="Tabs">
                                    {{-- Aba Biografia --}}
                                    <button @click="activeTab = 'biography'"
                                        :class="{ 'border-red-500 text-red-600': activeTab === 'biography', 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': activeTab !== 'biography' }"
                                        class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition-colors duration-200 flex items-center">
                                        <i class="ph ph-notepad text-lg mr-2"></i>
                                        Biografia
                                    </button>
                                    <button @click="activeTab = 'savedEvents'"
                                        :class="{ 'border-red-500 text-red-600': activeTab === 'savedEvents', 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': activeTab !== 'savedEvents' }"
                                        class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition-colors duration-200 flex items-center">
                                        <i class="ph ph-bookmark-simple text-lg mr-2"></i>
                                        Eventos Salvos (<span id="savedEventsCount">{{ $savedEvents->count() }}</span>)
                                    </button>
                                    {{-- Aba Meus Eventos --}}
                                    @if ($user->user_type === 'coordinator')
                                        <button @click="activeTab = 'createdEvents'"
                                            :class="{ 'border-red-500 text-red-600': activeTab === 'createdEvents', 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': activeTab !== 'createdEvents' }"
                                            class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition-colors duration-200 flex items-center">
                                            <i class="ph ph-calendar-plus text-lg mr-2"></i>
                                            Meus Eventos ({{ $createdEvents->count() }})
                                        </button>
                                    @endif
                                </nav>
                            </div>

                            <div class="mt-2">
                                <div x-show="activeTab === 'biography'">
                                    <div class="p-2 py-4" x-data="bioEditor()" @keydown.escape="cancelEdit()">

                                        <h3 class="text-lg font-bold mb-4 text-gray-800 flex items-center pb-2">
                                            <i class="ph ph-info text-xl mr-2"></i> Sobre Mim
                                        </h3>

                                        {{-- Visualização --}}
                                        <div x-show="!editing" @click="startEdit()"
                                            :class="bio ? 'justify-start text-left' : 'justify-center text-center'"
                                            class="cursor-pointer text-sm text-gray-700 min-h-[3rem] whitespace-pre-wrap break-words pb-2 pr-4 pl-4 pt-2 rounded-lg bg-gray-50 hover:bg-gray-100 
        transition-colors duration-200 border border-transparent hover:border-red-200 shadow-sm
        flex items-center">
                                            <span id="bioDisplay"
                                                x-text="bio || 'Clique aqui ou no botão para adicionar uma biografia atraente.'"></span>
                                        </div>

                                        <form x-show="editing" x-transition @submit.prevent="saveBio()"
                                            x-ref="bioForm" action="{{ route('profile.updateBio') }}" <!-- ✅
                                            Adicionado -->
                                            @csrf
                                            <textarea name="bio" rows="5" x-model="bio"
                                                class="w-full border-gray-300 rounded-lg p-3 text-sm focus:ring-red-500 focus:border-red-500 shadow-inner"
                                                placeholder="Escreva sua biografia aqui (máx. 500 caracteres)..." maxlength="500"></textarea>

                                            <div class="mt-3 text-right space-x-2">
                                                <button type="button" @click="cancelEdit()"
                                                    class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-5 py-2 text-sm rounded-lg shadow-lg transition-colors duration-200 flex-inline items-center">
                                                    <i class="ph ph-x text-lg mr-1 relative" style="top: 2px;"></i>
                                                    Cancelar
                                                </button>

                                                <button type="submit"
                                                    class="bg-red-600 hover:bg-red-700 text-white px-5 py-2 text-sm rounded-lg shadow-lg transition-colors duration-200 flex-inline items-center">
                                                    <i class="ph ph-floppy-disk text-lg mr-1 relative"
                                                        style="top: 2px;"></i>
                                                    Salvar Biografia
                                                </button>
                                            </div>

                                        </form>
                                    </div>
                                </div>

                                {{-- CONTEÚDO DA ABA: Eventos Salvos --}}
                                <div x-show="activeTab === 'savedEvents'">
                                    <h3 class="text-lg font-bold mb-4 text-gray-800 flex items-center pt-4 pb-2">
                                        <i class="ph ph-heart-straight text-xl mr-2 text-red-500"></i>
                                        Eventos que você salvou
                                    </h3>

                                    @if ($savedEvents->isEmpty())
                                        <div class="text-center py-10 border border-dashed rounded-lg bg-gray-50">
                                            <i class="ph ph-magnifying-glass text-4xl text-gray-400"></i>
                                            <p class="text-gray-500 text-sm mt-2">
                                                Você ainda não salvou nenhum evento interessante.
                                            </p>
                                            <a href="{{ route('events.index') }}"
                                                class="mt-4 inline-block text-red-600 font-medium hover:text-red-800 transition-colors">
                                                <i class="ph ph-arrow-right text-sm mr-1"></i>
                                                Explore eventos agora!
                                            </a>
                                        </div>
                                    @else
                                        {{-- Container com altura máxima e scroll vertical --}}
                                        <div
                                            class="max-h-[800px] overflow-y-auto grid grid-cols-1 md:grid-cols-2 gap-6">
                                            @foreach ($savedEvents as $event)
                                                <div
                                                    class="bg-white rounded-xl shadow-md border border-gray-100 hover:shadow-xl transition-all duration-300 overflow-hidden relative">

                                                    {{-- Link do evento --}}
                                                    <a href="{{ route('events.show', $event) }}" class="block">
                                                        {{-- Imagem / Placeholder --}}
                                                        <div
                                                            class="h-40 bg-gray-200 flex items-center justify-center overflow-hidden">
                                                            @if ($event->event_image)
                                                                <img src="{{ asset('storage/' . $event->event_image) }}"
                                                                    alt="{{ $event->event_name }}"
                                                                    class="w-full h-full object-cover">
                                                            @else
                                                                <div
                                                                    class="flex flex-col items-center justify-center w-full h-full text-red-500">
                                                                    <i class="ph-bold ph-calendar-blank text-6xl"></i>
                                                                    <p class="mt-2 text-sm">Sem Imagem de Capa</p>
                                                                </div>
                                                            @endif
                                                        </div>

                                                        {{-- Nome do evento --}}
                                                        <div class="px-6 pt-6 pb-0">
                                                            <p
                                                                class="font-bold text-gray-900 line-clamp-2 break-words mb-0">
                                                                {{ $event->event_name }}
                                                            </p>
                                                        </div>

                                                        {{-- Linha divisória + Data e hora --}}
                                                        <div class="px-6 pb-6 mt-0.5">
                                                            @if ($event->event_scheduled_at)
                                                                <p
                                                                    class="flex items-center gap-1 text-gray-500 mt-2 text-sm">
                                                                    <i
                                                                        class="ph-fill ph-clock-clockwise text-red-600 text-base"></i>
                                                                    {{ \Carbon\Carbon::parse($event->event_scheduled_at)->isoFormat('D [de] MMMM [de] YYYY, [às] HH:mm') }}
                                                                </p>
                                                            @endif
                                                        </div>
                                                    </a>

                                                    {{-- Botão de remover dos salvos --}}
                                                    <form action="{{ route('events.unsave', $event) }}"
                                                        method="POST" class="absolute top-2 right-2 unsave-form">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit"
                                                            class="bg-white/80 backdrop-blur-sm p-2 pt-2 pb-0.5 rounded-full text-red-500 hover:text-red-700 shadow-md transition-colors duration-200"
                                                            title="Remover dos Salvos">
                                                            <i class="ph ph-x-circle text-xl"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>

                                {{-- CONTEÚDO DA ABA: Eventos Criados (apenas para coordenadores) --}}
                                @if ($user->user_type === 'coordinator')
                                    <div x-show="activeTab === 'createdEvents'">
                                        <div class="flex items-center justify-between pt-4 pb-6">
                                            <h3 class="text-lg font-bold text-gray-800 flex items-center">
                                                <i class="ph ph-rocket-launch text-xl mr-2 text-red-500"></i> Eventos
                                                que você publicou
                                            </h3>
                                            {{-- Botão Criar Evento --}}
                                            <a href="{{ route('events.create') }}"
                                                class="inline-flex items-center px-4 py-2 bg-red-600 hover:bg-red-700 text-white text-sm font-medium rounded-lg transition-colors">
                                                <i class="ph ph-plus-circle text-sm mr-1"></i> Criar Evento
                                            </a>
                                        </div>

                                        @if ($createdEvents->isEmpty())
                                            <div class="text-center py-10 border border-dashed rounded-lg bg-gray-50">
                                                <i class="ph ph-package text-4xl text-gray-400"></i>
                                                <p class="text-gray-500 text-sm mt-2">Você ainda não criou nenhum
                                                    evento. Está na hora de começar!</p>
                                                <a href="{{ route('events.create') }}"
                                                    class="mt-4 inline-block text-red-600 font-medium hover:text-red-800 transition-colors">
                                                    <i class="ph ph-plus-circle text-sm mr-1"></i> Crie seu primeiro
                                                    evento!
                                                </a>
                                            </div>
                                        @else
                                            <div
                                                class="max-h-[800px] overflow-y-auto grid grid-cols-1 md:grid-cols-2 gap-6">
                                                @foreach ($createdEvents as $event)
                                                    <div
                                                        class="bg-white rounded-xl shadow-md border border-gray-100 hover:shadow-xl transition-all duration-300 overflow-hidden relative">
                                                        {{-- Link do evento --}}
                                                        <a href="{{ route('events.show', $event) }}" class="block">
                                                            {{-- Imagem / Placeholder --}}
                                                            <div
                                                                class="h-40 bg-gray-200 flex items-center justify-center overflow-hidden">
                                                                @if ($event->event_image)
                                                                    <img src="{{ asset('storage/' . $event->event_image) }}"
                                                                        alt="{{ $event->event_name }}"
                                                                        class="w-full h-full object-cover">
                                                                @else
                                                                    <div
                                                                        class="flex flex-col items-center justify-center w-full h-full text-red-500">
                                                                        <i
                                                                            class="ph-bold ph-calendar-blank text-6xl"></i>
                                                                        <p class="mt-2 text-sm">Sem Imagem de Capa</p>
                                                                    </div>
                                                                @endif
                                                            </div>

                                                            {{-- Nome do evento --}}
                                                            <div class="px-6 pt-6 pb-0">
                                                                <p
                                                                    class="font-bold text-gray-900 line-clamp-2 break-words mb-0">
                                                                    {{ $event->event_name }}
                                                                </p>
                                                            </div>

                                                            {{-- Linha divisória + Data e hora --}}
                                                            <div class="px-6 pb-6 mt-0.5">
                                                                @if ($event->event_scheduled_at)
                                                                    <p
                                                                        class="flex items-center gap-1 text-gray-500 mt-2 text-sm">
                                                                        <i
                                                                            class="ph-fill ph-clock-clockwise text-red-600 text-base"></i>
                                                                        {{ \Carbon\Carbon::parse($event->event_scheduled_at)->isoFormat('D [de] MMMM [de] YYYY, [às] HH:mm') }}
                                                                    </p>
                                                                @endif
                                                            </div>
                                                        </a>
                                                    </div>
                                                @endforeach
                                            </div>
                                        @endif
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- Coluna SECUNDÁRIA (Sidebar com Detalhes e Ações Estáticas) --}}
                    <div class="lg:col-span-1 space-y-6">

                        {{-- Card de Informações Básicas --}}
                        <div class="bg-white p-6 rounded-xl shadow-lg border border-gray-100">
                            <h3 class="text-xl font-bold mb-4 text-gray-800 flex items-center">
                                <i class="ph ph-identification-card text-2xl mr-2 text-red-500"></i> Detalhes da Conta
                            </h3>

                            <div class="space-y-4 text-sm text-gray-700">
                                {{-- E-mail (Visível apenas para o próprio usuário) --}}
                                @if (auth()->id() === $user->id)
                                    <div class="flex items-center">
                                        <i class="ph ph-at text-lg w-5 text-red-500 mr-3"></i>
                                        <div class="flex-1">
                                            <p class="font-semibold text-gray-900">E-mail</p>
                                            <p class="truncate">{{ $user->email }}</p>
                                        </div>
                                    </div>
                                @endif

                                {{-- Tipo de Usuário --}}
                                @php
                                    $type = $userTypeData[$user->user_type] ?? [
                                        'label' => ucfirst($user->user_type),
                                        'color' => 'bg-red-500',
                                        'icon' => 'ph-person',
                                    ];
                                @endphp
                                <div class="flex items-center">
                                    <i class="ph ph-user-circle text-lg w-5 text-red-500 mr-3"></i>
                                    <div class="flex-1">
                                        <p class="font-semibold text-gray-900">Nível</p>
                                        <span
                                            class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold text-white {{ $type['color'] }}">
                                            {{ $type['label'] }}
                                        </span>
                                    </div>
                                </div>

                                {{-- CAMPO: Curso Coordenado (Apenas se for Coordenador e houver um curso) --}}
                                @if ($user->user_type === 'coordinator' && $user->coordinated_course_name)
                                    <div class="flex items-start">
                                        <i class="ph ph-chalkboard-teacher text-lg w-5 text-red-500 mr-3 mt-1"></i>
                                        <div class="flex-1">
                                            <p class="font-semibold text-gray-900">Coordenador do Curso</p>
                                            <p class="text-sm font-medium text-gray-700">
                                                {{ $user->coordinated_course_name }}</p>
                                        </div>
                                    </div>
                                @endif

                                {{-- Membro Desde --}}
                                <div class="flex items-center">
                                    <i class="ph ph-calendar-check text-lg w-5 text-red-500 mr-3"></i>
                                    <div class="flex-1">
                                        <p class="font-semibold text-gray-900">Membro desde</p>
                                        <p>{{ $user->created_at->format('d/m/Y') }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Card de Ação --}}
                        @if (auth()->id() === $user->id)
                            <div class="bg-white p-6 rounded-xl shadow-lg border border-gray-100">
                                <h3 class="text-xl font-bold mb-4 text-gray-800 flex items-center">
                                    <i class="ph ph-sign-out text-2xl mr-2 text-red-500"></i> Ações Rápidas
                                </h3>

                                {{-- NOVO BOTÃO: Abrir Modal de Configurações --}}
                                <button @click="settingsModalOpen = true"
                                    class="w-full text-center bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold py-2 rounded-lg shadow-md transition-colors duration-200 flex items-center justify-center mb-3 border border-gray-200">
                                    <i class="ph ph-gear text-lg mr-2"></i>
                                    Configurações da Conta
                                </button>

                                {{-- Botão Logout (Sair) --}}
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit"
                                        class="w-full text-center bg-red-500 hover:bg-red-600 text-white font-semibold py-2 rounded-lg shadow-md transition-colors duration-200 flex items-center justify-center">
                                        <i class="ph ph-sign-out text-lg mr-2"></i>
                                        Sair da Conta
                                    </button>
                                </form>
                            </div>
                        @endif

                    </div>

                </div>
            </div>

            {{-- MODAL DE CONFIGURAÇÕES (LAYOUT DE DUAS COLUNAS COM DIVISORES) --}}
            <div x-cloak x-show="settingsModalOpen" x-transition:enter="ease-out duration-300"
                x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0"
                class="fixed inset-0 z-50 overflow-y-auto bg-gray-900 bg-opacity-75 flex items-start justify-center p-4 sm:p-6 lg:p-8">

                <div x-show="settingsModalOpen" x-transition:enter="ease-out duration-300"
                    x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                    x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                    x-transition:leave="ease-in duration-200"
                    x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                    x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                    class="bg-white rounded-xl shadow-2xl overflow-hidden max-w-6xl w-full mx-auto my-12 transform transition-all p-6 sm:p-8">

                    <div class="flex justify-between items-center pb-4 border-b border-gray-100">
                        <h3 class="text-xl font-extrabold text-gray-900 flex items-center">
                            <i class="ph ph-gear-six text-2xl mr-2 text-red-600"></i> Configurações da Conta
                        </h3>
                        <button @click="settingsModalOpen = false"
                            class="text-gray-400 hover:text-gray-600 transition-colors">
                            <i class="ph ph-x-circle text-2xl"></i>
                        </button>
                    </div>

                    {{-- LAYOUT DE 2 COLUNAS PRINCIPAIS --}}
                    <div class="mt-6 grid grid-cols-1 lg:grid-cols-2 gap-x-8 gap-y-10 relative">

                        {{-- DIVISOR VERTICAL PARA SEPARAR AS COLUNAS (VISÍVEL APENAS EM TELAS GRANDES) --}}
                        <div
                            class="hidden lg:block absolute top-0 bottom-0 left-1/2 w-px bg-gray-200 transform -translate-x-1/2">
                        </div>

                        {{-- COLUNA 1: DADOS BÁSICOS (Informações e Senha) --}}
                        <div class="space-y-10">
                            {{-- Formulário de Informações de Perfil --}}
                            @livewire('profile.update-profile-information-form')

                            {{-- DIVISOR HORIZONTAL ENTRE PERFIL E SENHA --}}
                            <div class="border-t border-gray-100"></div>

                            {{-- Formulário de Atualização de Senha --}}
                            @livewire('profile.update-password-form')
                        </div>

                        {{-- COLUNA 2: SEGURANÇA E SESSÕES (apenas Sair de outros navegadores e Deletar conta) --}}
                        <div class="space-y-10 pt-0 lg:pt-0">

                            {{-- Sessões de Navegador --}}
                            @livewire('profile.logout-other-browser-sessions-form')

                            {{-- DIVISOR HORIZONTAL ENTRE SESSÕES E EXCLUSÃO DE CONTA --}}
                            <div class="border-t border-gray-100"></div>

                            {{-- SEÇÃO DE EXCLUSÃO DE CONTA --}}
                            @if (Laravel\Jetstream\Jetstream::hasAccountDeletionFeatures())
                                @livewire('profile.delete-user-form')
                            @endif
                        </div>
                    </div>
                </div>
                {{-- FIM DO MODAL DE CONFIGURAÇÕES --}}

            </div>
</x-app-layout>

<script>
    const eventsIndexUrl = "{{ route('events.index') }}";
</script>

<script>
    document.addEventListener('DOMContentLoaded', function() {

        // Função genérica de AJAX POST com FormData
        async function ajaxPost(form, successCallback, errorMessage) {
            try {
                const formData = new FormData(form);
                const res = await fetch(form.action, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')
                            .getAttribute('content')
                    }
                });
                if (!res.ok) throw new Error('Erro na requisição.');
                const data = await res.json();
                if (data.success) {
                    successCallback(data);
                } else {
                    alert(data.message || errorMessage);
                }
            } catch (err) {
                console.error(err);
                alert(errorMessage);
            }
        }

        // -------------------------------
        // Avatares padrão
        // -------------------------------
        document.querySelectorAll('.default-avatar-form').forEach(form => {
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                ajaxPost(this, data => {
                    const avatarImg = document.querySelector('img[alt="Avatar"]');
                    if (avatarImg) avatarImg.src = data.avatar_url;

                    // Atualiza classes dos botões
                    document.querySelectorAll('.default-avatar-form button').forEach(
                        btn => {
                            btn.classList.remove('border-red-500', 'ring-4',
                                'ring-red-100');
                            btn.classList.add('border-gray-200',
                                'hover:border-red-300');
                        });
                    this.querySelector('button').classList.add('border-red-500',
                        'ring-4', 'ring-red-100');
                    this.querySelector('button').classList.remove('border-gray-200',
                        'hover:border-red-300');
                }, 'Erro ao atualizar avatar.');
            });
        });

        // -------------------------------
        // Função genérica de validação de imagem
        // -------------------------------
        function validateImage(file) {
            const validTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
            if (!validTypes.includes(file.type)) {
                alert('Formato de imagem inválido! Use JPEG, PNG, GIF ou WEBP.');
                return false;
            }
            return true;
        }

        // -------------------------------
        // Uploads de avatar do usuário
        // -------------------------------
        const photoInput = document.getElementById('photoUpload');
        const photoForm = document.getElementById('photoForm');
        const avatarImg = document.querySelector('img[alt="Avatar"]');
        if (photoInput && photoForm && avatarImg) {
            photoInput.addEventListener('change', () => {
                const file = photoInput.files[0];
                if (!file || !validateImage(file)) {
                    photoInput.value = '';
                    return;
                }
                ajaxPost(photoForm, data => {
                    avatarImg.src = data.avatar_url;
                }, 'Erro ao atualizar foto de perfil.');
            });
        }

        // -------------------------------
        // Upload banner imagem
        // -------------------------------
        const bannerInput = document.getElementById('bannerUpload');
        const bannerForm = document.getElementById('bannerForm');
        const bannerDiv = document.getElementById('userBanner');
        if (bannerInput && bannerForm && bannerDiv) {
            bannerInput.addEventListener('change', () => {
                const file = bannerInput.files[0];
                if (!file || !validateImage(file)) {
                    bannerInput.value = '';
                    return;
                }
                ajaxPost(bannerForm, data => {
                    bannerDiv.style.backgroundImage = `url('${data.banner_url}')`;
                    bannerDiv.style.backgroundColor = '';
                }, 'Erro ao atualizar banner.');
            });
        }

        // -------------------------------
        // Banner cor
        // -------------------------------
        const bannerColorInput = document.getElementById('bannerColor');
        const bannerColorForm = document.getElementById('bannerColorForm');
        if (bannerColorInput && bannerColorForm && bannerDiv) {
            bannerColorInput.addEventListener('change', () => {
                ajaxPost(bannerColorForm, data => {
                    bannerDiv.style.backgroundColor = data.color;
                    bannerDiv.style.backgroundImage = '';
                }, 'Erro ao atualizar cor do banner.');
            });
        }

        document.querySelectorAll('.unsave-form').forEach(form => {
            form.addEventListener('submit', function(e) {
                e.preventDefault();

                const formData = new FormData(form);

                fetch(form.action, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector(
                                'meta[name="csrf-token"]').getAttribute('content'),
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        body: formData
                    })
                    .then(res => res.json())
                    .then(data => {
                        if (data.success) {
                            // Remove o card do evento
                            const eventCard = form.closest('.bg-white.rounded-xl');
                            if (eventCard) eventCard.remove();

                            // Atualiza a contagem de eventos salvos
                            const countEl = document.getElementById('savedEventsCount');
                            if (countEl) {
                                let currentCount = parseInt(countEl.innerText);
                                countEl.innerText = currentCount > 0 ? currentCount - 1 : 0;
                            }

                            // Seleciona o container da aba de eventos salvos
                            const eventsContainer = document.querySelector(
                                '[x-show="activeTab === \'savedEvents\'"]');

                            // Se não houver mais eventos, mostra a mensagem
                            if (eventsContainer && eventsContainer.querySelectorAll(
                                    '.unsave-form').length === 0) {
                                eventsContainer.innerHTML = `
                        <h3 class="text-lg font-bold mb-4 text-gray-800 flex items-center pt-4 pb-2">
                            <i class="ph ph-heart-straight text-xl mr-2 text-red-500"></i> Eventos que você salvou
                        </h3>
                        <div class="text-center py-10 border border-dashed rounded-lg bg-gray-50">
                            <i class="ph ph-magnifying-glass text-4xl text-gray-400"></i>
                            <p class="text-gray-500 text-sm mt-2">Você ainda não salvou nenhum evento interessante.</p>
                            <a href="${eventsIndexUrl}" class="mt-4 inline-block text-red-600 font-medium hover:text-red-800 transition-colors">
                                <i class="ph ph-arrow-right text-sm mr-1"></i> Explore eventos agora!
                            </a>
                        </div>
                    `;
                            }
                        } else {
                            console.error('Erro ao des-salvar:', data.message);
                        }
                    })
                    .catch(err => console.error('Erro na requisição:', err));
            });
        });
    });
</script>

<script>
    function bioEditor() {
        return {
            editing: false,
            bio: @js(old('bio', $user->bio ?? '')),
            original: @js($user->bio ?? ''),

            startEdit() {
                this.editing = true;
            },

            cancelEdit() {
                this.editing = false;
                this.bio = this.original;
            },

            async saveBio() {
                try {
                    const form = this.$refs.bioForm;

                    // Limpa e normaliza o texto
                    const cleanBio = this.bio
                        .replace(/\r\n/g, '\n') // normaliza quebras de linha Windows
                        .replace(/\r/g, '\n') // normaliza CR
                        .replace(/[^\S\n]+/g, ' ') // remove tabs e espaços extras
                        .trim(); // remove espaços no início/fim

                    const formData = new FormData(form);
                    formData.set('bio', cleanBio); // define o valor limpo

                    const res = await fetch(form.action, {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                                'content')
                        }
                    });

                    if (!res.ok) throw new Error('Erro na requisição.');

                    const data = await res.json();

                    if (data.success) {
                        this.bio = data.bio;
                        this.original = data.bio;
                        this.editing = false;

                        // Atualiza a visualização da bio com quebras de linha
                        const bioDisplay = document.getElementById('bioDisplay');
                        if (bioDisplay) bioDisplay.innerHTML = data.bio ? data.bio.replace(/\n/g, '<br>') : '';
                    } else {
                        alert(data.message || 'Erro ao atualizar biografia.');
                    }
                } catch (err) {
                    console.error('Erro ao atualizar biografia:', err);
                    alert('Ocorreu um erro ao atualizar sua biografia. Tente novamente.');
                }
            }
        }
    }
</script>
