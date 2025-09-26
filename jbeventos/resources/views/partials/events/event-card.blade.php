<a href="{{ route('events.show', $event->id) }}"
   class="block shadow-lg transform transition-transform duration-300 hover:scale-105 rounded-xl overflow-hidden cursor-pointer">
    {{-- Event Card --}}
    <div class="relative bg-white border border-gray-200 rounded-xl shadow-md p-4 flex flex-col h-full">
        {{-- Image --}}
        <div class="relative w-full h-48 rounded-md overflow-hidden mb-4">
            <img src="{{ $event->event_image ? asset('storage/' . $event->event_image) : asset('imgs/placeholder.png') }}"
                 alt="{{ $event->event_name }}" class="object-cover w-full h-full">

            {{-- Tag para tipo de evento --}}
            <span class="absolute top-2 right-2 bg-blue-600 text-white text-xs font-semibold px-2 py-1 rounded-full shadow">
                {{ $event->event_type === 'course' ? 'Curso' : 'Evento' }}
            </span>

            {{-- Tag para visibilidade (somente se o usuário é o coordenador e o evento é dele) --}}
            @php $loggedCoordinator = auth()->user()->coordinator ?? null; @endphp
            @if ($loggedCoordinator && $loggedCoordinator->id === $event->eventCoordinator->user_account_id)
                @if ($event->is_visible)
                    <span class="absolute top-2 left-2 text-xs font-semibold px-2 py-1 rounded-full shadow bg-green-500 text-white">
                        Visível
                    </span>
                @else
                    <span class="absolute top-2 left-2 text-xs font-semibold px-2 py-1 rounded-full shadow bg-red-500 text-white">
                        Oculto
                    </span>
                @endif
            @endif
        </div>

        {{-- Content --}}
        <div class="flex-1 flex flex-col justify-between">
            <div>
                <h3 class="text-lg font-bold text-gray-900 mb-1 leading-tight line-clamp-2">
                    {{ $event->event_name }}
                </h3>
                <p class="text-sm text-gray-600 mb-2 line-clamp-2">
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
                <p class="text-sm text-gray-800 font-medium mt-2">
                    <i class="fas fa-map-marker-alt text-gray-500 mr-1"></i>
                    {{ $event->event_location }}
                </p>
                <p class="text-sm text-gray-800 font-medium mt-1">
                    <i class="far fa-calendar-alt text-gray-500 mr-1"></i>
                    {{ \Carbon\Carbon::parse($event->event_scheduled_at)->isoFormat('D MMMM YYYY, HH:mm') }}
                </p>
            </div>
        </div>
    </div>
</a>