<x-app-layout>
    {{-- Main Content Container --}}
    <div class="py-6 min-h-screen mt-8">
        <div class="w-full max-w-[100rem] mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-md rounded-2xl p-4 sm:p-6 lg:p-9 mx-auto min-h-[70vh]">

                {{-- Page Header and Search --}}
                <div class="flex flex-col md:flex-row justify-between items-center mb-8 gap-4 md:gap-0">
                    <h1 class="text-3xl font-bold text-gray-800 leading-tight">
                        {{ __('Explorar') }}
                    </h1>

                    {{-- Search Bar --}}
                    <form action="{{ route('explore.index') }}" method="GET" class="flex items-center w-full md:w-1/3">
                        <div class="relative w-full">
                            <input type="text" name="search" value="{{ request('search') }}" placeholder="Pesquisar..." class="w-full rounded-full border-2 border-gray-300 bg-white py-2 pl-5 pr-12 text-gray-700 focus:border-blue-500 focus:ring-blue-500 transition-colors">
                            <button type="submit" class="absolute right-0 top-0 mt-1 mr-2 px-3 py-2">
                                <svg class="w-6 h-6 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                </svg>
                            </button>
                        </div>
                    </form>
                </div>

                {{-- Combined Results Grid --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                    {{-- Events --}}
                    @foreach ($events as $event)
                        <div class="bg-white border border-gray-200 rounded-xl shadow-md overflow-hidden flex flex-col transform transition-transform duration-300 hover:scale-105 hover:shadow-lg">
                            <a href="{{ route('events.show', $event->id) }}">
                                {{-- Event Image --}}
                                <div class="relative w-full h-48">
                                    <img src="{{ $event->event_image ? asset('storage/' . $event->event_image) : asset('imgs/placeholder.png') }}" alt="{{ $event->event_name }}" class="object-cover w-full h-full">
                                    <span class="absolute top-2 right-2 bg-blue-600 text-white text-xs font-semibold px-2 py-1 rounded-full shadow">
                                        Evento
                                    </span>
                                </div>
                                {{-- Event Content --}}
                                <div class="p-4 flex flex-col flex-1">
                                    <h3 class="font-bold text-lg text-gray-900 mb-1 leading-tight line-clamp-2">
                                        {{ $event->event_name }}
                                    </h3>
                                    <p class="text-sm text-gray-600 mb-2 line-clamp-2">
                                        {{ $event->event_description }}
                                    </p>
                                    <div class="mt-auto pt-2 border-t border-gray-200">
                                        <p class="text-sm text-gray-800 font-medium">
                                            <i class="fas fa-map-marker-alt text-gray-500 mr-1"></i>
                                            {{ $event->event_location }}
                                        </p>
                                        <p class="text-sm text-gray-800 font-medium mt-1">
                                            <i class="far fa-calendar-alt text-gray-500 mr-1"></i>
                                            {{ \Carbon\Carbon::parse($event->event_scheduled_at)->isoFormat('D MMMM YYYY, HH:mm') }}
                                        </p>
                                        <div class="flex items-center mt-2 text-sm text-gray-600">
                                            <i class="fas fa-thumbs-up mr-1 text-blue-500"></i>
                                            <span>{{ $event->likes_count }} Curtidas</span>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        </div>
                    @endforeach

                    {{-- Courses --}}
                    @foreach ($courses as $course)
                        <a href="{{ route('courses.show', $course->id) }}" class="block group relative overflow-hidden bg-white border border-gray-200 rounded-2xl shadow-md hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1">
                            {{-- Course Icon --}}
                            <div class="relative h-48 w-full flex items-center justify-center bg-gray-100">
                                <img src="{{ $course->course_icon ? asset('storage/' . $course->course_icon) : asset('imgs/placeholder.png') }}" alt="{{ $course->course_name }}" class="h-full w-full object-cover p-6 transition-transform duration-300 group-hover:scale-105">
                                <span class="absolute top-2 right-2 bg-purple-600 text-white text-xs font-semibold px-2 py-1 rounded-full shadow">
                                    Curso
                                </span>
                            </div>
                            {{-- Course Content --}}
                            <div class="p-6 flex flex-col flex-grow">
                                <h3 class="font-bold text-2xl text-stone-800 mb-2 truncate">{{ $course->course_name }}</h3>
                                <p class="text-sm text-gray-500 mb-4">
                                    Coordenador: {{ $course->courseCoordinator->userAccount->name ?? 'Não definido' }}
                                </p>
                                <div class="absolute bottom-0 left-0 right-0 p-4 bg-white/80 backdrop-blur-sm transform translate-y-full group-hover:translate-y-0 transition-transform duration-300">
                                    <p class="text-sm text-gray-700 font-semibold text-center">Ver detalhes</p>
                                </div>
                            </div>
                        </a>
                    @endforeach

                    {{-- Coordinators --}}
                    @foreach ($coordinators as $coordinator)
                        <div class="bg-white border border-gray-200 rounded-xl shadow-md overflow-hidden flex flex-col items-center justify-center p-6 text-center transform transition-transform duration-300 hover:scale-105 hover:shadow-lg">
                            <a href="{{ route('coordinators.show', $coordinator->id) }}" class="flex flex-col items-center">
                                <img src="{{ $coordinator->userAccount->profile_photo_url }}" alt="{{ $coordinator->userAccount->name }}" class="size-24 rounded-full object-cover border-4 border-blue-500 mb-4">
                                <h3 class="font-bold text-lg text-gray-900 leading-tight mb-1">
                                    {{ $coordinator->userAccount->name }}
                                </h3>
                                <p class="text-sm text-gray-600 font-medium">Coordenador do Curso</p>
                                <p class="text-sm text-blue-600 font-bold mt-1">{{ $coordinator->course->course_name ?? 'Não definido' }}</p>
                                <span class="bg-green-500 text-white text-xs font-semibold px-2 py-1 rounded-full shadow mt-2">
                                    Coordenador
                                </span>
                            </a>
                        </div>
                    @endforeach

                    {{-- Users --}}
                    @foreach ($users as $user)
                        <div class="bg-white border border-gray-200 rounded-xl shadow-md overflow-hidden flex flex-col items-center justify-center p-6 text-center transform transition-transform duration-300 hover:scale-105 hover:shadow-lg">
                            <a href="{{ route('profile.view', $user->id) }}" class="flex flex-col items-center">
                                <img src="{{ $user->profile_photo_url }}" alt="{{ $user->name }}" class="size-24 rounded-full object-cover border-4 border-gray-300 mb-4">
                                <h3 class="font-bold text-lg text-gray-900 leading-tight mb-1">
                                    {{ $user->name }}
                                </h3>
                                <p class="text-sm text-gray-600 font-medium">Usuário</p>
                                <span class="bg-gray-200 text-gray-700 text-xs font-semibold px-2 py-1 rounded-full shadow mt-2">
                                    Pessoa
                                </span>
                            </a>
                        </div>
                    @endforeach

                    {{-- No Results --}}
                    @if ($events->isEmpty() && $courses->isEmpty() && $coordinators->isEmpty())
                        <div class="col-span-full flex flex-col items-center justify-center p-12">
                            <p class="text-xl font-semibold text-gray-500">Nenhum resultado encontrado...</p>
                            <p class="text-sm text-gray-400 mt-2">Tente uma pesquisa diferente.</p>
                        </div>
                    @endif
                </div>

                {{-- Pagination (if you want to implement it later for combined results) --}}
                {{-- <div class="mt-8 flex justify-center">
                     {{ $results->links() }}
                 </div> --}}
            </div>
        </div>
    </div>
</x-app-layout>