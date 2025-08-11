<x-app-layout>
    <div class="py-12">
        <div class="w-full max-w-7xl mx-auto sm:px-6 lg:px-8 flex justify-center">
            <div class="w-full bg-white shadow-md rounded-2xl p-4 sm:p-6 lg:p-9 mx-auto mt-2">

                <!-- Título -->
                <div class="w-full grid place-items-center mb-5 text-center">
                    <p class="text-2xl sm:text-3xl text-stone-900 font-semibold">Cursos</p>
                </div>

                <!-- Mensagem de sucesso -->
                @if (session('success'))
                <div class="mb-4 text-green-600 text-sm sm:text-base">{{ session('success') }}</div>
                @endif

                <!-- Verifica se existem cursos -->
                @if ($courses->count())
                <div class="overflow-x-auto mt-4">
                    <table class="min-w-full text-xs sm:text-sm text-left border-collapse">
                        <thead class="bg-gray-100">
                            <tr>
                                <th class="p-2 whitespace-nowrap">Ícone</th>
                                <th class="p-2 whitespace-nowrap">Banner</th>
                                <th class="p-2 whitespace-nowrap">Nome</th>
                                <th class="p-2 whitespace-nowrap">Coordenador</th>
                                <th class="p-2 whitespace-nowrap">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($courses as $course)
                            <tr class="border-b hover:bg-gray-50">
                                <td class="p-2">
                                    @if ($course->course_icon)
                                    <img src="{{ asset('storage/' . $course->course_icon) }}" alt="Ícone"
                                        class="w-10 sm:w-12 h-auto rounded-md">
                                    @else
                                    <span class="text-gray-400">---</span>
                                    @endif
                                </td>
                                <td class="p-2">
                                    @if ($course->course_banner)
                                    <img src="{{ asset('storage/' . $course->course_banner) }}"
                                        alt="Banner" class="w-20 sm:w-24 h-auto rounded-md">
                                    @else
                                    <span class="text-gray-400">---</span>
                                    @endif
                                </td>
                                <td class="p-2">
                                    <a href="{{ route('courses.show', $course->id) }}"
                                        class="text-blue-600 hover:underline break-words">
                                        {{ $course->course_name }}
                                    </a>
                                </td>
                                <td class="p-2">
                                    {{ $course->courseCoordinator?->userAccount?->name ?? 'Nenhum coordenador definido' }}
                                </td>
                                <td class="p-2 space-x-1">
                                    @auth
                                    @if (auth()->user()->user_type === 'admin')
                                    <a href="{{ route('courses.edit', $course->id) }}"
                                        class="text-yellow-600 hover:underline">Editar</a>
                                    <a href="{{ route('courses.show', $course->id) }}"
                                        class="text-blue-600 hover:underline">Ver</a>
                                    <form action="{{ route('courses.destroy', $course->id) }}"
                                        method="POST" class="inline-block"
                                        onsubmit="return confirm('Tem certeza que deseja excluir?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                            class="text-red-600 hover:underline">Excluir</button>
                                    </form>
                                    @else
                                    <a href="{{ route('courses.show', $course->id) }}"
                                        class="text-blue-600 hover:underline">Ver</a>
                                    @endif
                                    @else
                                    <a href="{{ route('courses.show', $course->id) }}"
                                        class="text-blue-600 hover:underline">Ver</a>
                                    @endauth
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <div class="w-full flex flex-col items-center">
                    <p class="text-gray-500 mt-5 text-base sm:text-lg">Nenhum curso cadastrado . . .</p>
                    <img src="{{ asset('imgs/notFound.png') }}" class="w-2/3 sm:w-1/3 lg:w-1/5 mt-6">
                </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>