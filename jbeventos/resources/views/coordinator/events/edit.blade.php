<x-app-layout>
    {{-- Cabeçalho da Página --}}
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Editar Evento
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
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

                {{-- Formulário de Edição --}}
                <form id="event-edit-form" action="{{ route('events.update', $event->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    {{-- Abas de Navegação --}}
                    <div class="flex flex-col md:flex-row gap-4 mb-8 border-b pb-4">
                        <button type="button" data-tab-target="tab1" class="tab-button w-full md:w-auto flex-1 text-center py-2 px-4 rounded-lg transition-colors duration-200 ease-in-out border text-gray-700 active:bg-blue-50 active:text-blue-600 active:border-blue-500">
                            <span class="inline-flex items-center justify-center w-6 h-6 mr-2 font-bold rounded-full border border-gray-300 bg-white text-gray-500">1</span>
                            Informações
                        </button>
                        <button type="button" data-tab-target="tab2" class="tab-button w-full md:w-auto flex-1 text-center py-2 px-4 rounded-lg transition-colors duration-200 ease-in-out border text-gray-700 active:bg-blue-50 active:text-blue-600 active:border-blue-500">
                            <span class="inline-flex items-center justify-center w-6 h-6 mr-2 font-bold rounded-full border border-gray-300 bg-white text-gray-500">2</span>
                            Imagens
                        </button>
                    </div>

                    {{-- Conteúdo das Abas --}}
                    <div id="tab1" class="tab-content">
                        <div class="space-y-6">
                            <h3 class="text-2xl font-semibold text-gray-800 border-b pb-2 mb-6">Detalhes do Evento</h3>

                            <div>
                                <x-input-label for="event_name" value="Nome do Evento" />
                                <x-text-input type="text" name="event_name" id="event_name"
                                    value="{{ old('event_name', $event->event_name) }}" required />
                                @error('event_name')
                                    <div class="text-red-600 text-sm mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <div>
                                <x-input-label for="event_description" value="Descrição" />
                                <textarea name="event_description" id="event_description" rows="4"
                                    class="w-full mt-1 border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500" required>{{ old('event_description', $event->event_description) }}</textarea>
                                @error('event_description')
                                    <div class="text-red-600 text-sm mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <div>
                                <x-input-label for="event_location" value="Local" />
                                <x-text-input type="text" name="event_location" id="event_location"
                                    value="{{ old('event_location', $event->event_location) }}" required />
                                @error('event_location')
                                    <div class="text-red-600 text-sm mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <x-input-label for="event_scheduled_at" value="Data e Hora do Evento" />
                                    <x-text-input type="datetime-local" name="event_scheduled_at" id="event_scheduled_at"
                                        min="{{ \Carbon\Carbon::now()->format('Y-m-d\TH:i') }}"
                                        value="{{ old('event_scheduled_at', $event->event_scheduled_at ? \Carbon\Carbon::parse($event->event_scheduled_at)->format('Y-m-d\TH:i') : '') }}" required />
                                    @error('event_scheduled_at')
                                        <div class="text-red-600 text-sm mt-1">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div>
                                    <x-input-label for="event_expired_at" value="Exclusão Automática (opcional)" />
                                    <x-text-input type="datetime-local" name="event_expired_at" id="event_expired_at"
                                        min="{{ \Carbon\Carbon::now()->format('Y-m-d\TH:i') }}"
                                        value="{{ old('event_expired_at', $event->event_expired_at ? \Carbon\Carbon::parse($event->event_expired_at)->format('Y-m-d\TH:i') : '') }}" />
                                    @error('event_expired_at')
                                        <div class="text-red-600 text-sm mt-1">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div>
                                <x-input-label value="Categorias do Evento" />
                                <div class="mt-2 flex flex-wrap gap-4">
                                    @foreach ($categories as $category)
                                        <label class="inline-flex items-center space-x-2 transition-colors duration-200 ease-in-out cursor-pointer hover:text-blue-600">
                                            <input type="checkbox" name="categories[]" value="{{ $category->id }}"
                                                class="rounded text-blue-600 border-gray-300 focus:ring-blue-500"
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

                        <div class="flex justify-end mt-8">
                            <button type="button" data-next-tab="tab2" class="next-button px-6 py-2 bg-blue-600 text-white rounded-md font-semibold hover:bg-blue-700 transition-colors duration-200">
                                Próximo
                            </button>
                        </div>
                    </div>

                    <div id="tab2" class="tab-content hidden">
                        <div class="space-y-6">
                            <h3 class="text-2xl font-semibold text-gray-800 border-b pb-2 mb-6">Imagens do Evento</h3>

                            {{-- Imagem de Capa --}}
                            <div>
                                <x-input-label for="event_image" value="Imagem de Capa (principal)" />
                                <div id="dropzone-cover" class="group relative flex flex-col items-center justify-center border-2 border-dashed border-gray-300 rounded-lg p-6 text-center transition-all duration-300 hover:border-blue-500 hover:bg-gray-50">
                                    <input type="file" name="event_image" id="event_image" accept="image/*" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer">
                                    <svg class="h-10 w-10 text-gray-400 group-hover:text-blue-500 transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                    <p class="mt-2 text-sm text-gray-500 group-hover:text-blue-500 transition-colors">Arraste e solte ou clique para enviar uma **nova** imagem de capa.</p>
                                </div>
                                <div id="event_image_preview" class="mt-4 flex justify-center">
                                    {{-- A imagem de capa existente será injetada aqui pelo JS --}}
                                </div>
                            </div>

                            {{-- Outras Imagens (Galeria) --}}
                            <div>
                                <x-input-label for="event_images" value="Galeria de Imagens (opcional)" />
                                <div id="dropzone-gallery" class="group relative flex flex-col items-center justify-center border-2 border-dashed border-gray-300 rounded-lg p-6 text-center transition-all duration-300 hover:border-blue-500 hover:bg-gray-50">
                                    <input type="file" name="event_images[]" id="event_images" accept="image/*" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer" multiple>
                                    <svg class="h-10 w-10 text-gray-400 group-hover:text-blue-500 transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 11V5" />
                                    </svg>
                                    <p class="mt-2 text-sm text-gray-500 group-hover:text-blue-500 transition-colors">Arraste e solte ou clique para adicionar **mais** imagens.</p>
                                </div>
                                <div id="event_images_preview" class="mt-4 grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4">
                                    {{-- As imagens da galeria existentes serão injetadas aqui pelo JS --}}
                                </div>
                            </div>
                        </div>

                        <div class="flex items-center justify-between mt-8">
                            <button type="button" data-prev-tab="tab1" class="prev-button px-6 py-2 bg-gray-200 text-gray-700 rounded-md font-semibold hover:bg-gray-300 transition-colors duration-200">
                                Anterior
                            </button>
                            <x-primary-button>
                                Atualizar Evento
                            </x-primary-button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Lógica de abas
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

        showTab('tab1');

        // --- Lógica de Imagens (Capa e Galeria) ---
        const dropzoneCover = document.getElementById('dropzone-cover');
        const eventImageInput = document.getElementById('event_image');
        const previewCover = document.getElementById('event_image_preview');

        const dropzoneGallery = document.getElementById('dropzone-gallery');
        const eventImagesInput = document.getElementById('event_images');
        const previewGallery = document.getElementById('event_images_preview');

        // Inicializa o estado com as imagens existentes do evento
        const eventData = @json($event);

        if (eventData.event_image) {
            const imageContainer = createImageContainer(`/storage/${eventData.event_image}`, false, eventData.id, true);
            previewCover.appendChild(imageContainer);
        }

        if (eventData.images) {
            eventData.images.forEach(img => {
                const imageContainer = createImageContainer(`/storage/${img.image_path}`, true, img.id, true);
                previewGallery.appendChild(imageContainer);
            });
        }

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
                const changeEvent = new Event('change', { bubbles: true });
                inputElement.dispatchEvent(changeEvent);
            }, false);
        }

        // Lógica para a imagem de capa (única)
        eventImageInput.addEventListener('change', (e) => {
            const file = e.target.files[0];
            if (file) {
                previewCover.innerHTML = '';
                const reader = new FileReader();
                reader.onload = (e) => {
                    const imageContainer = createImageContainer(e.target.result, false, null, false);
                    previewCover.appendChild(imageContainer);
                };
                reader.readAsDataURL(file);
            }
        });

        // Lógica para a galeria de imagens (múltiplas)
        eventImagesInput.addEventListener('change', (e) => {
            const files = Array.from(e.target.files);
            files.forEach(file => {
                const reader = new FileReader();
                reader.onload = (e) => {
                    const imageContainer = createImageContainer(e.target.result, true, null, false);
                    previewGallery.appendChild(imageContainer);
                };
                reader.readAsDataURL(file);
            });
        });

        // Função para criar o container da imagem com botão de remover
        function createImageContainer(src, isMultiple, imageId = null, isExisting = false) {
            const imageContainer = document.createElement('div');
            imageContainer.className = `relative rounded-lg overflow-hidden shadow-lg border border-gray-200 group ${isMultiple ? 'w-full aspect-[4/3]' : 'w-full max-w-sm aspect-video'}`;
            if (imageId) {
                imageContainer.dataset.id = imageId;
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
                if (isExisting) {
                    const removedInput = document.createElement('input');
                    removedInput.type = 'hidden';
                    removedInput.name = 'removed_images[]';
                    removedInput.value = imageId;
                    document.getElementById('event-edit-form').appendChild(removedInput);
                } else {
                    if (isMultiple) {
                        const files = Array.from(eventImagesInput.files);
                        const dataTransfer = new DataTransfer();
                        const newFiles = files.filter(file => URL.createObjectURL(file) !== src);
                        newFiles.forEach(file => dataTransfer.items.add(file));
                        eventImagesInput.files = dataTransfer.files;
                    } else {
                        eventImageInput.value = '';
                    }
                }
            };
            imageContainer.appendChild(removeButton);

            return imageContainer;
        }

        setupDropzone(dropzoneCover, eventImageInput);
        setupDropzone(dropzoneGallery, eventImagesInput);
    });
</script>