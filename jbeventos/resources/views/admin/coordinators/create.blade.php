<x-app-layout>
    <div class="py-12">
        <div class="w-full max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white shadow-lg rounded-2xl p-6 sm:p-8 lg:p-12">
                
                <!-- Título e Ícone da Página -->
                <div class="flex flex-col items-center justify-center mb-8 text-center">
                    <div class="p-3 bg-indigo-50 rounded-full mb-4 shadow-sm flex items-center justify-center">
                        <img src="{{ asset('imgs/coordinator.png') }}" class="h-10 w-10 text-indigo-600">
                    </div>
                    <h2 class="text-3xl font-bold text-gray-900">Criar Coordenador</h2>
                </div>

                <!-- Mensagens de Erro -->
                @if ($errors->any())
                    <div class="mb-6 p-4 rounded-lg bg-red-50 text-red-700 border border-red-200 shadow-sm">
                        <ul class="list-disc list-inside space-y-1 text-sm">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                
                <form action="{{ route('coordinators.store') }}" method="POST" class="space-y-6">
                    @csrf

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Nome do Coordenador</label>
                            <input type="text" id="name" name="name" value="{{ old('name') }}" autocomplete="off"
                                placeholder="Digite o nome do Coordenador"
                                class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500" required>
                        </div>
    
                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                            <input type="email" id="email" name="email" value="{{ old('email') }}" autocomplete="off"
                                placeholder="Digite o email do Coordenador"
                                class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500" required>
                        </div>
                    </div>

                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Senha Provisória</label>
                        <div class="flex items-center gap-2">
                            <input type="text" id="password" name="password" value="{{ old('password') }}"
                                class="w-full border-gray-300 rounded-md shadow-sm bg-gray-100 cursor-not-allowed" readonly required>
                            <button type="button" onclick="generatePassword()" class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all text-sm font-medium whitespace-nowrap">
                                Gerar Senha
                            </button>
                        </div>
                    </div>

                    <div>
                        <label for="coordinator_type" class="block text-sm font-medium text-gray-700 mb-1">Tipo de Coordenador</label>
                        <select id="coordinator_type" name="coordinator_type" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500" required>
                            <option value="" disabled selected>Selecione...</option>
                            <option value="general" {{ old('coordinator_type') == 'general' ? 'selected' : '' }}>Geral</option>
                            <option value="course" {{ old('coordinator_type') == 'course' ? 'selected' : '' }}>Curso</option>
                        </select>
                    </div>

                    <div id="course-select-container" class="hidden">
                        <label for="course_id" class="block text-sm font-medium text-gray-700 mb-1">Curso</label>
                        <select id="course_id" name="course_id" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="" disabled selected>Selecione um curso...</option>
                            @foreach ($courses as $course)
                                <option value="{{ $course->id }}">{{ $course->course_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div class="flex justify-end space-x-3 mt-8">
                        <a href="{{ route('coordinators.index') }}" class="inline-flex items-center px-6 py-2 border border-gray-300 rounded-full shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all">
                            Cancelar
                        </a>
                        <button type="submit" class="inline-flex items-center px-6 py-2 border border-transparent rounded-full shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all">
                            Criar Coordenador
                        </button>
                    </div>
                </form>

            </div>
        </div>
    </div>
</x-app-layout>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const coordinatorTypeSelect = document.getElementById('coordinator_type');
        const courseSelectContainer = document.getElementById('course-select-container');
        const courseSelect = document.getElementById('course_id');
        const passwordInput = document.getElementById('password');

        function generatePassword() {
            const length = 10;
            const charset = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*()_+~`|}{[]:;?><,./-=";
            let password = "";
            for (let i = 0, n = charset.length; i < length; ++i) {
                password += charset.charAt(Math.floor(Math.random() * n));
            }
            passwordInput.value = password;
        }
        window.generatePassword = generatePassword;
        
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
</script>
