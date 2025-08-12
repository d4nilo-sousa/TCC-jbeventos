<x-app-layout>
    <div class="py-12">
        <div class="w-full max-w-7xl mx-auto sm:px-6 lg:px-8 flex justify-center">
            <div class="w-full bg-white shadow-md rounded-2xl p-4 sm:p-6 lg:p-9 mx-auto mt-10 min-h-[70vh]">

                {{-- Barra de pesquisa e botão --}}
                <div class="flex flex-col sm:flex-row justify-between items-center mb-6">
                    <form action="{{ route('courses.index') }}" method="GET"
                        class="flex items-center w-full sm:w-auto mb-4 sm:mb-0">
                        <input type="text" name="search" value="{{ request('search') }}"
                            placeholder="Pesquisar cursos..."
                            class="border border-gray-300 rounded-l-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-indigo-500 w-full sm:w-64">
                        <button type="submit"
                            class="flex justify-center items-center bg-stone-900 py-1 text-white px-4 rounded-r-lg">
                            <img src="{{ asset('imgs/lupaBranca.png') }}" class="w-10">
                        </button>
                    </form>

                    <div class="flex justify-center gap-1">
                        <a href="{{ route('courses.create') }}"
                            class="border-2 border-blue-500 text-black gap-2 px-5 py-2 rounded-lg flex items-center justify-center
                                   hover:bg-blue-500 hover:text-white transition-colors duration-200">
                            <img src="{{ asset('imgs/add-button.png') }}" class="w-8">
                            Criar Curso
                        </a>
                    </div>


                </div>

                {{-- Mensagens de sucesso --}}
                @if (session('success'))
                    <div class="mb-4 p-4 text-green-800 bg-green-100 rounded-lg">
                        {{ session('success') }}
                    </div>
                @endif

                {{-- Lista de cursos --}}
                @if ($courses->count())
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        @foreach ($courses as $course)
                            <div class="border rounded-lg shadow hover:shadow-lg transition overflow-hidden">
                                <img src="{{ $course->image ? asset('storage/' . $course->image) : asset('imgs/placeholder.png') }}"
                                    alt="{{ $course->name }}" class="w-full h-40 object-cover">
                                <div class="p-4">
                                    <h3 class="font-bold text-lg">{{ $course->name }}</h3>
                                    <p class="text-sm text-gray-600 mb-2">{{ $course->description }}</p>
                                    <p class="text-xs text-gray-500 mb-4">
                                        Coordenador: {{ $course->coordinator->userAccount->name ?? 'Não definido' }}
                                    </p>
                                    <div class="flex flex-wrap gap-2">
                                        <a href="{{ route('courses.show', $course) }}"
                                            class="bg-blue-500 hover:bg-blue-600 text-white px-3 py-1 rounded text-sm">Ver</a>
                                        <a href="{{ route('courses.edit', $course) }}"
                                            class="bg-yellow-500 hover:bg-yellow-600 text-white px-3 py-1 rounded text-sm">Editar</a>
                                        <form action="{{ route('courses.destroy', $course) }}" method="POST"
                                            class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                onclick="return confirm('Tem certeza que deseja excluir?')"
                                                class="bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded text-sm">Excluir</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
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
