<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Lista de Cursos
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-md rounded p-6">
                <a href="{{ route('courses.create') }}" class="mb-4 inline-block bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Criar Novo Curso</a>

                @if(session('success'))
                    <div class="mb-4 text-green-600">{{ session('success') }}</div>
                @endif

                @if($courses->count())
                    <div class="overflow-x-auto">
                        <table class="min-w-full text-sm text-left">
                            <thead class="bg-gray-100">
                                <tr>
                                    <th class="p-2">Ícone</th>
                                    <th class="p-2">Banner</th>
                                    <th class="p-2">Nome</th>
                                    <th class="p-2">Coordenador</th>
                                    <th class="p-2">Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($courses as $course)
                                    <tr class="border-b">
                                        <td class="p-2">
                                            @if($course->course_icon)
                                                <img src="{{ asset('storage/' . $course->course_icon) }}" alt="Ícone" class="w-12 h-auto">
                                            @else
                                                ---
                                            @endif
                                        </td>
                                        <td class="p-2">
                                            @if($course->course_banner)
                                                <img src="{{ asset('storage/' . $course->course_banner) }}" alt="Banner" class="w-24 h-auto">
                                            @else
                                                ---
                                            @endif
                                        </td>
                                        <td class="p-2">
                                            <a href="{{ route('courses.show', $course->id) }}" class="text-blue-600 hover:underline">
                                                {{ $course->course_name }}
                                            </a>
                                        </td>
                                        <td class="p-2">
                                            {{ $course->courseCoordinator?->userAccount?->name ?? 'Nenhum coordenador definido' }}
                                        </td>
                                        <td class="p-2 space-x-1">
                                            <a href="{{ route('courses.edit', $course->id) }}" class="text-yellow-600 hover:underline">Editar</a>
                                            <a href="{{ route('courses.show', $course->id) }}" class="text-blue-600 hover:underline">Ver</a>
                                            <form action="{{ route('courses.destroy', $course->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Tem certeza que deseja excluir?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600 hover:underline">Excluir</button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <p class="text-gray-600">Nenhum curso cadastrado.</p>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>