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
                                            <form action="{{ route('coordinators.destroy', $coordinator->id) }}"
                                                method="POST" class="inline-block"
                                                onsubmit="return confirm('Confirma exclusão?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                    class="inline-flex w-16 justify-center bg-red-500 text-white py-1.5 rounded text-sm sm:text-base m-1">
                                                    Excluir
                                                </button>
                                            </form>
                                        </td>

                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="w-full flex flex-col items-center mt-16">
                        <!-- flex-grow para preencher o espaço restante -->
                        <p class="text-gray-500 mt-5 text-base sm:text-lg">Nenhum Coordenador cadastrado . . .</p>
                        <img src="{{ asset('imgs/notFound2.png') }}" class="w-2/3 sm:w-1/3 lg:w-1/5 mt-6">
                    </div>
                @endif

            </div>
        </div>
    </div>
</x-app-layout>
