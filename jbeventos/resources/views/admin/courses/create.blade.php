<x-app-layout>
    <div class="w-full min-h-screen py-12">
        <div class="w-full max-w-[100rem] mx-auto px-4 sm:px-6 lg:px-8 flex justify-center">
            <div class="w-full bg-white shadow-xl rounded-2xl p-6 sm:p-8 lg:p-10 mx-auto min-h-[75vh] border border-red-100">
                <div class="flex flex-col lg:flex-row gap-8">
                    <!-- IMAGEM -->
                    <div class="w-full lg:w-1/2 h-[300px] lg:h-auto relative group">
                        <div class="absolute rounded-xl"></div>
                        <img src="{{ asset('imgs/etec.jpg') }}" alt="Imagem do curso"
                            class="w-full h-full object-cover rounded-xl shadow-lg transition-transform duration-300 group-hover:scale-[1.02]">
                        <div
                            class="absolute bottom-4 left-4 text-white text-xs sm:text-sm font-semibold px-4 py-1 rounded-lg shadow-md">
                            AMPARO • Etec JB
                        </div>
                    </div>

                    <!-- FORMULÁRIO -->
                    <div class="w-full lg:w-1/2">
                        @if ($errors->any())
                            <div class="mb-4 bg-red-100 border border-red-300 text-red-700 p-3 rounded-lg">
                                <ul class="list-disc pl-5">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <!-- TÍTULO DO FORMULÁRIO -->
                        <div class="mb-6">
                            <p
                                class="text-center bg-gradient-to-r from-red-600 to-red-400 bg-clip-text text-transparent font-extrabold text-3xl sm:text-5xl tracking-wide drop-shadow-md">
                                Criar Curso
                            </p>
                            <div class="w-24 h-1 bg-red-500 mx-auto rounded-full mt-3 shadow"></div>
                        </div>

                        <form action="{{ route('courses.store') }}" method="POST" enctype="multipart/form-data"
                            class="space-y-6">
                            @csrf

                            <!-- Nome do Curso & Coordenador -->
                            <div class="flex flex-col sm:flex-row gap-5">
                                <div class="flex-1">
                                    <label for="course_name" class="block font-semibold text-gray-700 mb-1">Nome do
                                        Curso</label>
                                    <input type="text" name="course_name" id="course_name"
                                        value="{{ old('course_name') }}" placeholder="Digite o nome do curso"
                                        class="w-full border border-gray-300 rounded-lg p-3 shadow-sm focus:border-stone-500 focus:ring-2 focus:ring-stone-500 outline-none transition"
                                        required>
                                </div>

                                <div class="flex-1">
                                    <label for="coordinator_id"
                                        class="block font-semibold text-gray-700 mb-1">Coordenador (opcional)</label>
                                    <select name="coordinator_id" id="coordinator_id"
                                        class="w-full border border-gray-300 rounded-lg p-3 shadow-sm focus:border-stone-500 focus:ring-2 focus:ring-stone-500 cursor-pointer transition">
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

                            <!-- Descrição -->
                            <div>
                                <label for="course_description" class="block font-semibold text-gray-700 mb-1">Descrição
                                    do Curso</label>
                                <textarea name="course_description" id="course_description" placeholder="Digite a descrição do curso" rows="4"
                                    class="w-full border border-gray-300 rounded-lg p-3 shadow-sm
                                    focus:border-stone-500 focus:ring-2 focus:ring-stone-500 outline-none
                                    resize-y max-h-40 transition">{{ old('course_description') }}</textarea>
                            </div>

                            <!-- Ícone do Curso -->
                            <div>
                                <label class="block font-semibold text-gray-700 mb-1">Ícone do Curso</label>
                                <div class="flex items-center gap-4">
                                    <label for="course_icon_input"
                                        class="bg-blue-600 text-white py-2 px-4 rounded-lg cursor-pointer hover:bg-red-700 transition">
                                        Selecionar imagem
                                    </label>
                                    <input id="course_icon_input" name="course_icon" type="file" class="hidden"
                                        onchange="updateFileName('course_icon_input', 'course_icon_name')">
                                    <p id="course_icon_name" class="text-gray-600 text-sm">Nenhum ícone escolhido</p>
                                </div>
                            </div>

                            <!-- Banner do Curso -->
                            <div>
                                <label class="block font-semibold text-gray-700 mb-1">Banner do Curso</label>
                                <div class="flex items-center gap-4">
                                    <label for="course_banner_input"
                                        class="bg-blue-600 text-white py-2 px-4 rounded-lg cursor-pointer hover:bg-red-700 transition">
                                        Selecionar imagem
                                    </label>
                                    <input id="course_banner_input" name="course_banner" type="file" class="hidden"
                                        onchange="updateFileName('course_banner_input', 'course_banner_name')">
                                    <p id="course_banner_name" class="text-gray-600 text-sm">Nenhum banner escolhido</p>
                                </div>
                            </div>

                            <script>
                                function updateFileName(inputId, paragraphId) {
                                    const fileInput = document.getElementById(inputId);
                                    const fileName = document.getElementById(paragraphId);
                                    fileName.textContent = fileInput.files.length > 0 ?
                                        fileInput.files[0].name :
                                        "Nenhuma imagem escolhida";
                                }
                            </script>

                            <!-- Botões -->
                            <div class="flex justify-end gap-3 pt-4">
                                <button type="submit"
                                    class="bg-green-600 text-white px-6 py-2 rounded-lg shadow-md hover:bg-green-700 transition">
                                    Salvar Curso
                                </button>
                                <a href="{{ route('courses.index') }}"
                                    class="bg-gray-700 text-white px-6 py-2 rounded-lg shadow-md hover:bg-gray-800 transition">
                                    Cancelar
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
