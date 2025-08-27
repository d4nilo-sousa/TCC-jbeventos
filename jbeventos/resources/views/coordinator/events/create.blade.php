<x-app-layout>
    {{-- Cabeçalho da Página --}}
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ isset($event) ? 'Editar Evento' : 'Criar Evento' }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-md rounded p-6">

                {{-- Exibição de erros de validação --}}
                @if ($errors->any())
                    <div class="mb-4 text-red-600">
                        <ul class="list-disc pl-5">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                {{-- Verifica se o coordenador está vinculado a algum curso --}}
                @if(auth()->user()->coordinator->coordinator_type === 'course' && !auth()->user()->coordinator->coordinatedCourse)
                    <div class="p-4 bg-red-100 text-red-700 rounded">
                        Você é coordenador de curso, mas ainda não está vinculado a nenhum curso.
                        Portanto, não é possível criar eventos até que um curso seja atribuído a você.
                    </div>
                @else
                    {{-- Formulário de criação ou edição de evento --}}
                    <form action="{{ isset($event) ? route('events.update', $event->id) : route('events.store') }}"
                          method="POST"
                          enctype="multipart/form-data"
                          class="space-y-4">

                        @csrf

                        {{-- Caso seja edição, define o método como PUT --}}
                        @if(isset($event))
                            @method('PUT')
                        @endif

                        {{-- Imagem de capa do evento --}}
                        <div>
                            <label for="event_image" class="block font-medium">Imagem de Capa</label>
                            <input type="file" name="event_image" id="event_image" accept="image/*" class="w-full border-gray-300 rounded shadow-sm">
                            
                            {{-- Exibe a imagem atual se houver --}}
                            @if(isset($event) && $event->event_image)
                                <div class="mt-2">
                                    <strong>Imagem atual:</strong>
                                    <img src="{{ asset('storage/' . $event->event_image) }}" alt="Imagem atual" class="mt-1 rounded max-h-72">
                                </div>
                            @endif
                        </div>

                        {{-- Nome do evento --}}
                        <div>
                            <label for="event_name" class="block font-medium">Nome do Evento</label>
                            <input type="text" name="event_name" id="event_name"
                                   value="{{ old('event_name', $event->event_name ?? '') }}"
                                   class="w-full border-gray-300 rounded shadow-sm" required>
                        </div>

                        {{-- Descrição do evento --}}
                        <div>
                            <label for="event_description" class="block font-medium">Descrição</label>
                            <textarea name="event_description" id="event_description" rows="4"
                                      class="w-full border-gray-300 rounded shadow-sm" required>{{ old('event_description', $event->event_description ?? '') }}</textarea>
                        </div>

                        {{-- Local do evento --}}
                        <div>
                            <label for="event_location" class="block font-medium">Local</label>
                            <input type="text" name="event_location" id="event_location"
                                   value="{{ old('event_location', $event->event_location ?? '') }}"
                                   class="w-full border-gray-300 rounded shadow-sm" required>
                        </div>

                        {{-- Categorias (checkboxes) --}}
                        <div>
                            <label class="block font-medium mb-1">Categorias do Evento</label>
                            <div class="flex flex-wrap gap-4">
                                @foreach($categories as $category)
                                    <label class="inline-flex items-center space-x-2">
                                        <input type="checkbox" name="categories[]" value="{{ $category->id }}" class="rounded"
                                            {{ in_array($category->id, old('categories', isset($event) ? $event->eventCategories->pluck('id')->toArray() : [])) ? 'checked' : '' }}>
                                        <span>{{ $category->category_name }}</span>
                                    </label>
                                @endforeach
                            </div>
                            @error('categories')
                                <div class="text-red-600 mt-2">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Datas: início e encerramento --}}
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="event_scheduled_at" class="block font-medium">Data e Hora do Evento</label>
                                <input type="datetime-local" name="event_scheduled_at" id="event_scheduled_at"
                                       min="{{ \Carbon\Carbon::now()->format('Y-m-d\TH:i') }}"
                                       class="w-full border-gray-300 rounded shadow-sm"
                                       value="{{ old('event_scheduled_at', isset($event) ? \Carbon\Carbon::parse($event->event_scheduled_at)->format('Y-m-d\TH:i') : '') }}"
                                       required>
                            </div>

                            <div>
                                <label for="event_expired_at" class="block font-medium">Exclusão Automática (opcional)</label>
                                <input type="datetime-local" name="event_expired_at" id="event_expired_at"
                                       min="{{ \Carbon\Carbon::now()->format('Y-m-d\TH:i') }}"
                                       class="w-full border-gray-300 rounded shadow-sm"
                                       value="{{ old('event_expired_at', isset($event) && $event->event_expired_at ? \Carbon\Carbon::parse($event->event_expired_at)->format('Y-m-d\TH:i') : '') }}">
                            </div>
                        </div>

                        {{-- Coordenador responsável (somente leitura) --}}
                        <div class="mb-4">
                            <x-input-label for="event_coordinator" value="Coordenador Responsável" />
                            <x-text-input id="coordinator_name" type="text" class="block mt-1 w-full bg-gray-100"
                                          value="{{ auth()->user()->name }}" readonly disabled />
                            <input type="hidden" name="coordinator_name" value="{{ auth()->user()->coordinator->id }}">
                        </div>

                        {{-- Tipo do evento: curso ou geral --}}
                        @php
                            $coordinatorType = auth()->user()->coordinator->coordinator_type;
                            $eventoTipoLabel = $coordinatorType === 'course' ? 'Evento de Curso' : 'Evento Geral';
                        @endphp

                        <div class="mb-4">
                            <x-input-label for="event_type" value="Tipo do Evento" />
                            <x-text-input id="coordinator_type" type="text" class="block mt-1 w-full bg-gray-100"
                                          value="{{ $eventoTipoLabel }}" readonly disabled />
                            <input type="hidden" name="coordinator_type" value="{{ $coordinatorType }}">
                        </div>

                        {{-- Curso vinculado (se aplicável) --}}
                        @if(auth()->user()->coordinator->coordinator_type === 'course')
                            <div class="mb-4">
                                <x-input-label for="event_course" value="Curso" />
                                <x-text-input id="course_name" type="text" class="block mt-1 w-full bg-gray-100"
                                              value="{{ auth()->user()->coordinator->coordinatedCourse->course_name ?? 'Sem curso' }}" readonly disabled />
                                <input type="hidden" name="coordinator_course" value="{{ auth()->user()->coordinator->coordinatedCourse->id ?? '' }}">
                            </div>
                        @endif

                        {{-- Botões de ação --}}
                        <div class="flex justify-between mt-4">
                            <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">
                                {{ isset($event) ? 'Atualizar Evento' : 'Criar Evento' }}
                            </button>
                            <a href="{{ route('events.index') }}" class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600">Cancelar</a>
                        </div>
                    </form>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>

@vite('resources/js/app.js')
