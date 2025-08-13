<x-app-layout>
    <div class="py-12">
        <div class="w-full max-w-[100rem] mx-auto sm:px-6 lg:px-5 flex justify-center">
            <div class="w-full bg-white shadow-md rounded-2xl p-4 sm:p-6 lg:p-9 mx-auto mt-10 min-h-[70vh]">

               {{-- Barra de pesquisa e botão --}}
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 px-3 gap-5">

                    {{-- Lado esquerdo: título --}}
                    <p class="text-stone-800 text-base font-bold sm:text-4xl m-0 sm:m-3">Lista de Cursos</p>

                    {{-- Lado direito: botão e pesquisa --}}
                    <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3 sm:gap-5 w-full sm:w-auto">

                        {{-- Botão Criar Curso --}}
                        <div class="flex justify-center gap-1 border-2 rounded-full overflow-hidden shadow-md transition-colors duration-200">
                            <a href="{{ route('courses.create') }}"
                            class="text-blue-700 gap-2 px-5 py-2 rounded-lg flex items-center justify-center
                            hover:bg-blue-500 hover:text-white transition-colors duration-200">
                                <img src="{{ asset('imgs/add-button.png') }}" class="w-8">
                                Criar Curso
                            </a>
                        </div>

                        {{-- Barra de pesquisa --}}
                        <form action="{{ route('courses.index') }}" method="GET"
                        class="flex items-center w-full sm:w-auto">
                            <div class="flex items-center bg-transparent rounded-full overflow-hidden shadow-md border-2 w-full">
                                <input type="text" name="search" value="{{ request('search') }}"
                                placeholder="Pesquisar cursos..."
                                autocomplete="off"
                                class="px-6 py-3 w-full sm:w-96 text-gray-800 placeholder-gray-500
                                border-none outline-none focus:ring-0 bg-white">
                                    <button type="submit"
                                    class="flex items-center justify-center bg-stone-900 hover:bg-stone-800 transition-colors px-6 py-3">
                                        <img src="{{ asset('imgs/lupaBranca.svg') }}" class="w-7 h-7">
                                    </button>
                            </div>
                        </form>

                    </div>

                </div>

                <hr class="flex mx-auto w-full">

                {{-- Mensagens de sucesso --}}
                @if (session('success'))
                    <div class="mb-4 p-4 text-green-800 bg-green-100 rounded-lg">
                        {{ session('success') }}
                    </div>
                @endif

                {{-- Lista de cursos --}}
                
                @if ($courses->count())
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-10 mt-20 px-10">
                        @foreach ($courses as $course)
                        <a href="{{ route('courses.show', $course) }}">
                            <div class="border rounded-lg shadow hover:shadow-lg transition overflow-hidden">
                                <img src="{{ $course->image ? asset('storage/' . $course->image) : asset('imgs/placeholder.png') }}"
                                    alt="{{ $course->name }}" class="w-full h-40 object-cover">
                                <div class="p-6 w-auto">
                                    <h3 class="font-bold text-2xl text-blue-500 mb-2">{{ $course->course_name }}</h3>
                                    <p class="text-xs text-gray-500 mb-4">
                                        Coordenador: {{ $course->coordinator->userAccount->name ?? 'Não definido' }}
                                    </p>
                                </div>
                            </div>
                        </a>
                        @endforeach
                    </div>

                    {{-- Paginação --}}
                    <div class="mt-6">
                        {{ $courses->links() }}
                    </div>
                @else
                    <div class="w-full flex flex-col items-center mt-16">
                        <p class="text-gray-500 mt-5 text-base sm:text-lg">Nenhum evento cadastrado . . .</p>
                    </div>
                @endif

            </div>
        </div>
    </div>
</x-app-layout>
