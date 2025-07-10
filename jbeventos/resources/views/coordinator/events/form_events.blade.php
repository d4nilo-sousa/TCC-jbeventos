<form action="{{ isset($event) ? route('events.update', $event->id) : route('events.store') }}" method="POST" enctype="multipart/form-data">
    @csrf
    @if(isset($event))
        @method('PUT')
    @endif

    {{-- Imagem de Capa --}}
    <div class="mb-4">
        <x-input-label for="event_image" value="Imagem de Capa" />
        <x-text-input id="event_image" name="event_image" type="file" class="block mt-1 w-full" accept="image/*" />
        @if(isset($event) && $event->event_image)
            <div class="mt-3">
                <p class="text-sm text-gray-600">Imagem atual:</p>
                <img src="{{ asset('storage/' . $event->event_image) }}" alt="Imagem atual" class="rounded mt-2 max-h-64">
            </div>
        @endif
    </div>

    {{-- Nome --}}
    <div class="mb-4">
        <x-input-label for="event_name" value="Nome do Evento" />
        <x-text-input id="event_name" class="block mt-1 w-full" type="text" name="event_name"
                      :value="old('event_name', $event->event_name ?? '')" required autofocus />
    </div>

    {{-- Descrição --}}
    <div class="mb-4">
        <x-input-label for="event_description" value="Descrição" />
        <textarea name="event_description" id="event_description" rows="4"
                  class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm w-full"
                  required>{{ old('event_description', $event->event_description ?? '') }}</textarea>
    </div>

    {{-- Local --}}
    <div class="mb-4">
        <x-input-label for="event_location" value="Local" />
        <x-text-input id="event_location" class="block mt-1 w-full" type="text" name="event_location"
                      :value="old('event_location', $event->event_location ?? '')" required />
    </div>

    {{-- Categorias --}}
    <div class="mb-4">
        <x-input-label value="Categorias do Evento" />
        <div class="flex flex-wrap gap-3">
            @foreach($categories as $category)
                <label class="flex items-center space-x-2">
                    <input type="checkbox" name="categories[]" value="{{ $category->id }}"
                           {{ in_array($category->id, old('categories', isset($event) ? $event->eventCategories->pluck('id')->toArray() : [])) ? 'checked' : '' }}
                           class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                    <span class="text-sm text-gray-700">{{ $category->category_name }}</span>
                </label>
            @endforeach
        </div>
        @error('categories')
            <div class="text-red-500 text-sm mt-2">{{ $message }}</div>
        @enderror
    </div>

    {{-- Datas --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
            <x-input-label for="event_scheduled_at" value="Data e Hora do Evento" />
            <x-text-input id="event_scheduled_at" type="datetime-local" name="event_scheduled_at"
                          class="block mt-1 w-full"
                          :value="old('event_scheduled_at', isset($event) ? \Carbon\Carbon::parse($event->event_scheduled_at)->format('Y-m-d\TH:i') : '')" required />
        </div>

        <div>
            <x-input-label for="event_expired_at" value="Data/Hora de Encerramento (opcional)" />
            <x-text-input id="event_expired_at" type="datetime-local" name="event_expired_at"
                          class="block mt-1 w-full"
                          :value="old('event_expired_at', isset($event) && $event->event_expired_at ? \Carbon\Carbon::parse($event->event_expired_at)->format('Y-m-d\TH:i') : '')" />
        </div>
    </div>

    {{-- Coordenador atual (somente leitura) --}}
    <div class="mt-4">
        <x-input-label value="Coordenador Responsável" />
        <p class="text-gray-700 text-sm mt-1">
            {{ auth()->user()->name }} — 
            {{ auth()->user()->coordinator?->coordinatedCourse?->course_name ?? 'Evento Geral' }}
        </p>
    </div>


    {{-- Botões --}}
    <div class="mt-6 flex justify-between">
        <x-primary-button>
            {{ isset($event) ? 'Atualizar Evento' : 'Criar Evento' }}
        </x-primary-button>
        <a href="{{ route('events.index') }}" class="text-gray-600 hover:underline">Cancelar</a>
    </div>
</form>
