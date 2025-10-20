<x-app-layout backgroundClass="bg-gray-100">
    <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">

        {{-- Cabeçalho: Título, Botões e Pesquisa --}}
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-end gap-5 mb-10">
            {{-- Título --}}
            <div class="mt-1">
                <p class="text-3xl sm:text-4xl font-extrabold text-stone-800 tracking-tight drop-shadow-sm">
                    Catálogo de Cursos
                </p>
                <div class="w-16 h-1 bg-red-500 rounded-full mt-2 shadow-lg"></div>
            </div>

            @php
                $loggedAdmin = auth()->user()->user_type === 'admin';
            @endphp

            {{-- Área de Ações: Botão de Criar e Pesquisa --}}
            <div class="flex flex-col sm:flex-row items-center gap-4 w-full sm:w-auto">

                {{-- Formulário de Pesquisa --}}
                <form action="{{ route('courses.index') }}" method="GET" class="w-full flex-grow max-w-sm">
                    <div class="relative flex items-center w-full shadow-md rounded-full bg-white">
                        <svg class="absolute left-4 w-5 h-5 text-gray-500" fill="none"
                            stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"
                            xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M21 21l-4.35-4.35m0 0A7.5 7.5 0 1110.5 3a7.5 7.5 0 016.15 13.65z"></path>
                        </svg>
                        {{-- Input de pesquisa --}}
                        <input id="searchInput" type="text" name="search" value="{{ request('search') }}"
                            placeholder="Pesquisar cursos..." autocomplete="off"
                            class="w-full pl-11 pr-5 py-2.5 border-none rounded-full focus:ring-red-500 focus:border-red-500 text-sm placeholder-gray-500 bg-transparent">
                    </div>
                </form>

                {{-- Botão de Criar Novo Curso (Apenas para Admin) --}}
                @if ($loggedAdmin)
                    <a href="{{ route('courses.create') }}"
                        class="flex-shrink-0 flex items-center justify-center px-5 py-2.5 text-sm font-semibold rounded-full shadow-xl text-white bg-red-600 hover:bg-red-700 transition duration-150 ease-in-out whitespace-nowrap w-full sm:w-auto transform hover:scale-[1.02]">
                        <i class="ph-bold ph-plus-circle text-lg mr-2"></i>
                        Novo Curso
                    </a>
                @endif
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
                {{-- Mensagem de Vazio/Não Encontrado --}}
                <div id="noCoursesMessage"
                    class="col-span-full flex flex-col items-center justify-center gap-6 text-center w-full my-10 p-16">
                    <img src="{{ asset('imgs/notFound.png') }}" class="w-auto h-40 object-contain" alt="not-found">
                    <div>
                        <p class="text-2xl font-bold text-stone-800">Ops! Nada foi encontrado...</p>
                        <p class="text-gray-500 mt-2 text-md max-w-lg mx-auto">
                            Não encontramos nenhum curso com os termos de busca. Tente refinar a pesquisa ou comece a
                            criar novos cursos (se você for um administrador).
                        </p>
                    </div>
                </div>
            @endforelse
        </div>

        {{-- Footer de Fim do Catálogo --}}
        @if ($courses->isNotEmpty())
            <div id="catalogEnd" class="mt-16 mb-4 flex justify-center items-center">
                <p class="text-gray-400 text-sm font-semibold tracking-wide border-t border-gray-300 pt-4 px-8">
                    Fim do Catálogo
                </p>
            </div>
        @endif
    </div>
</x-app-layout>

<script>
    /**
     * Atualiza o conteúdo da lista de cursos após uma pesquisa via AJAX.
     * @param {string} html - O novo HTML para a lista de cursos.
     */
    function atualizarListaDeCursos(html) {
        const coursesList = document.getElementById('coursesList');
        const catalogEnd = document.getElementById('catalogEnd');
        const newContent = new DOMParser().parseFromString(html, 'text/html');
        const hasCourses = newContent.getElementById('noCoursesMessage') === null;

        coursesList.innerHTML = html;

        // Gerencia a visibilidade do footer de "Fim do Catálogo"
        if (catalogEnd) {
            // Verifica se a mensagem de "noCoursesMessage" existe no novo HTML
            const noCoursesMessageExists = coursesList.querySelector('#noCoursesMessage');
            
            // Exibe o footer apenas se houver cursos E se a mensagem de 'noCoursesMessage' não estiver visível.
            catalogEnd.style.display = noCoursesMessageExists ? 'none' : 'flex';
        }
    }
</script>

@vite('resources/js/app.js')
<script src="https://unpkg.com/@phosphor-icons/web"></script>