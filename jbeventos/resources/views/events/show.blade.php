<x-app-layout>
    {{-- Main Container --}}
    <div class="py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-7xl mx-auto space-y-8">
            {{-- Breadcrumbs and Title --}}
            <div class="flex items-center justify-between">
                <a href="{{ route('events.index') }}" class="text-gray-500 hover:text-gray-700 transition-colors">
                    ← Voltar para todos os eventos
                </a>
            </div>

            <h1 class="text-4xl font-bold text-gray-900 leading-tight">{{ $event->event_name }}</h1>

            {{-- Main Content Grid (IMAGEM e DETALHES RÁPIDOS FICAM LADO A LADO) --}}
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

                {{-- Image Carousel Section (2/3 da largura em telas grandes) --}}
                <div class="lg:col-span-2 relative">
                    <div class="bg-white rounded-2xl shadow-lg overflow-hidden border">
                        @if ($event->images->count())
                            <div class="relative aspect-video w-full" id="carouselContainer">
                                {{-- Carousel Images --}}
                                <div class="w-full h-full relative" id="carousel">
                                    @foreach ($event->images as $img)
                                        <img src="{{ asset('storage/' . $img->image_path) }}"
                                            class="absolute top-0 left-0 w-full h-full object-cover transition-opacity duration-500 ease-in-out carousel-img {{ $loop->first ? '' : 'hidden' }}">
                                    @endforeach
                                </div>

                                {{-- Carousel Controls & Indicator --}}
                                @if ($event->images->count() > 1)
                                    {{-- Botões de navegação --}}
                                    <button id="prevBtn"
                                        class="hidden absolute left-4 top-1/2 -translate-y-1/2 bg-white/50 text-gray-800 p-2 rounded-full hover:bg-white/80 transition-colors shadow-md">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none"
                                            viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
                                        </svg>
                                    </button>
                                    <button id="nextBtn"
                                        class="absolute right-4 top-1/2 -translate-y-1/2 bg-white/50 text-gray-800 p-2 rounded-full hover:bg-white/80 transition-colors shadow-md">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none"
                                            viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                                        </svg>
                                    </button>
                                @endif

                                {{-- Indicador (mostrar mesmo com 1 imagem) --}}
                                @if ($event->images->count() > 0)
                                    <div id="indicator"
                                        class="absolute bottom-4 right-4 bg-black/60 text-white text-sm px-3 py-1 rounded-full">
                                        1 / {{ $event->images->count() }}
                                    </div>
                                @endif

                                {{-- Zoom Button --}}
                                <button id="zoomBtn"
                                    class="absolute bottom-4 left-4 bg-white/50 text-gray-800 p-2 rounded-full hover:bg-white/80 transition-colors shadow-md">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M21 21l-4.35-4.35m1.61-5.52a7 7 0 11-14 0 7 7 0 0114 0z" />
                                    </svg>
                                </button>
                            </div>
                        @else
                            <div class="aspect-video w-full flex items-center justify-center bg-gray-100 rounded-2xl">
                                <span class="text-gray-400 text-lg">Sem imagem de galeria</span>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Event Info & Details (1/3 da largura em telas grandes, ao lado da imagem) --}}
                <div class="lg:col-span-1 space-y-6">
                    {{-- Quick Details Card --}}
                    <div class="bg-white rounded-2xl shadow-lg p-6 border space-y-4">
                        <div class="flex items-center gap-3">
                            <i class="fas fa-map-marker-alt text-blue-600"></i>
                            <p class="text-gray-800 text-base">
                                <span class="font-bold">Local:</span> {{ $event->event_location }}
                            </p>
                        </div>
                        <div class="flex items-center gap-3">
                            <i class="far fa-calendar-alt text-blue-600"></i>
                            <p class="text-gray-800 text-base">
                                <span class="font-bold">Data:</span>
                                {{ \Carbon\Carbon::parse($event->event_scheduled_at)->isoFormat('D MMMM YYYY') }}
                            </p>
                        </div>
                        <div class="flex items-center gap-3">
                            <i class="far fa-clock text-blue-600"></i>
                            <p class="text-gray-800 text-base">
                                <span class="font-bold">Horário:</span>
                                {{ \Carbon\Carbon::parse($event->event_scheduled_at)->isoFormat('HH:mm') }}
                            </p>
                        </div>
                        {{-- Coordinator's Actions --}}
                        @if (auth()->check() &&
                                auth()->user()->user_type === 'coordinator' &&
                                auth()->user()->coordinator->id === $event->coordinator_id)
                            <div class="pt-4 mt-4 border-t border-gray-200 flex flex-wrap gap-2">
                                <a href="{{ route('events.edit', $event->id) }}"
                                    class="flex items-center gap-2 px-4 py-2 bg-yellow-400 text-yellow-900 rounded-lg font-semibold hover:bg-yellow-500 transition-colors">
                                    <i class="fas fa-edit"></i> Editar Evento
                                </a>
                                {{-- Botão de Excluir que abre o modal --}}
                                <button onclick="openModal('deleteModal-{{ $event->id }}')"
                                    class="flex items-center gap-2 px-4 py-2 bg-red-500 text-white rounded-lg font-semibold hover:bg-red-600 transition-colors">
                                    <i class="fas fa-trash-alt"></i> Excluir Evento
                                </button>
                            </div>
                        @endif
                    </div>

                    {{-- Additional Details Card --}}
                    <div class="bg-white rounded-2xl shadow-lg p-6 border space-y-4 text-gray-700">
                        <div>
                            <p class="font-bold mb-1">Coordenador:</p>
                            <p>
                                @if (!$event->eventCoordinator || $event->eventCoordinator->coordinator_type !== $event->event_type)
                                    Nenhum coordenador definido
                                @else
                                    {{ $event->eventCoordinator?->userAccount?->name ?? 'N/A' }}
                                @endif
                            </p>
                        </div>
                        <div>
                            <p class="font-bold mb-1">Tipo de Evento:</p>
                            <p>{{ $event->event_type === 'general' ? 'Evento Geral' : ($event->event_type === 'course' ? 'Evento de Curso' : 'N/A') }}
                            </p>
                        </div>
                        @if ($event->event_type === 'course')
                            <div>
                                <p class="font-bold mb-1">Curso Relacionado:</p>
                                <p>{{ $event->eventCourse->course_name ?? 'Sem Curso' }}</p>
                            </div>
                        @endif
                        <div>
                            <p class="font-bold mb-1">Categorias:</p>
                            <div class="mt-2 flex flex-wrap gap-2">
                                @forelse($event->eventCategories as $category)
                                    <span
                                        class="inline-block bg-blue-100 text-blue-800 text-xs font-semibold px-2.5 py-0.5 rounded-full">
                                        {{ $category->category_name }}
                                    </span>
                                @empty
                                    <span class="text-gray-400 text-sm">Nenhuma categoria atribuída.</span>
                                @endforelse
                            </div>
                        </div>
                        {{-- Botão de navegação --}}
                        <div class="flex justify-start pt-4 border-t border-gray-200">
                            <a href="{{ route('events.index') }}"
                                class="inline-flex items-center rounded-md bg-gray-200 px-4 py-2 text-gray-700 hover:bg-gray-300">
                                ← Voltar
                            </a>
                        </div>
                    </div>
                </div>
            </div> {{-- FIM do grid grid-cols-1 lg:grid-cols-3 --}}

            {{-- **SEÇÕES EMPILHADAS DE LARGURA TOTAL** --}}

            {{-- Description Section (100% de largura) --}}
            <div class="bg-white rounded-2xl shadow-lg p-6 border">
                <h2 class="text-2xl font-bold text-gray-900 mb-4">Sobre o Evento</h2>

                <h3 class="text-xl font-semibold text-gray-700 border-b pb-2"></h3>

                <p class="text-gray-700 leading-relaxed break-words whitespace-pre-line">
                    {{ $event->event_info ?? '(Sem informações sobre o evento)' }}
                </p>
            </div>

            {{-- Reactions Section (100% de largura) --}}
            <div class="bg-white rounded-2xl shadow-lg p-6 border">
                <h2 class="text-2xl font-bold text-gray-900 mb-4 flex items-center gap-2">
                    <i class="far fa-heart text-red-500"></i> Reações
                </h2>
                <div id="reactions" class="flex flex-wrap gap-4">
                    @foreach (['like' => 'Curtir', 'save' => 'Salvar', 'notify' => 'Notificar'] as $type => $label)
                        @php
                            $isActive = in_array($type, $userReactions);
                            $count = $event->reactions->where('reaction_type', $type)->count();
                        @endphp

                        <form class="reaction-form" method="POST"
                            action="{{ route('events.react', ['event' => $event->id]) }}">
                            @csrf
                            <input type="hidden" name="reaction_type" value="{{ $type }}">

                            {{-- Lógica para o botão CURTIR (Com contador) --}}
                            @if ($type === 'like')
                                <button type="submit" data-type="{{ $type }}"
                                    data-count="{{ $count }}"
                                    class="reaction-btn flex items-center gap-2 px-4 py-2 rounded-full border transition-colors
                                    {{ $isActive ? 'bg-blue-600 text-white border-blue-600' : 'bg-white text-blue-600 border-blue-500 hover:bg-blue-50' }}">
                                    <i class="fas fa-thumbs-up"></i>
                                    {{ $label }}
                                    <span
                                        class="reaction-count text-xs font-semibold px-2 py-1 rounded-full {{ $isActive ? 'bg-white text-blue-600' : 'bg-blue-100' }}">
                                        {{ $count }}
                                    </span>
                                </button>

                                {{-- Lógica para SALVAR e NOTIFICAR (Ação binária sem contador) --}}
                            @else
                                <button type="submit" data-type="{{ $type }}"
                                    class="reaction-btn-toggle flex items-center gap-2 px-4 py-2 rounded-full border transition-colors
                                    {{ $isActive ? 'bg-blue-600 text-white border-blue-600' : 'bg-white text-blue-600 border-blue-500 hover:bg-blue-50' }}">
                                    <i class="fas fa-{{ $type == 'save' ? 'bookmark' : 'bell' }}"></i>
                                    <span class="toggle-text font-semibold">
                                        {{ $isActive ? ($type == 'save' ? 'Salvo' : 'Notificando') : $label }}
                                    </span>
                                </button>
                            @endif

                        </form>
                    @endforeach
                </div>
            </div>

            {{-- Comments Section (Livewire) (100% de largura) --}}
            <div class="bg-white rounded-2xl shadow-lg p-6 border">
                <h2 class="text-2xl font-bold text-gray-900 mb-4">
                    Comentários
                </h2>
                @livewire('event-comments', ['event' => $event])
            </div>

        </div> {{-- FIM do max-w-7xl mx-auto space-y-8 --}}
    </div> {{-- FIM do Main Container --}}

    {{-- MODAIS PRONTOS NO HTML --}}

    {{-- Modal de Zoom --}}
    <div id="zoomModal" class="fixed inset-0 bg-black/90 flex items-center justify-center z-[100] hidden">
        <div class="relative w-full h-full p-4 flex items-center justify-center">
            <img id="zoomImg" src="" class="max-w-full max-h-full object-contain rounded-lg">
            <button id="closeZoom"
                class="absolute top-5 right-5 text-white text-4xl font-light hover:text-gray-300 transition-colors">
                &times;
            </button>
        </div>
    </div>

    {{-- Modal de Exclusão --}}
    <div id="deleteModal-{{ $event->id }}"
        class="modal hidden fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
        <div class="bg-white p-6 rounded-md shadow-md w-full max-w-md">
            <h2 class="text-lg font-semibold mb-4 text-red-600">Confirmar Exclusão</h2>
            <p>Tem certeza que deseja excluir este evento? Esta ação não poderá ser desfeita.</p>
            <div class="mt-6 flex justify-end space-x-2">
                <button onclick="closeModal('deleteModal-{{ $event->id }}')"
                    class="px-4 py-2 bg-gray-300 rounded hover:bg-gray-400">Cancelar</button>
                <form action="{{ route('events.destroy', $event->id) }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="px-4 py-2 bg-red-500 text-white rounded hover:bg-red-600">Confirmar
                        Exclusão</button>
                </form>
            </div>
        </div>
    </div>

    {{-- Toast simples (mantido) --}}
    <div id="toast" class="fixed bottom-5 right-5 bg-blue-600 text-white px-4 py-2 rounded shadow hidden z-50">
        <span id="toast-message"></span>
    </div>

</x-app-layout>

<script>
    // Função showToast (necessária para o event-reactions.js)
    function showToast(message) {
        const toast = document.getElementById('toast');
        const toastMsg = document.getElementById('toast-message');

        toastMsg.textContent = message;
        toast.classList.remove('hidden');

        setTimeout(() => {
            toast.classList.add('hidden');
        }, 3000);
    }
</script>

{{-- Scripts compilados (Agora o app.js importa o event-reactions.js) --}}
@vite('resources/js/app.js')
