<x-app-layout>
    <div class="py-6">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-lg rounded-xl overflow-hidden p-6 md:p-10">

                {{-- Título da Página --}}
                <div class="flex flex-col items-center justify-center mb-10 text-center">
                    <div class="p-4 bg-red-100 rounded-full mb-4 shadow-md flex items-center justify-center w-16 h-16">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8 text-red-600" viewBox="0 0 256 256"
                            fill="currentColor">
                            <path
                                d="M208,32H184V24a8,8,0,0,0-16,0v8H88V24a8,8,0,0,0-16,0v8H48A16,16,0,0,0,32,48V208a16,16,0,0,0,16,16H208a16,16,0,0,0,16-16V48A16,16,0,0,0,208,32ZM72,48V56a8,8,0,0,0,16,0V48h80v8a8,8,0,0,0,16,0V48h24V80H48V48ZM208,208H48V96H208V208Z" />
                        </svg>
                    </div>
                    <h1 class="text-3xl font-bold text-gray-800">Criar Novo Evento</h1>
                    <p class="mt-2 text-gray-600">Preencha os detalhes do evento abaixo</p>
                </div>

                {{-- Exibição de erros de validação do Laravel/PHP --}}
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

                {{-- Mensagem de alerta para coordenador de curso sem curso vinculado --}}
                @if (auth()->user()->coordinator->coordinator_type === 'course' && !auth()->user()->coordinator->coordinatedCourse)
                    <div class="mb-6 p-4 bg-yellow-50 border-l-4 border-yellow-500 text-yellow-700 rounded-md">
                        <div class="flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-3 text-yellow-500" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <p class="text-sm">Você é coordenador de curso, mas ainda não está vinculado a nenhum curso.
                                Não é possível criar eventos até que um curso seja atribuído a você.</p>
                        </div>
                    </div>
                @else
                    {{-- BARRA DE NAVEGAÇÃO DAS ABAS --}}
                    <div class="flex items-center justify-center mb-10 text-center">
                        <div class="flex items-center space-x-2 sm:space-x-4">
                            {{-- Aba 1: Mídias --}}
                            <button type="button" data-tab-target="tab-media"
                                class="tab-button flex flex-col items-center group transition-colors duration-300">
                                <span
                                    class="w-8 h-8 flex items-center justify-center rounded-full border-2 border-gray-300 bg-white text-gray-500 transition-all duration-300 
                                    group-[.active]:bg-red-600 group-[.active]:border-red-600 group-[.active]:text-white">
                                    1
                                </span>
                                <span
                                    class="mt-2 text-xs font-medium text-gray-500 group-[.active]:text-red-600 hidden sm:inline">
                                    Mídias
                                </span>
                            </button>

                            <span class="w-8 h-px bg-gray-300 transition-colors duration-300"></span>

                            {{-- Aba 2: Informações --}}
                            <button type="button" data-tab-target="tab-info"
                                class="tab-button inactive flex flex-col items-center group transition-colors duration-300">
                                <span
                                    class="w-8 h-8 flex items-center justify-center rounded-full border-2 border-gray-300 bg-white text-gray-500 transition-all duration-300 
                                    group-[.active]:bg-red-600 group-[.active]:border-red-600 group-[.active]:text-white">
                                    2
                                </span>
                                <span
                                    class="mt-2 text-xs font-medium text-gray-500 group-[.active]:text-red-600 hidden sm:inline">
                                    Informações
                                </span>
                            </button>

                            <span class="w-8 h-px bg-gray-300 transition-colors duration-300"></span>

                            {{-- Aba 3: Detalhes --}}
                            <button type="button" data-tab-target="tab-details"
                                class="tab-button inactive flex flex-col items-center group transition-colors duration-300">
                                <span
                                    class="w-8 h-8 flex items-center justify-center rounded-full border-2 border-gray-300 bg-white text-gray-500 transition-all duration-300 
                                    group-[.active]:bg-red-600 group-[.active]:border-red-600 group-[.active]:text-white">
                                    3
                                </span>
                                <span
                                    class="mt-2 text-xs font-medium text-gray-500 group-[.active]:text-red-600 hidden sm:inline">
                                    Detalhes
                                </span>
                            </button>
                        </div>
                    </div>


                    {{-- Formulário de Criação --}}
                    <form id="event-form" action="{{ route('events.store') }}" method="POST"
                        enctype="multipart/form-data" class="space-y-8">
                        @csrf

                        {{-- Aba 1: Mídias --}}
                        <div id="tab-media" class="tab-content active space-y-6">
                            <h3 class="text-xl font-semibold text-gray-700 border-b pb-2">Imagens do Evento</h3>
                            <div>
                                <label for="event_image" class="block font-medium text-gray-700 mb-2">Imagem de
                                    Capa</label>
                                <div id="dropzone-cover"
                                    class="group relative flex flex-col items-center justify-center border-2 border-dashed border-gray-300 rounded-lg p-6 text-center cursor-pointer transition-all duration-300 hover:border-red-500 hover:bg-gray-50">
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
                                <div id="event_image_preview" class="mt-4 flex justify-start"></div>
                                @error('event_image')
                                    <div class="text-red-600 text-sm mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <div>
                                <label for="event_images" class="block font-medium text-gray-700 mb-2">Galeria de
                                    Imagens</label>
                                <div id="dropzone-gallery"
                                    class="group relative flex flex-col items-center justify-center border-2 border-dashed border-gray-300 rounded-lg p-6 text-center cursor-pointer transition-all duration-300 hover:border-red-500 hover:bg-gray-50">
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
                                        mais
                                        imagens.
                                    </p>
                                </div>
                                <div id="event_images_preview" class="mt-4 flex justify-start gap-4"></div>
                                @error('event_images')
                                    <div class="text-red-600 text-sm mt-1">{{ $message }}</div>
                                @enderror

                                <h3 class="text-xl font-semibold text-gray-700 border-b pb-2"></h3>

                                <div class="flex justify-between mt-5">
                                    <a href="{{ route('events.index') }}"
                                        class="inline-flex items-center px-6 py-3 border border-gray-300 rounded-md font-semibold text-sm text-gray-700 bg-white hover:bg-gray-100 transition ease-in-out duration-150">
                                        Cancelar
                                    </a>

                                    <button type="button" data-next-tab="tab-info"
                                        class="next-button inline-flex items-center px-6 py-3 border border-transparent rounded-md font-semibold text-sm text-white bg-red-600 hover:bg-red-700 transition ease-in-out duration-150">
                                        Próximo
                                    </button>
                                </div>
                            </div>
                        </div>


                        {{-- Aba 2: Informações do Evento (Campos de Nome e Local em largura total) --}}
                        <div id="tab-info" class="tab-content hidden space-y-6">
                            <h3 class="text-xl font-semibold text-gray-700 border-b pb-2">Informações do Evento
                            </h3>

                            {{-- Nome do Evento --}}
                            <div class="space-y-6">
                                <div>
                                    <x-input-label for="event_name" value="Nome do Evento" />
                                    {{-- Garante largura total com w-full (já está dentro do x-text-input, mas reforçamos o div pai) --}}
                                    <x-text-input type="text" name="event_name" id="event_name" maxlength="50"
                                        value="{{ old('event_name') }}"
                                        class="w-full border-gray-300 focus:border-red-500 focus:ring-red-500"
                                        required />
                                    @error('event_name')
                                        <div class="text-red-600 text-sm mt-1">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- Local do Evento --}}
                                <div>
                                    <x-input-label for="event_location" value="Local" />
                                    {{-- Garante largura total com w-full --}}
                                    <x-text-input type="text" name="event_location" id="event_location"
                                        value="{{ old('event_location') }}"
                                        class="w-full border-gray-300 focus:border-red-500 focus:ring-red-500"
                                        required />
                                    @error('event_location')
                                        <div class="text-red-600 text-sm mt-1">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>


                            {{-- Sobre o Evento --}}
                            <div>
                                <x-input-label for="event_info" value="Sobre o Evento" />
                                <textarea name="event_info" id="event_info" rows="4"
                                    class="w-full mt-1 border-gray-300 rounded-md shadow-sm focus:border-red-500 focus:ring-red-500">{{ old('event_description') }}</textarea>
                                @error('event_info')
                                    <div class="text-red-600 text-sm mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Categorias --}}
                            <div>
                                <x-input-label value="Categorias do Evento" />
                                <div class="mt-2 flex flex-wrap gap-4">
                                    @foreach ($categories as $category)
                                        <label
                                            class="inline-flex items-center space-x-2 transition-colors duration-200 ease-in-out cursor-pointer hover:text-red-600">
                                            <input type="checkbox" name="categories[]" value="{{ $category->id }}"
                                                class="rounded text-red-600 border-gray-300 focus:ring-red-500"
                                                {{ in_array($category->id, old('categories', [])) ? 'checked' : '' }}>
                                            <span class="text-sm">{{ $category->category_name }}</span>
                                        </label>
                                    @endforeach
                                </div>
                                @error('categories')
                                    <div class="text-red-600 text-sm mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <h3 class="text-xl font-semibold text-gray-700 border-b pb-2"></h3>

                            <div class="flex justify-between">
                                <button type="button" data-prev-tab="tab-media"
                                    class="prev-button inline-flex items-center px-6 py-3 border border-gray-300 rounded-md font-semibold text-sm text-gray-700 bg-white hover:bg-gray-100 transition ease-in-out duration-150">
                                    Anterior
                                </button>
                                <button type="button" data-next-tab="tab-details"
                                    class="next-button inline-flex items-center px-6 py-3 border border-transparent rounded-md font-semibold text-sm text-white bg-red-600 hover:bg-red-700 transition ease-in-out duration-150">
                                    Próximo
                                </button>
                            </div>
                        </div>

                        {{-- Aba 3: Datas e Afiliação --}}
                        <div id="tab-details" class="tab-content hidden space-y-6">
                            <h3 class="text-xl font-semibold text-gray-700 border-b pb-2">Datas e Afiliação</h3>

                            {{-- CAMPOS DE DATAS --}}
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                {{-- Data e Hora do Evento --}}
                                <div>
                                    <x-input-label for="event_scheduled_at" value="Data e Hora do Evento" />
                                    <x-text-input type="datetime-local" name="event_scheduled_at"
                                        id="event_scheduled_at" value="{{ old('event_scheduled_at') }}"
                                        class="w-full border-gray-300 focus:border-red-500 focus:ring-red-500"
                                        required />
                                    @error('event_scheduled_at')
                                        <div class="text-red-600 text-sm mt-1">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- Exclusão Automática --}}
                                <div>
                                    <x-input-label for="event_expired_at" value="Exclusão Automática (Opcional)" />
                                    <x-text-input type="datetime-local" name="event_expired_at" id="event_expired_at"
                                        {{-- Mantém a restrição mínima padrão (hora atual) que será sobrescrita pelo JS --}} min="{{ \Carbon\Carbon::now()->format('Y-m-d\TH:i') }}"
                                        value="{{ old('event_expired_at') }}"
                                        class="w-full border-gray-300 focus:border-red-500 focus:ring-red-500" />
                                    @error('event_expired_at')
                                        <div class="text-red-600 text-sm mt-1">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            {{-- Coordenador, Tipo do Evento e (opcionalmente) Curso --}}
                            @php
                                $coordinator = auth()->user()->coordinator;
                                $coordinatorType = $coordinator->coordinator_type;
                                $coordinatedCourse = $coordinator->coordinatedCourse;

                                $eventTypeLabel = $coordinatorType === 'course' ? 'Evento de Curso' : 'Evento Geral';

                                $defaultCourseName = $coordinatedCourse ? $coordinatedCourse->course_name : 'Nenhum';
                                $defaultCourseId = $coordinatedCourse ? $coordinatedCourse->id : null;

                                $availableCourses = $allCourses->where('id', '!=', $defaultCourseId);
                                $selectedCourses = old('courses', []);
                            @endphp

                            @if ($coordinatorType === 'course')
                                {{-- Layout com 3 campos: Coordenador, Tipo do Evento e Curso --}}
                                <div class="flex flex-wrap items-end justify-between">
                                    {{-- Coordenador Responsável --}}
                                    <div class="w-[280px]">
                                        <x-input-label for="coordinator_name" value="Coordenador Responsável" />
                                        <x-text-input id="coordinator_name" type="text"
                                            class="block mt-1 w-full bg-red-200 text-gray-700 font-medium cursor-not-allowed border-gray-400"
                                            value="{{ auth()->user()->name }}" readonly disabled />
                                        <input type="hidden" name="coordinator_id"
                                            value="{{ auth()->user()->coordinator->id }}">
                                    </div>

                                    {{-- Tipo do Evento --}}
                                    <div class="w-[150px] ml-[1.3rem]">
                                        <x-input-label for="event_type" value="Tipo do Evento" />
                                        <x-text-input id="coordinator_type" type="text"
                                            class="block mt-1 w-full bg-red-200 text-gray-700 font-medium cursor-not-allowed border-gray-400"
                                            value="{{ $eventTypeLabel }}" readonly disabled />
                                        <input type="hidden" name="coordinator_type"
                                            value="{{ $coordinatorType }}">
                                    </div>

                                    {{-- Curso Padrão --}}
                                    <div class="w-[280px] ml-auto">
                                        <x-input-label for="default_course" value="Curso Padrão (Obrigatório)" />
                                        <x-text-input id="default_course_name" type="text"
                                            class="block mt-1 w-full bg-red-200 text-gray-700 font-medium cursor-not-allowed border-gray-400"
                                            value="{{ $defaultCourseName }}" readonly disabled />
                                        @if ($defaultCourseId)
                                            <input type="hidden" name="courses[]" value="{{ $defaultCourseId }}">
                                        @endif
                                    </div>
                                </div>
                            @else
                                {{-- Layout com 2 campos: Coordenador e Tipo do Evento --}}
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    {{-- Coordenador Responsável --}}
                                    <div>
                                        <x-input-label for="coordinator_name" value="Coordenador Responsável" />
                                        <x-text-input id="coordinator_name" type="text"
                                            class="block mt-1 w-full bg-red-200 text-gray-700 font-medium cursor-not-allowed border-gray-400"
                                            value="{{ auth()->user()->name }}" readonly disabled />
                                        <input type="hidden" name="coordinator_id"
                                            value="{{ auth()->user()->coordinator->id }}">
                                    </div>

                                    {{-- Tipo do Evento --}}
                                    <div>
                                        <x-input-label for="event_type" value="Tipo do Evento" />
                                        <x-text-input id="coordinator_type" type="text"
                                            class="block mt-1 w-full bg-red-200 text-gray-700 font-medium cursor-not-allowed border-gray-400"
                                            value="{{ $eventTypeLabel }}" readonly disabled />
                                        <input type="hidden" name="coordinator_type"
                                            value="{{ $coordinatorType }}">
                                    </div>
                                </div>
                            @endif

                            @if ($coordinatorType === 'course')
                                {{-- NOVO CAMPO: Seleção de Outros Cursos como Checkboxes --}}
                                <div class="mt-4">
                                    <x-input-label for="courses" value="Cursos Adicionais (Opcional)" />

                                    {{-- Container para os checkboxes com scroll --}}
                                    <div
                                        class="mt-2 p-3 border border-gray-300 rounded-md shadow-sm h-40 overflow-y-auto bg-white">
                                        @forelse ($availableCourses as $course)
                                            <div class="flex items-center mb-1">
                                                {{-- O nome courses[] garante que o Laravel receba um array de IDs selecionados --}}
                                                <input id="course-{{ $course->id }}" name="courses[]"
                                                    type="checkbox" value="{{ $course->id }}"
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
                                </div>
                                <x-input-error for="courses" class="mt-2" />
                            @endif

                            <h3 class="text-xl font-semibold text-gray-700 border-b pb-2"></h3>

                            <div class="flex justify-between">
                                <button type="button" data-prev-tab="tab-info"
                                    class="prev-button inline-flex items-center px-6 py-3 border border-gray-300 rounded-md font-semibold text-sm text-gray-700 bg-white hover:bg-gray-100 transition ease-in-out duration-150">
                                    Anterior
                                </button>
                                {{-- BOTÃO DE SUBMISSÃO --}}
                                <button type="submit"
                                    class="submit-button inline-flex items-center px-6 py-3 border border-transparent rounded-md font-semibold text-sm text-white bg-red-600 hover:bg-red-700 transition ease-in-out duration-150">
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
