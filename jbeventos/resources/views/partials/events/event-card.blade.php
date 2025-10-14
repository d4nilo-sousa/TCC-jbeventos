<div id="event-card-{{ $event->id }}">

    <a href="{{ route('events.show', $event->id) }}"
        class="block shadow-lg transform transition-transform duration-300 hover:scale-105 rounded-xl overflow-hidden cursor-pointer">
        <div class="relative bg-white border border-gray-200 rounded-xl shadow-md p-4 flex flex-col h-full">
            <div class="relative w-full h-48 rounded-md overflow-hidden mb-4">
                <div class="relative w-full h-48 rounded-md overflow-hidden mb-4 bg-gray-100">
                    @if ($event->event_image)
                        <img src="{{ asset('storage/' . $event->event_image) }}" alt="{{ $event->event_name }}"
                            class="object-cover w-full h-full">
                    @else
                        <div
                            class="flex flex-col items-center justify-center w-full h-full text-indigo-500 dark:text-indigo-400">
                            <i class="ph-bold ph-calendar-blank text-6xl"></i>
                            <p class="mt-2 text-sm">Evento Sem Imagem</p>
                        </div>
                    @endif
                </div>
                <span
                    class="absolute top-2 right-2 bg-blue-600 text-white text-xs font-semibold px-2 py-1 rounded-full shadow">
                    {{ $event->event_type === 'course' ? 'Curso' : ($event->event_type === 'general' ? 'Geral' : '') }}
                </span>
            </div>

            <div class="flex-1 flex flex-col justify-between">
                <div>
                    <h3 class="event-name-searchable text-lg font-bold text-gray-900 mb-1 leading-tight line-clamp-2">
                        {{ $event->event_name }}
                    </h3>
                    <p class="text-sm text-gray-600 mb-2 break-words whitespace-normal">
                        {{ $event->event_description }}
                    </p>

                    <div class="flex flex-wrap gap-2 text-xs mb-2">
                        @forelse ($event->eventCategories as $category)
                            <span class="bg-gray-200 text-gray-700 px-2 py-1 rounded-full">
                                {{ $category->category_name }}
                            </span>
                        @empty
                            <span class="bg-gray-200 text-gray-700 px-2 py-1 rounded-full">
                                Sem Categoria
                            </span>
                        @endforelse
                    </div>
                </div>

                <div class="mt-auto">
                    <p class="text-sm text-gray-800 font-medium mt-2 flex items-center gap-1">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-500" viewBox="0 0 20 20"
                            fill="currentColor">
                            <path fill-rule="evenodd"
                                d="M5.05 4.05a7 7 0 119.9 9.9L10 19.9l-4.95-5.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z"
                                clip-rule="evenodd" />
                        </svg>
                        {{ $event->event_location }}
                    </p>
                    <p class="text-sm text-gray-800 font-medium mt-1 flex items-center gap-1">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-500" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M8 7V3m8 4V3m-9 8h.01M3 15h18M3 21h18a2 2 0 002-2V7a2 2 0 00-2-2H3a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        {{ \Carbon\Carbon::parse($event->event_scheduled_at)->isoFormat('D MMMM YYYY, HH:mm') }}
                    </p>
                </div>
            </div>
        </div>
    </a>
</div>
