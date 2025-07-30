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
            <div class="bg-white shadow-md rounded-2xl p-6 mt-10">
                <!-- -->
                <div class="m-8">
                    <img src="{{ asset('imgs/add.png') }}" class="w-20 mx-auto mt-4"> 
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
                <form action="{{ route('courses.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                    @csrf <!-- Token de proteção contra CSRF -->

                    <!-- Label/Input para inserir o nome do curso e nome do coordenador -->
                    <div class="flex w-full gap-4">
                        <!-- Campo Nome -->
                        <div class="flex flex-col flex-1">
                          <label for="nome" class="block font-medium mb-1">Nome</label>
                          <input
                            type="text"
                            id="nome"
                            name="nome"
                            class="w-full border border-gray-300 focus:border-stone-600 focus:ring-stone-600 
                                   focus:bg-white hover:bg-neutral-50 transition-colors duration-100 rounded p-2"
                            value="{{ old('nome') }}"
                          />
                        </div>
                      
                        <!-- Campo Coordenador -->
                        <div class="flex flex-col flex-1">
                          <label for="coordinator_id" class="block font-medium mb-1">
                            Coordenador (opcional)
                          </label>
                          <select
                            name="coordinator_id"
                            id="coordinator_id"
                            class="w-full border border-gray-300 rounded focus:bg-white hover:bg-neutral-50 transition-colors duration-100 cursor-pointer p-2"
                          >
                            <option value="">-- Nenhum --</option>
                            @foreach ($coordinators as $coordinator)
                              @if ($coordinator->coordinator_type === 'course')
                                @php $isDisabled = $coordinator->coordinatedCourse !== null; @endphp
                                <option
                                  value="{{ $coordinator->id }}"
                                  @selected(old('coordinator_id') == $coordinator->id)
                                  @disabled($isDisabled)
                                >
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
                        <textarea name="course_description" id="course_description" 
                                  class="w-full border-gray-300 focus:border-stone-600 focus:ring-stone-600
                                        focus:bg-white hover:bg-neutral-100 transition-colors duration-100 rounded shadow-sm">{{ old('course_description') }}</textarea>
                    </div>

                    <!-- Upload do ícone do curso -->
                    <div>
                        <label for="course_icon" class="block font-medium">Ícone do Curso (imagem)</label>
                        <input type="file" name="course_icon" id="course_icon" accept="image/*" 
                               class="w-full border-gray-300 rounded shadow-sm cursor-pointer">
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
