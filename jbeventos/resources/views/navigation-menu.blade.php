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
                        <x-application-mark class="block h-9 w-auto" />
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

                <!-- Link "Eventos" visível para user, admin e coordinator -->
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

                <!-- Link "Cursos" visível para user, admin e coordinator -->
                @if(auth()->check() && in_array(auth()->user()->user_type, ['user', 'admin', 'coordinator']))
                    <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                        <x-nav-link href="{{ route('courses.index') }}" :active="request()->routeIs('courses.index')">
                            {{ __('Cursos') }}
                        </x-nav-link>
                    </div>
                @endif

                <!-- Links exclusivos para admin: criar cursos, ver e criar coordenadores -->
                @if (Auth::user()->user_type === 'admin')
                    <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                        <x-nav-link href="{{ route('courses.create') }}" :active="request()->routeIs('courses.create')">
                            {{ __('Criar Cursos') }}
                        </x-nav-link>
                    </div>

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
                @endif
            </div>

            <!-- Área da direita: dropdowns de equipe e configurações -->
            <div class="hidden sm:flex sm:items-center sm:ms-6">
                
                <!-- Dropdown de times (se recurso estiver ativado no Jetstream) -->
                @if (Laravel\Jetstream\Jetstream::hasTeamFeatures())
                    <div class="ms-3 relative">
                        <x-dropdown align="right" width="60">
                            <x-slot name="trigger">
                                <!-- Botão com nome da equipe atual -->
                                <span class="inline-flex rounded-md">
                                    <button type="button" class="...">
                                        {{ Auth::user()->currentTeam->name }}
                                        <svg class="ms-2 -me-0.5 size-4" ...>...</svg>
                                    </button>
                                </span>
                            </x-slot>

                            <x-slot name="content">
                                <div class="w-60">
                                    <!-- Gerenciamento da equipe -->
                                    <div class="block px-4 py-2 text-xs text-gray-400">
                                        {{ __('Manage Team') }}
                                    </div>

                                    <!-- Link para configurações da equipe -->
                                    <x-dropdown-link href="{{ route('teams.show', Auth::user()->currentTeam->id) }}">
                                        {{ __('Team Settings') }}
                                    </x-dropdown-link>

                                    <!-- Criar nova equipe (se permitido) -->
                                    @can('create', Laravel\Jetstream\Jetstream::newTeamModel())
                                        <x-dropdown-link href="{{ route('teams.create') }}">
                                            {{ __('Create New Team') }}
                                        </x-dropdown-link>
                                    @endcan

                                    <!-- Alternar entre equipes -->
                                    @if (Auth::user()->allTeams()->count() > 1)
                                        <div class="border-t border-gray-200"></div>
                                        <div class="block px-4 py-2 text-xs text-gray-400">
                                            {{ __('Switch Teams') }}
                                        </div>

                                        @foreach (Auth::user()->allTeams() as $team)
                                            <x-switchable-team :team="$team" />
                                        @endforeach
                                    @endif
                                </div>
                            </x-slot>
                        </x-dropdown>
                    </div>
                @endif

                <!-- Dropdown de configurações da conta -->
                <div class="ms-3 relative">
                    <x-dropdown align="right" width="48">
                        <x-slot name="trigger">
                            @if (Laravel\Jetstream\Jetstream::managesProfilePhotos())
                                <!-- Foto de perfil -->
                                <button class="flex text-sm border-2 border-transparent rounded-full ...">
                                    <img class="size-8 rounded-full object-cover" src="{{ Auth::user()->profile_photo_url }}" alt="{{ Auth::user()->name }}" />
                                </button>
                            @else
                                <!-- Nome do usuário como botão -->
                                <span class="inline-flex rounded-md">
                                    <button type="button" class="...">
                                        {{ Auth::user()->name }}
                                        <svg class="ms-2 -me-0.5 size-4" ...>...</svg>
                                    </button>
                                </span>
                            @endif
                        </x-slot>

                        <x-slot name="content">
                            <!-- Opções de conta -->
                            <div class="block px-4 py-2 text-xs text-gray-400">
                                {{ __('Manage Account') }}
                            </div>

                            <!-- Link para perfil -->
                            <x-dropdown-link href="{{ route('profile.show') }}">
                                {{ __('Profile') }}
                            </x-dropdown-link>

                            <!-- Link para tokens de API -->
                            @if (Laravel\Jetstream\Jetstream::hasApiFeatures())
                                <x-dropdown-link href="{{ route('api-tokens.index') }}">
                                    {{ __('API Tokens') }}
                                </x-dropdown-link>
                            @endif

                            <div class="border-t border-gray-200"></div>

                            <!-- Formulário de logout -->
                            <form method="POST" action="{{ route('logout') }}" x-data>
                                @csrf
                                <x-dropdown-link href="{{ route('logout') }}"
                                         @click.prevent="$root.submit();">
                                    {{ __('Log Out') }}
                                </x-dropdown-link>
                            </form>
                        </x-slot>
                    </x-dropdown>
                </div>
            </div>

            <!-- Ícone do menu hambúrguer (aparece apenas no mobile) -->
            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="...">
                    <!-- Ícone de menu aberto/fechado -->
                    <svg class="size-6" ...>
                        <path :class="{'hidden': open, 'inline-flex': ! open }" ... />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" ... />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Menu responsivo (mobile) -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden">
        <!-- Link para dashboard -->
        <div class="pt-2 pb-3 space-y-1">
            <x-responsive-nav-link href="{{ route('dashboard') }}" :active="request()->routeIs('dashboard')">
                {{ __('Dashboard') }}
            </x-responsive-nav-link>
        </div>

        <!-- Dados do usuário e logout no menu mobile -->
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
            
            <!-- Opções de perfil e logout no menu mobile -->
            <div class="mt-3 space-y-1">
                <x-responsive-nav-link href="{{ route('profile.show') }}" :active="request()->routeIs('profile.show')">
                    {{ __('Profile') }}
                </x-responsive-nav-link>

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
