<x-app-layout>
    <!-- Cabeçalho da página -->
    <x-slot name="header">
        <h2 class="font-semibold text-2xl sm:text-3xl text-gray-800 leading-tight">
            Editar Curso
        </h2>
    </x-slot>

    <!-- Conteúdo principal -->
    <div class="py-6">
        <div class="w-full max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-md rounded-2xl p-4 sm:p-6 lg:p-8">

                <!-- Erros de validação -->
                @if ($errors->any())
                <div class="mb-4 text-red-600 text-sm sm:text-base">
                    <ul class="list-disc pl-5 space-y-1">
                        @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
                @endif

                <!-- Formulário -->
                <form action="{{ route('courses.update', $course->id) }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                    @csrf
                    @method('PUT')

                    <!-- Nome -->
                    <div>
                        <label for="course_name" class="block font-medium mb-1">Nome do Curso</label>
                        <input type="text" name="course_name" id="course_name"
                            value="{{ old('course_name', $course->course_name) }}"
                            class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 text-sm sm:text-base"
                            required>
                    </div>

                    <!-- Descrição -->
                    <div>
                        <label for="course_description" class="block font-medium mb-1">Descrição</label>
                        <textarea name="course_description" id="course_description"
                            class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 text-sm sm:text-base"
                            rows="4">{{ old('course_description', $course->course_description) }}</textarea>
                    </div>

                    <!-- Coordenador -->
                    <div>
                        <label for="coordinator_id" class="block font-medium mb-1">Coordenador (opcional)</label>
                        <select name="coordinator_id" id="coordinator_id"
                            class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 text-sm sm:text-base">
                            <option value="">-- Nenhum --</option>
                            @foreach($coordinators as $coordinator)
                                @if($coordinator->coordinator_type === 'course')
                                    @php
                                        $isSelected = old('coordinator_id', $course->coordinator_id) == $coordinator->id;
                                        $isDisabled = $coordinator->coordinatedCourse && $coordinator->coordinatedCourse->id != $course->id;
                                    @endphp

                                    <option value="{{ $coordinator->id }}" 
                                            {{ $isSelected ? 'selected' : '' }} 
                                            {{ $isDisabled ? 'disabled' : '' }}>
                                        {{ $coordinator->userAccount->name ?? 'Sem nome' }}
                                        @if($isDisabled)
                                            ({{ $coordinator->coordinatedCourse->course_name }})
                                        @endif
                                    </option>
                                @endif
                            @endforeach
                        </select>
                    </div>

                    <!-- Ícone atual -->
                    <div>
                        <label class="block font-medium mb-1">Ícone Atual</label>
                        <div class="mt-1">
                            @if($course->course_icon)
                            <img src="{{ asset('storage/' . $course->course_icon) }}" alt="Ícone do curso" class="w-16 sm:w-20 h-auto rounded-md">
                            @else
                            <p class="text-gray-500 text-sm">Nenhum ícone cadastrado.</p>
                            @endif
                        </div>
                    </div>

                    <!-- Alterar ícone -->
                    <div>
                        <label for="course_icon" class="block font-medium mb-1">Alterar Ícone do Curso</label>
                        <input type="file" name="course_icon" id="course_icon" accept="image/*"
                            class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 text-sm sm:text-base">
                    </div>

                    <!-- Banner atual -->
                    <div>
                        <label class="block font-medium mb-1">Banner Atual</label>
                        <div class="mt-1">
                            @if($course->course_banner)
                            <img src="{{ asset('storage/' . $course->course_banner) }}" alt="Banner do curso" class="w-28 sm:w-36 h-auto rounded-md">
                            @else
                            <p class="text-gray-500 text-sm">Nenhum banner cadastrado.</p>
                            @endif
                        </div>
                    </div>

                    <!-- Alterar banner -->
                    <div>
                        <label for="course_banner" class="block font-medium mb-1">Alterar Banner do Curso</label>
                        <input type="file" name="course_banner" id="course_banner" accept="image/*"
                            class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 text-sm sm:text-base">
                    </div>

                    <!-- Botões -->
                    <div class="flex flex-col sm:flex-row sm:space-x-2 space-y-2 sm:space-y-0 pt-4">
                        <button type="submit"
                            class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors text-sm sm:text-base">
                            Atualizar Curso
                        </button>
                        <a href="{{ route('courses.index') }}"
                            class="bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600 transition-colors text-sm sm:text-base text-center">
                            Cancelar
                        </a>
                    </div>
                </form>

            </div>
        </div>
    </div>
</x-app-layout>
