<x-app-layout>
    <!-- Cabeçalho da página com o nome do curso -->
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ $course->course_name }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-md rounded p-6">
                <!-- Exibe o ícone do curso se existir -->
                @if($course->course_icon)
                    <div class="mb-4">
                        <strong class="block">Ícone do Curso:</strong>
                        <img src="{{ asset('storage/' . $course->course_icon) }}" alt="Ícone do Curso" class="w-24 mt-1">
                    </div>
                @endif

                <!-- Exibe o banner do curso se existir -->
                @if($course->course_banner)
                    <div class="mb-4">
                        <img src="{{ asset('storage/' . $course->course_banner) }}" alt="Banner do Curso" class="w-full max-h-64 object-cover rounded">
                    </div>
                @endif

                <!-- Descrição do curso -->
                <div class="mb-4 text-gray-700">
                    {{ $course->course_description }}
                </div>

                <!-- Nome do coordenador, ou texto padrão se não definido -->
                <div class="mb-4">
                    <strong>Coordenador:</strong> {{ $course->courseCoordinator?->userAccount?->name ?? 'Nenhum coordenador definido' }}
                </div>

                <!-- Exibe datas de criação e atualização somente se o usuário for admin -->
                @if(auth()->user()->is_admin)
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
                @endif

                <!-- Botões para voltar à lista, editar e excluir (editar e excluir só para admin) -->
                <div class="flex items-center gap-2 mt-4">
                    <a href="{{ route('courses.index') }}" class="inline-block bg-gray-600 text-white px-4 py-2 rounded hover:bg-gray-700">Voltar para lista</a>

                    @if(auth()->user()->is_admin)
                        <a href="{{ route('courses.edit', $course->id) }}" class="inline-block bg-yellow-500 text-white px-4 py-2 rounded hover:bg-yellow-600">Editar</a>

                        <form action="{{ route('courses.destroy', $course->id) }}" method="POST" onsubmit="return confirm('Tem certeza que deseja excluir este curso?')" class="inline-block">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700">Excluir</button>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
