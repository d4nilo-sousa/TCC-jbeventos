<x-app-layout>
    <div class="py-6">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-lg rounded-xl overflow-hidden p-6 md:p-10">
                {{-- Título da Página --}}
                <div class="flex flex-col items-center justify-center mb-10 text-center">
                    <div class="p-3 bg-red-100 rounded-full mb-4 shadow-md flex items-center justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-10 h-10 text-red-600" viewBox="0 0 256 256"
                            fill="currentColor">
                            <path
                                d="M216,48H40A16,16,0,0,0,24,64V192a16,16,0,0,0,16,16H216a16,16,0,0,0,16-16V64A16,16,0,0,0,216,48ZM40,64H128v48H40Zm136,112H56V136h120v40Zm40-112H168v48h48Z" />
                        </svg>
                    </div>
                    <h1 class="text-3xl font-bold text-gray-800">Editar Curso</h1>
                    <p class="mt-2 text-gray-600">Atualize as informações do curso conforme necessário.</p>
                </div>

                {{-- Exibição de erros --}}
                @if ($errors->any())
                    <div class="mb-6 p-4 bg-red-50 border-l-4 border-red-500 text-red-700 rounded-md animate-fade-in"
                        role="alert">
                        <div class="flex items-center">
                            <svg class="w-6 h-6 mr-3 text-red-500" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
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

                {{-- Formulário --}}
                <form id="course-edit-form" action="{{ route('courses.update', $course->id) }}" method="POST"
                    enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    {{-- Abas --}}
                    <div class="flex items-center justify-center mb-10">
                        <div class="flex items-center space-x-2 md:space-x-8">

                            <button type="button" data-tab-target="tab1"
                                class="tab-button flex flex-col items-center group active">
                                <span
                                    class="inline-flex items-center justify-center w-8 h-8 font-bold rounded-full border-2 border-red-500 text-white bg-red-500 transition-all duration-300">1</span>
                                <span
                                    class="mt-1 text-sm text-red-600 font-medium transition-colors duration-300">Informações</span>
                            </button>

                            <div class="h-0.5 w-6 md:w-16 bg-gray-300"></div>

                            <button type="button" data-tab-target="tab2" class="tab-button flex flex-col items-center">
                                <span
                                    class="inline-flex items-center justify-center w-8 h-8 font-bold rounded-full border-2 border-gray-300 bg-white text-gray-500 transition-all duration-300">2</span>
                                <span class="mt-1 text-sm text-gray-600 transition-colors duration-300">Imagens</span>
                            </button>
                        </div>
                    </div>

                    {{-- Conteúdo das Abas --}}
                    <div id="tab1" class="tab-content">
                        <div class="space-y-6">
                            <h3 class="text-2xl font-semibold text-gray-800 border-b pb-2 mb-6">Detalhes do Curso</h3>

                            <div>
                                <x-input-label for="course_name" value="Nome do Curso" />
                                <x-text-input type="text" name="course_name" id="course_name"
                                    class="w-full focus:border-red-500 focus:ring-red-500"
                                    value="{{ old('course_name', $course->course_name) }}" required />
                                @error('course_name')
                                    <div class="text-red-600 text-sm mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <div>
                                <x-input-label for="course_description" value="Descrição" />
                                <textarea name="course_description" id="course_description" rows="4"
                                    class="w-full mt-1 border-gray-300 rounded-md shadow-sm focus:border-red-500 focus:ring-red-500">{{ old('course_description', $course->course_description) }}</textarea>
                                @error('course_description')
                                    <div class="text-red-600 text-sm mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <div>
                                <x-input-label for="coordinator_id" value="Coordenador" />
                                <select name="coordinator_id" id="coordinator_id"
                                    class="w-full mt-1 border-gray-300 rounded-md shadow-sm focus:border-red-500 focus:ring-red-500">
                                    <option value="">Nenhum</option>
                                    @foreach ($coordinators as $coordinator)
                                        @if ($coordinator->coordinator_type === 'course')
                                            @php
                                                $isSelected =
                                                    old('coordinator_id', $course->coordinator_id) == $coordinator->id;
                                                $isDisabled =
                                                    $coordinator->coordinatedCourse &&
                                                    $coordinator->coordinatedCourse->id != $course->id;
                                            @endphp
                                            <option value="{{ $coordinator->id }}" {{ $isSelected ? 'selected' : '' }}
                                                {{ $isDisabled ? 'disabled' : '' }}>
                                                {{ $coordinator->userAccount->name ?? 'Sem nome' }}
                                                @if ($isDisabled)
                                                    ({{ $coordinator->coordinatedCourse->course_name }})
                                                @endif
                                            </option>
                                        @endif
                                    @endforeach
                                </select>
                                @error('coordinator_id')
                                    <div class="text-red-600 text-sm mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <h3 class="text-xl font-semibold text-gray-700 border-b pb-2 mt-10"></h3>

                        <div class="flex justify-between mt-8">
                            <a href="{{ route('courses.show', $course->id) }}"
                                class="prev-button px-6 py-2 bg-gray-200 text-gray-700 rounded-md font-semibold hover:bg-gray-300 transition-colors duration-200">
                                Cancelar
                            </a>
                            <button type="button" data-next-tab="tab2"
                                class="next-button px-6 py-2 bg-red-600 text-white rounded-md font-semibold hover:bg-red-700 transition-colors duration-200">
                                Próximo
                            </button>
                        </div>
                    </div>

                    <div id="tab2" class="tab-content hidden">
                        <div class="space-y-6">
                            <h3 class="text-2xl font-semibold text-gray-800 border-b pb-2 mb-6">Imagens do Curso</h3>

                            {{-- Ícone do Curso --}}
                            <div>
                                <x-input-label for="course_icon" value="Ícone do Curso" />
                                <div id="dropzone-icon"
                                    class="group relative flex flex-col items-center justify-center border-2 border-dashed border-gray-300 rounded-lg p-6 text-center hover:border-red-500 hover:bg-red-50 transition-all duration-300">
                                    <input type="file" name="course_icon" id="course_icon" accept="image/*"
                                        class="absolute inset-0 w-full h-full opacity-0 cursor-pointer">
                                    <svg class="h-10 w-10 text-gray-400 group-hover:text-red-500 transition-colors"
                                        fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                    <p class="mt-2 text-sm text-gray-500 group-hover:text-red-500">Arraste e solte ou
                                        clique para enviar um ícone.</p>
                                </div>

                                <input type="hidden" name="remove_course_icon" id="remove_course_icon_input" value="0">

                                <div id="course_icons_preview" class="mt-4 flex flex-wrap gap-2 justify-center">
                                    @if ($course->course_icon)
                                        <div id="existing-icon-preview"
                                            class="flex items-center p-2 border border-gray-300 rounded-md bg-gray-50 shadow-sm">
                                            <span class="text-sm text-gray-700 mr-2">{{ basename($course->course_icon) }}</span>
                                            <button type="button" data-type="icon"
                                                class="delete-image-btn w-6 h-6 flex items-center justify-center text-red-600 hover:text-red-800 transition-colors rounded-full"
                                                onclick="removeExistingImage(this, '{{ $course->course_icon }}', 'icon')">
                                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                                </svg>
                                            </button>
                                        </div>
                                    @endif
                                </div>
                                @error('course_icon')
                                    <div class="text-red-600 text-sm mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Banner do Curso --}}
                            <div>
                                <x-input-label for="course_banner" value="Banner do Curso" />
                                <div id="dropzone-banner"
                                    class="group relative flex flex-col items-center justify-center border-2 border-dashed border-gray-300 rounded-lg p-6 text-center hover:border-red-500 hover:bg-red-50 transition-all duration-300">
                                    <input type="file" name="course_banner" id="course_banner" accept="image/*"
                                        class="absolute inset-0 w-full h-full opacity-0 cursor-pointer">
                                    <svg class="h-10 w-10 text-gray-400 group-hover:text-red-500 transition-colors"
                                        fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 11V5" />
                                    </svg>
                                    <p class="mt-2 text-sm text-gray-500 group-hover:text-red-500">Arraste e solte ou
                                        clique para enviar um banner.</p>
                                </div>

                                <input type="hidden" name="remove_course_banner" id="remove_course_banner_input" value="0">

                                <div id="course_banners_preview" class="mt-4 flex flex-wrap gap-2 justify-center">
                                    @if ($course->course_banner)
                                        <div id="existing-banner-preview"
                                            class="flex items-center p-2 border border-gray-300 rounded-md bg-gray-50 shadow-sm">
                                            <span class="text-sm text-gray-700 mr-2">{{ basename($course->course_banner) }}</span>
                                            <button type="button" data-type="banner"
                                                class="delete-image-btn w-6 h-6 flex items-center justify-center text-red-600 hover:text-red-800 transition-colors rounded-full"
                                                onclick="removeExistingImage(this, '{{ $course->course_banner }}', 'banner')">
                                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                                </svg>
                                            </button>
                                        </div>
                                    @endif
                                </div>
                                @error('course_banner')
                                    <div class="text-red-600 text-sm mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <h3 class="text-xl font-semibold text-gray-700 border-b pb-2 mt-10"></h3>

                        <div class="flex items-center justify-between mt-8">
                            <button type="button" data-prev-tab="tab1"
                                class="prev-button px-6 py-2 bg-gray-200 text-gray-700 rounded-md font-semibold hover:bg-gray-300 transition-colors duration-200">
                                Anterior
                            </button>
                            <button type="submit"
                                class="submit-button px-6 py-2 bg-red-600 text-white rounded-md font-semibold hover:bg-red-700 transition-colors duration-200">
                                Atualizar Curso
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>

