<x-app-layout>
    <div class="py-6">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-lg rounded-xl overflow-hidden p-6 md:p-10">

                {{-- Título da Página --}}
                <div class="flex flex-col items-center justify-center mb-10 text-center">
                    <div class="p-3 bg-indigo-50 rounded-full mb-4 shadow-sm flex items-center justify-center">
                        <img src="{{ asset('imgs/coordinator.png') }}" class="h-10 w-10 text-indigo-600">
                    </div>
                    <h2 class="text-3xl font-bold text-gray-900">Cadastro de Novo Coordenador</h2>
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

                {{-- Barra de navegação das abas --}}
                <div class="flex justify-center mb-12">
                    <div class="flex items-center space-x-4">
                        <button type="button" data-tab-target="tab-basic-info"
                            class="tab-button active flex items-center space-x-2 text-gray-700 font-medium transition-colors duration-200">
                            <span
                                class="w-8 h-8 flex items-center justify-center rounded-full border-2 border-blue-500 bg-blue-50 text-blue-600 transition-colors duration-200">1</span>
                            <span class="hidden sm:inline">Informações Básicas</span>
                        </button>
                        <span class="w-16 h-px bg-gray-300"></span>
                        <button type="button" data-tab-target="tab-affiliation"
                            class="tab-button flex items-center space-x-2 text-gray-400 font-medium transition-colors duration-200">
                            <span
                                class="w-8 h-8 flex items-center justify-center rounded-full border-2 border-gray-300 text-gray-500 transition-colors duration-200">2</span>
                            <span class="hidden sm:inline">Afiliação</span>
                        </button>
                    </div>
                </div>

                {{-- Formulário de Criação --}}
                <form id="coordinator-form" action="{{ route('coordinators.store') }}" method="POST"
                    class="space-y-8">
                    @csrf

                    {{-- Aba 1: Informações Básicas --}}
                    <div id="tab-basic-info" class="tab-content active space-y-6">
                        <h3 class="text-xl font-semibold text-gray-700 border-b pb-2 mb-4">Informações Básicas</h3>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            {{-- Nome do Coordenador --}}
                            <div>
                                <x-input-label for="name" value="Nome do Coordenador" />
                                <x-text-input type="text" name="name" id="name"
                                    value="{{ old('name') }}" required />
                                @error('name')
                                    <div class="text-red-600 text-sm mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Email --}}
                            <div>
                                <x-input-label for="email" value="Email" />
                                <x-text-input type="email" name="email" id="email"
                                    value="{{ old('email') }}" required />
                                @error('email')
                                    <div class="text-red-600 text-sm mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        {{-- Senha Provisória --}}
                        <div>
                            <x-input-label for="password" value="Senha Provisória" />
                            <div class="relative mt-1">
                                <x-text-input type="text" name="password" id="password"
                                    value="{{ old('password') }}"
                                    class="w-full pr-12 bg-gray-100 cursor-not-allowed" readonly required />
                                <button type="button" onclick="generatePassword()"
                                    class="absolute inset-y-0 right-0 px-4 flex items-center text-sm font-semibold text-white bg-blue-600 rounded-r-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                    Gerar
                                </button>
                            </div>
                            @error('password')
                                <div class="text-red-600 text-sm mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="flex justify-end pt-4">
                            <button type="button" data-next-tab="tab-affiliation"
                                class="next-button inline-flex items-center px-6 py-3 border border-transparent rounded-md font-semibold text-sm text-white bg-blue-600 hover:bg-blue-700 transition ease-in-out duration-150">
                                Próximo
                            </button>
                        </div>
                    </div>

                    {{-- Aba 2: Afiliação --}}
                    <div id="tab-affiliation" class="tab-content hidden space-y-6">
                        <h3 class="text-xl font-semibold text-gray-700 border-b pb-2 mb-4">Afiliação</h3>

                        {{-- Tipo de Coordenador --}}
                        <div>
                            <x-input-label for="coordinator_type" value="Tipo de Coordenador" />
                            <select id="coordinator_type" name="coordinator_type"
                                class="w-full mt-1 border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                                required>
                                <option value="" disabled selected>Selecione...</option>
                                <option value="general"
                                    {{ old('coordinator_type') == 'general' ? 'selected' : '' }}>Geral</option>
                                <option value="course" {{ old('coordinator_type') == 'course' ? 'selected' : '' }}>
                                    Curso</option>
                            </select>
                            @error('coordinator_type')
                                <div class="text-red-600 text-sm mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Seleção de Curso --}}
                        <div id="course-select-container" class="hidden">
                            <x-input-label for="course_id" value="Curso" />
                            <select id="course_id" name="course_id"
                                class="w-full mt-1 border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                <option value="" disabled selected>Selecione um curso...</option>
                                @foreach ($courses as $course)
                                    <option value="{{ $course->id }}"
                                        {{ old('course_id') == $course->id ? 'selected' : '' }}>
                                        {{ $course->course_name }}</option>
                                @endforeach
                            </select>
                            @error('course_id')
                                <div class="text-red-600 text-sm mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="flex justify-between pt-4">
                            <button type="button" data-prev-tab="tab-basic-info"
                                class="prev-button inline-flex items-center px-6 py-3 border border-gray-300 rounded-md font-semibold text-sm text-gray-700 bg-white hover:bg-gray-100 transition ease-in-out duration-150">
                                Anterior
                            </button>
                            <button type="submit"
                                class="submit-button inline-flex items-center px-6 py-3 border border-transparent rounded-md font-semibold text-sm text-white bg-green-600 hover:bg-green-700 transition ease-in-out duration-150">
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
        // Lógica de abas (mesma do formulário de eventos)
        const tabs = document.querySelectorAll('.tab-content');
        const tabButtons = document.querySelectorAll('.tab-button');
        const nextButtons = document.querySelectorAll('.next-button');
        const prevButtons = document.querySelectorAll('.prev-button');

        function showTab(tabId) {
            tabs.forEach(tab => tab.classList.add('hidden'));
            document.getElementById(tabId).classList.remove('hidden');

            tabButtons.forEach(button => {
                const buttonSpan = button.querySelector('span:first-child');
                if (button.dataset.tabTarget === tabId) {
                    button.classList.add('active', 'text-gray-700');
                    buttonSpan.classList.add('bg-blue-50', 'border-blue-500', 'text-blue-600');
                    buttonSpan.classList.remove('bg-white', 'border-gray-300', 'text-gray-500');
                } else {
                    button.classList.remove('active', 'text-gray-700');
                    buttonSpan.classList.remove('bg-blue-50', 'border-blue-500', 'text-blue-600');
                    buttonSpan.classList.add('bg-white', 'border-gray-300', 'text-gray-500');
                }
            });

            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        }

        nextButtons.forEach(button => {
            button.addEventListener('click', () => {
                const currentTab = button.closest('.tab-content');
                const inputs = currentTab.querySelectorAll('input[required], select[required]');
                let allInputsValid = true;

                inputs.forEach(input => {
                    if (!input.checkValidity()) {
                        allInputsValid = false;
                        input.reportValidity();
                    }
                });

                if (allInputsValid) {
                    const nextTabId = button.dataset.nextTab;
                    showTab(nextTabId);
                }
            });
        });

        prevButtons.forEach(button => {
            button.addEventListener('click', () => {
                const prevTabId = button.dataset.prevTab;
                showTab(prevTabId);
            });
        });

        tabButtons.forEach(button => {
            button.addEventListener('click', () => {
                showTab(button.dataset.tabTarget);
            });
        });

        // Lógica de tipo de coordenador e senha
        const coordinatorTypeSelect = document.getElementById('coordinator_type');
        const courseSelectContainer = document.getElementById('course-select-container');
        const courseSelect = document.getElementById('course_id');
        const passwordInput = document.getElementById('password');

        function generatePassword() {
            const length = 12;
            const charset = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*()_+-=[]{};:'\",.<>/?`~";
            let password = "";
            for (let i = 0; i < length; ++i) {
                password += charset.charAt(Math.floor(Math.random() * charset.length));
            }
            passwordInput.value = password;
        }

        function toggleCourseSelect() {
            if (coordinatorTypeSelect.value === 'course') {
                courseSelectContainer.classList.remove('hidden');
                courseSelect.required = true;
            } else {
                courseSelectContainer.classList.add('hidden');
                courseSelect.required = false;
            }
        }

        coordinatorTypeSelect.addEventListener('change', toggleCourseSelect);
        toggleCourseSelect(); // Chama na inicialização para garantir o estado correto.
        generatePassword(); // Gera uma senha ao carregar a página
    });

    function generatePassword() {
        const passwordInput = document.getElementById('password');
        const length = 12;
        const charset = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*()_+-=[]{};:'\",.<>/?`~";
        let password = "";
        for (let i = 0; i < length; ++i) {
            password += charset.charAt(Math.floor(Math.random() * charset.length));
        }
        passwordInput.value = password;
    }
</script>