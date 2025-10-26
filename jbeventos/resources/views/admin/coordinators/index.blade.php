<x-app-layout>
    <div class="py-10 bg-gray-50 min-h-screen">
        <div class="max-w-[1400px] mx-auto sm:px-6 lg:px-16 space-y-6">

            {{-- Header: Título + Botão Adicionar --}}
            <div class="flex flex-col sm:flex-row justify-between items-center mb-6 border-b pb-4 mt-1">
                <h1 class="text-4xl font-extrabold text-gray-900 mb-4 sm:mb-0">Coordenadores</h1>
                <a href="{{ route('coordinators.create') }}"
                    class="inline-flex items-center px-6 py-3 rounded-full shadow-lg bg-red-600 hover:bg-red-700 text-white font-semibold text-sm transition-all">
                    <i class="ph-bold ph-plus-circle text-lg mr-2"></i> Adicionar Coordenador
                </a>
            </div>

            {{-- Mensagem de sucesso --}}
            @if (session('success'))
                <div
                    class="p-4 rounded-xl bg-green-50 text-green-700 border border-green-200 shadow flex items-center gap-3">
                    <i class="ph-fill ph-check-circle text-green-500 text-2xl"></i>
                    <span class="font-medium">{{ session('success') }}</span>
                </div>
            @endif

            {{-- Lista de Coordenadores --}}
            <div class="bg-white rounded-3xl shadow-xl border border-gray-100 overflow-hidden">
                @if ($coordinators->count())
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Nome
                                    </th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Email
                                    </th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Curso
                                    </th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Tipo
                                    </th>
                                    <th
                                        class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Ações
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach ($coordinators as $coordinator)
                                    <tr class="hover:bg-gray-50 transition-colors">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                            <div class="flex items-center gap-4">
                                                <img class="h-10 w-10 rounded-full object-cover"
                                                    src="{{ $coordinator->userAccount->user_icon_url }}"
                                                    alt="Foto de Perfil">
                                                <span>{{ $coordinator->userAccount->name }}</span>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $coordinator->userAccount->email }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            @if ($coordinator->coordinatedCourse)
                                                <div class="flex items-center gap-3">
                                                    @if ($coordinator->coordinatedCourse->course_icon)
                                                        {{-- Se houver ícone de foto, mostra a imagem --}}
                                                        <img class="h-8 w-8 rounded-full object-cover"
                                                            src="{{ asset('storage/' . $coordinator->coordinatedCourse->course_icon) }}"
                                                            alt="Ícone do Curso">
                                                    @else
                                                        {{-- Se NÃO houver ícone de foto (mas o curso existe), mostra o ícone Phosphor --}}
                                                        {{-- Ajustei o tamanho (text-2xl) para se aproximar do h-8 w-8 da imagem e adicionei classes de alinhamento --}}
                                                        <div class="h-8 w-8 flex items-center justify-center">
                                                            <i class="ph ph-book-open text-2xl text-red-600"></i>
                                                        </div>
                                                    @endif
                                                    <span
                                                        class="font-semibold text-gray-800">{{ $coordinator->coordinatedCourse->course_name }}</span>
                                                </div>
                                            @else
                                                {{-- Se não houver curso coordenado, mostra o span de "Nenhum Curso Atribuído" --}}
                                                <span
                                                    class="px-2 py-1 inline-flex text-xs font-semibold rounded-full bg-gray-100 text-gray-800">
                                                    Nenhum Curso Atribuído
                                                </span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ ['general' => 'Geral', 'course' => 'Curso'][$coordinator->coordinator_type] }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-center">
                                            <div class="inline-flex gap-2">
                                                {{-- Editar --}}
                                                <a href="{{ route('coordinators.edit', $coordinator->id) }}"
                                                    class="inline-flex items-center justify-center h-10 w-10 rounded-full text-gray-600 hover:bg-gray-100 transition-colors"
                                                    title="Editar">
                                                    <i class="ph-fill ph-pencil text-lg"></i>
                                                </a>
                                                {{-- Excluir --}}
                                                <button type="button"
                                                    onclick="openModal('deleteModal-{{ $coordinator->id }}')"
                                                    class="inline-flex items-center justify-center h-10 w-10 rounded-full text-red-600 hover:bg-red-100 transition-colors"
                                                    title="Excluir">
                                                    <i class="ph-fill ph-trash text-lg"></i>
                                                </button>

                                                {{-- Modal de Exclusão --}}
                                                <div id="deleteModal-{{ $coordinator->id }}"
                                                    class="modal hidden fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4">
                                                    <div class="bg-white p-8 rounded-xl shadow-2xl w-full max-w-md overflow-hidden"
                                                        onclick="event.stopPropagation();">
                                                        <h2
                                                            class="text-xl font-bold mb-4 text-red-600 flex items-center gap-2 flex-wrap">
                                                            <i class="ph-bold ph-warning-circle text-2xl"></i> Confirmar
                                                            Exclusão
                                                        </h2>
                                                        <p
                                                            class="text-gray-700 w-full break-words whitespace-normal text-left">
                                                            Tem certeza que deseja excluir o(a) coordenador(a)
                                                            <strong
                                                                class="break-words whitespace-normal">{{ $coordinator->userAccount->name }}</strong>?
                                                            Esta ação é irreversível.
                                                        </p>
                                                        <div class="mt-6 flex justify-end space-x-3 flex-wrap">
                                                            <button
                                                                onclick="closeModal('deleteModal-{{ $coordinator->id }}')"
                                                                class="px-4 py-2 text-sm bg-gray-200 text-gray-700 rounded-full hover:bg-gray-300 font-medium transition">
                                                                Cancelar
                                                            </button>
                                                            <form
                                                                action="{{ route('coordinators.destroy', $coordinator->id) }}"
                                                                method="POST">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit"
                                                                    class="px-4 py-2 text-sm bg-red-600 text-white rounded-full hover:bg-red-700 font-medium transition">
                                                                    Confirmar
                                                                </button>
                                                            </form>
                                                        </div>
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
                    {{-- Estado vazio --}}
                    <div class="flex flex-col items-center justify-center text-center py-16 space-y-4">
                        <i class="ph-fill ph-users text-9xl text-gray-200"></i>
                        <h3 class="text-2xl font-bold text-gray-500">Nenhum Coordenador cadastrado</h3>
                        <p class="text-gray-400">Parece que não há coordenadores para mostrar. Clique abaixo para
                            adicionar.</p>
                        <a href="{{ route('coordinators.create') }}"
                            class="mt-4 inline-flex items-center px-6 py-3 rounded-full shadow-lg bg-red-600 hover:bg-red-700 text-white font-semibold transition-all">
                            <i class="ph-fill ph-plus text-lg mr-2"></i> Adicionar Coordenador
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Toast simples --}}
    <div id="toast"
        class="fixed bottom-5 right-5 text-white px-4 py-2 rounded-lg shadow-xl hidden z-50 transition-all duration-300">
        <span id="toast-message" class="font-medium"></span>
    </div>
</x-app-layout>

<script>
    function openModal(id) {
        document.getElementById(id).classList.remove('hidden');
    }

    function closeModal(id) {
        document.getElementById(id).classList.add('hidden');
    }

    function showToast(message) {
        const toast = document.getElementById('toast');
        const toastMsg = document.getElementById('toast-message');
        toastMsg.textContent = message;
        toast.classList.remove('hidden');
        setTimeout(() => toast.classList.add('hidden'), 3000);
    }
</script>

@vite('resources/js/app.js')
