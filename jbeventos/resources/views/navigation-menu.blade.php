<nav x-data="{ open: false }" class="bg-white border-b border-gray-100">
    
    <!-- Container principal da barra de navegação -->
    <div class="w-full max-w-[1600px] mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center h-[10vh] sm:h-[12vh] lg:h-[14vh]">

            <div class="flex">

                <div class="shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}">
                       <img src="{{ asset('imgs/logoJb.png') }}" alt="Logo" class="w-[7rem] h-auto mx-auto">
                    </a>
                </div>

                <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
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

                @if(auth()->check() && in_array(auth()->user()->user_type, ['user', 'admin', 'coordinator']))
                    <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                        <x-nav-link href="{{ route('events.index') }}" :active="request()->routeIs('events.index')">
                            {{ __('Eventos') }}
                        </x-nav-link>
                    </div>
                @endif

                @if (Auth::user()->user_type === 'coordinator')
                    <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                        <x-nav-link href="{{ route('events.create') }}" :active="request()->routeIs('events.create')">
                            {{ __('Criar Evento') }}
                        </x-nav-link>
                    </div>
                @endif

                @if(auth()->check() && in_array(auth()->user()->user_type, ['user', 'admin', 'coordinator']))
                    <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                        <x-nav-link href="{{ route('courses.index') }}" :active="request()->routeIs('courses.index')">
                            {{ __('Cursos') }}
                        </x-nav-link>
                    </div>
                @endif

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

            <div class="relative mt-4">
                <x-dropdown align="right" width="[37rem]">
                    <x-slot name="trigger">
                        <button class="flex items-center p-2 rounded-full text-gray-600 hover:bg-gray-100 dark:text-gray-400 dark:hover:bg-gray-700 transition">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M8 10h.01M12 10h.01M16 10h.01M21 12c0 4.418-4.03 8-9 8a9.77 9.77 0 01-4-.8l-4 1 1-3.6A7.95 7.95 0 013 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                            </svg>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                    <div class="bg-white rounded-xl shadow-xl border border-gray-100 dark:bg-gray-800 dark:border-gray-700 overflow-hidden">
                        <div class="p-3 border-b border-gray-100 dark:border-gray-700 flex items-center justify-center">
                            <h2 class="text-sm font-semibold text-gray-700 dark:text-gray-300">Suas Conversas</h2>
                        </div>
                        @livewire('conversation-list')
                    </div>
                </x-slot>
                </x-dropdown>
            </div>


            <div class="hidden sm:flex sm:items-center sm:ms-6">

                @if (Laravel\Jetstream\Jetstream::hasTeamFeatures())
                    @include('navigation-dropdown-team')
                @endif

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
                            <div class="block px-4 py-2 text-xs text-gray-400">
                                {{ __('Manage Account') }}
                            </div>

                            <x-dropdown-link href="{{ route('profile.show') }}">
                                {{ __('Profile') }}
                            </x-dropdown-link>

                            <x-dropdown-link href="{{ route('settings') }}">
                                {{ __('Configurações') }}
                            </x-dropdown-link>

                            @if (Laravel\Jetstream\Jetstream::hasApiFeatures())
                                <x-dropdown-link href="{{ route('api-tokens.index') }}">
                                    {{ __('API Tokens') }}
                                </x-dropdown-link>
                            @endif

                            <div class="border-t border-gray-200"></div>

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

    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden">
        <div class="pt-2 pb-3 space-y-1">
            <x-responsive-nav-link href="{{ route('dashboard') }}" :active="request()->routeIs('dashboard')">
                {{ __('Dashboard') }}
            </x-responsive-nav-link>
        </div>

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
                    {{ __('Profile') }}
                </x-responsive-nav-link>

                <x-responsive-nav-link href="{{ route('settings') }}" :active="request()->routeIs('settings')">
                    {{ __('Configurações') }}
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