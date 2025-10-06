<x-app-layout>
    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-lg rounded-xl overflow-hidden p-6 md:p-10">

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

                <form id="course-edit-form" action="{{ route('courses.update', $course->id) }}" method="POST"
                    enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

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

                    <div id="tab1" class="tab-content">
                        <div class="space-y-6">
                            <h3 class="text-2xl font-semibold text-gray-800 border-b pb-2 mb-6">Detalhes do Curso</h3>

                            <div>
                                <x-input-label for="course_name" value="Nome do Curso" />
                                <x-text-input type="text" name="course_name" id="course_name"
                                    value="{{ old('course_name', $course->course_name) }}" required />
                                @error('course_name')
                                    <div class="text-red-600 text-sm mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <div>
                                <x-input-label for="course_description" value="Descrição" />
                                <textarea name="course_description" id="course_description" rows="4"
                                    class="w-full mt-1 border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500">{{ old('course_description', $course->course_description) }}</textarea>
                                @error('course_description')
                                    <div class="text-red-600 text-sm mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <div>
                                <x-input-label for="coordinator_id" value="Coordenador" />
                                <select name="coordinator_id" id="coordinator_id"
                                    class="w-full mt-1 border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500">
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
                                class="next-button px-6 py-2 bg-blue-600 text-white rounded-md font-semibold hover:bg-blue-700 transition-colors duration-200">
                                Próximo
                            </button>
                        </div>
                    </div>

                    <div id="tab2" class="tab-content hidden">
                        <div class="space-y-6">
                            <h3 class="text-2xl font-semibold text-gray-800 border-b pb-2 mb-6">Imagens do Curso</h3>

                            <div>
                                <x-input-label for="course_icon" value="Ícone do Curso" />
                                <div id="dropzone-icon"
                                    class="group relative flex flex-col items-center justify-center border-2 border-dashed border-gray-300 rounded-lg p-6 text-center hover:border-blue-500 hover:bg-gray-50 transition-all duration-300">
                                    <input type="file" name="course_icon" id="course_icon" accept="image/*"
                                        class="absolute inset-0 w-full h-full opacity-0 cursor-pointer">
                                    <svg class="h-10 w-10 text-gray-400 group-hover:text-blue-500 transition-colors"
                                        fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                    <p class="mt-2 text-sm text-gray-500 group-hover:text-blue-500">Arraste e solte ou
                                        clique para enviar um ícone.</p>
                                </div>

                                <input type="hidden" name="remove_course_icon" value="0">

                                <div id="course_icon_preview" class="mt-4 flex flex-col items-center gap-2">
                                    @if ($course->course_icon)
                                        <div data-filename="{{ basename($course->course_icon) }}"
                                            style="display: flex; align-items: center; gap: 10px;">
                                            <input type="text" value="{{ basename($course->course_icon) }}" readonly
                                                style="cursor: default;">
                                            <button type="button" {{-- AQUI ESTÁ A MUDANÇA: Chamando a função JavaScript para excluir via AJAX --}}
                                                onclick="window.deleteImage({{ $course->id }}, this, 'icon');"
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
                                <x-input-label for="course_banner" value="Banner do Curso" />
                                <div id="dropzone-banner"
                                    class="group relative flex flex-col items-center justify-center border-2 border-dashed border-gray-300 rounded-lg p-6 text-center hover:border-blue-500 hover:bg-gray-50 transition-all duration-300">
                                    <input type="file" name="course_banner" id="course_banner" accept="image/*"
                                        class="absolute inset-0 w-full h-full opacity-0 cursor-pointer">
                                    <svg class="h-10 w-10 text-gray-400 group-hover:text-blue-500 transition-colors"
                                        fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 11V5" />
                                    </svg>
                                    <p class="mt-2 text-sm text-gray-500 group-hover:text-blue-500">Arraste e solte ou
                                        clique para enviar um banner.</p>
                                </div>

                                <input type="hidden" name="remove_course_banner" value="0">

                                <div id="course_banner_preview" class="mt-4 flex flex-col items-center gap-2">
                                    @if ($course->course_banner)
                                        <div data-filename="{{ basename($course->course_banner) }}"
                                            style="display: flex; align-items: center; gap: 10px;">
                                            <input type="text" value="{{ basename($course->course_banner) }}"
                                                readonly style="cursor: default;">
                                            <button type="button" {{-- AQUI ESTÁ A MUDANÇA: Chamando a função JavaScript para excluir via AJAX --}}
                                                onclick="window.deleteImage({{ $course->id }}, this, 'banner');"
                                                style="background-color: #007BFF; color: #fff; border: none; padding: 5px 10px; border-radius: 4px; cursor: pointer;"
                                                onmouseenter="this.style.backgroundColor='#0056b3'"
                                                onmouseleave="this.style.backgroundColor='#007BFF'">
                                                Excluir
                                            </button>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <h3 class="text-xl font-semibold text-gray-700 border-b pb-2 mt-7"></h3>

                            <div class="flex items-center justify-between mt-8">
                                <button type="button" data-prev-tab="tab1"
                                    class="prev-button px-6 py-2 bg-gray-200 text-gray-700 rounded-md font-semibold hover:bg-gray-300 transition-colors duration-200">
                                    Anterior
                                </button>
                                <button type="submit"
                                    class="submit-button inline-flex items-center px-6 py-3 border border-transparent rounded-md font-semibold text-sm text-white bg-green-600 hover:bg-green-700 transition ease-in-out duration-150">
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

        function showTab(tabId) {
            tabs.forEach(tab => tab.classList.add('hidden'));
            const activeTab = document.getElementById(tabId);
            if (activeTab) {
                activeTab.classList.remove('hidden');
            }

            tabButtons.forEach(button => {
                const buttonSpan = button.querySelector('span:first-child');
                if (button.dataset.tabTarget === tabId) {
                    button.classList.add('active', 'text-gray-700', 'font-semibold');
                    buttonSpan?.classList.add('bg-blue-50', 'border-blue-500', 'text-blue-600');
                    buttonSpan?.classList.remove('bg-white', 'border-gray-300', 'text-gray-500');
                } else {
                    button.classList.remove('active', 'text-gray-700', 'font-semibold');
                    buttonSpan?.classList.remove('bg-blue-50', 'border-blue-500', 'text-blue-600');
                    buttonSpan?.classList.add('bg-white', 'border-gray-300', 'text-gray-500');
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
                const nextTabId = button.dataset.nextTab;

                const inputs = currentTab?.querySelectorAll('input, textarea, select');
                let allInputsValid = true;

                if (inputs) {
                    for (const input of inputs) {
                        if (!input.checkValidity()) {
                            input.reportValidity();
                            allInputsValid = false;
                            break;
                        }
                    }
                }

                if (allInputsValid && nextTabId) {
                    showTab(nextTabId);
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
                    showTab(target);
                }
            });
        });

        const path = window.location.pathname;

        const isCreate = path === '/admin/courses/create';
        const isEdit = /^\/admin\/courses\/\d+\/edit$/.test(path);

        if ((isCreate || isEdit) && document.getElementById('tab1')) {
            showTab('tab1');
        }
    });
</script>
