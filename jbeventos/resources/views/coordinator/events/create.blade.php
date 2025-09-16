<x-app-layout>
    <div class="py-6">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-lg rounded-xl overflow-hidden p-6 md:p-10">

                {{-- Exibição de erros de validação --}}
                @if ($errors->any())
                    <div class="mb-6 p-4 bg-red-50 border-l-4 border-red-500 text-red-700 rounded-md animate-fade-in" role="alert">
                        <div class="flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 mr-3 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
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

                {{-- Mensagem de alerta para coordenador de curso sem curso vinculado --}}
                @if (auth()->user()->coordinator->coordinator_type === 'course' && !auth()->user()->coordinator->coordinatedCourse)
                    <div class="mb-6 p-4 bg-yellow-50 border-l-4 border-yellow-500 text-yellow-700 rounded-md">
                        <div class="flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-3 text-yellow-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <p class="text-sm">Você é coordenador de curso, mas ainda não está vinculado a nenhum curso. Não é possível criar eventos até que um curso seja atribuído a você.</p>
                        </div>
                    </div>
                @else
                    {{-- Barra de navegação das abas --}}
                    <div class="flex justify-center mb-8">
                        <div class="flex items-center space-x-4">
                            <button type="button" data-tab-target="tab-media" class="tab-button active flex items-center space-x-2 text-gray-700 font-medium transition-colors duration-200">
                                <span class="w-8 h-8 flex items-center justify-center rounded-full border-2 border-blue-500 bg-blue-50 text-blue-600 transition-colors duration-200">1</span>
                                <span class="hidden sm:inline">Mídias</span>
                            </button>
                            <span class="w-16 h-px bg-gray-300"></span>
                            <button type="button" data-tab-target="tab-info" class="tab-button flex items-center space-x-2 text-gray-400 font-medium transition-colors duration-200">
                                <span class="w-8 h-8 flex items-center justify-center rounded-full border-2 border-gray-300 text-gray-500 transition-colors duration-200">2</span>
                                <span class="hidden sm:inline">Informações</span>
                            </button>
                            <span class="w-16 h-px bg-gray-300"></span>
                            <button type="button" data-tab-target="tab-details" class="tab-button flex items-center space-x-2 text-gray-400 font-medium transition-colors duration-200">
                                <span class="w-8 h-8 flex items-center justify-center rounded-full border-2 border-gray-300 text-gray-500 transition-colors duration-200">3</span>
                                <span class="hidden sm:inline">Detalhes</span>
                            </button>
                        </div>
                    </div>

                    {{-- Formulário de Criação --}}
                    <form id="event-form" action="{{ route('events.store') }}" method="POST" enctype="multipart/form-data" class="space-y-8">
                        @csrf

                        {{-- Aba 1: Mídias --}}
                        <div id="tab-media" class="tab-content active space-y-6">
                            <h3 class="text-xl font-semibold text-gray-700 border-b pb-2">Imagens do Evento</h3>

                            {{-- Imagem de Capa --}}
                            <div>
                                <label for="event_image" class="block font-medium text-gray-700 mb-2">Imagem de Capa (principal)</label>
                                <div id="dropzone-cover" class="group relative flex flex-col items-center justify-center border-2 border-dashed border-gray-300 rounded-lg p-6 text-center cursor-pointer transition-all duration-300 hover:border-blue-500 hover:bg-gray-50">
                                    <input type="file" name="event_image" id="event_image" accept="image/*" class="hidden" required>
                                    <svg class="h-10 w-10 text-gray-400 group-hover:text-blue-500 transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                    <p class="mt-2 text-sm text-gray-500 group-hover:text-blue-500 transition-colors"><span class="font-semibold">Arraste e solte</span> ou clique para enviar a imagem de capa.</p>
                                </div>
                                <div id="event_image_preview" class="mt-4 flex justify-center"></div>
                                @error('event_image')
                                    <div class="text-red-600 text-sm mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Outras Imagens (Galeria) --}}
                            <div>
                                <label for="event_images" class="block font-medium text-gray-700 mb-2">Galeria de Imagens (opcional)</label>
                                <div id="dropzone-gallery" class="group relative flex flex-col items-center justify-center border-2 border-dashed border-gray-300 rounded-lg p-6 text-center cursor-pointer transition-all duration-300 hover:border-blue-500 hover:bg-gray-50">
                                    <input type="file" name="event_images[]" id="event_images" accept="image/*" class="hidden" multiple>
                                    <svg class="h-10 w-10 text-gray-400 group-hover:text-blue-500 transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 11V5" />
                                    </svg>
                                    <p class="mt-2 text-sm text-gray-500 group-hover:text-blue-500 transition-colors"><span class="font-semibold">Arraste e solte</span> ou clique para adicionar mais imagens.</p>
                                </div>
                                <div id="event_images_preview" class="mt-4 grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4"></div>
                                @error('event_images')
                                    <div class="text-red-600 text-sm mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="flex justify-end">
                                <button type="button" data-next-tab="tab-info" class="next-button inline-flex items-center px-6 py-3 border border-transparent rounded-md font-semibold text-sm text-white bg-blue-600 hover:bg-blue-700 transition ease-in-out duration-150">
                                    Próximo
                                </button>
                            </div>
                        </div>

                        {{-- Aba 2: Informações do Evento --}}
                        <div id="tab-info" class="tab-content hidden space-y-6">
                            <h3 class="text-xl font-semibold text-gray-700 border-b pb-2">Informações do Evento</h3>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                {{-- Nome do evento --}}
                                <div>
                                    <x-input-label for="event_name" value="Nome do Evento" />
                                    <x-text-input type="text" name="event_name" id="event_name"
                                        value="{{ old('event_name') }}" required />
                                    @error('event_name')
                                        <div class="text-red-600 text-sm mt-1">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- Local do evento --}}
                                <div>
                                    <x-input-label for="event_location" value="Local" />
                                    <x-text-input type="text" name="event_location" id="event_location"
                                        value="{{ old('event_location') }}" required />
                                    @error('event_location')
                                        <div class="text-red-600 text-sm mt-1">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            {{-- Descrição do evento --}}
                            <div>
                                <x-input-label for="event_description" value="Descrição" />
                                <textarea name="event_description" id="event_description" rows="4"
                                    class="w-full mt-1 border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500" required>{{ old('event_description') }}</textarea>
                                @error('event_description')
                                    <div class="text-red-600 text-sm mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Categorias --}}
                            <div>
                                <x-input-label value="Categorias do Evento" />
                                <div class="mt-2 flex flex-wrap gap-4">
                                    @foreach ($categories as $category)
                                        <label class="inline-flex items-center space-x-2 transition-colors duration-200 ease-in-out cursor-pointer hover:text-blue-600">
                                            <input type="checkbox" name="categories[]" value="{{ $category->id }}"
                                                class="rounded text-blue-600 border-gray-300 focus:ring-blue-500"
                                                {{ in_array($category->id, old('categories', [])) ? 'checked' : '' }}>
                                            <span class="text-sm">{{ $category->category_name }}</span>
                                        </label>
                                    @endforeach
                                </div>
                                @error('categories')
                                    <div class="text-red-600 text-sm mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="flex justify-between">
                                <button type="button" data-prev-tab="tab-media" class="prev-button inline-flex items-center px-6 py-3 border border-gray-300 rounded-md font-semibold text-sm text-gray-700 bg-white hover:bg-gray-100 transition ease-in-out duration-150">
                                    Anterior
                                </button>
                                <button type="button" data-next-tab="tab-details" class="next-button inline-flex items-center px-6 py-3 border border-transparent rounded-md font-semibold text-sm text-white bg-blue-600 hover:bg-blue-700 transition ease-in-out duration-150">
                                    Próximo
                                </button>
                            </div>
                        </div>

                        {{-- Aba 3: Datas e Afiliação --}}
                        <div id="tab-details" class="tab-content hidden space-y-6">
                            <h3 class="text-xl font-semibold text-gray-700 border-b pb-2">Datas e Afiliação</h3>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                {{-- Data e Hora do Evento --}}
                                <div>
                                    <x-input-label for="event_scheduled_at" value="Data e Hora do Evento" />
                                    <x-text-input type="datetime-local" name="event_scheduled_at" id="event_scheduled_at"
                                        min="{{ \Carbon\Carbon::now()->format('Y-m-d\TH:i') }}"
                                        value="{{ old('event_scheduled_at') }}" required />
                                    @error('event_scheduled_at')
                                        <div class="text-red-600 text-sm mt-1">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- Exclusão Automática --}}
                                <div>
                                    <x-input-label for="event_expired_at" value="Exclusão Automática (opcional)" />
                                    <x-text-input type="datetime-local" name="event_expired_at" id="event_expired_at"
                                        min="{{ \Carbon\Carbon::now()->format('Y-m-d\TH:i') }}"
                                        value="{{ old('event_expired_at') }}" />
                                    @error('event_expired_at')
                                        <div class="text-red-600 text-sm mt-1">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            {{-- Coordenador e Tipo do Evento --}}
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <x-input-label for="coordinator_name" value="Coordenador Responsável" />
                                    <x-text-input id="coordinator_name" type="text" class="block mt-1 w-full bg-gray-100 cursor-not-allowed"
                                        value="{{ auth()->user()->name }}" readonly disabled />
                                    <input type="hidden" name="coordinator_id" value="{{ auth()->user()->coordinator->id }}">
                                </div>
                                @php
                                    $coordinatorType = auth()->user()->coordinator->coordinator_type;
                                    $eventTypeLabel = $coordinatorType === 'course' ? 'Evento de Curso' : 'Evento Geral';
                                    $courseName = ($coordinatorType === 'course' && auth()->user()->coordinator->coordinatedCourse) ? auth()->user()->coordinator->coordinatedCourse->course_name : 'Nenhum';
                                    $courseId = ($coordinatorType === 'course' && auth()->user()->coordinator->coordinatedCourse) ? auth()->user()->coordinator->coordinatedCourse->id : '';
                                @endphp
                                <div>
                                    <x-input-label for="event_type" value="Tipo do Evento" />
                                    <x-text-input id="coordinator_type" type="text" class="block mt-1 w-full bg-gray-100 cursor-not-allowed"
                                        value="{{ $eventTypeLabel }}" readonly disabled />
                                    <input type="hidden" name="coordinator_type" value="{{ $coordinatorType }}">
                                </div>
                            </div>

                            {{-- Curso do evento (se aplicável) --}}
                            @if ($coordinatorType === 'course')
                                <div class="md:col-span-2">
                                    <x-input-label for="event_course" value="Curso" />
                                    <x-text-input id="course_name" type="text" class="block mt-1 w-full bg-gray-100 cursor-not-allowed"
                                        value="{{ $courseName }}" readonly disabled />
                                    <input type="hidden" name="course_id" value="{{ $courseId }}">
                                </div>
                            @endif

                            <div class="flex justify-between">
                                <button type="button" data-prev-tab="tab-info" class="prev-button inline-flex items-center px-6 py-3 border border-gray-300 rounded-md font-semibold text-sm text-gray-700 bg-white hover:bg-gray-100 transition ease-in-out duration-150">
                                    Anterior
                                </button>
                                <button type="submit" class="submit-button inline-flex items-center px-6 py-3 border border-transparent rounded-md font-semibold text-sm text-white bg-green-600 hover:bg-green-700 transition ease-in-out duration-150">
                                    Criar Evento
                                </button>
                            </div>
                        </div>
                    </form>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>

