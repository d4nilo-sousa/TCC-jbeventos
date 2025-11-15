<x-app-layout>
    <div class="py-10 bg-gray-50 min-h-screen">
        <div class="max-w-[1400px] mx-auto sm:px-6 lg:px-16 space-y-6">

            {{-- Cabeçalho: Título, Botões e Pesquisa --}}
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-end gap-5 mb-10">
                {{-- Título --}}
                <div class="mt-1">
                    <p class="text-3xl sm:text-4xl mt-5 font-extrabold text-stone-800 tracking-tight drop-shadow-sm">
                        Catálogo de Cursos
                    </p>
                    <div class="w-16 h-1 bg-red-500 rounded-full mt-2 shadow-lg"></div>
                </div>

                {{-- Área de Ações: Botão de Criar e Pesquisa --}}
                <div class="flex flex-col sm:flex-row items-center gap-4 w-full sm:w-auto">

                    {{-- Formulário de Pesquisa --}}
<form action="{{ route('courses.index') }}" method="GET" class="w-full flex-grow max-w-sm">
    <div class="relative flex items-center w-full shadow-md rounded-full bg-white">
        {{-- Ícone de lupa --}}
        <svg class="absolute left-4 w-5 h-5 text-gray-500" fill="none" stroke="currentColor"
            stroke-width="2" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"
            aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round"
                d="M21 21l-4.35-4.35m0 0A7.5 7.5 0 1110.5 3a7.5 7.5 0 016.15 13.65z"></path>
        </svg>

        {{-- Input de pesquisa --}}
        <input id="searchInput" type="text" name="search" value="{{ request('search') }}"
            placeholder="Pesquisar cursos..." autocomplete="off"
            class="w-full pl-11 pr-10 py-2.5 border border-gray-200 rounded-full focus:ring-red-500 focus:border-red-500 text-sm placeholder-gray-500 bg-transparent">

        {{-- Botão de limpar --}}
        <button type="button" id="clearSearchButton"
            class="absolute right-3 text-gray-400 hover:text-red-600 transition hidden"
            onclick="
                const input = document.getElementById('searchInput');
                input.value = '';
                input.focus();
                this.classList.add('hidden');
                input.dispatchEvent(new Event('input')); // força o AJAX a atualizar
            ">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none"
                viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>
    </div>
</form>

<script>
const searchInput = document.getElementById('searchInput');
const clearButton = document.getElementById('clearSearchButton');

// Mostra ou esconde o botão conforme o valor do input
searchInput.addEventListener('input', () => {
    if (searchInput.value.trim() === '') {
        clearButton.classList.add('hidden');
    } else {
        clearButton.classList.remove('hidden');
    }
});

// Se houver valor inicial (ex.: reload com query), mostra o botão
if (searchInput.value.trim() !== '') {
    clearButton.classList.remove('hidden');
}
</script>

                </div>
            </div>

            {{-- Mensagem de Sucesso --}}
            @if (session('success'))
                <div id="success-message"
                    class="mb-6 p-4 text-green-700 bg-green-50 rounded-xl shadow-md border border-green-200 flex items-center gap-3 transition-all duration-500">
                    <i class="ph-fill ph-check-circle text-green-500 text-2xl"></i>
                    <span class="font-medium">{{ session('success') }}</span>
                </div>
            @endif

            <script>
                // Faz a mensagem sumir depois de 4 segundos
                setTimeout(() => {
                    const msg = document.getElementById('success-message');
                    if (msg) {
                        msg.classList.add('opacity-0', 'translate-y-2'); // animação suave
                        setTimeout(() => msg.remove(), 500); // remove do DOM após sumir
                    }
                }, 4000);
            </script>

            {{-- Lista de Cursos --}}
            <div id="coursesList" data-url="{{ route('courses.index') }}"
                class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-8 mt-4">
                @forelse ($courses as $course)
                    @include('partials.courses.course-card', ['course' => $course])
                @empty
                    {{-- Mensagem de Vazio/Não Encontrado (Classes de espaçamento ajustadas) --}}
                    <div id="noCoursesMessage" {{-- ALTERADO: Redução de my-10 para my-4 e p-16 para p-6 --}}
                        class="col-span-full flex flex-col items-center justify-center gap-6 text-center w-full my-4 p-6">
                        <img src="{{ asset('imgs/notFound.png') }}" class="w-auto h-40 object-contain" alt="not-found">
                        <div>
                            <p class="text-2xl font-bold text-stone-800">Ops! Nada foi encontrado...</p>
                            <p class="text-gray-500 mt-2 text-md max-w-lg mx-auto">
                                Não encontramos nenhum curso com os termos de busca. Tente refazer pesquisa!
                            </p>
                        </div>
                    </div>
                @endforelse
            </div>
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
