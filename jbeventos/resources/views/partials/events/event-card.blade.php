<div id="event-card-{{ $event->id }}"
    class="feed-card bg-white rounded-xl overflow-hidden transform transition duration-300 hover:shadow-2xl border border-gray-200 w-[408px]">

    <a href="{{ route('events.show', $event->id) }}" class="block">
        <!-- Imagem -->
        <div class="relative w-full h-56 bg-gray-200">
            @if ($event->event_image)
                <img src="{{ asset('storage/' . $event->event_image) }}" alt="{{ $event->event_name }}"
                    class="object-cover w-full h-full">
            @else
                <div class="flex flex-col items-center justify-center w-full h-full text-red-500">
                    <i class="ph-bold ph-calendar-blank text-6xl"></i>
                    <p class="mt-2 text-sm">Sem Imagem de Capa</p>
                </div>
            @endif

            <span
                class="absolute top-3 right-3 bg-red-600 text-white text-xs font-bold px-3 py-1.5 rounded-full shadow-lg">
                {{ $event->event_type === 'course' ? 'CURSO' : ($event->event_type === 'general' ? 'GERAL' : '') }}
            </span>
        </div>

        <!-- Conteúdo do Card -->
        <div class="p-5 flex flex-col justify-between h-auto">
            <!-- Nome do evento -->
            <h3 class="text-xl font-bold text-gray-900 hover:text-red-600 transition mb-2">
                {{ $event->event_name }}
            </h3>

            <!-- Categorias -->
            <div class="flex flex-wrap gap-2 mb-3 text-xs mt-1 mb-4">
                @forelse ($event->eventCategories as $category)
                    <span class="bg-gray-100 text-gray-700 px-2 py-1 rounded-full border border-gray-200">
                        {{ $category->category_name }}
                    </span>
                @empty
                    <span class="bg-gray-100 text-gray-700 px-2 py-1 rounded-full border border-gray-200">
                        Sem Categoria
                    </span>
                @endforelse
            </div>

            <!-- Local e Data -->
            <div class="border-t border-gray-100 pt-4 text-sm text-gray-800 space-y-2">
                <div class="flex items-center gap-2">
                    <i class="ph-fill ph-map-pin text-red-600 text-lg"></i>
                    <span>{{ $event->event_location }}</span>
                </div>
                <div class="flex items-center gap-2">
                    <i class="ph-fill ph-calendar-check text-red-600 text-lg"></i>
                    <span>{{ \Carbon\Carbon::parse($event->event_scheduled_at)->isoFormat('D [de] MMMM [de] YYYY, [às] HH:mm') }}</span>
                </div>
            </div>
        </div>
    </a>
</div>
