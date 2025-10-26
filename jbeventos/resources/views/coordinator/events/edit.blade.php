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

                    <div class="flex items-center justify-center mb-10">
                        {{-- Tab 1: Informações (ATIVA: Fundo Vermelho, Borda Vermelha, Texto Branco) --}}
                        <button type="button" data-tab-target="tab1"
                            class="tab-button flex items-center focus:outline-none transition-all duration-300">
                            <span
                                class="inline-flex items-center justify-center w-8 h-8 font-bold rounded-full transition-colors duration-300 border border-gray-300 bg-white text-gray-500">
                                {{-- **CLASSE ADICIONADA: border border-red-600** --}}
                                1
                            </span>
                            <span class="ml-2 text-sm md:text-base text-red-600">
                                Informações
                            </span>
                        </button>

                        <div class="border-t border-gray-300 w-20 mx-3 transition-colors duration-300"></div>

                        {{-- Tab 2: Imagens (INATIVA: Fundo Branco, Borda Cinza, Texto Cinza) --}}
                        <button type="button" data-tab-target="tab2"
                            class="tab-button flex items-center focus:outline-none transition-all duration-300 text-gray-500 hover:text-red-500">
                            <span
                                class="inline-flex items-center justify-center w-8 h-8 font-bold rounded-full transition-colors duration-300 border border-gray-300 bg-white text-gray-500">
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
                                <x-input-label for="event_info" value="Sobre o Evento"
                                    class="text-left mb-1 block" />
                                <textarea name="event_info" id="event_info" rows="4"
                                    class="w-full mt-1 border-gray-300 rounded-md shadow-sm focus:border-red-500 focus:ring-red-500 text-left block">{{ old('event_description', $event->event_description) }}</textarea>
                                @error('event_info')
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

                            @if ($event->event_type === 'course')
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
                                                <label for="course-{{ $course->id }}"
                                                    class="ml-2 text-sm text-gray-700">
                                                    {{ $course->course_name }}
                                                </label>
                                            </div>
                                        @empty
                                            <p class="text-sm text-gray-500">Nenhum curso adicional disponível para
                                                seleção.</p>
                                        @endforelse
                                    </div>

                                    <x-input-error for="courses" class="mt-2" />
                                </div>
                            @endif

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

                        <h3 class="text-xl font-semibold text-gray-700 border-b pb-2 mt-7"></h3>

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

                    {{-- Aba 2 --}}
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

                                <input type="hidden" name="remove_event_image" id="remove_event_image_input"
                                    value="0">

                                <div id="event_image_preview" class="mt-4 flex flex-wrap gap-4">
                                    {{-- Novo container APENAS para os dados iniciais da capa, renderizado pelo JS --}}
                                    @if ($event->event_image)
                                        {{-- O ID 'existing-event_image-preview' é o que o JS irá procurar --}}
                                        <div id="existing-event_image-preview" data-file-id="{{ $event->id }}"
                                            data-filename="{{ basename($event->event_image) }}" class="hidden">
                                            {{-- Mantenha hidden. O JS lerá e removerá. --}}
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

                                <div id="event_images_preview" class="mt-4 flex flex-wrap gap-4"></div>

                                {{-- Container que será preenchido pelo JS para as imagens existentes --}}
                                {{-- O ID original 'event_images_preview' é para novos uploads. Usamos um novo ID temporário --}}
                                <div id="existing-event_images-gallery" class="hidden"> {{-- Adicione 'hidden' para não aparecer antes do JS renderizar --}}
                                    @foreach ($event->images as $img)
                                        {{-- Div com os dados cruciais para o JS --}}
                                        <div data-id="{{ $img->id }}"
                                            data-filename="{{ basename($img->image_path) }}">
                                        </div>
                                    @endforeach
                                </div>
                            </div>

                            @error('event_images')
                                <div class="text-red-600 text-sm mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <h3 class="text-xl font-semibold text-gray-700 border-b pb-2 mt-7"></h3>

                        {{-- Botões somente na Aba 2 --}}
                        <div class="flex items-center justify-between mt-6">
                            <button type="button" data-prev-tab="tab1"
                                class="prev-button px-6 py-2 bg-gray-200 text-gray-700 rounded-md font-semibold hover:bg-gray-300 transition-colors duration-200">
                                Anterior
                            </button>

                            <button type="submit"
                                class="submit-button inline-flex items-center px-6 py-3 border border-transparent rounded-md font-semibold text-sm text-white bg-red-600 hover:bg-red-700 transition ease-in-out duration-150">
                                Atualizar Evento
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>

@vite('resources/js/tab-navigation.js')
