<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ $course->course_name }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-md rounded p-6">
                @if($course->course_icon)
                    <div class="mb-4">
                        <strong class="block">Ícone do Curso:</strong>
                        <img src="{{ asset('storage/' . $course->course_icon) }}" alt="Ícone do Curso" class="w-24 mt-1">
                    </div>
                @endif

                @if($course->course_banner)
                    <div class="mb-4">
                        <img src="{{ asset('storage/' . $course->course_banner) }}" alt="Banner do Curso" class="w-full max-h-64 object-cover rounded">
                    </div>
                @endif

                <div class="mb-4 text-gray-700">
                    {{ $course->course_description }}
                </div>

                <div class="mb-4">
                    <strong>Coordenador:</strong> {{ $course->courseCoordinator?->userAccount?->name ?? 'Nenhum coordenador definido' }}
                </div>

                <dl class="mb-4 grid grid-cols-1 md:grid-cols-2 gap-x-8">
                    <div>
                        <dt class="font-medium text-gray-600">Criado em</dt>
                        <dd>{{ $course->created_at->format('d/m/Y H:i') }}</dd>
                    </div>
                    <div>
                        <dt class="font-medium text-gray-600">Atualizado em</dt>
                        <dd>{{ $course->updated_at->format('d/m/Y H:i') }}</dd>
                    </div>
                </dl>

                <a href="{{ route('courses.index') }}" class="inline-block bg-gray-600 text-white px-4 py-2 rounded hover:bg-gray-700">Voltar para lista</a>
            </div>
        </div>
    </div>
</x-app-layout>
