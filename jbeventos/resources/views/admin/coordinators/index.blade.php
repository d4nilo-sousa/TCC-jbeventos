<x-app-layout>
    <div class="py-12">
        <div class="w-full max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white shadow-lg rounded-2xl p-6 sm:p-8 lg:p-12">

                <!-- Header com Título e Botão de Adicionar -->
                <div class="flex flex-col sm:flex-row justify-between items-center mb-6 border-b pb-4">
                    <h2 class="text-2xl sm:text-3xl font-bold text-gray-900 mb-4 sm:mb-0">Coordenadores</h2>
                    <a href="{{ route('coordinators.create') }}"
                        class="inline-flex items-center px-6 py-3 border border-transparent text-sm font-medium rounded-full shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20"
                            fill="currentColor">
                            <path fill-rule="evenodd"
                                d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z"
                                clip-rule="evenodd" />
                        </svg>
                        Adicionar Coordenador
                    </a>
                </div>

                <!-- Mensagem de sucesso -->
                @if (session('success'))
                    <div
                        class="mb-6 p-4 rounded-lg bg-green-50 text-green-700 border border-green-200 shadow-sm flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-3 text-green-500" viewBox="0 0 20 20"
                            fill="currentColor">
                            <path fill-rule="evenodd"
                                d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                clip-rule="evenodd" />
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
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Nome</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Email</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Curso</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Tipo</th>
                                    <th
                                        class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Ações</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach ($coordinators as $coordinator)
                                    <tr class="hover:bg-gray-50 transition-colors">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                            <div class="flex items-center">
                                                <div class="flex-shrink-0 h-10 w-10">
                                                    <img class="h-10 w-10 rounded-full object-cover"
                                                        src="{{ $coordinator->userAccount->user_icon_url }}"
                                                        alt="Foto de Perfil">
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
                                                        <img class="h-8 w-8 rounded-full object-cover"
                                                            src="{{ $coordinator->coordinatedCourse->course_icon ? asset('storage/' . $coordinator->coordinatedCourse->course_icon) : asset('imgs/default_course_icon.svg') }}"
                                                            alt="Ícone do Curso">
                                                    </div>
                                                    <div class="ml-3 font-medium text-gray-800">
                                                        {{ $coordinator->coordinatedCourse->course_name }}
                                                    </div>
                                                </div>
                                            @else
                                                <span
                                                    class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                                    Nenhum Curso Atribuído
                                                </span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ ['general' => 'Geral', 'course' => 'Curso'][$coordinator->coordinator_type] }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-center">
                                            <div class="inline-flex space-x-2">
                                                
                                                <!-- Botão Editar -->
                                                <a href="{{ route('coordinators.edit', $coordinator->id) }}"
                                                    class="inline-flex items-center justify-center h-8 w-8 rounded-full text-indigo-600 hover:bg-indigo-100 transition-colors"
                                                    title="Editar">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5"
                                                        viewBox="0 0 20 20" fill="currentColor">
                                                        <path
                                                            d="M17.414 2.586a2 2 0 00-2.828 0L7 10.172V13h2.828l7.586-7.586a2 2 0 000-2.828z" />
                                                        <path fill-rule="evenodd"
                                                            d="M2 6a2 2 0 012-2h4a1 1 0 010 2H4v10h10v-4a1 1 0 112 0v4a2 2 0 01-2 2H4a2 2 0 01-2-2V6z"
                                                            clip-rule="evenodd" />
                                                    </svg>
                                                </a>

                                                <!-- Botão Excluir -->
                                                <button type="button"
                                                    onclick="openModal('deleteModal-{{ $coordinator->id }}')"
                                                    class="inline-flex items-center justify-center h-8 w-8 rounded-full text-gray-800 hover:bg-gray-100 transition-colors"
                                                    title="Excluir">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5"
                                                        viewBox="0 0 20 20" fill="currentColor">
                                                        <path fill-rule="evenodd"
                                                            d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z"
                                                            clip-rule="evenodd" />
                                                    </svg>
                                                </button>

                                                <div id="deleteModal-{{ $coordinator->id }}"
                                                    class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 hidden">
                                                    <div
                                                        class="bg-white p-6 sm:p-8 rounded-md shadow-md max-w-md w-full">
                                                        <h2
                                                            class="text-lg sm:text-xl font-semibold mb-4 text-red-600 text-center">
                                                            Confirmar Exclusão
                                                        </h2>
                                                        <p class="text-sm sm:text-base text-gray-700 mb-6 text-justify">
                                                            Tem certeza que deseja excluir este coordenador? <br> Esta ação
                                                            não poderá ser desfeita.
                                                        </p>
                                                        <div class="flex justify-center space-x-3">
                                                            <button type="button"
                                                                onclick="closeModal('deleteModal-{{ $coordinator->id }}')"
                                                                class="px-4 py-2 bg-gray-300 text-gray-800 rounded hover:bg-gray-400 transition-colors">
                                                                Cancelar
                                                            </button>
                                                            <form
                                                                action="{{ route('coordinators.destroy', $coordinator->id) }}"
                                                                method="POST">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit"
                                                                    class="px-4 py-2 bg-red-500 text-white rounded hover:bg-red-600 transition-colors">
                                                                    Confirmar Exclusão
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
                    <!-- Estado de lista vazia -->
                    <div class="w-full flex flex-col items-center justify-center text-center mt-12">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-20 w-20 text-gray-300" viewBox="0 0 20 20"
                            fill="currentColor">
                            <path fill-rule="evenodd"
                                d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-8-3a1 1 0 00-.867.5L5.433 14h9.134l-3.7-6.5A1 1 0 0010 7z"
                                clip-rule="evenodd" />
                        </svg>
                        <p class="text-xl sm:text-2xl font-semibold text-gray-500 mt-4">Nenhum Coordenador cadastrado.
                        </p>
                        <p class="text-sm text-gray-400 mt-2">Parece que não há nenhum coordenador para mostrar. Clique
                            no botão abaixo para adicionar um.</p>
                        <a href="{{ route('coordinators.create') }}"
                            class="mt-6 inline-flex items-center px-6 py-3 border border-transparent text-sm font-medium rounded-full shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all">
                            Adicionar Coordenador
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>

{{-- Scripts compilados --}}
@vite('resources/js/app.js')
