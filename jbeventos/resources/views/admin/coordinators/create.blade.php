<x-app-layout>
    <div class="py-6">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-lg rounded-xl overflow-hidden p-6 md:p-10">

                {{-- Título da Página --}}
                <div class="flex flex-col items-center justify-center mb-10 text-center">
                    <div class="p-3 bg-red-100 rounded-full mb-4 shadow-md flex items-center justify-center">
                        {{-- Ícone ajustado para ser mais genérico ou usando uma classe de ícone comum --}}
                        <i class="ph ph-users text-red-600 text-4xl"></i>
                    </div>
                    <h1 class="text-3xl font-bold text-gray-800">Criar Coordenador</h1>
                    <p class="mt-2 text-gray-600">Crie um novo coordenador e preencha as informações conforme
                        necessário.</p>
                </div>

                {{-- Exibição de erros de validação --}}
                @if ($errors->any())
                    <div class="mb-6 p-4 bg-red-50 border-l-4 border-red-500 text-red-700 rounded-md animate-fade-in"
                        role="alert">
                        <div class="flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 mr-3 text-red-500" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <span class="font-bold">Atenção!</span>
                        </div>
                        <ul class="mt-2 list-disc list-inside space-y-1 text-sm">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                {{-- Formulário de Criação --}}
                {{-- ADICIONANDO DATA ATTRIBUTES PARA O JS DE INICIALIZAÇÃO PÓS-ERRO --}}
                <form id="coordinator-form" action="{{ route('coordinators.store') }}" method="POST"
                    class="space-y-8"
                    data-form-type="coordinator-create" {{-- Identificador para o JS --}}
                    data-has-errors="{{ $errors->any() ? 'true' : 'false' }}"
                    data-error-fields="{{ json_encode(array_keys($errors->messages())) }}"> {{-- Passa os campos com erro --}}
                    @csrf

                    {{-- Barra de navegação das abas --}}
                    <div class="flex items-center justify-center mb-10">
                        <div class="flex items-center space-x-2 md:space-x-8">

                            {{-- Botão para a Aba 1 (tab1) --}}
                            <button type="button" data-tab-target="tab1"
                                class="tab-button flex flex-col items-center group active">
                                <span
                                    class="inline-flex items-center justify-center w-8 h-8 font-bold rounded-full border-2 border-red-500 text-white bg-red-500 transition-all duration-300">1</span>
                                <span
                                    class="mt-1 text-sm text-red-600 font-medium transition-colors duration-300">Informações
                                    Básicas</span>
                            </button>

                            <div class="h-0.5 w-6 md:w-16 bg-gray-300"></div>

                            {{-- Botão para a Aba 2 (tab2) --}}
                            <button type="button" data-tab-target="tab2"
                                class="tab-button flex flex-col items-center">
                                <span
                                    class="inline-flex items-center justify-center w-8 h-8 font-bold rounded-full border-2 border-gray-300 bg-white text-gray-500 transition-all duration-300">2</span>
                                <span class="mt-1 text-sm text-gray-600 transition-colors duration-300">Afiliação</span>
                            </button>
                        </div>
                    </div>


                    {{-- Aba 1: Informações Básicas --}}
                    <div id="tab1" class="tab-content active space-y-6">
                        <h3 class="text-2xl font-semibold text-gray-800 border-b pb-2 mb-6">Detalhes Pessoais e de
                            Acesso</h3>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            {{-- Nome do Coordenador --}}
                            <div>
                                <x-input-label for="name" value="Nome do Coordenador" />
                                <x-text-input type="text" name="name" id="name" value="{{ old('name') }}"
                                    class="w-full focus:border-red-500 focus:ring-red-500" required />
                            </div>

                            {{-- Email --}}
                            <div>
                                <x-input-label for="email" value="Email" />
                                <x-text-input type="email" name="email" id="email"
                                    class="lowercase w-full focus:border-red-500 focus:ring-red-500"
                                    value="{{ old('email') }}" required />
                            </div>
                        </div>

                        {{-- Senha Provisória --}}
                        <div>
                            <x-input-label for="password" value="Senha Provisória" />
                            <div class="relative mt-1">
                                <x-text-input type="text" name="password" id="generated_password" value=""
                                    class="w-full pr-12 bg-gray-100 cursor-not-allowed" readonly required />
                                {{-- Mantendo o onclick para a função generatePassword() (assumindo que está em um JS utilitário) --}}
                                <button type="button" onclick="generatePassword()"
                                    class="absolute inset-y-0 right-0 px-4 flex items-center text-sm font-semibold text-white bg-red-600 rounded-r-md hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                                    Gerar
                                </button>
                            </div>
                        </div>

                        <h3 class="text-xl font-semibold text-gray-700 border-b pb-2 mt-10"></h3>

                        <div class="flex justify-between mt-8">
                            <a href="{{ route('coordinators.index') }}"
                                class="prev-button inline-flex items-center px-6 py-2 border border-gray-300 rounded-md font-semibold text-sm text-gray-700 bg-white hover:bg-gray-100 transition ease-in-out duration-150">
                                Cancelar
                            </a>
                            <button type="button" data-next-tab="tab2"
                                class="next-button inline-flex items-center px-6 py-2 border border-transparent rounded-md font-semibold text-sm text-white bg-red-600 hover:bg-red-700 transition ease-in-out duration-150">
                                Próximo
                            </button>
                        </div>
                    </div>

                    {{-- Aba 2: Afiliação --}}
                    <div id="tab2" class="tab-content hidden space-y-6">
                        <h3 class="text-2xl font-semibold text-gray-800 border-b pb-2 mb-6">Afiliação</h3>

                        {{-- Tipo de Coordenador --}}
                        <div>
                            <x-input-label for="coordinator_type" value="Tipo de Coordenador" />
                            <select id="coordinator_type" name="coordinator_type"
                                class="w-full mt-1 border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500"
                                required>
                                <option value="" disabled selected>Selecione...</option>
                                <option value="general" {{ old('coordinator_type') == 'general' ? 'selected' : '' }}>
                                    Geral</option>
                                <option value="course" {{ old('coordinator_type') == 'course' ? 'selected' : '' }}>
                                    Curso</option>
                            </select>
                            @error('coordinator_type')
                                <div class="text-red-600 text-sm mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <h3 class="text-xl font-semibold text-gray-700 border-b pb-2 mt-10"></h3>

                        <div class="flex justify-between pt-4">
                            <button type="button" data-prev-tab="tab1"
                                class="prev-button inline-flex items-center px-6 py-2 border border-gray-300 rounded-md font-semibold text-sm text-gray-700 bg-white hover:bg-gray-100 transition ease-in-out duration-150">
                                Anterior
                            </button>
                            <button type="submit"
                                class="submit-button inline-flex items-center px-6 py-2 border border-transparent rounded-md font-semibold text-sm text-white bg-red-600 hover:bg-red-700 transition ease-in-out duration-150">
                                Criar Coordenador
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>

@vite(['resources/js/tabs-navigation.js', 'resources/js/coordinator-utils.js'])