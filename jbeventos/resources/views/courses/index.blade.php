<x-app-layout backgroundClass="bg-gray-100">
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            {{-- Título e botões de ação --}}
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center px-4 sm:px-0 gap-5 mb-10">
                <div class="mt-1">
                    <p class="text-3xl sm:text-4xl font-extrabold text-stone-800 tracking-tight drop-shadow-sm">
                        Catálogo de Cursos
                    </p>
                    <div class="w-16 h-1 bg-blue-500 rounded-full mt-2 shadow-lg"></div>
                </div>

                @php
                    $loggedAdmin = auth()->user()->user_type === 'admin';
                @endphp

                <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3 w-full sm:w-auto">
                    @if ($loggedAdmin)
                        <a href="{{ route('courses.create') }}"
                            class="flex items-center justify-center gap-2 bg-stone-800 text-white px-6 py-2 rounded-full shadow-lg hover:bg-stone-700 transition-colors duration-200 text-sm font-medium">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z" clip-rule="evenodd" />
                            </svg>
                            Novo Curso
                        </a>
                    @endif

                    <form action="{{ route('courses.index') }}" method="GET" class="flex items-center w-full sm:w-auto">
                        <div class="relative w-full">
                            <input type="text" name="search" value="{{ request('search') }}"
                                placeholder="Pesquisar..." autocomplete="off"
                                class="w-full pl-5 pr-12 py-3 text-gray-800 placeholder-gray-400 border-2 border-gray-300 rounded-full outline-none focus:ring-2 focus:ring-blue-500 transition-shadow">
                            <button type="submit" class="absolute right-2 top-1/2 -translate-y-1/2 p-2 bg-blue-500 rounded-full text-white hover:bg-blue-600 transition-colors">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" />
                                </svg>
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            {{-- Mensagens de sucesso --}}
            @if (session('success'))
                <div class="mb-6 p-4 text-green-700 bg-green-100 rounded-lg shadow-sm">
                    {{ session('success') }}
                </div>
            @endif

            {{-- Lista de cursos --}}
            <div class="bg-white shadow-xl rounded-2xl p-6 sm:p-9 border border-gray-200">
                @if ($courses->count())
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-8 p-2">
                        @foreach ($courses as $course)
                            <a href="{{ route('courses.show', $course->id) }}"
                                class="block group relative overflow-hidden bg-white border border-gray-200 rounded-2xl shadow-md hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1">
                                <img src="{{ $course->course_icon ? asset('storage/' . $course->course_icon) : asset('imgs/placeholder.png') }}"
                                    alt="{{ $course->course_name }}" class="h-48 w-full object-cover transition-transform duration-300 group-hover:scale-105">

                                <div class="p-6 flex flex-col flex-grow">
                                    <h3 class="font-bold text-2xl text-stone-800 mb-2 truncate">{{ $course->course_name }}</h3>
                                    <p class="text-sm text-gray-500 mb-4">
                                        Coordenador: {{ $course->courseCoordinator->userAccount->name ?? 'Não definido' }}
                                    </p>
                                </div>
                                <div class="absolute bottom-0 left-0 right-0 p-4 bg-white/80 backdrop-blur-sm transform translate-y-full group-hover:translate-y-0 transition-transform duration-300">
                                    <p class="text-sm text-gray-700 font-semibold text-center">Ver detalhes</p>
                                </div>
                            </a>
                        @endforeach
                    </div>
                    {{-- Paginação --}}
                    <div class="mt-8 flex justify-center">
                        {{ $courses->links('pagination::tailwind') }}
                    </div>
                @else
                    <div class="w-full flex flex-col items-center py-20">
                        <p class="text-gray-500 text-lg font-medium">Nenhum curso encontrado 😥</p>
                    </div>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>