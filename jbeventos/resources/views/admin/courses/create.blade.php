<x-app-layout>
    <!-- Slot para o cabeçalho da página -->
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Criar Curso
        </h2>
    </x-slot>

    <div class="py-3">
        <!-- Container centralizado com largura máxima -->
        <div class="w-100% mx-auto sm:px-6 lg:px-8 flex justify-center">
            <!-- Caixa branca com sombra e bordas arredondadas para o formulário -->
            <div class="bg-white shadow-md rounded-2xl p-10 mt-10 w-[70rem] mx-auto">
                <!-- -->
                <div class="m-5 mb-16">
                    <img src="{{ asset('imgs/create.png') }}" class="w-32 mx-auto mt-4">
                </div>
                <!-- Se houver erros de validação, exibe a lista de erros -->
                @if ($errors->any())
                    <div class="mb-4 text-red-600">
                        <ul class="list-disc pl-5">
                            <!-- Loop para listar todos os erros -->
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <!-- Formulário que envia via POST para a rota 'courses.store' -->
                <form action="{{ route('courses.store') }}" method="POST" enctype="multipart/form-data"
                    class="space-y-4">
                    @csrf <!-- Token de proteção contra CSRF -->

                    <!-- Label/Input para inserir o nome do curso e nome do coordenador -->
                    <div class="flex w-full gap-4 mt">
                        <!-- Campo Nome -->
                        <div class="flex flex-col flex-1">
                            <label for="course_name" class="block font-medium mb-1">Nome do Curso</label>
                            <input type="text" name="course_name" id="course_name" autocomplete="off"
                                value="{{ old('course_name') }}"
                                placeholder="Digite o nome do curso"
                                class="w-full border border-gray-300 focus:border-stone-600 focus:ring-stone-600 
                                    focus:bg-white rounded p-2"
                                required>
                        </div>

                        <!-- Campo Coordenador -->
                        <div class="flex flex-col flex-1">
                            <label for="coordinator_id" class="block font-medium mb-1">
                                Coordenador (opcional)
                            </label>
                            <select name="coordinator_id" id="coordinator_id"
                                class="w-full border border-gray-300 rounded focus:bg-white focus:border-stone-600 focus:ring-stone-600 cursor-pointer p-2">
                                <option value="">Nenhum</option>
                                @foreach ($coordinators as $coordinator)
                                    @if ($coordinator->coordinator_type === 'course')
                                        @php $isDisabled = $coordinator->coordinatedCourse !== null; @endphp
                                        <option value="{{ $coordinator->id }}" @selected(old('coordinator_id') == $coordinator->id)
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


                    <!-- Campo para descrição do curso -->
                    <div>
                        <label for="course_description" class="block font-medium">Descrição</label>
                        <textarea name="course_description" id="course_description" placeholder="Digite a descrição do curso"
                            class="w-full border-gray-300 focus:border-stone-600 focus:ring-stone-600
                                        focus:bg-white rounded shadow-sm">{{ old('course_description') }}</textarea>
                    </div>

                    <!-- Upload do ícone do curso -->
                    <div>
                        <label for="course_icon_input" class="block font-medium">Ícone do Curso (imagem)</label>
                        <!-- Contêiner flex para organizar o botão e a mensagem -->
                        <div class="flex items-center space-x-4">
                            <!-- Label para o botão de upload de arquivo -->
                            <label for="course_icon_input"
                                class="bg-blue-500 text-white py-1 px-3 rounded-md cursor-pointer hover:bg-blue-600">
                                Selecionar imagem
                            </label>

                            <!-- Input de arquivo escondido -->
                            <input id="course_icon_input" type="file" class="hidden"
                                onchange="updateFileName('course_icon_input', 'course_icon_name')">

                            <!-- Parágrafo para exibir o nome do arquivo ou mensagem padrão -->
                            <p id="course_icon_name" class="text-gray-600">Nenhuma imagem escolhida</p>
                        </div>
                    </div>

                    <!-- Upload do banner do curso -->
                    <div>
                        <label for="course_banner_input" class="block font-medium">Banner do Curso (imagem)</label>
                        <!-- Contêiner flex para organizar o botão e a mensagem -->
                        <div class="flex items-center space-x-4">
                            <!-- Label para o botão de upload de arquivo -->
                            <label for="course_banner_input"
                                class="bg-blue-500 text-white py-1 px-3 rounded-md cursor-pointer hover:bg-blue-600">
                                Selecionar imagem
                            </label>

                            <!-- Input de arquivo escondido -->
                            <input id="course_banner_input" type="file" class="hidden"
                                onchange="updateFileName('course_banner_input', 'course_banner_name')">

                            <!-- Parágrafo para exibir o nome do arquivo ou mensagem padrão -->
                            <p id="course_banner_name" class="text-gray-600">Nenhuma imagem escolhida</p>
                        </div>
                    </div>

                    <script>
                        // Função para atualizar o nome do arquivo baseado no input e no parágrafo associados
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



                    <!-- Botões para enviar ou cancelar -->
                    <div class="w-100% flex justify-end space-x-2">
                        <button type="submit" class="bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700">
                            Salvar Curso
                        </button>
                        <a href="{{ route('courses.index') }}"
                            class="bg-stone-900 text-white px-4 py-2 rounded hover:bg-stone-700">
                            Cancelar
                        </a>
                    </div>

                </form>
            </div>
        </div>
    </div>
</x-app-layout>
