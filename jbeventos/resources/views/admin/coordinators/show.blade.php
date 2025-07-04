<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Detalhes do Coordenador
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-md rounded p-6">
                <dl class="grid grid-cols-1 md:grid-cols-3 gap-x-4 gap-y-2">
                    <dt class="font-medium">Nome</dt>
                    <dd class="md:col-span-2">{{ $coordinator->userAccount->name }}</dd>

                    <dt class="font-medium">Email</dt>
                    <dd class="md:col-span-2">{{ $coordinator->userAccount->email }}</dd>

                    <dt class="font-medium">Tipo de Coordenador</dt>
                    <dd class="md:col-span-2">{{ ['general' => 'Geral', 'course' => 'Curso'][$coordinator->coordinator_type] }}</dd>

                    @if($coordinator->coordinator_type === 'course')
                        <dt class="font-medium">Curso que gerencia</dt>
                        <dd class="md:col-span-2">
                            {{ $coordinator->coordinatedCourse->course_name ?? 'Nenhum curso gerenciado' }}
                        </dd>
                    @endif

                    <dt class="font-medium">Criado em</dt>
                    <dd class="md:col-span-2">{{ $coordinator->created_at->format('d/m/Y H:i') }}</dd>

                    <dt class="font-medium">Atualizado em</dt>
                    <dd class="md:col-span-2">{{ $coordinator->updated_at->format('d/m/Y H:i') }}</dd>
                </dl>

                <div class="mt-6">
                    <a href="{{ route('coordinators.index') }}" class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600">Voltar</a>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
