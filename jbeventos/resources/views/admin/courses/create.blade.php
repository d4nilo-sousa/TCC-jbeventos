<x-app-layout>
    <!-- Slot para o cabeçalho da página -->
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Criar Curso
        </h2>
    </x-slot>

    <div class="py-6">
        <!-- Container centralizado com largura máxima -->
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <!-- Caixa branca com sombra e bordas arredondadas para o formulário -->
            <div class="bg-white shadow-md rounded p-6">
                
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
                <form action="{{ route('courses.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                    @csrf <!-- Token de proteção contra CSRF -->

                    <!-- Campo para nome do curso -->
                    <div>
                        <label for="course_name" class="block font-medium">Nome do Curso</label>
                        <input type="text" name="course_name" id="course_name" 
                               value="{{ old('course_name') }}" 
                               class="w-full border-gray-300 rounded shadow-sm" 
                               required>
                    </div>

                    <!-- Campo para descrição do curso -->
                    <div>
                        <label for="course_description" class="block font-medium">Descrição</label>
                        <textarea name="course_description" id="course_description" 
                                  class="w-full border-gray-300 rounded shadow-sm">{{ old('course_description') }}</textarea>
                    </div>

                    <!-- Select para escolher coordenador do curso (opcional) -->
                    <div>
                        <label for="coordinator_id" class="block font-medium">Coordenador (opcional)</label>
                        <select name="coordinator_id" id="coordinator_id" class="w-full border-gray-300 rounded shadow-sm">
                            <option value="">-- Nenhum --</option>
                            <!-- Loop para listar os coordenadores disponíveis -->
                            @foreach($coordinators as $coordinator)
                                @if($coordinator->coordinator_type === 'course')
                                    @if($coordinator->coordinatedCourse)
                                        {{-- Coordenador já vinculado a um curso, opção desabilitada --}}
                                        <option value="{{ $coordinator->id }}" disabled>
                                            {{ $coordinator->userAccount->name ?? 'Sem nome' }} 
                                            ({{ $coordinator->coordinatedCourse->course_name }})
                                        </option>
                                    @else
                                        {{-- Coordenador disponível, opção selecionável --}}
                                        <option value="{{ $coordinator->id }}" {{ old('coordinator_id') == $coordinator->id ? 'selected' : '' }}>
                                            {{ $coordinator->userAccount->name ?? 'Sem nome' }}
                                        </option>
                                    @endif
                                @endif
                            @endforeach
                        </select>
                    </div>

                    <!-- Upload do ícone do curso -->
                    <div>
                        <label for="course_icon" class="block font-medium">Ícone do Curso (imagem)</label>
                        <input type="file" name="course_icon" id="course_icon" accept="image/*" 
                               class="w-full border-gray-300 rounded shadow-sm">
                    </div>

                    <!-- Upload do banner do curso -->
                    <div>
                        <label for="course_banner" class="block font-medium">Banner do Curso (imagem)</label>
                        <input type="file" name="course_banner" id="course_banner" accept="image/*" 
                               class="w-full border-gray-300 rounded shadow-sm">
                    </div>

                    <!-- Botões para enviar ou cancelar -->
                    <div class="flex space-x-2">
                        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                            Salvar Curso
                        </button>
                        <a href="{{ route('courses.index') }}" 
                           class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600">
                            Cancelar
                        </a>
                    </div>

                </form>
            </div>
        </div>
    </div>
</x-app-layout>
