<!-- Componente de navegação principal com Alpine.js para controle do menu responsivo -->
<nav x-data="{ open: false }" class="bg-white border-b border-gray-100">
    
    <!-- Container principal da barra de navegação -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">

            <!-- Área da esquerda: logo e links -->
            <div class="flex">

                <!-- Logo do sistema -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}">
                       <img src="{{ asset('imgs/logoJb.png') }}" alt="Logo" class="w-[7rem] h-auto mx-auto">
                    </a>
                </div>

                <!-- Links de navegação visíveis em telas médias/grandes -->
                <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                    <!-- Link de dashboard com base no tipo de usuário -->
                    @if (auth()->user()->user_type === 'admin')
                        <x-nav-link href="{{ route('dashboard') }}" :active="request()->routeIs('admin.dashboard')">
                            {{ __('Dashboard Admin') }}
                        </x-nav-link>
                    @elseif (auth()->user()->user_type === 'coordinator')
                        <x-nav-link href="{{ route('dashboard') }}" :active="request()->routeIs('coordinator.dashboard')">
                            {{ __('Dashboard Coordinator') }}
                        </x-nav-link>
                    @else
                        <x-nav-link href="{{ route('dashboard') }}" :active="request()->routeIs('user.dashboard')">
                            {{ __('Dashboard User') }}
                        </x-nav-link>
                    @endif
                </div>

                <!-- Link "Eventos" -->
                @if(auth()->check() && in_array(auth()->user()->user_type, ['user', 'admin', 'coordinator']))
                    <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                        <x-nav-link href="{{ route('events.index') }}" :active="request()->routeIs('events.index')">
                            {{ __('Eventos') }}
                        </x-nav-link>
                    </div>
                @endif

                <!-- Link "Criar Evento" apenas para coordinator -->
                @if (Auth::user()->user_type === 'coordinator')
                    <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                        <x-nav-link href="{{ route('events.create') }}" :active="request()->routeIs('events.create')">
                            {{ __('Criar Evento') }}
                        </x-nav-link>
                    </div>
                @endif

                <!-- Link "Cursos" -->
                @if(auth()->check() && in_array(auth()->user()->user_type, ['user', 'admin', 'coordinator']))
                    <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                        <x-nav-link href="{{ route('courses.index') }}" :active="request()->routeIs('courses.index')">
                            {{ __('Cursos') }}
                        </x-nav-link>
                    </div>
                @endif

                <!-- Links exclusivos para admin -->
                @if (Auth::user()->user_type === 'admin')
                    <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                        <x-nav-link href="{{ route('coordinators.index') }}" :active="request()->routeIs('coordinators.index')">
                            {{ __('Coordenadores') }}
                        </x-nav-link>
                    </div>
                    <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                        <x-nav-link href="{{ route('coordinators.create') }}" :active="request()->routeIs('coordinators.create')">
                            {{ __('Criar Coordenador') }}
                        </x-nav-link>
                    </div>
                    <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                        <x-nav-link href="{{ route('courses.create') }}" :active="request()->routeIs('courses.create')">
                            {{ __('Criar Curso') }}
                        </x-nav-link>
                    </div>
                @endif
            </div>

            <!-- Área da direita: dropdowns -->
            <div class="hidden sm:flex sm:items-center sm:ms-6">
                
                <!-- Dropdown de times (Jetstream) -->
                @if (Laravel\Jetstream\Jetstream::hasTeamFeatures())
                    @include('navigation-dropdown-team')
                @endif

                <!-- Dropdown da conta -->
                <div class="ms-3 relative">
                    <x-dropdown align="right" width="48">
                        <x-slot name="trigger">
                            @if (Laravel\Jetstream\Jetstream::managesProfilePhotos())
                                <button class="flex text-sm border-2 border-transparent rounded-full">
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
                            <!-- Opções de conta -->
                            <div class="block px-4 py-2 text-xs text-gray-400">
                                {{ __('Manage Account') }}
                            </div>

                            <!-- Perfil social -->
                            <x-dropdown-link href="{{ route('profile.show') }}">
                                {{ __('Profile') }}
                            </x-dropdown-link>

                            <!-- Configurações (NOVO) -->
                            <x-dropdown-link href="{{ route('settings') }}">
                                {{ __('Configurações') }}
                            </x-dropdown-link>

                            @if (Laravel\Jetstream\Jetstream::hasApiFeatures())
                                <x-dropdown-link href="{{ route('api-tokens.index') }}">
                                    {{ __('API Tokens') }}
                                </x-dropdown-link>
                            @endif

                            <div class="border-t border-gray-200"></div>

                            <!-- Logout -->
                            <form method="POST" action="{{ route('logout') }}" x-data>
                                @csrf
                                <x-dropdown-link href="{{ route('logout') }}" @click.prevent="$root.submit();">
                                    {{ __('Log Out') }}
                                </x-dropdown-link>
                            </form>
                        </x-slot>
                    </x-dropdown>
                </div>
            </div>

            <!-- Botão mobile -->
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

    <!-- Menu mobile -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden">
        <!-- Dashboard -->
        <div class="pt-2 pb-3 space-y-1">
            <x-responsive-nav-link href="{{ route('dashboard') }}" :active="request()->routeIs('dashboard')">
                {{ __('Dashboard') }}
            </x-responsive-nav-link>
        </div>

        <!-- Dados do usuário -->
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

            <!-- Opções mobile -->
            <div class="mt-3 space-y-1">
                <x-responsive-nav-link href="{{ route('profile.show') }}" :active="request()->routeIs('profile.show')">
                    {{ __('Profile') }}
                </x-responsive-nav-link>

                <!-- Configurações (NOVO) -->
                <x-responsive-nav-link href="{{ route('settings') }}" :active="request()->routeIs('settings')">
                    {{ __('Configurações') }}
                </x-responsive-nav-link>

                <!-- Logout -->
                <form method="POST" action="{{ route('logout') }}" x-data>
                    @csrf
                    <x-responsive-nav-link href="{{ route('logout') }}" @click.prevent="$root.submit();">
                        {{ __('Log Out') }}
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>
</nav>
