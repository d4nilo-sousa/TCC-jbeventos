<x-app-layout>
    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-lg rounded-xl overflow-hidden p-6 md:p-10">

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
                    <div class="flex flex-col md:flex-row gap-4 mb-8 border-b pb-4">
                        <button type="button" data-tab-target="tab1"
                            class="tab-button w-full md:w-auto flex-1 text-center py-2 px-4 rounded-lg transition-colors duration-200 ease-in-out border text-gray-700 active:bg-blue-50 active:text-blue-600 active:border-blue-500">
                            <span
                                class="inline-flex items-center justify-center w-6 h-6 mr-2 font-bold rounded-full border border-gray-300 bg-white text-gray-500">1</span>
                            Informações
                        </button>
                        <button type="button" data-tab-target="tab2"
                            class="tab-button w-full md:w-auto flex-1 text-center py-2 px-4 rounded-lg transition-colors duration-200 ease-in-out border text-gray-700 active:bg-blue-50 active:text-blue-600 active:border-blue-500">
                            <span
                                class="inline-flex items-center justify-center w-6 h-6 mr-2 font-bold rounded-full border border-gray-300 bg-white text-gray-500">2</span>
                            Imagens
                        </button>
                    </div>

                    {{-- Conteúdo das Abas --}}
                    <div id="tab1" class="tab-content">
                        <div class="space-y-6">
                            <h3 class="text-2xl font-semibold text-gray-800 border-b pb-2 mb-6">Detalhes do Evento</h3>

                            <div>
                                <x-input-label for="event_name" value="Nome do Evento" />
                                <x-text-input type="text" name="event_name" id="event_name" maxlength="50"
                                    value="{{ old('event_name', $event->event_name) }}" required />
                                @error('event_name')
                                    <div class="text-red-600 text-sm mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-4">
                                <x-input-label for="event_info" value="Sobre o Evento" class="text-left mb-1 block" />
                                <textarea name="event_info" id="event_info" rows="4"
                                    class="w-full mt-1 border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500 text-left mb-1 block">{{ old('event_info', $event->event_info) }}</textarea>
                                @error('event_info')
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
                                    <x-text-input type="datetime-local" name="event_scheduled_at"
                                        id="event_scheduled_at"
                                        value="{{ old('event_scheduled_at', $event->event_scheduled_at ? \Carbon\Carbon::parse($event->event_scheduled_at)->format('Y-m-d\TH:i') : '') }}"
                                        required />
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
                                                class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500"
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

                            <div>
                                <x-input-label value="Categorias do Evento" />
                                <div class="mt-2 flex flex-wrap gap-4">
                                    @foreach ($categories as $category)
                                        <label
                                            class="inline-flex items-center space-x-2 transition-colors duration-200 ease-in-out cursor-pointer hover:text-blue-600">
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

                        <h3 class="text-xl font-semibold text-gray-700 border-b pb-2 mt-5"></h3>

                        <div class="flex justify-between mt-8">
                            <a href="{{ route('events.show', $event->id) }}"
                                class="px-6 py-2 bg-gray-300 text-gray-700 rounded-md font-semibold hover:bg-gray-400 transition-colors duration-200">
                                Cancelar
                            </a>

                            <button type="button" data-next-tab="tab2"
                                class="next-button px-6 py-2 bg-blue-600 text-white rounded-md font-semibold hover:bg-blue-700 transition-colors duration-200">
                                Próximo
                            </button>
                        </div>
                    </div>

                    <div id="tab2" class="tab-content hidden">
                        <div class="space-y-6">
                            <h3 class="text-2xl font-semibold text-gray-800 border-b pb-2 mb-6">Imagens do Evento</h3>

                            <div>
                                <label for="event_image" class="block font-medium text-gray-700 mb-2">Imagem de
                                    Capa</label>
                                <div id="dropzone-cover"
                                    class="group relative flex flex-col items-center justify-center border-2 border-dashed border-gray-300 rounded-lg p-6 text-center cursor-pointer transition-all duration-300 hover:border-blue-500 hover:bg-gray-50">
                                    <input type="file" name="event_image" id="event_image" accept="image/*"
                                        class="absolute inset-0 w-full h-full opacity-0 cursor-pointer">
                                    <svg class="h-10 w-10 text-gray-400 group-hover:text-blue-500 transition-colors"
                                        fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                    <p class="mt-2 text-sm text-gray-500 group-hover:text-blue-500 transition-colors">
                                        <span class="font-semibold">Arraste e solte</span> ou clique para enviar a
                                        imagem de capa.
                                    </p>
                                </div>

                                <input type="hidden" name="remove_event_image" value="0">

                                <div id="event_image_preview" class="mt-4 flex flex-col items-center gap-2">
                                    @if ($event->event_image)
                                        <div data-filename="{{ basename($event->event_image) }}"
                                            style="display: flex; align-items: center; gap: 10px;">
                                            <input type="text" value="{{ basename($event->event_image) }}"
                                                readonly style="cursor: default;">
                                            <button type="button"
                                                onclick="this.closest('div[data-filename]').remove(); document.querySelector('input[name=remove_event_image]').value = 1;"
                                                style="background-color: #007BFF; color: #fff; border: none; padding: 5px 10px; border-radius: 4px; cursor: pointer;"
                                                onmouseenter="this.style.backgroundColor='#0056b3'"
                                                onmouseleave="this.style.backgroundColor='#007BFF'">
                                                Excluir
                                            </button>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <div>
                                <label for="event_images" class="block font-medium text-gray-700 mb-2">Galeria de
                                    Imagens</label>

                                <div id="dropzone-gallery"
                                    class="group relative flex flex-col items-center justify-center border-2 border-dashed border-gray-300 rounded-lg p-6 text-center cursor-pointer transition-all duration-300 hover:border-blue-500 hover:bg-gray-50">
                                    <input type="file" name="event_images[]" id="event_images" accept="image/*"
                                        class="absolute inset-0 w-full h-full opacity-0 cursor-pointer" multiple>
                                    <svg class="h-10 w-10 text-gray-400 group-hover:text-blue-500 transition-colors"
                                        fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 11V5" />
                                    </svg>
                                    <p class="mt-2 text-sm text-gray-500 group-hover:text-blue-500 transition-colors">
                                        <span class="font-semibold">Arraste e solte</span> ou clique para adicionar
                                        mais imagens.
                                    </p>
                                </div>

                                <div id="event_images_preview" class="mt-4 flex flex-wrap gap-2 justify-center">
                                    @foreach ($event->images as $img)
                                        <div data-id="{{ $img->id }}"
                                            data-filename="{{ basename($img->image_path) }}">
                                            <input type="text" value="{{ basename($img->image_path) }}" readonly
                                                style="cursor: default;">

                                            <button type="button"
                                                onclick="deleteImage({{ $img->id }}, this, 'event')"
                                                style="background-color: #007BFF; color: #fff; border: none; padding: 5px 10px; border-radius: 4px; cursor: pointer;"
                                                onmouseenter="this.style.backgroundColor='#0056b3'"
                                                onmouseleave="this.style.backgroundColor='#007BFF'">
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
                                <button type="submit"
                                    class="submit-button inline-flex items-center px-6 py-3 border border-transparent rounded-md font-semibold text-sm text-white bg-green-600 hover:bg-green-700 transition ease-in-out duration-150">
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
