<a href="{{ route('courses.show', $course->id) }}"
    class="block group relative overflow-hidden bg-white border border-gray-200 rounded-2xl shadow-md hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1">

    <img src="{{ $course->course_icon ? asset('storage/' . $course->course_icon) : asset('imgs/placeholder.png') }}"
        alt="{{ $course->course_name }}"
        class="h-48 w-full object-cover transition-transform duration-300 group-hover:scale-105">

    <div class="p-6 flex flex-col flex-grow">
        <h3 class="course-title font-bold text-2xl text-stone-800 mb-2 truncate">
            {{ $course->course_name }}
        </h3>
        <p class="text-sm text-gray-500 mb-4">
            Coordenador:
            {{ $course->courseCoordinator->userAccount->name ?? 'Não definido' }}
        </p>
    </div>

    <div
        class="absolute bottom-0 left-0 right-0 p-4 bg-white/80 backdrop-blur-sm transform translate-y-full group-hover:translate-y-0 transition-transform duration-300">
        <p class="text-sm text-gray-700 font-semibold text-center">Ver detalhes</p>
    </div>
</a>
