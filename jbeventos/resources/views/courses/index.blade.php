<x-app-layout backgroundClass="bg-gray-100">
    <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">

        {{-- Cabeçalho: Título, Botões e Pesquisa --}}
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-5 mb-10">
            <div class="mt-1">
                <p class="text-3xl sm:text-4xl font-extrabold text-stone-800 tracking-tight drop-shadow-sm">
                    Catálogo de Cursos
                </p>
                <div class="w-16 h-1 bg-red-500 rounded-full mt-2 shadow-lg"></div>
            </div>

            @php
                $loggedAdmin = auth()->user()->user_type === 'admin';
            @endphp

            <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3 w-full sm:w-auto">
                <div
                    class="flex flex-col sm:flex-row items-center space-y-3 sm:space-y-0 sm:space-x-3 w-full md:w-auto">
                    <form action="{{ route('courses.index') }}" method="GET" class="w-full flex-grow">
                        <div class="relative flex items-center w-full">
                            <svg class="absolute left-3 w-5 h-5 text-gray-400 pointer-events-none" fill="none"
                                stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"
                                xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M21 21l-4.35-4.35m0 0A7.5 7.5 0 1110.5 3a7.5 7.5 0 016.15 13.65z"></path>
                            </svg>
                            {{-- Input de pesquisa --}}
                            <input id="searchInput" type="text" name="search" value="{{ request('search') }}"
                                placeholder="Pesquisar cursos..." autocomplete="off"
                                class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-red-500 focus:border-red-500 text-sm">
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- Mensagem de Sucesso --}}
        @if (session('success'))
            <div
                class="mb-6 p-4 text-green-700 bg-green-50 rounded-xl shadow-md border border-green-200 flex items-center gap-3">
                <i class="ph-fill ph-check-circle text-green-500 text-2xl"></i>
                <span class="font-medium">{{ session('success') }}</span>
            </div>
        @endif

        {{-- Lista de Cursos --}}
        <div id="coursesList" data-url="{{ route('courses.index') }}"
            class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-8 mt-4">
            @forelse ($courses as $course)
                @include('partials.courses.course-card', ['course' => $course])
            @empty
                <div id="noCoursesMessage"
                    class="col-span-full flex flex-col items-center justify-center gap-6 text-center w-full my-10 p-16">
                    <img src="{{ asset('imgs/notFound.png') }}" class="w-auto h-40 object-contain" alt="not-found">
                    <div>
                        <p class="text-2xl font-bold text-stone-800">Ops! Nada foi encontrado...</p>
                        <p class="text-gray-500 mt-2 text-md max-w-lg mx-auto">
                            Não encontramos nenhum curso com os termos de busca. Tente refinar a pesquisa.
                        </p>
                    </div>
                </div>
            @endforelse
        </div>

    </div>
</x-app-layout>

<script>
    function atualizarListaDeCursos(html) {
        const coursesList = document.getElementById('coursesList');
        coursesList.innerHTML = html;

        const noCourses = document.getElementById('noCoursesMessage');
        const footer = document.getElementById('catalogEnd');
        if (footer) {
            footer.style.display = noCourses ? 'none' : 'block';
        }
    }
</script>

@vite('resources/js/app.js')
<script src="https://unpkg.com/@phosphor-icons/web"></script>
