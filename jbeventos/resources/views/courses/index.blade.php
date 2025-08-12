<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Cursos') }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">

                {{-- Barra de pesquisa e botão --}}
                <div class="flex flex-col sm:flex-row justify-between items-center mb-6">
                    <form action="{{ route('courses.index') }}" method="GET" class="flex items-center w-full sm:w-auto mb-4 sm:mb-0">
                        <input 
                            type="text" 
                            name="search" 
                            value="{{ request('search') }}" 
                            placeholder="Pesquisar cursos..." 
                            class="border border-gray-300 rounded-l-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500 w-full sm:w-64"
                        >
                        <button 
                            type="submit" 
                            class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-r-lg"
                        >
                            🔍
                        </button>
                    </form>
                    <a 
                        href="{{ route('courses.create') }}" 
                        class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg"
                    >
                        ➕ Criar Curso
                    </a>
                </div>

                {{-- Mensagens de sucesso --}}
                @if(session('success'))
                    <div class="mb-4 p-4 text-green-800 bg-green-100 rounded-lg">
                        {{ session('success') }}
                    </div>
                @endif

                {{-- Lista de cursos --}}
                @if($courses->count())
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        @foreach($courses as $course)
                            <div class="border rounded-lg shadow hover:shadow-lg transition overflow-hidden">
                                <img 
                                    src="{{ $course->image ? asset('storage/' . $course->image) : asset('imgs/placeholder.png') }}" 
                                    alt="{{ $course->name }}" 
                                    class="w-full h-40 object-cover"
                                >
                                <div class="p-4">
                                    <h3 class="font-bold text-lg">{{ $course->name }}</h3>
                                    <p class="text-sm text-gray-600 mb-2">{{ $course->description }}</p>
                                    <p class="text-xs text-gray-500 mb-4">
                                        Coordenador: {{ $course->coordinator->userAccount->name ?? 'Não definido' }}
                                    </p>
                                    <div class="flex flex-wrap gap-2">
                                        <a href="{{ route('courses.show', $course) }}" class="bg-blue-500 hover:bg-blue-600 text-white px-3 py-1 rounded text-sm">Ver</a>
                                        <a href="{{ route('courses.edit', $course) }}" class="bg-yellow-500 hover:bg-yellow-600 text-white px-3 py-1 rounded text-sm">Editar</a>
                                        <form action="{{ route('courses.destroy', $course) }}" method="POST" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" onclick="return confirm('Tem certeza que deseja excluir?')" class="bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded text-sm">Excluir</button>
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
                    <p class="text-center text-gray-500">Nenhum curso encontrado.</p>
                @endif

            </div>
        </div>
    </div>
</x-app-layout>
