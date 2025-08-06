<x-app-layout>
    <div class="py-12">
        <div class="w-100% mx-auto sm:px-6 lg:px-8 flex justify-center">
            <div class="w-[100rem] h-[45rem] bg-white shadow-md rounded-2xl p-9 mx-auto mt-2">
                <div class="w-100% grid place-items-center mb-5">
                    <p class="text-[2rem] text-stone-900">Coordenadores</p>
                    <hr class="border-t-1 border-gray-100">
                </div>

                @if (session('success'))
                    <div class="mt-4 text-green-600">{{ session('success') }}</div>
                @endif

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
                                        <td class="px-4 py-2 border">{{ $coordinator->userAccount->name }}</td>
                                        <td class="px-4 py-2 border">{{ $coordinator->userAccount->email }}</td>
                                        <td class="px-4 py-2 border">
                                            {{ ['general' => 'Geral', 'course' => 'Curso'][$coordinator->coordinator_type] }}
                                        </td>
                                        <td class="px-4 py-2 border space-x-1">
                                            <a href="{{ route('coordinators.show', $coordinator->id) }}"
                                                class="bg-blue-500 text-white px-2 py-1 rounded text-sm">Ver</a>
                                            <a href="{{ route('coordinators.edit', $coordinator->id) }}"
                                                class="bg-yellow-400 text-white px-2 py-1 rounded text-sm">Editar</a>
                                            <form action="{{ route('coordinators.destroy', $coordinator->id) }}"
                                                method="POST" class="inline-block"
                                                onsubmit="return confirm('Confirma exclusão?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                    class="bg-red-500 text-white px-2 py-1 rounded text-sm">Excluir</button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                <br>
                    <div class="w-100% flex justify-center">
                        <p class="text-gray-500 mt-5 text-lg">Nenhum Coordenador cadastrado . . .</p>
                    </div>

                    <div class="w-100% flex justify-center mt-10 mb-20">
                        <img src="{{ asset('imgs/notFound2.png') }}" class="w-1/5">
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