@vite('resources/js/app.js')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Lógica de abas (permanece a mesma)
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

            window.scrollTo({ top: 0, behavior: 'smooth' });
        }

        nextButtons.forEach(button => {
            button.addEventListener('click', () => {
                const currentTab = button.closest('.tab-content');
                const inputs = currentTab.querySelectorAll('input[required], textarea[required]');
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

        // Seletores dos elementos de upload
        const dropzoneCover = document.getElementById('dropzone-cover');
        const eventImageInput = document.getElementById('event_image');
        const previewCover = document.getElementById('event_image_preview');

        const dropzoneGallery = document.getElementById('dropzone-gallery');
        const eventImagesInput = document.getElementById('event_images');
        const previewGallery = document.getElementById('event_images_preview');

        // Função genérica para configurar drag-and-drop
        function setupDropzone(dropzone, inputElement) {
            ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
                dropzone.addEventListener(eventName, e => {
                    e.preventDefault();
                    e.stopPropagation();
                }, false);
            });

            ['dragenter', 'dragover'].forEach(eventName => {
                dropzone.addEventListener(eventName, () => dropzone.classList.add('!border-blue-500', '!bg-blue-50'), false);
            });

            ['dragleave', 'drop'].forEach(eventName => {
                dropzone.addEventListener(eventName, () => dropzone.classList.remove('!border-blue-500', '!bg-blue-50'), false);
            });

            dropzone.addEventListener('drop', (e) => {
                inputElement.files = e.dataTransfer.files;
            }, false);

            dropzone.addEventListener('click', () => inputElement.click(), false);
        }

        // --- Lógica de pré-visualização e gerenciamento de arquivos ---

        // Map para armazenar o arquivo da capa
        const coverFile = new Map();

        // Lógica para a imagem de capa (única)
        eventImageInput.addEventListener('change', (e) => {
            coverFile.clear(); // Limpa a pré-visualização anterior
            previewCover.innerHTML = '';
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = (e) => {
                    const imageContainer = createImageContainer(e.target.result, false);
                    previewCover.appendChild(imageContainer);
                };
                reader.readAsDataURL(file);
                coverFile.set('main', file);
            }
        });

        // Array para armazenar os arquivos da galeria
        let galleryFiles = [];

        // Lógica para a galeria de imagens (múltiplas)
        eventImagesInput.addEventListener('change', (e) => {
            const newFiles = Array.from(e.target.files);
            newFiles.forEach(file => {
                if (!galleryFiles.some(existingFile => existingFile.name === file.name && existingFile.size === file.size)) {
                    galleryFiles.push(file);
                    const reader = new FileReader();
                    reader.onload = (e) => {
                        const imageContainer = createImageContainer(e.target.result, true);
                        previewGallery.appendChild(imageContainer);
                    };
                    reader.readAsDataURL(file);
                }
            });
        });

        // Função para criar o container da imagem com botão de remover
        function createImageContainer(src, isMultiple) {
            const imageContainer = document.createElement('div');
            imageContainer.className = `relative aspect-[4/3] rounded-lg overflow-hidden shadow-lg border border-gray-200 group ${isMultiple ? 'w-full' : 'max-w-sm w-full'}`;
            if (!isMultiple) {
                 imageContainer.className = 'relative w-full max-w-sm aspect-video rounded-lg overflow-hidden shadow-lg border border-gray-200 group';
            }

            const image = document.createElement('img');
            image.src = src;
            image.className = 'object-cover w-full h-full';
            imageContainer.appendChild(image);

            const removeButton = document.createElement('button');
            removeButton.type = 'button';
            removeButton.className = 'absolute top-2 right-2 p-1 bg-red-600 text-white rounded-full text-xs transition-all duration-200 opacity-0 group-hover:opacity-100 hover:scale-110';
            removeButton.innerHTML = `<svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                      </svg>`;
            removeButton.onclick = () => {
                imageContainer.remove();
                if (isMultiple) {
                    galleryFiles = galleryFiles.filter(file => {
                        return URL.createObjectURL(file) !== src;
                    });
                    const dataTransfer = new DataTransfer();
                    galleryFiles.forEach(file => dataTransfer.items.add(file));
                    eventImagesInput.files = dataTransfer.files;
                } else {
                    coverFile.clear();
                    eventImageInput.value = '';
                }
            };
            imageContainer.appendChild(removeButton);

            return imageContainer;
        }

        setupDropzone(dropzoneCover, eventImageInput);
        setupDropzone(dropzoneGallery, eventImagesInput);
    });
</script>