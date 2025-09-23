<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">

                {{-- Barra de Pesquisa --}}
                <div class="mb-8">
                    <form action="{{ route('explore.index') }}" method="GET">
                        <div class="relative">
                            <input type="text" name="search" placeholder="Pesquisar por eventos, cursos ou pessoas..."
                                class="w-full pl-10 pr-4 py-2 border rounded-full focus:outline-none focus:ring-2 focus:ring-indigo-500"
                                value="{{ request('search') }}">
                            <div class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400">
                                <i class="fas fa-search"></i>
                            </div>
                        </div>
                    </form>
                </div>

                {{-- Seção de Eventos --}}
                <div class="mb-12">
                    <h2 class="text-3xl font-bold text-gray-800 mb-6">Eventos</h2>
                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                        @forelse ($events->take(4) as $event)
                            <div class="bg-white border border-gray-200 rounded-lg shadow-md overflow-hidden transform transition duration-300 hover:scale-105 hover:shadow-lg">
                                <a href="{{ route('events.show', $event->id) }}">
                                    <img src="{{ asset('storage/' . $event->event_image) }}" alt="{{ $event->event_name }}" class="w-full h-48 object-cover">
                                    <div class="p-4">
                                        <h3 class="font-bold text-lg text-gray-900 leading-tight">{{ $event->event_name }}</h3>
                                        <p class="text-sm text-gray-600 mt-1">{{ $event->event_description }}</p>
                                        <p class="text-sm text-gray-500 mt-2">{{ $event->event_scheduled_at->format('d/m/Y H:i') }}</p>
                                    </div>
                                </a>
                            </div>
                        @empty
                            <p class="text-gray-500 text-center col-span-full">Nenhum evento encontrado.</p>
                        @endforelse
                    </div>
                    @if ($events->count() > 4)
                        <div class="text-right mt-4">
                            <a href="#" class="text-indigo-600 font-semibold hover:underline">Ver todos os eventos →</a>
                        </div>
                    @endif
                </div>

                {{-- Seção de Cursos --}}
                <div class="mb-12">
                    <h2 class="text-3xl font-bold text-gray-800 mb-6">Cursos</h2>
                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                        @forelse ($courses->take(4) as $course)
                            <div class="bg-white border border-gray-200 rounded-lg shadow-md overflow-hidden flex flex-col items-center justify-center p-6 text-center transform transition duration-300 hover:scale-105 hover:shadow-lg">
                                <a href="{{ route('courses.show', $course->id) }}" class="flex flex-col items-center">
                                    <img src="{{ asset('storage/' . $course->course_icon) }}" alt="{{ $course->course_name }}" class="size-24 rounded-full object-cover border-4 border-gray-300 mb-4">
                                    <h3 class="font-bold text-lg text-gray-900 leading-tight mb-1">
                                        {{ $course->course_name }}
                                    </h3>
                                    <p class="text-sm text-gray-600 font-medium">Curso</p>
                                </a>
                            </div>
                        @empty
                            <p class="text-gray-500 text-center col-span-full">Nenhum curso encontrado.</p>
                        @endforelse
                    </div>
                    @if ($courses->count() > 4)
                        <div class="text-right mt-4">
                            <a href="#" class="text-indigo-600 font-semibold hover:underline">Ver todos os cursos →</a>
                        </div>
                    @endif
                </div>

                {{-- Seção de Pessoas (apenas Coordenadores) --}}
                <div class="mb-12">
                    <h2 class="text-3xl font-bold text-gray-800 mb-6">Coordenadores</h2>
                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                        @forelse ($coordinators->take(4) as $coordinator)
                            <div class="bg-white border border-gray-200 rounded-lg shadow-md overflow-hidden flex flex-col items-center justify-center p-6 text-center transform transition duration-300 hover:scale-105 hover:shadow-lg">
                                <a href="{{ route('profile.view', $coordinator->userAccount->id) }}" class="flex flex-col items-center">
                                    {{-- Acessa a foto do usuário do Coordenador --}}
                                    <img src="{{ $coordinator->userAccount->user_icon_url }}" alt="{{ $coordinator->userAccount->name }}" class="size-24 rounded-full object-cover border-4 border-gray-300 mb-4">
                                    <h3 class="font-bold text-lg text-gray-900 leading-tight mb-1">
                                        {{ $coordinator->userAccount->name }}
                                    </h3>
                                    <p class="text-sm text-gray-600 font-medium">Coordenador</p>
                                    @if($coordinator->course)
                                        <span class="bg-indigo-100 text-indigo-800 text-xs font-semibold px-2 py-1 rounded-full shadow mt-2">
                                            {{ $coordinator->course->course_name }}
                                        </span>
                                    @endif
                                </a>
                            </div>
                        @empty
                            <p class="text-gray-500 text-center col-span-full">Nenhum coordenador encontrado.</p>
                        @endforelse
                    </div>
                    @if ($coordinators->count() > 4)
                        <div class="text-right mt-4">
                            <a href="#" class="text-indigo-600 font-semibold hover:underline">Ver todos os coordenadores →</a>
                        </div>
                    @endif
                </div>

            </div>
        </div>
    </div>
</x-app-layout>