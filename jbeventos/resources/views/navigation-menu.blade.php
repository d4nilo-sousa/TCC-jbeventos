<div>
    {{-- NAV BAR MODERNA E FIXA NO TOPO --}}
    <nav x-data="{ open: false }"
        class="bg-white shadow-xl border-b border-gray-100/50 fixed w-full z-50 top-0 rounded-b-xl">
        <div class="w-full max-w-[1600px] mx-auto px-4 sm:px-6 lg:px-8">
            {{-- Altura da navbar ajustada para um visual mais premium --}}
            <div class="flex justify-between items-center h-[60px] lg:h-[70px]">

                {{-- 1. Logo e Links de Navegação (Esquerda/Centro) --}}
                <div class="flex items-center space-x-6 h-full">
                    {{-- Logo --}}
                    <div class="shrink-0 flex items-center h-full">
                        <a href="{{ route('feed.index') }}" class="flex items-center h-full mb-2 m">
                            <img src="{{ asset('imgs/logoJb.png') }}" alt="Logo" class="w-[6rem] h-auto">
                        </a>
                    </div>

                    {{-- Links de Navegação Principais (Desktop) --}}
                    <div class="hidden space-x-2 lg:-my-px lg:ms-6 lg:flex items-center h-full">

                        {{-- Feed --}}
                        <x-nav-link href="{{ route('feed.index') }}" :active="request()->routeIs('feed.index')"
                            class="group text-base text-gray-700 transition duration-150 ease-in-out p-2 rounded-lg 
                                {{ request()->routeIs('feed.index') ? 'bg-red-50 text-red-600 font-bold shadow-sm' : 'hover:bg-gray-50' }}">
                            <i
                                class="ph-fill ph-house-simple text-lg 
                                {{ request()->routeIs('feed.index') ? 'text-red-600' : 'text-gray-700 group-hover:text-red-600' }}"></i>
                            <span
                                class="hidden sm:inline ms-2 
                                {{ request()->routeIs('feed.index') ? 'text-red-600' : 'text-gray-700 group-hover:text-red-600' }}">
                                {{ __('Feed') }}
                            </span>
                        </x-nav-link>


                        {{-- Dashboard --}}
                        <x-nav-link href="{{ route('dashboard') }}" :active="request()->routeIs('dashboard*')"
                            class="group text-base text-gray-700 transition duration-150 ease-in-out p-2 rounded-lg 
                                {{ request()->routeIs('dashboard') ? 'bg-red-50 text-red-600 font-bold shadow-sm' : 'hover:bg-gray-50' }}">
                            <i
                                class="ph-fill ph-gauge text-lg
                                {{ request()->routeIs('dashboard') ? 'text-red-600' : 'text-gray-700 group-hover:text-red-600' }}"></i>
                            <span
                                class="hidden sm:inline ms-2
                                {{ request()->routeIs('dashboard') ? 'text-red-600' : 'text-gray-700 group-hover:text-red-600' }}">
                                {{ __('Dashboard') }}
                            </span>
                        </x-nav-link>


                        {{-- Explorar --}}
                        <x-nav-link href="{{ route('explore.index') }}" :active="request()->routeIs('explore.*')"
                            class="group text-base text-gray-700 transition duration-150 ease-in-out p-2 rounded-lg 
        {{ request()->routeIs('explore.*') ? 'bg-red-50 text-red-600 font-bold shadow-sm' : 'hover:bg-gray-50' }}">
                            <i
                                class="ph-fill ph-magnifying-glass text-lg
        {{ request()->routeIs('explore.*') ? 'text-red-600' : 'text-gray-700 group-hover:text-red-600' }}"></i>
                            <span
                                class="hidden sm:inline ms-2
        {{ request()->routeIs('explore.*') ? 'text-red-600' : 'text-gray-700 group-hover:text-red-600' }}">
                                {{ __('Explorar') }}
                            </span>
                        </x-nav-link>

                        {{-- Dropdown de Eventos (Coordenador) --}}
                        @if (in_array(auth()->user()->user_type, ['coordinator']))
                            <div class="relative flex items-center h-full" x-data="{ open: false }"
                                @click.away="open = false">
                                <button @click="open = ! open"
                                    class="inline-flex items-center p-2 rounded-lg text-base font-medium leading-5 text-gray-700 hover:text-red-600 focus:outline-none transition duration-150 ease-in-out
                                   {{-- ADICIONE A CLASSE 'group' AQUI --}}
                                   group
                                   {{ request()->routeIs('events.*') ? 'bg-red-50 text-red-600 font-bold shadow-sm' : 'hover:bg-gray-50' }}">
                                    <i
                                        class="ph-fill ph-calendar-check text-lg
                                   {{ request()->routeIs('events.*') ? 'text-red-600' : 'text-gray-700' }} group-hover:text-red-600"></i>
                                    <span
                                        class="hidden sm:inline ms-2
                                   {{ request()->routeIs('events.*') ? 'text-red-600' : 'text-gray-700' }} group-hover:text-red-600">
                                        {{ __('Eventos') }}
                                    </span>
                                    {{-- ... restante do código do botão ... --}}
                                    <svg class="ms-2 h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none"
                                        viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path x-show="!open" stroke-linecap="round" stroke-linejoin="round"
                                            d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                                        <path x-show="open" stroke-linecap="round" stroke-linejoin="round"
                                            d="M4.5 15.75l7.5-7.5 7.5 7.5" />
                                    </svg>
                                </button>

                                {{-- Conteúdo do Dropdown --}}
                                <div x-show="open" x-transition:enter="transition ease-out duration-200"
                                    x-transition:enter-start="opacity-0 scale-95"
                                    x-transition:enter-end="opacity-100 scale-100"
                                    x-transition:leave="transition ease-in duration-75"
                                    x-transition:leave-start="opacity-100 scale-100"
                                    x-transition:leave-end="opacity-0 scale-95"
                                    class="absolute z-50 mt-2 w-48 rounded-xl shadow-xl ring-1 ring-black/5 top-full bg-white overflow-hidden"
                                    style="display: none;">
                                    <div class="py-1">
                                        <x-dropdown-link href="{{ route('events.index') }}" :active="request()->routeIs('events.index')"
                                            class="group flex items-center px-4 py-2 text-gray-700 transition duration-150 ease-in-out rounded-lg
                        {{ request()->routeIs('events.index') ? 'bg-red-50 text-red-600 font-bold' : 'hover:bg-red-50' }}">
                                            <i
                                                class="ph-duotone ph-list-checks mr-2 text-gray-700
                        {{ request()->routeIs('events.index') ? 'text-red-600' : '' }} group-hover:text-red-600"></i>
                                            <span
                                                class="text-gray-700
                        {{ request()->routeIs('events.index') ? 'text-red-600' : '' }} group-hover:text-red-600">
                                                {{ __('Listar Eventos') }}
                                            </span>
                                        </x-dropdown-link>

                                        <x-dropdown-link href="{{ route('events.create') }}" :active="request()->routeIs('events.create')"
                                            class="group flex items-center px-4 py-2 text-gray-700 transition duration-150 ease-in-out rounded-lg
                        {{ request()->routeIs('events.create') ? 'bg-red-50 text-red-600 font-bold' : 'hover:bg-red-50' }}">
                                            <i
                                                class="ph-duotone ph-calendar-plus mr-2 text-gray-700
                        {{ request()->routeIs('events.create') ? 'text-red-600' : '' }} group-hover:text-red-600"></i>
                                            <span
                                                class="text-gray-700
                        {{ request()->routeIs('events.create') ? 'text-red-600' : '' }} group-hover:text-red-600">
                                                {{ __('Criar Evento') }}
                                            </span>
                                        </x-dropdown-link>
                                    </div>
                                </div>
                            </div>
                        @else
                            {{-- Link Eventos (User e Admin) --}}
                            <x-nav-link href="{{ route('events.index') }}" :active="request()->routeIs('events.*')"
                                class="group text-base text-gray-700 transition duration-150 ease-in-out p-2 rounded-lg
            {{ request()->routeIs('events.*') ? 'bg-red-50 text-red-600 font-bold shadow-sm' : 'hover:bg-gray-50' }}">
                                <i
                                    class="ph-fill ph-calendar-check text-lg text-gray-700
            {{ request()->routeIs('events.*') ? 'text-red-600' : '' }} group-hover:text-red-600"></i>
                                <span
                                    class="hidden sm:inline ms-2 text-gray-700
            {{ request()->routeIs('events.*') ? 'text-red-600' : '' }} group-hover:text-red-600">
                                    {{ __('Eventos') }}
                                </span>
                            </x-nav-link>
                        @endif

                        {{-- Dropdown de Cursos (Admin) --}}
                        @if (auth()->user()->user_type === 'admin')
                            <div class="relative flex items-center h-full" x-data="{ open: false }"
                                @click.away="open = false">
                                <button @click="open = ! open"
                                    class="inline-flex items-center p-2 rounded-lg text-base font-medium leading-5 text-gray-700 hover:text-red-600 focus:outline-none transition duration-150 ease-in-out
                                    {{-- Adicione a classe 'group' aqui --}}
                                    group
                                    {{ request()->routeIs('courses.*') ? 'bg-red-50 text-red-600 font-bold shadow-sm' : 'hover:bg-gray-50' }}">
                                    <i
                                        class="ph-fill ph-book-open text-lg
                                    {{ request()->routeIs('courses.*') ? 'text-red-600' : 'text-gray-700' }} group-hover:text-red-600"></i>
                                    <span
                                        class="hidden sm:inline ms-2
                                    {{ request()->routeIs('courses.*') ? 'text-red-600' : 'text-gray-700' }} group-hover:text-red-600">
                                        {{ __('Cursos') }}
                                    </span>
                                    <svg class="ms-2 h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none"
                                        viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path x-show="!open" stroke-linecap="round" stroke-linejoin="round"
                                            d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                                        <path x-show="open" stroke-linecap="round" stroke-linejoin="round"
                                            d="M4.5 15.75l7.5-7.5 7.5 7.5" />
                                    </svg>
                                </button>

                                {{-- Conteúdo do Dropdown --}}
                                <div x-show="open" x-transition:enter="transition ease-out duration-200"
                                    x-transition:enter-start="opacity-0 scale-95"
                                    x-transition:enter-end="opacity-100 scale-100"
                                    x-transition:leave="transition ease-in duration-75"
                                    x-transition:leave-start="opacity-100 scale-100"
                                    x-transition:leave-end="opacity-0 scale-95"
                                    class="absolute z-50 mt-2 w-48 rounded-xl shadow-xl ring-1 ring-black/5 top-full bg-white overflow-hidden"
                                    style="display: none;">
                                    <div class="py-1">
                                        <x-dropdown-link href="{{ route('courses.index') }}" :active="request()->routeIs('courses.index')"
                                            class="group flex items-center px-4 py-2 text-gray-700 transition duration-150 ease-in-out rounded-lg
                        {{ request()->routeIs('courses.index') ? 'bg-red-50 text-red-600 font-bold' : 'hover:bg-red-50' }}">
                                            <i
                                                class="ph-duotone ph-list-dashes mr-2 text-gray-700
                        {{ request()->routeIs('courses.index') ? 'text-red-600' : '' }} group-hover:text-red-600"></i>
                                            <span
                                                class="text-gray-700
                        {{ request()->routeIs('courses.index') ? 'text-red-600' : '' }} group-hover:text-red-600">
                                                {{ __('Listar Cursos') }}
                                            </span>
                                        </x-dropdown-link>

                                        <x-dropdown-link href="{{ route('courses.create') }}" :active="request()->routeIs('courses.create')"
                                            class="group flex items-center px-4 py-2 text-gray-700 transition duration-150 ease-in-out rounded-lg
                        {{ request()->routeIs('courses.create') ? 'bg-red-50 text-red-600 font-bold' : 'hover:bg-red-50' }}">
                                            <i
                                                class="ph-duotone ph-folder-plus mr-2 text-gray-700
                        {{ request()->routeIs('courses.create') ? 'text-red-600' : '' }} group-hover:text-red-600"></i>
                                            <span
                                                class="text-gray-700
                        {{ request()->routeIs('courses.create') ? 'text-red-600' : '' }} group-hover:text-red-600">
                                                {{ __('Criar Curso') }}
                                            </span>
                                        </x-dropdown-link>
                                    </div>
                                </div>
                            </div>
                        @else
                            {{-- Link Cursos (Coordenador e User) --}}
                            <x-nav-link href="{{ route('courses.index') }}" :active="request()->routeIs('courses.*')"
                                class="group text-base text-gray-700 transition duration-150 ease-in-out p-2 rounded-lg
            {{ request()->routeIs('courses.*') ? 'bg-red-50 text-red-600 font-bold shadow-sm' : 'hover:bg-gray-50' }}">
                                <i
                                    class="ph-fill ph-book-open text-lg text-gray-700
            {{ request()->routeIs('courses.*') ? 'text-red-600' : '' }} group-hover:text-red-600"></i>
                                <span
                                    class="hidden sm:inline ms-2 text-gray-700
            {{ request()->routeIs('courses.*') ? 'text-red-600' : '' }} group-hover:text-red-600">
                                    {{ __('Cursos') }}
                                </span>
                            </x-nav-link>
                        @endif

                        @if (auth()->user()->user_type === 'admin')
                            <div class="relative flex items-center h-full" x-data="{ open: false }"
                                @click.away="open = false">
                                <button @click="open = ! open"
                                    class="inline-flex items-center p-2 rounded-lg text-base font-medium leading-5 text-gray-700 hover:text-red-600 focus:outline-none transition duration-150 ease-in-out
            {{-- ADICIONADO: Classe 'group' para habilitar o group-hover nos filhos --}}
            group 
            {{ request()->routeIs('coordinators.*') ? 'bg-red-50 text-red-600 font-bold shadow-sm' : 'hover:bg-gray-50' }}">
                                    <i
                                        class="ph-fill ph-shield-star text-lg
            {{-- CORRIGIDO: Agora usa group-hover:text-red-600 --}}
            {{ request()->routeIs('coordinators.*') ? 'text-red-600' : 'text-gray-700' }} group-hover:text-red-600"></i>
                                    <span
                                        class="hidden sm:inline ms-2 
            {{-- ADICIONADO: Classe group-hover:text-red-600 e cor base --}}
            {{ request()->routeIs('coordinators.*') ? 'text-red-600 font-bold' : 'text-gray-700' }} group-hover:text-red-600">
                                        {{ __('Coordenadores') }}
                                    </span>
                                    <svg class="ms-2 h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none"
                                        viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path x-show="!open" stroke-linecap="round" stroke-linejoin="round"
                                            d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                                        <path x-show="open" stroke-linecap="round" stroke-linejoin="round"
                                            d="M4.5 15.75l7.5-7.5 7.5 7.5" />
                                    </svg>
                                </button>
                                {{-- Conteúdo do Dropdown (MANTIDO) --}}
                                <div x-show="open" x-transition:enter="transition ease-out duration-200"
                                    x-transition:enter-start="opacity-0 scale-95"
                                    x-transition:enter-end="opacity-100 scale-100"
                                    x-transition:leave="transition ease-in duration-75"
                                    x-transition:leave-start="opacity-100 scale-100"
                                    x-transition:leave-end="opacity-0 scale-95"
                                    class="absolute z-50 mt-2 w-48 rounded-xl shadow-xl ring-1 ring-black/5 top-full bg-white overflow-hidden"
                                    style="display: none;">
                                    <div class="py-1">
                                        <x-dropdown-link href="{{ route('coordinators.index') }}" :active="request()->routeIs('coordinators.index')"
                                            class="group flex items-center px-4 py-2 text-gray-700 transition duration-150 ease-in-out rounded-lg
        {{ request()->routeIs('coordinators.index') ? 'bg-red-50 text-red-600 font-bold shadow-sm' : 'hover:bg-gray-50' }}">

                                            <i
                                                class="ph-duotone ph-users mr-2 text-gray-700
        {{ request()->routeIs('coordinators.index') ? 'text-red-600' : '' }} group-hover:text-red-600"></i>

                                            <span
                                                class="text-gray-700
        {{ request()->routeIs('coordinators.index') ? 'text-red-600' : '' }} group-hover:text-red-600">
                                                {{ __('Listar Coordenadores') }}
                                            </span>
                                        </x-dropdown-link>
                                        <x-dropdown-link href="{{ route('coordinators.create') }}" :active="request()->routeIs('coordinators.create')"
                                            class="group flex items-center px-4 py-2 text-gray-700 transition duration-150 ease-in-out rounded-lg
        {{ request()->routeIs('coordinators.create') ? 'bg-red-50 text-red-600 font-bold shadow-sm' : 'hover:bg-gray-50' }}">

                                            <i
                                                class="ph-duotone ph-user-plus mr-2 text-gray-700
        {{ request()->routeIs('coordinators.create') ? 'text-red-600' : '' }} group-hover:text-red-600"></i>

                                            <span
                                                class="text-gray-700
        {{ request()->routeIs('coordinators.create') ? 'text-red-600' : '' }} group-hover:text-red-600">
                                                {{ __('Criar Coordenador') }}
                                            </span>
                                        </x-dropdown-link>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- 2. Menus do Lado Direito: Conversas, Perfil e Logoff --}}
                <div class="flex items-center space-x-3 sm:ms-6">

                    {{-- Dropdown de Conversas/Mensagens --}}
                    <div class="hidden sm:block" x-data="{ open: false }" @click.away="open = false"
                        @keydown.escape.window="open = false">
                        <button @click="open = !open" :class="open ? 'ring-2 ring-red-500 shadow-sm' : ''"
                            class="relative flex items-center size-9 rounded-full justify-center text-gray-700 bg-gray-100 hover:bg-gray-200 transition">
                            <i class="ph-fill ph-chat-text text-lg"></i>

                            {{-- Badge de mensagens não lidas --}}
                            @livewire('unread-messages')
                        </button>

                        {{-- Conteúdo do dropdown --}}
                        <div x-show="open" x-transition:enter="transition ease-out duration-200"
                            x-transition:enter-start="opacity-0 scale-95"
                            x-transition:enter-end="opacity-100 scale-100"
                            x-transition:leave="transition ease-in duration-150"
                            x-transition:leave-start="opacity-100 scale-100"
                            x-transition:leave-end="opacity-0 scale-95"
                            class="absolute right-0 mt-2 !w-[600px] min-w-[600px] max-w-[700px] bg-white rounded-2xl border border-gray-200 shadow-2xl p-2 overflow-hidden z-50"
                            style="transform: translateX(-4rem); transform-origin: top right;">
                            @livewire('conversation-list')
                        </div>
                    </div>


                    <div class="hidden sm:block relative" x-data="profileIconUpdater()" x-init="init()"
                        x-ref="profileDiv">
                        <a href="{{ route('profile.show') }}" class="block" @mouseenter="profileTooltip = true"
                            @mouseleave="profileTooltip = false" @focus="profileTooltip = true"
                            @blur="profileTooltip = false">

                            <img :src="userIcon" alt="{{ Auth::user()->name }}"
                                class="size-9 rounded-full object-cover transition shadow-md
                {{ request()->routeIs('profile.show')
                    ? 'border-2 border-red-500'
                    : 'border-2 border-gray-200 hover:border-red-500' }}">
                        </a>

                        <!-- Tooltip/Popover com Nome e Email -->
                        <div x-cloak x-show="profileTooltip" x-transition:enter="transition ease-out duration-200"
                            x-transition:enter-start="opacity-0 scale-95 transform"
                            x-transition:enter-end="opacity-100 scale-100 transform"
                            x-transition:leave="transition ease-in duration-75"
                            x-transition:leave-start="opacity-100 scale-100 transform"
                            x-transition:leave-end="opacity-0 scale-95 transform"
                            class="absolute right-0 mt-3 w-48 bg-white p-3 rounded-xl shadow-2xl border border-gray-100 z-50 pointer-events-none"
                            style="min-width: max-content;">

                            <div class="text-sm font-bold text-gray-800 truncate">{{ Auth::user()->name }}</div>
                            <div class="text-xs text-gray-500 truncate">{{ Auth::user()->email }}</div>
                        </div>
                    </div>

                    {{-- Botão de Logoff Direto (Desktop) --}}
                    <div class="hidden sm:block">
                        <form method="POST" action="{{ route('logout') }}" x-data>
                            @csrf
                            <button type="submit"
                                class="flex items-center size-9 rounded-full justify-center text-red-500 bg-red-50 hover:bg-red-100 focus:outline-none transition shadow-md">
                                {{-- Ícone de Sair --}}
                                <i class="ph-fill ph-sign-out text-lg"></i>
                            </button>
                        </form>
                    </div>

                </div>

                {{-- Hamburger (Mobile) (MANTIDO) --}}
                <div class="-me-2 flex items-center sm:hidden">
                    <button @click="open = ! open"
                        class="inline-flex items-center justify-center p-2 rounded-md text-gray-500 hover:text-gray-700 hover:bg-gray-100 focus:outline-none transition">
                        <svg class="size-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                            <path :class="{ 'hidden': open, 'inline-flex': !open }" class="inline-flex"
                                stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 6h16M4 12h16M4 18h16" />
                            <path :class="{ 'hidden': !open, 'inline-flex': open }" class="hidden"
                                stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        {{-- Menu Mobile --}}
        <div :class="{ 'block': open, 'hidden': !open }" class="hidden sm:hidden bg-white shadow-xl rounded-b-xl">

            {{-- Links de Navegação Mobile --}}
            <div class="pt-2 pb-3 space-y-1">

                <x-responsive-nav-link href="{{ route('feed.index') }}" :active="request()->routeIs('feed.index')"
                    class="text-gray-700 hover:bg-red-50 hover:text-red-600">
                    <i class="ph-duotone ph-house-simple mr-3 w-5 text-center"></i> {{ __('Feed') }}
                </x-responsive-nav-link>

                <x-responsive-nav-link href="{{ route('dashboard') }}" :active="request()->routeIs('dashboard')"
                    class="text-gray-700 hover:bg-red-50 hover:text-red-600">
                    <i class="ph-duotone ph-gauge mr-3 w-5 text-center"></i> {{ __('Dashboard') }}
                </x-responsive-nav-link>

                <x-responsive-nav-link href="{{ route('explore.index') }}" :active="request()->routeIs('explore.index')"
                    class="text-gray-700 hover:bg-red-50 hover:text-red-600">
                    <i class="ph-duotone ph-magnifying-glass mr-3 w-5 text-center"></i> {{ __('Explorar') }}
                </x-responsive-nav-link>

                {{-- Links de Eventos Mobile --}}
                @if (in_array(auth()->user()->user_type, ['admin', 'coordinator']))
                    <div
                        class="block px-4 pt-4 pb-2 text-xs text-gray-400 font-semibold border-t border-gray-100 mt-2">
                        {{ __('Gerenciamento de Eventos') }}</div>
                    <x-responsive-nav-link href="{{ route('events.index') }}" :active="request()->routeIs('events.index')">
                        <i class="ph-duotone ph-list-checks mr-3 w-5 text-center"></i> {{ __('Listar Eventos') }}
                    </x-responsive-nav-link>
                    <x-responsive-nav-link href="{{ route('events.create') }}" :active="request()->routeIs('events.create')">
                        <i class="ph-duotone ph-calendar-plus mr-3 w-5 text-center"></i> {{ __('Criar Evento') }}
                    </x-responsive-nav-link>
                @else
                    <x-responsive-nav-link href="{{ route('events.index') }}" :active="request()->routeIs('events.index')"
                        class="text-gray-700 hover:bg-red-50 hover:text-red-600">
                        <i class="ph-duotone ph-calendar-check mr-3 w-5 text-center"></i> {{ __('Eventos') }}
                    </x-responsive-nav-link>
                @endif


                {{-- Links de Cursos Mobile --}}
                @if (auth()->user()->user_type === 'admin')
                    <div
                        class="block px-4 pt-4 pb-2 text-xs text-gray-400 font-semibold border-t border-gray-100 mt-2">
                        {{ __('Gerenciamento de Cursos') }}</div>
                    <x-responsive-nav-link href="{{ route('courses.index') }}" :active="request()->routeIs('courses.index')">
                        <i class="ph-duotone ph-list-dashes mr-3 w-5 text-center"></i> {{ __('Listar Cursos') }}
                    </x-responsive-nav-link>
                    <x-responsive-nav-link href="{{ route('courses.create') }}" :active="request()->routeIs('courses.create')">
                        <i class="ph-duotone ph-folder-plus mr-3 w-5 text-center"></i> {{ __('Criar Curso') }}
                    </x-responsive-nav-link>
                @else
                    <x-responsive-nav-link href="{{ route('courses.index') }}" :active="request()->routeIs('courses.index')"
                        class="text-gray-700 hover:bg-red-50 hover:text-red-600">
                        <i class="ph-duotone ph-book-open mr-3 w-5 text-center"></i> {{ __('Cursos') }}
                    </x-responsive-nav-link>
                @endif


                {{-- Dropdown de Coordenadores (Mobile) --}}
                @if (auth()->user()->user_type === 'admin')
                    <div
                        class="block px-4 pt-4 pb-2 text-xs text-gray-400 font-semibold border-t border-gray-100 mt-2">
                        {{ __('Gerenciamento de Coordenadores') }}</div>
                    <x-responsive-nav-link href="{{ route('coordinators.index') }}" :active="request()->routeIs('coordinators.index')">
                        <i class="ph-duotone ph-users mr-3 w-5 text-center"></i> {{ __('Listar Coordenadores') }}
                    </x-responsive-nav-link>
                    <x-responsive-nav-link href="{{ route('coordinators.create') }}" :active="request()->routeIs('coordinators.create')">
                        <i class="ph-duotone ph-user-plus mr-3 w-5 text-center"></i> {{ __('Criar Coordenador') }}
                    </x-responsive-nav-link>
                @endif
            </div>

            {{-- Conta do Usuário Mobile --}}
            <div class="pt-4 pb-3 border-t border-gray-200">
                <div class="flex items-center px-4">
                    @if (Laravel\Jetstream\Jetstream::managesProfilePhotos())
                        <div class="shrink-0 me-3">
                            <img class="size-10 rounded-full object-cover border-2 border-red-500"
                                src="{{ Auth::user()->profile_photo_url }}" alt="{{ Auth::user()->name }}" />
                        </div>
                    @endif
                    <div>
                        <div class="font-bold text-base text-gray-800">{{ Auth::user()->name }}</div>
                        <div class="font-medium text-sm text-gray-500">{{ Auth::user()->email }}</div>
                    </div>
                </div>

                <div class="mt-3 space-y-1">
                    <x-responsive-nav-link href="{{ route('profile.show') }}" :active="request()->routeIs('profile.show')"
                        class="text-gray-700 hover:bg-red-50 hover:text-red-600">
                        <i class="ph-duotone ph-user-circle mr-3 w-5 text-center"></i> {{ __('Perfil') }}
                    </x-responsive-nav-link>

                    {{-- Opcional: API Tokens no Mobile --}}
                    @if (Laravel\Jetstream\Jetstream::hasApiFeatures())
                        <x-responsive-nav-link href="{{ route('api-tokens.index') }}" :active="request()->routeIs('api-tokens.index')"
                            class="text-gray-700 hover:bg-red-50 hover:text-red-600">
                            <i class="ph-duotone ph-key mr-3 w-5 text-center"></i> {{ __('API Tokens') }}
                        </x-responsive-nav-link>
                    @endif

                    <form method="POST" action="{{ route('logout') }}" x-data>
                        @csrf
                        <x-responsive-nav-link href="{{ route('logout') }}" @click.prevent="$root.submit();"
                            class="text-red-500 hover:bg-red-100 font-semibold">
                            <i class="ph-duotone ph-sign-out mr-3 w-5 text-center"></i> {{ __('Sair') }}
                        </x-responsive-nav-link>
                    </form>
                </div>
            </div>
        </div>
    </nav>
</div>

<script>
    function profileIconUpdater() {
        return {
            userIcon: '{{ Auth::user()->user_icon_url }}',
            profileTooltip: false,

            init() {
                window.Echo.channel('user-icon.{{ Auth::id() }}')
                    .listen('UserIconUpdated', (e) => {
                        this.userIcon = e.icon_url;
                    });
            }
        }
    }
</script>
