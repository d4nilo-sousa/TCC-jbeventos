<x-app-layout>
    <div class="py-6">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-lg rounded-xl overflow-hidden p-6 md:p-10">
                {{-- Título da Página --}}
                <div class="flex flex-col items-center justify-center mb-10 text-center">
                    <div class="p-3 bg-red-100 rounded-full mb-4 shadow-md flex items-center justify-center">
                        <svg class="h-10 w-10 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                        </svg>
                    </div>
                    <h1 class="text-3xl font-bold text-gray-800">Editar Evento</h1>
                    <p class="mt-2 text-gray-600">Modifique as Informações do evento conforme necessário.</p>
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

                {{-- Formulário de Edição --}}
                <form id="event-edit-form" action="{{ route('events.update', $event->id) }}" method="POST"
                    enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    {{-- Abas de Navegação --}}
                    <div class="flex items-center justify-center mb-10">
                        {{-- Tab 1: Informações --}}
                        <button type="button" data-tab-target="tab1"
                            class="tab-button flex items-center focus:outline-none transition-all duration-300">
                            <span
                                class="inline-flex items-center justify-center w-8 h-8 font-bold rounded-full transition-colors duration-300 
                                border border-red-300 bg-red-50 text-red-600 
                                active-tab-style">
                                1
                            </span>
                            <span class="ml-2 text-sm md:text-base text-gray-700 active-tab-text-style">
                                Informações
                            </span>
                        </button>

                        <div class="border-t border-gray-300 w-20 mx-3 transition-colors duration-300"></div>

                        {{-- Tab 2: Imagens --}}
                        <button type="button" data-tab-target="tab2"
                            class="tab-button flex items-center focus:outline-none transition-all duration-300 text-gray-500 hover:text-red-500">
                            <span
                                class="inline-flex items-center justify-center w-8 h-8 font-bold rounded-full transition-colors duration-300 
                                border border-gray-300 bg-white text-gray-500 
                                active:border-red-500 active:bg-red-50 active:text-red-600">
                                2
                            </span>
                            <span class="ml-2 text-sm md:text-base">
                                Imagens
                            </span>
                        </button>
                    </div>

                    {{-- Conteúdo das Abas --}}
                    <div id="tab1" class="tab-content">
                        <div class="space-y-6">
                            <h3 class="text-2xl font-semibold text-gray-800 border-b pb-2 mb-6">Detalhes do Evento</h3>

                            {{-- CAMPO 1: Nome do Evento --}}
                            <div>
                                <x-input-label for="event_name" value="Nome do Evento" />
                                <x-text-input type="text" name="event_name" id="event_name" maxlength="50"
                                    value="{{ old('event_name', $event->event_name) }}" required
                                    class="w-full focus:border-red-500 focus:ring-red-500" />
                                @error('event_name')
                                    <div class="text-red-600 text-sm mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- CAMPO 2: Sobre o Evento --}}
                            <div class="mb-4">
                                <x-input-label for="event_description" value="Sobre o Evento" class="text-left mb-1 block" />
                                <textarea name="event_description" id="event_description" rows="4"
                                    class="w-full mt-1 border-gray-300 rounded-md shadow-sm focus:border-red-500 focus:ring-red-500 text-left block">{{ old('event_description', $event->event_description) }}</textarea>
                                @error('event_description')
                                    <div class="text-red-600 text-sm mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- CAMPO 3: Local --}}
                            <div>
                                <x-input-label for="event_location" value="Local" />
                                <x-text-input type="text" name="event_location" id="event_location"
                                    value="{{ old('event_location', $event->event_location) }}" required
                                    class="w-full focus:border-red-500 focus:ring-red-500" />
                                @error('event_location')
                                    <div class="text-red-600 text-sm mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- CAMPOS 4 e 5: Data e Exclusão --}}
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <x-input-label for="event_scheduled_at" value="Data e Hora do Evento" />
                                    <x-text-input type="datetime-local" name="event_scheduled_at"
                                        id="event_scheduled_at"
                                        value="{{ old('event_scheduled_at', $event->event_scheduled_at ? \Carbon\Carbon::parse($event->event_scheduled_at)->format('Y-m-d\TH:i') : '') }}"
                                        required class="w-full focus:border-red-500 focus:ring-red-500" />
                                    @error('event_scheduled_at')
                                        <div class="text-red-600 text-sm mt-1">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div>
                                    <x-input-label for="event_expired_at" value="Exclusão Automática (opcional)" />
                                    <x-text-input type="datetime-local" name="event_expired_at" id="event_expired_at"
                                        min="{{ \Carbon\Carbon::now()->format('Y-m-d\TH:i') }}"
                                        value="{{ old('event_expired_at', $event->event_expired_at ? \Carbon\Carbon::parse($event->event_expired_at)->format('Y-m-d\TH:i') : '') }}"
                                        class="w-full focus:border-red-500 focus:ring-red-500" />
                                    @error('event_expired_at')
                                        <div class="text-red-600 text-sm mt-1">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            {{-- Seleção de Cursos Adicionais --}}
                            @php
                                $coordinator = auth()->user()->coordinator;
                                $coordinatedCourse = $coordinator->coordinatedCourse;
                                $defaultCourseId = $coordinatedCourse ? $coordinatedCourse->id : null;
                                $availableCourses = $allCourses->where('id', '!=', $defaultCourseId);
                                $selectedCourses = old('courses', $event->courses->pluck('id')->toArray());
                            @endphp

                            <div class="mt-6">
                                <x-input-label for="courses" value="Cursos Adicionais (Opcional)" />

                                <div
                                    class="mt-2 p-3 border border-gray-300 rounded-md shadow-sm h-40 overflow-y-auto bg-white">
                                    @forelse ($availableCourses as $course)
                                        <div class="flex items-center mb-1">
                                            <input id="course-{{ $course->id }}" name="courses[]" type="checkbox"
                                                value="{{ $course->id }}"
                                                class="rounded border-gray-300 text-red-600 shadow-sm focus:ring-red-500"
                                                {{ in_array($course->id, $selectedCourses) ? 'checked' : '' }}>
                                            <label for="course-{{ $course->id }}" class="ml-2 text-sm text-gray-700">
                                                {{ $course->course_name }}
                                            </label>
                                        </div>
                                    @empty
                                        <p class="text-sm text-gray-500">Nenhum curso adicional disponível para seleção.
                                        </p>
                                    @endforelse
                                </div>

                                <x-input-error for="courses" class="mt-2" />
                            </div>

                            {{-- Categorias do Evento --}}
                            <div>
                                <x-input-label value="Categorias do Evento" />
                                <div class="mt-2 flex flex-wrap gap-4">
                                    @foreach ($categories as $category)
                                        <label
                                            class="inline-flex items-center space-x-2 transition-colors duration-200 ease-in-out cursor-pointer hover:text-red-600">
                                            <input type="checkbox" name="categories[]" value="{{ $category->id }}"
                                                class="rounded text-red-600 border-gray-300 focus:ring-red-500"
                                                {{ in_array($category->id, old('categories', $event->eventCategories->pluck('id')->toArray())) ? 'checked' : '' }}>
                                            <span class="text-sm">{{ $category->category_name }}</span>
                                        </label>
                                    @endforeach
                                </div>
                                @error('categories')
                                    <div class="text-red-600 text-sm mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <h3 class="text-xl font-semibold text-gray-700 border-b pb-2 mt-5"></h3>

                        <div class="flex justify-between mt-8">
                            <a href="{{ route('events.show', $event->id) }}"
                                class="px-6 py-2 bg-gray-300 text-gray-700 rounded-md font-semibold hover:bg-gray-400 transition-colors duration-200">
                                Cancelar
                            </a>

                            {{-- Botão Próximo --}}
                            <button type="button" data-next-tab="tab2"
                                class="next-button px-6 py-2 bg-red-600 text-white rounded-md font-semibold hover:bg-red-700 transition-colors duration-200">
                                Próximo
                            </button>
                        </div>
                    </div>

                    <div id="tab2" class="tab-content hidden">
                        <div class="space-y-6">
                            <h3 class="text-2xl font-semibold text-gray-800 border-b pb-2 mb-6">Imagens do Evento</h3>

                            {{-- Dropzone Capa --}}
                            <div>
                                <label for="event_image" class="block font-medium text-gray-700 mb-2">Imagem de
                                    Capa</label>
                                <div id="dropzone-cover"
                                    class="group relative flex flex-col items-center justify-center border-2 border-dashed border-gray-300 rounded-lg p-6 text-center cursor-pointer transition-all duration-300 hover:border-red-500 hover:bg-red-50">
                                    <input type="file" name="event_image" id="event_image" accept="image/*"
                                        class="absolute inset-0 w-full h-full opacity-0 cursor-pointer">
                                    <svg class="h-10 w-10 text-gray-400 group-hover:text-red-500 transition-colors"
                                        fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                    <p class="mt-2 text-sm text-gray-500 group-hover:text-red-500 transition-colors">
                                        <span class="font-semibold">Arraste e solte</span> ou clique para enviar a
                                        imagem de capa.
                                    </p>
                                </div>

                                <input type="hidden" name="remove_event_image" value="0">

                                {{-- Botão de Excluir Imagem --}}
                                <div id="event_image_preview" class="mt-4 flex flex-col items-center gap-2">
                                    @if ($event->event_image)
                                        <div data-filename="{{ basename($event->event_image) }}"
                                            style="display: flex; align-items: center; gap: 10px;">
                                            <input type="text" value="{{ basename($event->event_image) }}"
                                                readonly style="cursor: default;">
                                            <button type="button"
                                                onclick="this.closest('div[data-filename]').remove(); document.querySelector('input[name=remove_event_image]').value = 1;"
                                                style="background-color: #DC2626; color: #fff; border: none; padding: 5px 10px; border-radius: 4px; cursor: pointer;"
                                                onmouseenter="this.style.backgroundColor='#B91C1C'"
                                                onmouseleave="this.style.backgroundColor='#DC2626'">
                                                Excluir
                                            </button>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            {{-- Dropzone Galeria --}}
                            <div>
                                <label for="event_images" class="block font-medium text-gray-700 mb-2">Galeria de
                                    Imagens</label>

                                <div id="dropzone-gallery"
                                    class="group relative flex flex-col items-center justify-center border-2 border-dashed border-gray-300 rounded-lg p-6 text-center cursor-pointer transition-all duration-300 hover:border-red-500 hover:bg-red-50">
                                    <input type="file" name="event_images[]" id="event_images" accept="image/*"
                                        class="absolute inset-0 w-full h-full opacity-0 cursor-pointer" multiple>
                                    <svg class="h-10 w-10 text-gray-400 group-hover:text-red-500 transition-colors"
                                        fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 11V5" />
                                    </svg>
                                    <p class="mt-2 text-sm text-gray-500 group-hover:text-red-500 transition-colors">
                                        <span class="font-semibold">Arraste e solte</span> ou clique para adicionar
                                        mais imagens.
                                    </p>
                                </div>

                                {{-- Botões de Excluir Galeria --}}
                                <div id="event_images_preview" class="mt-4 flex flex-wrap gap-2 justify-center">
                                    @foreach ($event->images as $img)
                                        <div data-id="{{ $img->id }}"
                                            data-filename="{{ basename($img->image_path) }}">
                                            <input type="text" value="{{ basename($img->image_path) }}" readonly
                                                style="cursor: default;">

                                            <button type="button" onclick="deleteImage({{ $img->id }}, this, 'event')"
                                                style="background-color: #DC2626; color: #fff; border: none; padding: 5px 10px; border-radius: 4px; cursor: pointer;"
                                                onmouseenter="this.style.backgroundColor='#B91C1C'"
                                                onmouseleave="this.style.backgroundColor='#DC2626'">
                                                Excluir
                                            </button>

                                            <input type="hidden" name="keep_event_images[]"
                                                value="{{ $img->id }}">
                                        </div>
                                    @endforeach
                                </div>
                                @error('event_images')
                                    <div class="text-red-600 text-sm mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <h3 class="text-xl font-semibold text-gray-700 border-b pb-2 mt-7"></h3>

                            <div class="flex items-center justify-between mt-6">
                                <button type="button" data-prev-tab="tab1"
                                    class="prev-button px-6 py-2 bg-gray-200 text-gray-700 rounded-md font-semibold hover:bg-gray-300 transition-colors duration-200">
                                    Anterior
                                </button>
                                {{-- Botão de Submeter --}}
                                <button type="submit"
                                    class="submit-button inline-flex items-center px-6 py-3 border border-transparent rounded-md font-semibold text-sm text-white bg-red-600 hover:bg-red-700 transition ease-in-out duration-150">
                                    Atualizar Evento
                                </button>
                            </div>
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
        const tabs = document.querySelectorAll('.tab-content');
        const tabButtons = document.querySelectorAll('.tab-button');
        const nextButtons = document.querySelectorAll('.next-button');
        const prevButtons = document.querySelectorAll('.prev-button');

        // Função para atualizar o estado visual das abas
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

        // Função para exibir a aba desejada
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

            // Rola para o topo da página ao trocar de aba
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        }

        // Navegação para a próxima aba
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
                        if (!input.value.trim() && !input.getAttribute('disabled')) {
                            input.focus();
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
                    const form = document.getElementById('event-edit-form'); // Assumindo 'event-edit-form' como ID do formulário de eventos
                    if (form) {
                        form.reportValidity();
                    }
                }
            });
        });

        // Navegação para a aba anterior
        prevButtons.forEach(button => {
            button.addEventListener('click', () => {
                const prevTabId = button.dataset.prevTab;
                if (prevTabId) {
                    showTab(prevTabId);
                }
            });
        });

        // Navegação por clique nos botões de aba (círculos 1 e 2)
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
                    
                    // Remove o preview da imagem existente ao carregar uma nova
                    const existingPreview = document.getElementById(`existing-${inputId.replace('event_', '')}-preview`);
                    if (existingPreview) {
                        existingPreview.remove(); 
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
        
        setupImagePreview('event_icon', 'event_icons_preview'); // Adapte para o ID do ícone de evento
        setupImagePreview('event_banner', 'event_banners_preview'); // Adapte para o ID do banner de evento
        
        // Lógica para remover imagem existente
        window.removeExistingImage = function(button, filePath, type) {
            const container = button.closest('div');
            // Adapte o ID do input oculto para `remove_event_icon` e `remove_event_banner`
            const inputId = `remove_event_${type}_input`; 
            const hiddenInput = document.getElementById(inputId);

            if (confirm(`Tem certeza que deseja remover a imagem ${type}?`)) {
                // Remove o container de pré-visualização da imagem existente
                container.remove();

                // Marca o campo oculto para indicar que a imagem deve ser removida no backend
                hiddenInput.value = '1';

            }
        }


        // Inicializa a primeira aba ao carregar a página
        if (document.getElementById('tab1')) {
            showTab('tab1');
        }
    });
</script>