@vite('resources/js/app.js');

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const tabs = document.querySelectorAll('.tab-content');
        const tabButtons = document.querySelectorAll('.tab-button');
        const nextButtons = document.querySelectorAll('.next-button');
        const prevButtons = document.querySelectorAll('.prev-button');

        function updateTabState(button, isActive) {
            const circle = button.querySelector('span:first-child');
            const text = button.querySelector('span:last-child');

            if (isActive) {
                // Estado Ativo (Vermelho)
                circle.classList.add('border-red-500', 'bg-red-500', 'text-white');
                circle.classList.remove('border-gray-300', 'bg-white', 'text-gray-500');
                text.classList.add('text-red-600', 'font-medium');
                text.classList.remove('text-gray-600');
            } else {
                // Estado Inativo (Cinza)
                circle.classList.remove('border-red-500', 'bg-red-500', 'text-white');
                circle.classList.add('border-gray-300', 'bg-white', 'text-gray-500');
                text.classList.remove('text-red-600', 'font-medium');
                text.classList.add('text-gray-600');
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

        nextButtons.forEach(button => {
            button.addEventListener('click', () => {
                const currentTab = button.closest('.tab-content');
                const nextTabId = button.dataset.nextTab;

                // Validação dos campos obrigatórios visíveis na aba atual
                const inputs = currentTab?.querySelectorAll('input:required, textarea:required, select:required');
                let allInputsValid = true;

                if (inputs) {
                    for (const input of inputs) {
                        // Verifica se o campo está visível
                        if (!input.value.trim() && !input.getAttribute('disabled')) {
                            // Campo obrigatório vazio
                            input.focus();
                            // Opcional: Adicionar uma classe de erro para destacar o campo
                            allInputsValid = false;
                            break;
                        }
                    }
                }

                // Se todos os campos estiverem válidos, vá para a próxima aba
                if (allInputsValid && nextTabId) {
                    showTab(nextTabId);
                } else if (!allInputsValid) {
                    // Força a exibição das mensagens de validação do HTML5
                    const form = document.getElementById('course-edit-form');
                    if (form) {
                        form.reportValidity();
                    }
                }
            });
        });

        prevButtons.forEach(button => {
            button.addEventListener('click', () => {
                const prevTabId = button.dataset.prevTab;
                if (prevTabId) {
                    showTab(prevTabId);
                }
            });
        });

        tabButtons.forEach(button => {
            button.addEventListener('click', () => {
                const target = button.dataset.tabTarget;
                if (target) {
                    // Permite trocar de aba apenas clicando (sem validação forçada)
                    showTab(target);
                }
            });
        });

        // Lógica para pré-visualização de novas imagens
        function setupImagePreview(inputId, previewId) {
            const input = document.getElementById(inputId);
            const previewContainer = document.getElementById(previewId);

            if (input) {
                input.addEventListener('change', function() {
                    previewContainer.innerHTML = ''; // Limpa previews antigos
                    const existingPreview = document.getElementById(`existing-${inputId.replace('course_', '')}-preview`);
                    if (existingPreview) {
                        existingPreview.remove(); // Remove o preview da imagem existente ao carregar uma nova
                    }
                    if (this.files && this.files[0]) {
                        const file = this.files[0];
                        const reader = new FileReader();

                        reader.onload = function(e) {
                            const img = document.createElement('img');
                            img.src = e.target.result;
                            img.alt = file.name;
                            img.className = 'w-32 h-32 object-cover rounded-md border border-gray-200 shadow-sm';
                            
                            const nameSpan = document.createElement('span');
                            nameSpan.className = 'text-sm text-gray-600 mt-2 truncate max-w-full';
                            nameSpan.textContent = file.name;

                            const fileWrapper = document.createElement('div');
                            fileWrapper.className = 'flex flex-col items-center p-2';
                            fileWrapper.appendChild(img);
                            fileWrapper.appendChild(nameSpan);
                            
                            previewContainer.appendChild(fileWrapper);
                        }
                        reader.readAsDataURL(file);
                    }
                });
            }
        }
        
        setupImagePreview('course_icon', 'course_icons_preview');
        setupImagePreview('course_banner', 'course_banners_preview');
        
        // Lógica para remover imagem existente
        window.removeExistingImage = function(button, filePath, type) {
            const container = button.closest('div');
            const inputId = `remove_course_${type}_input`;
            const hiddenInput = document.getElementById(inputId);

            if (confirm(`Tem certeza que deseja remover a imagem ${type}?`)) {
                // Remove o container de pré-visualização da imagem existente
                container.remove();

                // Marca o campo oculto para indicar que a imagem deve ser removida no backend
                hiddenInput.value = '1';

                // Opcional: Adiciona novamente o dropzone
                const dropzone = document.getElementById(`dropzone-${type}`);
                if (dropzone) {
                    dropzone.classList.remove('hidden'); 
                }
            }
        }


        // Inicializa a primeira aba ao carregar a página
        if (document.getElementById('tab1')) {
            showTab('tab1');
        }
    });
</script>