<div>
    {{-- A navbar agora é fixa no topo, com uma sombra suave para um visual flutuante e moderno --}}
    <nav x-data="{ open: false }" class="bg-white shadow-lg border-b border-gray-100/50 fixed w-full z-50 top-0">
        <div class="w-full max-w-[1600px] mx-auto px-4 sm:px-6 lg:px-8">
            {{-- Altura da navbar levemente ajustada para ser mais robusta visualmente --}}
            <div class="flex justify-between items-center h-[60px] lg:h-[70px]">

                {{-- 1. Logo e Links de Navegação (Esquerda/Centro) --}}
                <div class="flex items-center space-x-6">
                    {{-- Logo --}}
                    <div class="shrink-0 flex items-center">
                        <a href="{{ route('feed.index') }}">
                            {{-- Tamanho ajustado para ser mais clean --}}
                            <img src="{{ asset('imgs/logoJb.png') }}" alt="Logo" class="w-[6rem] h-auto">
                        </a>
                    </div>

                    {{-- Links de Navegação Principais (Desktop) --}}
                    {{-- Links agora usam ícones maiores e feedback visual mais forte no hover/ativo --}}
                    <div class="hidden space-x-6 lg:-my-px lg:ms-6 lg:flex items-center h-full">

                        {{-- Feed (Ícone Home moderno para Redes Sociais) --}}
                        <x-nav-link href="{{ route('feed.index') }}" :active="request()->routeIs('feed.index')" 
                                    class="text-lg text-gray-700 hover:text-red-600 transition duration-150 ease-in-out px-3 py-2 rounded-lg 
                                           {{ request()->routeIs('feed.index') ? 'bg-red-50 text-red-600 font-semibold' : 'hover:bg-gray-50' }}">
                            {{-- Ícone de Casa/Home em vez de RSS --}}
                            <i class="fa-solid fa-house text-xl"></i> 
                            <span class="hidden sm:inline ms-2">{{ __('Feed') }}</span>
                        </x-nav-link>

                        {{-- Dashboard (Ícone para uma área de controle/perfil, ou mantenha o anterior se for um 'Home' secundário) --}}
                        <x-nav-link href="{{ route('dashboard') }}" :active="request()->routeIs('dashboard')" 
                                    class="text-lg text-gray-700 hover:text-red-600 transition duration-150 ease-in-out px-3 py-2 rounded-lg 
                                           {{ request()->routeIs('dashboard') ? 'bg-red-50 text-red-600 font-semibold' : 'hover:bg-gray-50' }}">
                            <i class="fa-solid fa-house-chimney text-xl"></i> 
                            <span class="hidden sm:inline ms-2">{{ __('Dashboard') }}</span>
                        </x-nav-link>

                        {{-- Explorar --}}
                        <x-nav-link href="{{ route('explore.index') }}" :active="request()->routeIs('explore.*')" 
                                    class="text-lg text-gray-700 hover:text-red-600 transition duration-150 ease-in-out px-3 py-2 rounded-lg 
                                           {{ request()->routeIs('explore.*') ? 'bg-red-50 text-red-600 font-semibold' : 'hover:bg-gray-50' }}">
                            <i class="fa-solid fa-compass text-xl"></i> 
                            <span class="hidden sm:inline ms-2">{{ __('Explorar') }}</span>
                        </x-nav-link>

                        {{-- Dropdown de Eventos (Mantendo a lógica de permissão) --}}
                        @if (in_array(auth()->user()->user_type, ['coordinator']))
                            <div class="relative flex items-center h-full" x-data="{ open: false }" @click.away="open = false">
                                <button @click="open = ! open" class="inline-flex items-center px-3 py-2 rounded-lg text-lg font-medium leading-5 text-gray-700 hover:text-red-600 hover:bg-gray-50 focus:outline-none transition duration-150 ease-in-out">
                                    <i class="fa-solid fa-calendar-days text-xl"></i> 
                                    <span class="hidden sm:inline ms-2">{{ __('Eventos') }}</span>
                                    <svg class="ms-2 h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path x-show="!open" stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                                        <path x-show="open" stroke-linecap="round" stroke-linejoin="round" d="M4.5 15.75l7.5-7.5 7.5 7.5" />
                                    </svg>
                                </button>
                                {{-- Dropdown Content --}}
                                <div x-show="open" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100" x-transition:leave="transition ease-in duration-75" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
                                     class="absolute z-50 mt-2 w-48 rounded-xl shadow-xl ring-1 ring-black/5 top-full bg-white overflow-hidden" style="display: none;">
                                    <div class="py-1">
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
                            {{-- Link Eventos (para User e Admin) --}}
                            <x-nav-link href="{{ route('events.index') }}" :active="request()->routeIs('events.*')" 
                                        class="text-lg text-gray-700 hover:text-red-600 transition duration-150 ease-in-out px-3 py-2 rounded-lg 
                                               {{ request()->routeIs('events.*') ? 'bg-red-50 text-red-600 font-semibold' : 'hover:bg-gray-50' }}">
                                <i class="fa-solid fa-calendar-days text-xl"></i> 
                                <span class="hidden sm:inline ms-2">{{ __('Eventos') }}</span>
                            </x-nav-link>
                        @endif

                        {{-- Dropdown ou Link de Cursos (Mantendo a lógica de permissão) --}}
                        @if (auth()->user()->user_type === 'admin')
                            <div class="relative flex items-center h-full" x-data="{ open: false }" @click.away="open = false">
                                <button @click="open = ! open" class="inline-flex items-center px-3 py-2 rounded-lg text-lg font-medium leading-5 text-gray-700 hover:text-red-600 hover:bg-gray-50 focus:outline-none transition duration-150 ease-in-out">
                                    <i class="fa-solid fa-graduation-cap text-xl"></i> 
                                    <span class="hidden sm:inline ms-2">{{ __('Cursos') }}</span>
                                    <svg class="ms-2 h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path x-show="!open" stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                                        <path x-show="open" stroke-linecap="round" stroke-linejoin="round" d="M4.5 15.75l7.5-7.5 7.5 7.5" />
                                    </svg>
                                </button>
                                {{-- Dropdown Content --}}
                                <div x-show="open" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100" x-transition:leave="transition ease-in duration-75" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
                                     class="absolute z-50 mt-2 w-48 rounded-xl shadow-xl ring-1 ring-black/5 top-full bg-white overflow-hidden" style="display: none;">
                                    <div class="py-1">
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
                            <x-nav-link href="{{ route('courses.index') }}" :active="request()->routeIs('courses.*')" 
                                        class="text-lg text-gray-700 hover:text-red-600 transition duration-150 ease-in-out px-3 py-2 rounded-lg 
                                               {{ request()->routeIs('courses.*') ? 'bg-red-50 text-red-600 font-semibold' : 'hover:bg-gray-50' }}">
                                <i class="fa-solid fa-graduation-cap text-xl"></i> 
                                <span class="hidden sm:inline ms-2">{{ __('Cursos') }}</span>
                            </x-nav-link>
                        @endif

                        {{-- Dropdown de Coordenadores (Visível apenas para Admin) --}}
                        @if (auth()->user()->user_type === 'admin')
                            <div class="relative flex items-center h-full" x-data="{ open: false }" @click.away="open = false">
                                <button @click="open = ! open" class="inline-flex items-center px-3 py-2 rounded-lg text-lg font-medium leading-5 text-gray-700 hover:text-red-600 hover:bg-gray-50 focus:outline-none transition duration-150 ease-in-out">
                                    <i class="fa-solid fa-user-tie text-xl"></i> 
                                    <span class="hidden sm:inline ms-2">{{ __('Coordenadores') }}</span>
                                    <svg class="ms-2 h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path x-show="!open" stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                                        <path x-show="open" stroke-linecap="round" stroke-linejoin="round" d="M4.5 15.75l7.5-7.5 7.5 7.5" />
                                    </svg>
                                </button>
                                {{-- Dropdown Content --}}
                                <div x-show="open" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100" x-transition:leave="transition ease-in duration-75" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
                                     class="absolute z-50 mt-2 w-48 rounded-xl shadow-xl ring-1 ring-black/5 top-full bg-white overflow-hidden" style="display: none;">
                                    <div class="py-1">
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

                {{-- 2. Menus do Lado Direito: Conversas e Perfil --}}
                <div class="flex items-center space-x-3 sm:ms-6">
                    
                    {{-- Dropdown de Conversas/Mensagens --}}
                    <div class="hidden sm:block">
                        <x-dropdown align="right" width="80">
                            <x-slot name="trigger">
                                {{-- Novo estilo de botão de ícone (círculo, cinza claro) --}}
                                <button class="relative flex items-center p-2 rounded-full text-gray-700 bg-gray-100 hover:bg-gray-200 transition">
                                    {{-- Ícone mais moderno para mensagens --}}
                                    <i class="fa-solid fa-envelope text-xl"></i>
                                    @livewire('unread-messages')
                                </button>
                            </x-slot>
                            <x-slot name="content">
                                {{-- Dropdown com estilo moderno --}}
                                <div class="bg-white rounded-xl shadow-xl border border-gray-100 overflow-hidden">
                                    <div class="p-3 border-b border-gray-100 flex items-center justify-center">
                                        <h2 class="text-base font-bold text-gray-800">{{ __('Suas Conversas') }}</h2>
                                    </div>
                                    @livewire('conversation-list')
                                </div>
                            </x-slot>
                        </x-dropdown>
                    </div>

                    {{-- Dropdown de Perfil --}}
                    <div class="relative">
                        <x-dropdown align="right" width="48">
                            <x-slot name="trigger">
                                @if (Laravel\Jetstream\Jetstream::managesProfilePhotos())
                                {{-- Avatar com foco sutil na cor principal --}}
                                <button class="flex text-sm border-2 border-transparent rounded-full focus:outline-none focus:border-red-500 transition shadow-md">
                                    <img class="size-9 rounded-full object-cover" src="{{ Auth::user()->profile_photo_url }}" alt="{{ Auth::user()->name }}" />
                                </button>
                                @else
                                <span class="inline-flex rounded-md">
                                    <button type="button" class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-700 bg-gray-100 hover:text-red-600 focus:outline-none transition">
                                        {{ Auth::user()->name }}
                                        <svg class="ms-2 -me-0.5 h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 10.94l3.71-3.71a.75.75 0 111.06 1.06l-4.24 4.25a.75.75 0 01-1.06 0L5.21 8.27a.75.75 0 01.02-1.06z" clip-rule="evenodd" />
                                        </svg>
                                    </button>
                                </span>
                                @endif
                            </x-slot>

                            <x-slot name="content">
                                {{-- Informação do Usuário em destaque no topo --}}
                                <div class="block px-4 py-2 text-sm font-semibold text-red-600 border-b border-gray-100">
                                    {{ Auth::user()->name }}
                                </div>
                                
                                <div class="block px-4 py-2 text-xs text-gray-400">
                                    {{ __('Gerenciar Conta') }}
                                </div>

                                <x-dropdown-link href="{{ route('profile.show') }}">
                                    <i class="fa-solid fa-user-circle mr-2"></i> {{ __('Perfil') }}
                                </x-dropdown-link>
                                
                                <x-dropdown-link href="{{ route('settings') }}">
                                    <i class="fa-solid fa-gear mr-2"></i> {{ __('Configurações') }}
                                </x-dropdown-link>

                                @if (Laravel\Jetstream\Jetstream::hasApiFeatures())
                                <x-dropdown-link href="{{ route('api-tokens.index') }}">
                                    <i class="fa-solid fa-key mr-2"></i> {{ __('API Tokens') }}
                                </x-dropdown-link>
                                @endif

                                <div class="border-t border-gray-200"></div>

                                <form method="POST" action="{{ route('logout') }}" x-data>
                                    @csrf
                                    {{-- Botão de sair em vermelho para destaque --}}
                                    <x-dropdown-link href="{{ route('logout') }}" @click.prevent="$root.submit();" class="text-red-500 hover:bg-red-50">
                                        <i class="fa-solid fa-right-from-bracket mr-2"></i> {{ __('Sair') }}
                                    </x-dropdown-link>
                                </form>
                            </x-slot>
                        </x-dropdown>
                    </div>
                </div>

                {{-- Hamburger (Mobile) --}}
                <div class="-me-2 flex items-center sm:hidden">
                    <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-gray-500 hover:text-gray-700 hover:bg-gray-100 focus:outline-none transition">
                        <svg class="size-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                            <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                            <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        {{-- Menu Mobile (Ajustado para o novo visual) --}}
        <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden bg-white shadow-lg">
            
            {{-- Links de Navegação Mobile --}}
            <div class="pt-2 pb-3 space-y-1">
                
                {{-- Feed (Home) --}}
                <x-responsive-nav-link href="{{ route('feed.index') }}" :active="request()->routeIs('feed.index')" class="text-gray-700 hover:bg-red-50 hover:text-red-600">
                    <i class="fa-solid fa-house mr-3"></i> {{ __('Feed') }}
                </x-responsive-nav-link>

                {{-- Dashboard --}}
                <x-responsive-nav-link href="{{ route('dashboard') }}" :active="request()->routeIs('dashboard')" class="text-gray-700 hover:bg-red-50 hover:text-red-600">
                    <i class="fa-solid fa-house-chimney mr-3"></i> {{ __('Dashboard') }}
                </x-responsive-nav-link>

                {{-- Explorar --}}
                <x-responsive-nav-link href="{{ route('explore.index') }}" :active="request()->routeIs('explore.index')" class="text-gray-700 hover:bg-red-50 hover:text-red-600">
                    <i class="fa-solid fa-compass mr-3"></i> {{ __('Explorar') }}
                </x-responsive-nav-link>
                
                {{-- Links de Eventos Mobile (Mantida a lógica de permissão) --}}
                @if (in_array(auth()->user()->user_type, ['admin', 'coordinator']))
                    <div class="block px-4 pt-4 pb-2 text-xs text-gray-400 font-semibold border-t border-gray-100 mt-2">{{ __('Gerenciar Eventos') }}</div>
                    <x-responsive-nav-link href="{{ route('events.index') }}" :active="request()->routeIs('events.index')">
                        <i class="fa-solid fa-list-check mr-3"></i> {{ __('Listar Eventos') }}
                    </x-responsive-nav-link>
                    <x-responsive-nav-link href="{{ route('events.create') }}" :active="request()->routeIs('events.create')">
                        <i class="fa-solid fa-calendar-plus mr-3"></i> {{ __('Criar Evento') }}
                    </x-responsive-nav-link>
                @else
                    <x-responsive-nav-link href="{{ route('events.index') }}" :active="request()->routeIs('events.index')">
                        <i class="fa-solid fa-calendar-days mr-3"></i> {{ __('Eventos') }}
                    </x-responsive-nav-link>
                @endif


                {{-- Links de Cursos Mobile (Mantida a lógica de permissão) --}}
                @if (auth()->user()->user_type === 'admin')
                    <div class="block px-4 pt-4 pb-2 text-xs text-gray-400 font-semibold border-t border-gray-100 mt-2">{{ __('Gerenciar Cursos') }}</div>
                    <x-responsive-nav-link href="{{ route('courses.index') }}" :active="request()->routeIs('courses.index')">
                        <i class="fa-solid fa-list-ul mr-3"></i> {{ __('Listar Cursos') }}
                    </x-responsive-nav-link>
                    <x-responsive-nav-link href="{{ route('courses.create') }}" :active="request()->routeIs('courses.create')">
                        <i class="fa-solid fa-book-medical mr-3"></i> {{ __('Criar Curso') }}
                    </x-responsive-nav-link>
                @else
                    <x-responsive-nav-link href="{{ route('courses.index') }}" :active="request()->routeIs('courses.index')">
                        <i class="fa-solid fa-graduation-cap mr-3"></i> {{ __('Cursos') }}
                    </x-responsive-nav-link>
                @endif


                {{-- Dropdown de Coordenadores (Mobile - Mantida a lógica de permissão) --}}
                @if (auth()->user()->user_type === 'admin')
                    <div class="block px-4 pt-4 pb-2 text-xs text-gray-400 font-semibold border-t border-gray-100 mt-2">{{ __('Gerenciar Coordenadores') }}</div>
                    <x-responsive-nav-link href="{{ route('coordinators.index') }}" :active="request()->routeIs('coordinators.index')">
                        <i class="fa-solid fa-users-viewfinder mr-3"></i> {{ __('Listar Coordenadores') }}
                    </x-responsive-nav-link>
                    <x-responsive-nav-link href="{{ route('coordinators.create') }}" :active="request()->routeIs('coordinators.create')">
                        <i class="fa-solid fa-user-plus mr-3"></i> {{ __('Criar Coordenador') }}
                    </x-responsive-nav-link>
                @endif
            </div>

            {{-- Conta do Usuário Mobile --}}
            <div class="pt-4 pb-3 border-t border-gray-200">
                <div class="flex items-center px-4">
                    @if (Laravel\Jetstream\Jetstream::managesProfilePhotos())
                    <div class="shrink-0 me-3">
                        {{-- Avatar com borda para destaque --}}
                        <img class="size-10 rounded-full object-cover border-2 border-red-500" src="{{ Auth::user()->profile_photo_url }}" alt="{{ Auth::user()->name }}" />
                    </div>
                    @endif
                    <div>
                        <div class="font-bold text-base text-gray-800">{{ Auth::user()->name }}</div>
                        <div class="font-medium text-sm text-gray-500">{{ Auth::user()->email }}</div>
                    </div>
                </div>

                <div class="mt-3 space-y-1">
                    <x-responsive-nav-link href="{{ route('profile.show') }}" :active="request()->routeIs('profile.show')">
                        <i class="fa-solid fa-user mr-3"></i> {{ __('Perfil') }}
                    </x-responsive-nav-link>
                    <x-responsive-nav-link href="{{ route('settings') }}" :active="request()->routeIs('settings')">
                        <i class="fa-solid fa-gear mr-3"></i> {{ __('Configurações') }}
                    </x-responsive-nav-link>
                    
                    <form method="POST" action="{{ route('logout') }}" x-data>
                        @csrf
                        {{-- Botão de sair em destaque no mobile --}}
                        <x-responsive-nav-link href="{{ route('logout') }}" @click.prevent="$root.submit();" class="text-red-500 hover:bg-red-50">
                            <i class="fa-solid fa-right-from-bracket mr-3"></i> {{ __('Sair') }}
                        </x-responsive-nav-link>
                    </form>
                </div>
            </div>
        </div>
    </nav>
</div>