<x-app-layout>
    {{-- Slot do cabeçalho da página --}}
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Editar Evento
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

                {{-- Formulário para editar evento --}}
                <form action="{{ route('events.update', $event->id) }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                    @csrf
                    @method('PUT')

                    {{-- Imagem de capa --}}
                    <div>
                        <label for="event_image" class="block font-medium">Imagem de Capa</label>
                        <input type="file" name="event_image" id="event_image" accept="image/*" class="w-full border-gray-300 rounded shadow-sm">

                        {{-- Exibe imagem atual, se houver --}}
                        @if($event->event_image)
                            <div class="mt-2">
                                <strong>Imagem atual:</strong>
                                <img src="{{ asset('storage/' . $event->event_image) }}" alt="Imagem atual" class="mt-1 rounded max-h-72">
                            </div>
                        @endif
                    </div>

                    {{-- Nome do evento --}}
                    <div>
                        <label for="event_name" class="block font-medium">Nome do Evento</label>
                        <input type="text" name="event_name" id="event_name" value="{{ old('event_name', $event->event_name) }}" class="w-full border-gray-300 rounded shadow-sm" required>
                    </div>

                    {{-- Descrição do evento --}}
                    <div>
                        <label for="event_description" class="block font-medium">Descrição</label>
                        <textarea name="event_description" id="event_description" rows="4" class="w-full border-gray-300 rounded shadow-sm" required>{{ old('event_description', $event->event_description) }}</textarea>
                    </div>

                    {{-- Local do evento --}}
                    <div>
                        <label for="event_location" class="block font-medium">Local</label>
                        <input type="text" name="event_location" id="event_location" value="{{ old('event_location', $event->event_location) }}" class="w-full border-gray-300 rounded shadow-sm" required>
                    </div>

                    {{-- Categorias do evento --}}
                    <div>
                        <label class="block font-medium mb-1">Categorias do Evento</label>
                        <div class="flex flex-wrap gap-4">
                            @foreach($categories as $category)
                                <label class="inline-flex items-center space-x-2">
                                    <input type="checkbox" name="categories[]" value="{{ $category->id }}" class="rounded"
                                        {{ in_array($category->id, old('categories', $event->eventCategories->pluck('id')->toArray())) ? 'checked' : '' }}>
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

                    {{-- Coordenador responsável (leitura e hidden) --}}
                    <div class="mb-4">
                        <x-input-label for="coordinator_name" value="Coordenador Responsável" />
                        <x-text-input id="coordinator_name" type="text" class="block mt-1 w-full bg-gray-100"
                                      value="{{ auth()->user()->name }}" readonly disabled />
                        <input type="hidden" name="coordinator_id" value="{{ auth()->user()->coordinator->id }}">
                    </div>

                    {{-- Tipo do evento (leitura e hidden) --}}
                    <div class="mb-4">
                        <x-input-label for="event_type" value="Tipo do Evento" />
                        <x-text-input id="coordinator_type" type="text" class="block mt-1 w-full bg-gray-100"
                                      value="{{ auth()->user()->coordinator->coordinator_type === 'course' ? 'Evento de Curso' : 'Evento Geral' }}" readonly disabled />
                        <input type="hidden" name="coordinator_type" value="{{ auth()->user()->coordinator->coordinator_type }}">
                    </div>

                    {{-- Curso do evento (apenas se for do tipo curso) --}}
                    @if(auth()->user()->coordinator->coordinator_type === 'course')
                        <div class="mb-4">
                            <x-input-label for="event_course" value="Curso" />
                            <x-text-input id="course_name" type="text" class="block mt-1 w-full bg-gray-100"
                                          value="{{ auth()->user()->coordinator->coordinatedCourse->course_name ?? 'Sem curso' }}" readonly disabled />
                            <input type="hidden" name="course_id" value="{{ auth()->user()->coordinator->coordinatedCourse->id ?? '' }}">
                        </div>
                    @endif

                    {{-- Botões de ação --}}
                    <div class="flex justify-between mt-4">
                        <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">
                            Atualizar Evento
                        </button>
                        <a href="{{ route('events.index') }}" class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600">
                            Cancelar
                        </a>
                    </div>
                </form>

            </div>
        </div>
    </div>
</x-app-layout>

{{-- Script para garantir que a data de encerramento seja posterior à de início --}}
@vite('resources/js/datetime-validation.js')

