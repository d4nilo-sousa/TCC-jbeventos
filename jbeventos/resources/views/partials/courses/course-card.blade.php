<a href="{{ route('courses.show', $course->id) }}"
    class="block group bg-white rounded-3xl p-6 text-center shadow-lg hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-1 h-full flex flex-col items-center border border-gray-100 hover:border-red-200">

    {{-- Área do Ícone Redondo (Estilo Avatar/Perfil) --}}
    <div class="mb-4 relative">
        <img src="{{ $course->course_icon ? asset('storage/' . $course->course_icon) : asset('imgs/placeholder.png') }}"
            alt="{{ $course->course_name }}"
            {{-- Borda sutil para dar um toque de cor --}}
            class="w-24 h-24 rounded-full object-cover border-4 border-red-500/30 shadow-md transition-transform duration-300 group-hover:scale-105 group-hover:shadow-xl">
        
        {{-- Adiciona um "selo" de destaque --}}
        {{-- @if ($course->is_new) --}}
            <span class="absolute bottom-0 right-0 bg-red-500 text-white text-xs font-bold px-2 py-0.5 rounded-full border-2 border-white shadow-md">
                Curso
            </span>
        {{-- @endif --}}
    </div>

    {{-- Detalhes do Curso --}}
    <div class="flex flex-col flex-grow w-full px-1">
        <h3 class="course-title font-extrabold text-xl text-stone-800 mb-1 truncate">
            {{ $course->course_name }}
        </h3>
        <p class="text-sm text-gray-500 mb-4 flex-grow">
            Coordenador:
            <span class="font-semibold text-gray-700 block mt-0.5">
                {{ $course->courseCoordinator->userAccount->name ?? 'Não definido' }}
            </span>
        </p>
    </div>

    {{-- Botão/Call to Action no Rodapé --}}
    <div class="w-full">
        <span class="inline-block w-full py-2 px-4 bg-red-600 text-white font-bold rounded-xl text-sm opacity-90 group-hover:bg-red-700 transition duration-200 shadow-lg hover:shadow-xl">
            Explorar Curso
        </span>
    </div>
</a>