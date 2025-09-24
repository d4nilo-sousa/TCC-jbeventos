<x-app-layout>
    <div class="py-12">
        <div class="w-full max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white shadow-lg rounded-2xl p-6 sm:p-8 lg:p-12">

                <!-- Header com Título e Botão de Adicionar -->
                <div class="flex flex-col sm:flex-row justify-between items-center mb-6 border-b pb-4">
                    <h2 class="text-2xl sm:text-3xl font-bold text-gray-900 mb-4 sm:mb-0">Coordenadores</h2>
                    <a href="{{ route('coordinators.create') }}" class="inline-flex items-center px-6 py-3 border border-transparent text-sm font-medium rounded-full shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z" clip-rule="evenodd" />
                        </svg>
                        Adicionar Coordenador
                    </a>
                </div>

                <!-- Mensagem de sucesso -->
                @if (session('success'))
                    <div class="mb-6 p-4 rounded-lg bg-green-50 text-green-700 border border-green-200 shadow-sm flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-3 text-green-500" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                        </svg>
                        {{ session('success') }}
                    </div>
                @endif

                <!-- Tabela de Coordenadores -->
                @if ($coordinators->count())
                    <div class="overflow-x-auto rounded-lg shadow-sm border border-gray-200">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Nome
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Email
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Curso
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Tipo
                                    </th>
                                    <th scope="col" class="relative px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Ações
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach ($coordinators as $coordinator)
                                    <tr class="hover:bg-gray-50 transition-colors">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                            <div class="flex items-center">
                                                <div class="flex-shrink-0 h-10 w-10">
                                                    <img class="h-10 w-10 rounded-full object-cover" src="{{ $coordinator->userAccount->user_icon_url }}" alt="Foto de Perfil">
                                                </div>
                                                <div class="ml-4">
                                                    {{ $coordinator->userAccount->name }}
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $coordinator->userAccount->email }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            @if ($coordinator->coordinatedCourse)
                                                <div class="flex items-center">
                                                    <div class="flex-shrink-0 h-8 w-8">
                                                        <img class="h-8 w-8 rounded-full object-cover" src="{{ $coordinator->coordinatedCourse->course_icon ? asset('storage/' . $coordinator->coordinatedCourse->course_icon) : asset('imgs/default_course_icon.svg') }}" alt="Ícone do Curso">
                                                    </div>
                                                    <div class="ml-3 font-medium text-gray-800">
                                                        {{ $coordinator->coordinatedCourse->course_name }}
                                                    </div>
                                                </div>
                                            @else
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                                    Nenhum Curso Atribuído
                                                </span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ ['general' => 'Geral', 'course' => 'Curso'][$coordinator->coordinator_type] }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-center">
                                            <div class="inline-flex space-x-2">
                                                <a href="{{ route('coordinators.show', $coordinator->id) }}" class="inline-flex items-center justify-center h-8 w-8 rounded-full text-blue-600 hover:bg-blue-100 transition-colors" title="Ver">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                                        <path d="M10 12a2 2 0 100-4 2 2 0 000 4z" />
                                                        <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd" />
                                                    </svg>
                                                </a>
                                                <a href="{{ route('coordinators.edit', $coordinator->id) }}" class="inline-flex items-center justify-center h-8 w-8 rounded-full text-indigo-600 hover:bg-indigo-100 transition-colors" title="Editar">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                                        <path d="M17.414 2.586a2 2 0 00-2.828 0L7 10.172V13h2.828l7.586-7.586a2 2 0 000-2.828z" />
                                                        <path fill-rule="evenodd" d="M2 6a2 2 0 012-2h4a1 1 0 010 2H4v10h10v-4a1 1 0 112 0v4a2 2 0 01-2 2H4a2 2 0 01-2-2V6z" clip-rule="evenodd" />
                                                    </svg>
                                                </a>
                                                <button type="button" onclick="openModal('deleteModal-{{ $coordinator->id }}') class="delete-button inline-flex items-center justify-center h-8 w-8 rounded-full text-red-600 hover:bg-red-100 transition-colors" title="Excluir" data-id="{{ $coordinator->id }}" data-name="{{ $coordinator->userAccount->name }}">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                                        <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                                                    </svg>
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
                    <!-- Estado de lista vazia -->
                    <div class="w-full flex flex-col items-center justify-center text-center mt-12">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-20 w-20 text-gray-300" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-8-3a1 1 0 00-.867.5L5.433 14h9.134l-3.7-6.5A1 1 0 0010 7z" clip-rule="evenodd" />
                        </svg>
                        <p class="text-xl sm:text-2xl font-semibold text-gray-500 mt-4">Nenhum Coordenador cadastrado.</p>
                        <p class="text-sm text-gray-400 mt-2">Parece que não há nenhum coordenador para mostrar. Clique no botão abaixo para adicionar um.</p>
                        <a href="{{ route('coordinators.create') }}" class="mt-6 inline-flex items-center px-6 py-3 border border-transparent text-sm font-medium rounded-full shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all">
                            Adicionar Coordenador
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>

<!-- Modal de Confirmação de Exclusão -->
<div id="deleteModal" class="fixed inset-0 z-50 overflow-y-auto hidden flex items-center justify-center bg-gray-900 bg-opacity-50 transition-opacity duration-300 ease-in-out" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="relative bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:max-w-lg sm:w-full">
        <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
            <div class="sm:flex sm:items-start">
                <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                    <svg class="h-6 w-6 text-red-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                </div>
                <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                    <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                        Confirmar Exclusão
                    </h3>
                    <div class="mt-2">
                        <p id="modal-text" class="text-sm text-gray-500">
                            Você tem certeza que deseja excluir este item? Esta ação não pode ser desfeita.
                        </p>
                    </div>
                </div>
            </div>
        </div>
        <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
            <button id="confirmDeleteButton" type="button" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto sm:text-sm">
                Excluir
            </button>
            <button id="cancelDeleteButton" type="button" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:w-auto sm:text-sm">
                Cancelar
            </button>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const modal = document.getElementById('deleteModal');
        const confirmBtn = document.getElementById('confirmDeleteButton');
        const cancelBtn = document.getElementById('cancelDeleteButton');
        const form = document.createElement('form');

        form.method = 'POST';
        form.style.display = 'none';
        form.innerHTML = `@csrf @method('DELETE')`;
        document.body.appendChild(form);

        document.querySelectorAll('.delete-button').forEach(button => {
            button.addEventListener('click', function (e) {
                const coordinatorId = this.dataset.id;
                const coordinatorName = this.dataset.name;
                
                document.getElementById('modal-text').textContent = `Você tem certeza que deseja excluir o coordenador(a) "${coordinatorName}"? Esta ação não pode ser desfeita.`;

                form.action = `{{ url('coordinators') }}/${coordinatorId}`;
                
                modal.classList.remove('hidden');
                setTimeout(() => modal.classList.add('opacity-100'), 10);
            });
        });

        cancelBtn.addEventListener('click', function () {
            modal.classList.add('hidden');
        });

        confirmBtn.addEventListener('click', function () {
            form.submit();
        });
    });
</script>
{{-- Scripts compilados --}}
@vite('resources/js/app.js')
