<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ isset($event) ? 'Editar Evento' : 'Criar Evento' }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-md rounded p-6">
                @if ($errors->any())
                    <div class="mb-4 text-red-600">
                        <ul class="list-disc pl-5">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ isset($event) ? route('events.update', $event->id) : route('events.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                    @csrf
                    @if(isset($event))
                        @method('PUT')
                    @endif

                    <div>
                        <label for="event_image" class="block font-medium">Imagem de Capa</label>
                        <input type="file" name="event_image" id="event_image" accept="image/*" class="w-full border-gray-300 rounded shadow-sm">

                        @if(isset($event) && $event->event_image)
                            <div class="mt-2">
                                <strong>Imagem atual:</strong>
                                <img src="{{ asset('storage/' . $event->event_image) }}" alt="Imagem atual" class="mt-1 rounded max-h-72">
                            </div>
                        @endif
                    </div>

                    <div>
                        <label for="event_name" class="block font-medium">Nome do Evento</label>
                        <input type="text" name="event_name" id="event_name" value="{{ old('event_name', $event->event_name ?? '') }}" class="w-full border-gray-300 rounded shadow-sm" required>
                    </div>

                    <div>
                        <label for="event_description" class="block font-medium">Descrição</label>
                        <textarea name="event_description" id="event_description" rows="4" class="w-full border-gray-300 rounded shadow-sm" required>{{ old('event_description', $event->event_description ?? '') }}</textarea>
                    </div>

                    <div>
                        <label for="event_location" class="block font-medium">Local</label>
                        <input type="text" name="event_location" id="event_location" value="{{ old('event_location', $event->event_location ?? '') }}" class="w-full border-gray-300 rounded shadow-sm" required>
                    </div>

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

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="event_scheduled_at" class="block font-medium">Data e Hora do Evento</label>
                            <input type="datetime-local" name="event_scheduled_at" id="event_scheduled_at" class="w-full border-gray-300 rounded shadow-sm" value="{{ old('event_scheduled_at', isset($event) ? \Carbon\Carbon::parse($event->event_scheduled_at)->format('Y-m-d\TH:i') : '') }}" required>
                        </div>
                        <div>
                            <label for="event_expired_at" class="block font-medium">Data/Hora de Encerramento (opcional)</label>
                            <input type="datetime-local" name="event_expired_at" id="event_expired_at" class="w-full border-gray-300 rounded shadow-sm" value="{{ old('event_expired_at', isset($event) && $event->event_expired_at ? \Carbon\Carbon::parse($event->event_expired_at)->format('Y-m-d\TH:i') : '') }}">
                        </div>
                    </div>

                    <div>
                        <label for="coordinator_id" class="block font-medium">Coordenador Responsável</label>
                        <select name="coordinator_id" id="coordinator_id" class="w-full border-gray-300 rounded shadow-sm" required>
                            <option value="">Selecione</option>
                            @foreach($coordinators as $coordinator)
                                <option value="{{ $coordinator->id }}" {{ old('coordinator_id', $event->coordinator_id ?? '') == $coordinator->id ? 'selected' : '' }}>
                                    {{ $coordinator->userAccount->name ?? 'Coordenador Sem Nome' }} — {{ $coordinator->coordinatedCourse->course_name ?? 'Evento Geral' }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="flex justify-between mt-4">
                        <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">
                            {{ isset($event) ? 'Atualizar Evento' : 'Criar Evento' }}
                        </button>
                        <a href="{{ route('events.index') }}" class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600">Cancelar</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
