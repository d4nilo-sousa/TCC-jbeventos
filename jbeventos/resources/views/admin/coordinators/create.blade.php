<x-app-layout>
    <div class="py-6">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-lg rounded-xl overflow-hidden p-6 md:p-10">

                {{-- Título da Página --}}
                <div class="flex flex-col items-center justify-center mb-10 text-center">
                    <div class="p-3 bg-red-100 rounded-full mb-4 shadow-md flex items-center justify-center">
                        <i class="ph ph-users text-red-600 text-4xl"></i>
                    </div>
                    <h1 class="text-3xl font-bold text-gray-800">Criar Coordenador</h1>
                    <p class="mt-2 text-gray-600">Crie um novo coordenador e preencha as informações conforme necessário.</p>
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
                <form id="coordinator-form" action="{{ route('coordinators.store') }}" method="POST" class="space-y-8">
                    @csrf

                    {{-- Barra de navegação das abas --}}
                    <div class="flex items-center justify-center mb-10">
                        <div class="flex items-center space-x-2 md:space-x-8">

                            <button type="button" data-tab-target="tab-basic-info"
                                class="tab-button flex flex-col items-center group active">
                                <span
                                    class="inline-flex items-center justify-center w-8 h-8 font-bold rounded-full border-2 border-red-500 text-white bg-red-500 transition-all duration-300">1</span>
                                <span
                                    class="mt-1 text-sm text-red-600 font-medium transition-colors duration-300">Informações
                                    Básicas</span>
                            </button>

                            <div class="h-0.5 w-6 md:w-16 bg-gray-300"></div>

                            <button type="button" data-tab-target="tab-affiliation"
                                class="tab-button flex flex-col items-center">
                                <span
                                    class="inline-flex items-center justify-center w-8 h-8 font-bold rounded-full border-2 border-gray-300 bg-white text-gray-500 transition-all duration-300">2</span>
                                <span class="mt-1 text-sm text-gray-600 transition-colors duration-300">Afiliação</span>
                            </button>
                        </div>
                    </div>


                    {{-- Aba 1: Informações Básicas --}}
                    <div id="tab-basic-info" class="tab-content active space-y-6">
                        <h3 class="text-2xl font-semibold text-gray-800 border-b pb-2 mb-6">Detalhes Pessoais e de Acesso</h3>

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
                                {{-- Mantendo o botão, mas mudando a cor para Vermelho/Verde como sugerido por um fluxo de criação comum --}}
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
                            <button type="button" data-next-tab="tab-affiliation"
                                class="next-button inline-flex items-center px-6 py-2 border border-transparent rounded-md font-semibold text-sm text-white bg-red-600 hover:bg-red-700 transition ease-in-out duration-150">
                                Próximo
                            </button>
                        </div>
                    </div>

                    {{-- Aba 2: Afiliação --}}
                    <div id="tab-affiliation" class="tab-content hidden space-y-6">
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
                            <button type="button" data-prev-tab="tab-basic-info"
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

@vite('resources/js/app.js')

<script>
    document.addEventListener('DOMContentLoaded', function() {
        
        // --- Lógica de Navegação das Abas (Corrigida para Vermelho) ---
        const tabs = document.querySelectorAll('.tab-content');
        const tabButtons = document.querySelectorAll('.tab-button');
        const nextButtons = document.querySelectorAll('.next-button');
        const prevButtons = document.querySelectorAll('.prev-button');

        function updateTabState(button, isActive) {
            const circle = button.querySelector('span:first-child');
            const text = button.querySelector('span:last-child');
            
            // Definição das classes de cor (Vermelho)
            const activeCircleClasses = ['border-red-500', 'bg-red-500', 'text-white'];
            const activeTextClasses = ['text-red-600', 'font-medium'];
            const inactiveCircleClasses = ['border-gray-300', 'bg-white', 'text-gray-500'];
            const inactiveTextClasses = ['text-gray-600', 'font-medium'];


            if (isActive) {
                // Estado Ativo (Vermelho)
                circle.classList.add(...activeCircleClasses);
                circle.classList.remove(...inactiveCircleClasses);
                text.classList.add(...activeTextClasses);
                text.classList.remove(...inactiveTextClasses);
            } else {
                // Estado Inativo (Cinza)
                circle.classList.remove(...activeCircleClasses);
                circle.classList.add(...inactiveCircleClasses);
                text.classList.remove(...activeTextClasses);
                text.classList.add(...inactiveTextClasses);
            }
        }

        function showTab(tabId) {
            tabs.forEach(tab => tab.classList.add('hidden'));
            const activeTab = document.getElementById(tabId);
            if (activeTab) {
                activeTab.classList.remove('hidden');
            }

            tabButtons.forEach(button => {
                const isActive = button.dataset.tabTarget === tabId;
                updateTabState(button, isActive);
            });

            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        }

        // Lógica do botão Próximo
        nextButtons.forEach(button => {
            button.addEventListener('click', () => {
                const currentTab = button.closest('.tab-content');
                const nextTabId = button.dataset.nextTab;

                // Validação dos campos obrigatórios visíveis na aba atual
                const inputs = currentTab?.querySelectorAll('input:required, textarea:required, select:required');
                let allInputsValid = true;

                if (inputs) {
                    for (const input of inputs) {
                        // Verifica se o campo está visível e vazio
                        if (!input.value.trim() && !input.getAttribute('disabled') && input.type !== 'file') {
                            // Se for o select, precisa de uma verificação mais robusta para options
                            if (input.tagName === 'SELECT' && input.value === '') {
                                allInputsValid = false;
                                input.focus();
                                break;
                            }
                            // Campo obrigatório vazio
                            if (input.tagName !== 'SELECT') {
                                allInputsValid = false;
                                input.focus();
                                break;
                            }
                        }
                    }
                }

                // Se todos os campos estiverem válidos, vá para a próxima aba
                if (allInputsValid && nextTabId) {
                    showTab(nextTabId);
                } else if (!allInputsValid) {
                    // Força a exibição das mensagens de validação do HTML5
                    const form = document.getElementById('coordinator-form');
                    if (form) {
                        form.reportValidity();
                    }
                }
            });
        });

        // Lógica do botão Anterior
        prevButtons.forEach(button => {
            button.addEventListener('click', () => {
                const prevTabId = button.dataset.prevTab;
                if (prevTabId) {
                    showTab(prevTabId);
                }
            });
        });

        // Lógica dos botões da aba de navegação
        tabButtons.forEach(button => {
            button.addEventListener('click', () => {
                const target = button.dataset.tabTarget;
                if (target) {
                    // Permite trocar de aba apenas clicando (sem validação forçada)
                    showTab(target);
                }
            });
        });

        // Inicialização da aba correta
        const path = window.location.pathname;

        const isCreate = path === '{{ route('coordinators.create') }}' || path.endsWith('/coordinators/create');
        const isEdit = /^\/admin\/coordinators\/\d+\/edit$/.test(path); 

        if ((isCreate || isEdit) && document.getElementById('tab-basic-info')) {
            // Se houver erros no back-end, verifica qual aba deve ser exibida
            @if ($errors->any())
                // Se houver erros, tenta descobrir qual aba deve ser ativa (exemplo: se há 'coordinator_type' em erros, pula para a aba 2)
                const defaultTab = (@json($errors->has('coordinator_type') || old('coordinator_type'))) ? 'tab-affiliation' : 'tab-basic-info';
                showTab(defaultTab);
            @else
                showTab('tab-basic-info');
            @endif
        }
    });
</script>