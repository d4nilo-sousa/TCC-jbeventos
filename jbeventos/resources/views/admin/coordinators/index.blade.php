<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Coordenadores
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-md rounded p-6">
                <a href="{{ route('coordinators.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Novo Coordenador</a>

                @if(session('success'))
                    <div class="mt-4 text-green-600">{{ session('success') }}</div>
                @endif

                @if($coordinators->count())
                    <div class="overflow-x-auto mt-4">
                        <table class="min-w-full bg-white border border-gray-200">
                            <thead class="bg-gray-100">
                                <tr>
                                    <th class="px-4 py-2 border">Nome</th>
                                    <th class="px-4 py-2 border">Email</th>
                                    <th class="px-4 py-2 border">Tipo</th>
                                    <th class="px-4 py-2 border">Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($coordinators as $coordinator)
                                <tr>
                                    <td class="px-4 py-2 border">{{ $coordinator->userAccount->name }}</td>
                                    <td class="px-4 py-2 border">{{ $coordinator->userAccount->email }}</td>
                                    <td class="px-4 py-2 border">{{ ['general' => 'Geral', 'course' => 'Curso'][$coordinator->coordinator_type] }}</td>
                                    <td class="px-4 py-2 border space-x-1">
                                        <a href="{{ route('coordinators.show', $coordinator->id) }}" class="bg-blue-500 text-white px-2 py-1 rounded text-sm">Ver</a>
                                        <a href="{{ route('coordinators.edit', $coordinator->id) }}" class="bg-yellow-400 text-white px-2 py-1 rounded text-sm">Editar</a>
                                        <form action="{{ route('coordinators.destroy', $coordinator->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Confirma exclusão?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="bg-red-500 text-white px-2 py-1 rounded text-sm">Excluir</button>
                                        </form>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <p class="mt-4">Nenhum coordenador cadastrado.</p>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
