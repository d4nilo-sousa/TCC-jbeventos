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
                    <h1 class="text-3xl font-bold text-gray-800">Criar Novo Curso</h1>
                    <p class="mt-2 text-gray-600">Preencha as informações para cadastrar um novo curso.</p>
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
                <form id="course-edit-form" action="{{ route('courses.store') }}" method="POST"
                    enctype="multipart/form-data">
                    @csrf
                    {{-- @method('POST') é implícito, não é necessário --}}

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
                                    value="{{ old('course_name') }}" required />
                                @error('course_name')
                                    <div class="text-red-600 text-sm mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <div>
                                <x-input-label for="course_description" value="Descrição" />
                                <textarea name="course_description" id="course_description" rows="4"
                                    class="w-full mt-1 border-gray-300 rounded-md shadow-sm focus:border-red-500 focus:ring-red-500">{{ old('course_description') }}</textarea>
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
                                                $isSelected = old('coordinator_id') == $coordinator->id;
                                                // Lógica de disabled mais simples, pois não há curso existente
                                                $isDisabled = $coordinator->coordinatedCourse;
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
                            <a href="{{ route('courses.index') }}"
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

                            {{-- Ícone do Curso (course_icon) --}}
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
                                <div id="course_icon_preview" class="mt-4 flex justify-start">
                                </div>
                                @error('course_icon')
                                    <div class="text-red-600 text-sm mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Banner do Curso (course_banner) --}}
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
                                <div id="course_banner_preview" class="mt-4 flex justify-start">
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
                                Criar Curso
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>

{{-- Importa o script JS adaptado --}}
@vite('resources/js/tabs-navigation-and-images.js')