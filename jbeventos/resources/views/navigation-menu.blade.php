<div>
    <nav x-data="{ open: false }" class="bg-gray-800 border-b border-gray-700">
        <div class="w-full max-w-[1600px] mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-[10vh] sm:h-[12vh] lg:h-[10vh]">
                {{-- Logo e Links de Navegação --}}
                <div class="flex items-center">
                    {{-- Logo --}}
                    <div class="shrink-0 flex items-center">
                        <a href="{{ route('dashboard') }}">
                            <img src="{{ asset('imgs/logoJb.png') }}" alt="Logo" class="w-[7rem] h-auto mx-auto">
                        </a>
                    </div>

                    {{-- Links de Navegação Principais (Desktop) --}}
                    <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex items-center">
                        {{-- Dashboard (Visível para todos) --}}
                        <x-nav-link href="{{ route('dashboard') }}" :active="request()->routeIs('dashboard')" class="text-white hover:text-red-500">
                            <i class="fa-solid fa-house-chimney mr-2"></i> {{ __('Dashboard') }}
                        </x-nav-link>

                        {{-- Dropdown de Eventos (Apenas para Coordenador) --}}
                        @if (auth()->user()->user_type === 'coordinator')
                            <div class="relative" x-data="{ open: false }" @click.away="open = false">
                                <div class="h-full">
                                    <button @click="open = ! open" class="inline-flex items-center px-1 pt-1 border-b-2 border-transparent text-sm font-medium leading-5 text-white hover:text-red-500 hover:border-red-500 focus:outline-none focus:text-red-500 focus:border-red-500 transition duration-150 ease-in-out">
                                        <i class="fa-solid fa-calendar-days mr-2"></i> {{ __('Eventos') }}
                                        <svg class="ms-2 -me-0.5 h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                            <path x-show="!open" stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                                            <path x-show="open" stroke-linecap="round" stroke-linejoin="round" d="M4.5 15.75l7.5-7.5 7.5 7.5" />
                                        </svg>
                                    </button>
                                </div>
                                <div x-show="open"
                                    x-transition:enter="transition ease-out duration-200"
                                    x-transition:enter-start="opacity-0 scale-95"
                                    x-transition:enter-end="opacity-100 scale-100"
                                    x-transition:leave="transition ease-in duration-75"
                                    x-transition:leave-start="opacity-100 scale-100"
                                    x-transition:leave-end="opacity-0 scale-95"
                                    class="absolute z-50 mt-2 w-48 rounded-md shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none"
                                    style="display: none;">
                                    <div class="py-1 bg-white rounded-md shadow-xs">
                                        <x-dropdown-link href="{{ route('events.index') }}" :active="request()->routeIs('events.index')">
                                            <i class="fa-solid fa-list mr-2"></i> {{ __('Listar Eventos') }}
                                        </x-dropdown-link>
                                        <x-dropdown-link href="{{ route('events.create') }}" :active="request()->routeIs('events.create')">
                                            <i class="fa-solid fa-plus mr-2"></i> {{ __('Criar Evento') }}
                                        </x-dropdown-link>
                                    </div>
                                </div>
                            </div>
                        @else
                            {{-- Link Eventos (para Admin e User) --}}
                            <x-nav-link href="{{ route('events.index') }}" :active="request()->routeIs('events.*')" class="text-white hover:text-red-500">
                                <i class="fa-solid fa-calendar-days mr-2"></i> {{ __('Eventos') }}
                            </x-nav-link>
                        @endif

                        {{-- Dropdown de Cursos (Apenas para Admin) --}}
                        @if (auth()->user()->user_type === 'admin')
                            <div class="relative" x-data="{ open: false }" @click.away="open = false">
                                <div class="h-full">
                                    <button @click="open = ! open" class="inline-flex items-center px-1 pt-1 border-b-2 border-transparent text-sm font-medium leading-5 text-white hover:text-red-500 hover:border-red-500 focus:outline-none focus:text-red-500 focus:border-red-500 transition duration-150 ease-in-out">
                                        <i class="fa-solid fa-graduation-cap mr-2"></i> {{ __('Cursos') }}
                                        <svg class="ms-2 -me-0.5 h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                            <path x-show="!open" stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                                            <path x-show="open" stroke-linecap="round" stroke-linejoin="round" d="M4.5 15.75l7.5-7.5 7.5 7.5" />
                                        </svg>
                                    </button>
                                </div>
                                <div x-show="open"
                                    x-transition:enter="transition ease-out duration-200"
                                    x-transition:enter-start="opacity-0 scale-95"
                                    x-transition:enter-end="opacity-100 scale-100"
                                    x-transition:leave="transition ease-in duration-75"
                                    x-transition:leave-start="opacity-100 scale-100"
                                    x-transition:leave-end="opacity-0 scale-95"
                                    class="absolute z-50 mt-2 w-48 rounded-md shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none"
                                    style="display: none;">
                                    <div class="py-1 bg-white rounded-md shadow-xs">
                                        <x-dropdown-link href="{{ route('courses.index') }}" :active="request()->routeIs('courses.index')">
                                            <i class="fa-solid fa-list mr-2"></i> {{ __('Listar Cursos') }}
                                        </x-dropdown-link>
                                        <x-dropdown-link href="{{ route('courses.create') }}" :active="request()->routeIs('courses.create')">
                                            <i class="fa-solid fa-plus mr-2"></i> {{ __('Criar Curso') }}
                                        </x-dropdown-link>
                                    </div>
                                </div>
                            </div>
                        @else
                            {{-- Link Cursos (para Coordenador e User) --}}
                            <x-nav-link href="{{ route('courses.index') }}" :active="request()->routeIs('courses.*')" class="text-white hover:text-red-500">
                                <i class="fa-solid fa-graduation-cap mr-2"></i> {{ __('Cursos') }}
                            </x-nav-link>
                        @endif

                        {{-- Dropdown de Coordenadores (Visível apenas para Admin) --}}
                        @if (auth()->user()->user_type === 'admin')
                            <div class="relative" x-data="{ open: false }" @click.away="open = false">
                                <div class="h-full">
                                    <button @click="open = ! open" class="inline-flex items-center px-1 pt-1 border-b-2 border-transparent text-sm font-medium leading-5 text-white hover:text-red-500 hover:border-red-500 focus:outline-none focus:text-red-500 focus:border-red-500 transition duration-150 ease-in-out">
                                        <i class="fa-solid fa-user-tie mr-2"></i> {{ __('Coordenadores') }}
                                        <svg class="ms-2 -me-0.5 h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                            <path x-show="!open" stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                                            <path x-show="open" stroke-linecap="round" stroke-linejoin="round" d="M4.5 15.75l7.5-7.5 7.5 7.5" />
                                        </svg>
                                    </button>
                                </div>
                                <div x-show="open"
                                    x-transition:enter="transition ease-out duration-200"
                                    x-transition:enter-start="opacity-0 scale-95"
                                    x-transition:enter-end="opacity-100 scale-100"
                                    x-transition:leave="transition ease-in duration-75"
                                    x-transition:leave-start="opacity-100 scale-100"
                                    x-transition:leave-end="opacity-0 scale-95"
                                    class="absolute z-50 mt-2 w-48 rounded-md shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none"
                                    style="display: none;">
                                    <div class="py-1 bg-white rounded-md shadow-xs">
                                        <x-dropdown-link href="{{ route('coordinators.index') }}" :active="request()->routeIs('coordinators.index')">
                                            <i class="fa-solid fa-list mr-2"></i> {{ __('Listar Coordenadores') }}
                                        </x-dropdown-link>
                                        <x-dropdown-link href="{{ route('coordinators.create') }}" :active="request()->routeIs('coordinators.create')">
                                            <i class="fa-solid fa-plus mr-2"></i> {{ __('Criar Coordenador') }}
                                        </x-dropdown-link>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Menus do Lado Direito: Conversas e Perfil --}}
                <div class="flex items-center space-x-2 sm:ms-6">
                    {{-- Dropdown de Conversas/Mensagens --}}
                    <div class="hidden sm:block">
                        <x-dropdown align="right" width="80">
                            <x-slot name="trigger">
                                <button class="relative flex items-center p-2 rounded-full text-white hover:bg-gray-700 transition">
                                    <i class="fa-solid fa-comments text-lg"></i>
                                    @livewire('unread-messages')
                                </button>
                            </x-slot>
                            <x-slot name="content">
                                <div class="bg-white rounded-xl shadow-xl border border-gray-100 overflow-hidden">
                                    <div class="p-3 border-b border-gray-100 flex items-center justify-center">
                                        <h2 class="text-sm font-semibold text-gray-700">Suas Conversas</h2>
                                    </div>
                                    @livewire('conversation-list')
                                </div>
                            </x-slot>
                        </x-dropdown>
                    </div>

                    {{-- Dropdown de Perfil --}}
                    <div class="ms-3 relative">
                        <x-dropdown align="right" width="48">
                            <x-slot name="trigger">
                                @if (Laravel\Jetstream\Jetstream::managesProfilePhotos())
                                <button class="flex text-sm border-2 border-transparent rounded-full focus:outline-none focus:border-gray-300 transition">
                                    <img class="size-8 rounded-full object-cover" src="{{ Auth::user()->profile_photo_url }}" alt="{{ Auth::user()->name }}" />
                                </button>
                                @else
                                <span class="inline-flex rounded-md">
                                    <button type="button" class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none transition">
                                        {{ Auth::user()->name }}
                                        <svg class="ms-2 -me-0.5 size-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 10.94l3.71-3.71a.75.75 0 111.06 1.06l-4.24 4.25a.75.75 0 01-1.06 0L5.21 8.27a.75.75 0 01.02-1.06z" clip-rule="evenodd" />
                                        </svg>
                                    </button>
                                </span>
                                @endif
                            </x-slot>

                            <x-slot name="content">
                                <div class="block px-4 py-2 text-xs text-gray-400">
                                    {{ __('Gerenciar Conta') }}
                                </div>
                                <x-dropdown-link href="{{ route('profile.show') }}">
                                    <i class="fa-solid fa-user mr-2"></i> {{ __('Perfil') }}
                                </x-dropdown-link>
                                <x-dropdown-link href="{{ route('settings') }}">
                                    <i class="fa-solid fa-gear mr-2"></i> {{ __('Configurações') }}
                                </x-dropdown-link>
                                @if (Laravel\Jetstream\Jetstream::hasApiFeatures())
                                <x-dropdown-link href="{{ route('api-tokens.index') }}">
                                    <i class="fa-solid fa-key mr-2"></i> {{ __('API Tokens') }}
                                </x-dropdown-link>
                                @endif
                                <div class="border-t border-gray-200 dark:border-gray-600"></div>
                                <form method="POST" action="{{ route('logout') }}" x-data>
                                    @csrf
                                    <x-dropdown-link href="{{ route('logout') }}" @click.prevent="$root.submit();">
                                        <i class="fa-solid fa-right-from-bracket mr-2"></i> {{ __('Sair') }}
                                    </x-dropdown-link>
                                </form>
                            </x-slot>
                        </x-dropdown>
                    </div>
                </div>

                {{-- Hamburger (Mobile) --}}
                <div class="-me-2 flex items-center sm:hidden">
                    <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none">
                        <svg class="size-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                            <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                            <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        {{-- Menu Mobile --}}
        <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden">
            {{-- Links de Navegação Mobile --}}
            <div class="pt-2 pb-3 space-y-1">
                <x-responsive-nav-link href="{{ route('dashboard') }}" :active="request()->routeIs('dashboard')">
                    <i class="fa-solid fa-house-chimney mr-2"></i> {{ __('Dashboard') }}
                </x-responsive-nav-link>

                {{-- Links de Eventos Mobile --}}
                @if (auth()->user()->user_type === 'coordinator')
                    <div class="border-t border-gray-200 pt-2">
                        <div class="block px-4 text-xs text-gray-400">
                            {{ __('Eventos') }}
                        </div>
                        <x-responsive-nav-link href="{{ route('events.index') }}" :active="request()->routeIs('events.index')">
                            <i class="fa-solid fa-list mr-2"></i> {{ __('Listar Eventos') }}
                        </x-responsive-nav-link>
                        <x-responsive-nav-link href="{{ route('events.create') }}" :active="request()->routeIs('events.create')">
                            <i class="fa-solid fa-plus mr-2"></i> {{ __('Criar Evento') }}
                        </x-responsive-nav-link>
                    </div>
                @else
                    <x-responsive-nav-link href="{{ route('events.index') }}" :active="request()->routeIs('events.*')">
                        <i class="fa-solid fa-calendar-days mr-2"></i> {{ __('Eventos') }}
                    </x-responsive-nav-link>
                @endif


                {{-- Links de Cursos Mobile --}}
                @if (auth()->user()->user_type === 'admin')
                    <div class="border-t border-gray-200 pt-2">
                        <div class="block px-4 text-xs text-gray-400">
                            {{ __('Cursos') }}
                        </div>
                        <x-responsive-nav-link href="{{ route('courses.index') }}" :active="request()->routeIs('courses.index')">
                            <i class="fa-solid fa-list mr-2"></i> {{ __('Listar Cursos') }}
                        </x-responsive-nav-link>
                        <x-responsive-nav-link href="{{ route('courses.create') }}" :active="request()->routeIs('courses.create')">
                            <i class="fa-solid fa-plus mr-2"></i> {{ __('Criar Curso') }}
                        </x-responsive-nav-link>
                    </div>
                @else
                    <x-responsive-nav-link href="{{ route('courses.index') }}" :active="request()->routeIs('courses.index')">
                        <i class="fa-solid fa-graduation-cap mr-2"></i> {{ __('Cursos') }}
                    </x-responsive-nav-link>
                @endif


                {{-- Dropdown de Coordenadores (Mobile) --}}
                @if (auth()->user()->user_type === 'admin')
                    <div class="border-t border-gray-200 pt-2">
                        <div class="block px-4 text-xs text-gray-400">
                            {{ __('Coordenadores') }}
                        </div>
                        <x-responsive-nav-link href="{{ route('coordinators.index') }}" :active="request()->routeIs('coordinators.index')">
                            <i class="fa-solid fa-list mr-2"></i> {{ __('Listar Coordenadores') }}
                        </x-responsive-nav-link>
                        <x-responsive-nav-link href="{{ route('coordinators.create') }}" :active="request()->routeIs('coordinators.create')">
                            <i class="fa-solid fa-plus mr-2"></i> {{ __('Criar Coordenador') }}
                        </x-responsive-nav-link>
                    </div>
                @endif
            </div>

            {{-- Conta do Usuário Mobile --}}
            <div class="pt-4 pb-1 border-t border-gray-200">
                <div class="flex items-center px-4">
                    @if (Laravel\Jetstream\Jetstream::managesProfilePhotos())
                    <div class="shrink-0 me-3">
                        <img class="size-10 rounded-full object-cover" src="{{ Auth::user()->profile_photo_url }}" alt="{{ Auth::user()->name }}" />
                    </div>
                    @endif
                    <div>
                        <div class="font-medium text-base text-gray-800">{{ Auth::user()->name }}</div>
                        <div class="font-medium text-sm text-gray-500">{{ Auth::user()->email }}</div>
                    </div>
                </div>

                <div class="mt-3 space-y-1">
                    <x-responsive-nav-link href="{{ route('profile.show') }}" :active="request()->routeIs('profile.show')">
                        <i class="fa-solid fa-user mr-2"></i> {{ __('Perfil') }}
                    </x-responsive-nav-link>
                    <x-responsive-nav-link href="{{ route('settings') }}" :active="request()->routeIs('settings')">
                        <i class="fa-solid fa-gear mr-2"></i> {{ __('Configurações') }}
                    </x-responsive-nav-link>
                    <form method="POST" action="{{ route('logout') }}" x-data>
                        @csrf
                        <x-responsive-nav-link href="{{ route('logout') }}" @click.prevent="$root.submit();">
                            <i class="fa-solid fa-right-from-bracket mr-2"></i> {{ __('Sair') }}
                        </x-responsive-nav-link>
                    </form>
                </div>
            </div>
        </div>
    </nav>
</div>