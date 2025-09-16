<x-app-layout>
    <div class="py-12">
        <div class="w-full max-w-7xl mx-auto sm:px-6 lg:px-8 flex justify-center">
            <div class="w-full bg-white shadow-md rounded-2xl p-4 sm:p-6 lg:p-9 mx-auto h-[65vh]">

                <!-- Título -->
                <div class="w-full grid place-items-center mb-5 text-center">
                    <p class="text-2xl sm:text-3xl text-stone-900 font-semibold">Coordenadores</p>
                </div>

                <!-- Mensagem de sucesso -->
                @if (session('success'))
                    <div class="mb-4 text-green-600 text-sm sm:text-base">{{ session('success') }}</div>
                @endif

                <!-- Tabela -->
                @if ($coordinators->count())
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
                                @foreach ($coordinators as $coordinator)
                                    <tr>
                                        <td class="px-4 py-5 border text-center">
                                            {{ $coordinator->userAccount->name }}
                                        </td>
                                        <td class="px-4 py-2 border text-center">
                                            {{ $coordinator->userAccount->email }}
                                        </td>
                                        <td class="px-4 py-2 border text-center">
                                            {{ ['general' => 'Geral', 'course' => 'Curso'][$coordinator->coordinator_type] }}
                                        </td>
                                        <td class="px-4 py-2 border text-center">
                                            <a href="{{ route('coordinators.show', $coordinator->id) }}"
                                                class="inline-flex w-16 justify-center bg-blue-500 text-white py-1.5 rounded text-sm sm:text-base m-1">
                                                Ver
                                            </a>
                                            <a href="{{ route('coordinators.edit', $coordinator->id) }}"
                                                class="inline-flex w-16 justify-center bg-stone-700 text-white py-1.5 rounded text-sm sm:text-base m-1">
                                                Editar
                                            </a>

                                            <button type="button"
                                                onclick="openModal('deleteModal-{{ $coordinator->id }}')"
                                                class="bg-red-500 text-white px-4 py-2 rounded hover:bg-red-600">
                                                Excluir
                                            </button>

                                            <!-- Modal para este coordenador -->
                                            <div id="deleteModal-{{ $coordinator->id }}"
                                                class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 hidden">
                                                <div class="bg-white p-6 rounded-md shadow-md max-w-md w-full">
                                                    <h2 class="text-lg font-semibold mb-4 text-red-600">Confirmar Exclusão</h2>
                                                    <p>Tem certeza que deseja excluir este coordenador? Esta ação não poderá ser desfeita.</p>
                                                    <div class="mt-6 flex justify-end space-x-2">
                                                        <button type="button"
                                                            onclick="closeModal('deleteModal-{{ $coordinator->id }}')"
                                                            class="px-4 py-2 bg-gray-300 rounded hover:bg-gray-400">
                                                            Cancelar
                                                        </button>
                                                        <form action="{{ route('coordinators.destroy', $coordinator->id) }}" method="POST">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="px-4 py-2 bg-red-500 text-white rounded hover:bg-red-600">
                                                                Confirmar Exclusão
                                                            </button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="w-full flex flex-col items-center mt-16">
                        <p class="text-gray-500 mt-5 text-base sm:text-lg">Nenhum Coordenador cadastrado . . .</p>
                        <img src="{{ asset('imgs/notFound2.png') }}" class="w-2/3 sm:w-1/3 lg:w-1/5 mt-6">
                    </div>
                @endif

            </div>
        </div>
    </div>
</x-app-layout>

<script>
    function openModal(id) {
        document.getElementById(id).classList.remove('hidden');
    }

    function closeModal(id) {
        document.getElementById(id).classList.add('hidden');
    }
</script>
