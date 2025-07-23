<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Lista de Cursos
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            {{-- Barra de pesquisa --}}
            <div class="bg-white p-4 rounded shadow mb-4 flex justify-between items-center">
                <form method="GET" action="{{ route('courses.index') }}" class="flex items-center space-x-2 w-full">
                    <input type="text" name="search" value="{{ $search ?? '' }}"
                        placeholder="Pesquisar por nome do curso ou coordenador..."
                        class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring focus:ring-blue-300">
                    <button type="submit"
                        class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Buscar</button>
                </form>

                @if(auth()->user()?->user_type === 'admin')
                    <a href="{{ route('courses.create') }}"
                       class="ml-4 bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">
                        + Criar Curso
                    </a>
                @endif
            </div>

            {{-- Mensagem de sucesso --}}
            @if(session('success'))
                <div class="mb-4 bg-green-100 text-green-800 p-3 rounded">
                    {{ session('success') }}
                </div>
            @endif

            {{-- Grid de cursos --}}
                        @if($courses->count())
                <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($courses as $course)
                        <div class="bg-white shadow rounded overflow-hidden hover:shadow-lg transition">
                            {{-- Banner do curso --}}
                            <a href="{{ route('courses.show', $course->id) }}">
                                <img src="{{ $course->course_banner ? asset('storage/' . $course->course_banner) : asset('images/default-banner.jpg') }}"
                                     class="w-full h-32 object-cover" alt="Banner do curso">
                            </a>

                            <div class="p-4">
                                {{-- Ícone e nome do curso --}}
                                <div class="flex items-center space-x-3 mb-2">
                                    <img src="{{ $course->course_icon ? asset('storage/' . $course->course_icon) : asset('images/default-icon.png') }}"
                                         class="w-12 h-12 rounded-full border object-cover">
                                    <div>
                                        <a href="{{ route('courses.show', $course->id) }}"
                                           class="font-bold text-lg text-gray-800 hover:text-blue-600">
                                            {{ $course->course_name }}
                                        </a>
                                        <p class="text-sm text-gray-500">
                                            Coordenador:
                                            @if($course->courseCoordinator?->userAccount)
                                                <a href="{{ route('profile.view', $course->courseCoordinator->userAccount->id) }}"
                                                   class="text-blue-600 hover:underline">
                                                    {{ $course->courseCoordinator->userAccount->name }}
                                                </a>
                                            @else
                                                <span class="text-gray-400">Não definido</span>
                                            @endif
                                        </p>
                                    </div>
                                </div>

                                {{-- Contador de eventos --}}
                                <p class="text-sm text-gray-600">
                                    {{ $course->events->count() }} evento(s) disponível(is)
                                </p>

                                {{-- Botões de ação --}}
                                <div class="mt-3 flex justify-between items-center">
                                    <a href="{{ route('courses.show', $course->id) }}"
                                       class="text-blue-600 hover:underline text-sm">Ver detalhes</a>

                                    @if(auth()->user()?->user_type === 'admin')
                                        <div class="flex space-x-2">
                                            <a href="{{ route('courses.edit', $course->id) }}"
                                               class="text-yellow-600 hover:underline text-sm">Editar</a>
                                            <form action="{{ route('courses.destroy', $course->id) }}" method="POST"
                                                  onsubmit="return confirm('Tem certeza que deseja excluir?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600 hover:underline text-sm">
                                                    Excluir
                                                </button>
                                            </form>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                {{-- Paginação --}}
                <div class="mt-6 flex justify-center">
                    {{ $courses->links() }}
                </div>
            @else
                <p class="text-gray-600 text-center mt-6">Nenhum curso cadastrado.</p>
            @endif

        </div>
    </div>
</x-app-layout>
