<x-app-layout>
    <div class="py-12">
        <div class="w-full mx-auto sm:px-10 lg:px-20 flex justify-center">
            <div class="w-full max-w-7xl bg-white shadow-md rounded-2xl p-4 sm:p-6 lg:p-9 mx-auto mt-2">

                <div class="w-full flex justify-center grid place-items-center mb-5">
                    <p class="text-2xl sm:text-3xl text-stone-900 font-semibold">Criar Curso</p>
                    <hr class="border-t border-gray-100 w-full mt-2">
                </div>

                <div class="m-5 mb-16">
                    <img src="{{ asset('imgs/create.png') }}" class="w-32 mx-auto mt-4">
                </div>
                <br>
                @if ($errors->any())
                <div class="mb-4 text-red-600">
                    <ul class="list-disc pl-5">
                        @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
                @endif

                <form action="{{ route('courses.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                    @csrf

                    <div class="flex flex-col sm:flex-row w-full px-3 gap-5">
                        <div class="flex flex-col flex-1">
                            <label for="course_name" class="block font-medium mb-1 px-2">Nome do Curso</label>
                            <input type="text" name="course_name" id="course_name" autocomplete="off" value="{{ old('course_name') }}"
                                placeholder="Digite o nome do curso"
                                class="w-full border border-gray-300 focus:border-stone-600 focus:ring-stone-600 focus:bg-white rounded p-3"
                                required>
                        </div>

                        <div class="flex flex-col flex-1 mt-4 sm:mt-0">
                            <label for="coordinator_id" class="block font-medium mb-1 px-2">Coordenador (opcional)</label>
                            <select name="coordinator_id" id="coordinator_id"
                                class="w-full border border-gray-300 rounded focus:bg-white focus:border-stone-600 focus:ring-stone-600 cursor-pointer p-3">
                                <option value="">Nenhum</option>
                                @foreach ($coordinators as $coordinator)
                                @if ($coordinator->coordinator_type === 'course')
                                @php $isDisabled = $coordinator->coordinatedCourse !== null; @endphp
                                <option value="{{ $coordinator->id }}" @selected(old('coordinator_id')==$coordinator->id)
                                    @disabled($isDisabled)>
                                    {{ $coordinator->userAccount->name ?? 'Sem nome' }}
                                    @if ($isDisabled)
                                    ({{ $coordinator->coordinatedCourse->course_name }})
                                    @endif
                                </option>
                                @endif
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="px-3 gap-5">
                        <label for="course_description" class="block font-medium px-2">Descrição</label>
                        <textarea name="course_description" id="course_description" placeholder="Digite a descrição do curso"
                            class="w-full border border-gray-300 focus:border-stone-600 focus:ring-stone-600 focus:bg-white rounded shadow-sm p-2">{{ old('course_description') }}</textarea>
                    </div>

                    <div class="px-3 gap-5">
                        <label for="course_icon_input" class="block font-medium ">Ícone do Curso (imagem)</label>
                        <div class="flex items-center space-x-4">
                            <label for="course_icon_input" class="bg-blue-500 text-white py-1 px-3 rounded-md cursor-pointer hover:bg-blue-600">
                                Selecionar imagem
                            </label>
                            <input id="course_icon_input" type="file" class="hidden" onchange="updateFileName('course_icon_input', 'course_icon_name')">
                            <p id="course_icon_name" class="text-gray-600">Nenhum ícone escolhido</p>
                        </div>
                    </div>

                    <div class="px-3 gap-5">
                        <label for="course_banner_input" class="block font-medium ">Banner do Curso (imagem)</label>
                        <div class="flex items-center space-x-4">
                            <label for="course_banner_input" class="bg-blue-500 text-white py-1 px-3 rounded-md cursor-pointer hover:bg-blue-600">
                                Selecionar imagem
                            </label>
                            <input id="course_banner_input" type="file" class="hidden" onchange="updateFileName('course_banner_input', 'course_banner_name')">
                            <p id="course_banner_name" class="text-gray-600">Nenhum Banner escolhido</p>
                        </div>
                    </div>

                    <script>
                        function updateFileName(inputId, paragraphId) {
                            var fileInput = document.getElementById(inputId);
                            var fileName = document.getElementById(paragraphId);
                            if (fileInput.files.length > 0) {
                                fileName.textContent = fileInput.files[0].name;
                            } else {
                                fileName.textContent = "Nenhuma imagem escolhida";
                            }
                        }
                    </script>

                    <div class="w-full flex justify-end space-x-2 mt-6 px-3 gap-1">
                        <button type="submit" class="bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700">
                            Salvar Curso
                        </button>
                        <a href="{{ route('courses.index') }}" class="bg-stone-900 text-white px-4 py-2 rounded hover:bg-stone-700">
                            Cancelar
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>