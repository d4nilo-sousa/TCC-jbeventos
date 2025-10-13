<x-app-layout>
    {{-- Main Container (AGORA COM LIMITAÇÃO AMPLA: max-w-screen-2xl ou similar) --}}
    <div class="py-6 sm:py-10 px-4 sm:px-6 lg:px-8">
        {{-- Usando mx-auto e uma largura máxima muito ampla para um layout mais confortável --}}
        <div class="max-w-screen-2xl mx-auto space-y-6">

            {{-- 1. TÍTULO E BREADCRUMBS (Largura Total) --}}
            <div class="mb-6">
                <a href="{{ route('events.index') }}" class="text-red-600 hover:text-red-800 transition-colors flex items-center gap-1 font-medium text-base mb-2">
                    <i class="ph-fill ph-arrow-left text-lg"></i> Voltar para o Feed de Eventos
                </a>
                <h1 class="text-4xl sm:text-4xl font-extrabold text-gray-900 leading-tight">{{ $event->event_name }}</h1>
            </div>

            {{-- 2. MAIN CONTENT GRID (12 Colunas: 6/12 Conteúdo | 6/12 Comentários) --}}
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-12">

                {{-- COLUNA DA ESQUERDA (Conteúdo Principal - 6/12 da Largura) --}}
                <div class="lg:col-span-6 space-y-6">

                    {{-- CARTÃO PRINCIPAL (Carrossel, Reações, Descrição e Detalhes) --}}
                    <div class="bg-white rounded-3xl shadow-xl overflow-hidden border border-gray-100">

                        {{-- IMAGEM / CARROSSEL --}}
                        <div class="relative aspect-video w-full" id="carouselContainer">
                            <div class="w-full h-full relative" id="carousel">
                                @if ($event->images->count())
                                    @foreach ($event->images as $img)
                                        <img src="{{ asset('storage/' . $img->image_path) }}"
                                            class="absolute top-0 left-0 w-full h-full object-cover transition-opacity duration-500 ease-in-out carousel-img {{ $loop->first }}">
                                    @endforeach

                                    {{-- Controles do Carrossel (Removendo z-index desnecessário) --}}
                                    <div class="absolute inset-0 flex items-center justify-between p-4 pointer-events-none">
                                        @if ($event->images->count() > 1)
                                            <button id="prevBtn"
                                                class="hidden absolute left-4 top-1/2 -translate-y-1/2 bg-black/40 text-white p-3 rounded-full hover:bg-black/60 transition-colors shadow-lg z-0 pointer-events-auto">
                                                <i class="ph ph-caret-left text-xl"></i>
                                            </button>
                                            <button id="nextBtn"
                                                class="absolute right-4 top-1/2 -translate-y-1/2 bg-black/40 text-white p-3 rounded-full hover:bg-black/60 transition-colors shadow-lg z-0 pointer-events-auto">
                                                <i class="ph ph-caret-right text-xl"></i>
                                            </button>
                                        @endif
                                        <div class="absolute bottom-4 right-4 flex gap-2 z-0 pointer-events-auto">
                                            <div id="indicator" class="bg-black/60 text-white text-sm px-3 py-1 rounded-full font-medium">
                                                1 / {{ $event->images->count() }}
                                            </div>
                                            <button id="zoomBtn" class="bg-black/60 text-white p-2 rounded-full hover:bg-black/80 transition-colors shadow-md" title="Visualizar em Tela Cheia">
                                                <i class="ph-fill ph-magnifying-glass text-lg"></i>
                                            </button>
                                        </div>
                                    </div>

                                @else
                                    <div class="aspect-video w-full flex items-center justify-center bg-gray-100">
                                        <span class="text-gray-400 text-lg flex items-center gap-2">
                                            <i class="ph-fill ph-image text-2xl"></i> Sem imagem de capa
                                        </span>
                                    </div>
                                @endif
                            </div>
                        </div>

                        {{-- BARRA DE REAÇÕES (Elementos usados pelo event-reactions.js) --}}
                        <div class="p-4 sm:p-6 flex items-center justify-between border-t border-gray-100">
                            <div id="reactions" class="flex flex-wrap gap-4">
                                @foreach (['like' => 'Curtir', 'save' => 'Salvar', 'notify' => 'Notificar'] as $type => $label)
                                    @php
                                        $isActive = in_array($type, $userReactions);
                                        $count = $event->reactions->where('reaction_type', $type)->count();

                                        // Definições de Estilos para o estado INICIAL (o JS irá manipulá-las)
                                        $icon = match ($type) {
                                            'like' => $isActive ? 'ph-fill ph-heart' : 'ph ph-heart',
                                            'save' => $isActive ? 'ph-fill ph-bookmark-simple' : 'ph ph-bookmark-simple',
                                            'notify' => $isActive ? 'ph-fill ph-bell-ringing' : 'ph ph-bell-ringing',
                                            default => 'ph ph-question'
                                        };

                                        // Ajuste das classes de cor conforme a lógica que está no JS (azul)
                                        $activeColor = 'bg-blue-600 text-white border-blue-600';
                                        $inactiveColor = 'bg-white text-red-600 border-blue-500 hover:bg-blue-50';

                                        // Exceções para cores de Notificar e Salvar se necessário (mantendo o que foi definido antes)
                                        if ($type === 'notify') {
                                            $activeColor = 'bg-yellow-500 text-gray-900 border-yellow-500 hover:bg-yellow-600';
                                            $inactiveColor = 'bg-white text-yellow-600 border-yellow-300 hover:bg-yellow-50';
                                        } elseif ($type === 'save') {
                                            $activeColor = 'bg-green-500 text-white border-green-500 hover:bg-green-600';
                                            $inactiveColor = 'bg-white text-green-600 border-green-300 hover:bg-green-50';
                                        } elseif ($type === 'like') {
                                            $activeColor = 'bg-red-500 text-white border-red-500 hover:bg-red-600';
                                            $inactiveColor = 'bg-white text-gray-700 border-gray-300 hover:bg-gray-50';
                                        }

                                        $buttonClass = $isActive ? $activeColor : $inactiveColor;
                                    @endphp

                                    <form class="reaction-form" method="POST" action="{{ route('events.react', ['event' => $event->id]) }}">
                                        @csrf
                                        <input type="hidden" name="reaction_type" value="{{ $type }}">

                                        <button type="submit" data-type="{{ $type }}" data-count="{{ $count }}"
                                            class="reaction-btn flex items-center gap-2 px-4 py-2 rounded-full border transition-all duration-200 text-sm font-semibold shadow-sm {{ $buttonClass }}">
                                            <i class="{{ $icon }} text-lg"></i>
                                            @if ($type === 'like')
                                                <span class="font-semibold">{{ $label }}</span>
                                                <span class="reaction-count text-xs px-2 py-0.5 rounded-full {{ $isActive ? 'bg-white text-red-500' : 'bg-gray-200 text-gray-700' }}">
                                                    {{ $count }}
                                                </span>
                                            @else
                                                <span class="toggle-text">
                                                    {{ $isActive ? ($type == 'save' ? 'Salvo' : 'Notificando') : $label }}
                                                </span>
                                            @endif
                                        </button>
                                    </form>
                                @endforeach
                            </div>
                        </div>

                        {{-- DESCRIÇÃO + INFORMAÇÕES ESSENCIAIS (UNIFICADOS) --}}
                        <div class="p-6 sm:p-8 border-t border-gray-100">
                            <h2 class="text-2xl font-bold text-gray-900 mb-4 flex items-center gap-2">
                                <i class="ph-fill ph-file-text text-red-600"></i> Sobre o Evento
                            </h2>
                            <div class="text-gray-700 leading-relaxed text-base whitespace-pre-wrap mb-8 pb-4 border-b border-gray-100">
                                {{ $event->event_description }}
                            </div>

                            {{-- INFORMAÇÕES ESSENCIAIS EM FORMATO GRID --}}
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">

                                {{-- Data e Hora --}}
                                <div class="flex items-start gap-3">
                                    <i class="ph-fill ph-calendar-blank text-2xl text-red-600 mt-0.5"></i>
                                    <div>
                                        <p class="text-sm font-bold text-gray-500">Data e Hora</p>
                                        <p class="text-gray-800 font-semibold">
                                            {{ \Carbon\Carbon::parse($event->event_scheduled_at)->isoFormat('D [de] MMMM [de] YYYY') }}
                                        </p>
                                        <p class="text-gray-600 text-sm">
                                            às {{ \Carbon\Carbon::parse($event->event_scheduled_at)->isoFormat('HH:mm') }}
                                        </p>
                                    </div>
                                </div>

                                {{-- Local --}}
                                <div class="flex items-start gap-3">
                                    <i class="ph-fill ph-map-pin text-2xl text-red-600 mt-0.5"></i>
                                    <div>
                                        <p class="text-sm font-bold text-gray-500">Local</p>
                                        <p class="text-gray-800 font-semibold">{{ $event->event_location }}</p>
                                    </div>
                                </div>

                                {{-- Tipo/Curso --}}
                                <div class="flex items-start gap-3">
                                    <i class="ph-fill ph-tag text-2xl text-red-600 mt-0.5"></i>
                                    <div>
                                        <p class="text-sm font-bold text-gray-500">Tipo de Evento</p>
                                        <span class="font-semibold text-gray-800">
                                            {{ $event->event_type === 'general' ? 'Geral' : ($event->event_type === 'course' ? 'De Curso' : 'N/A') }}
                                        </span>
                                        @if ($event->event_type === 'course')
                                            <p class="text-xs text-gray-500 mt-1">
                                                <i class="ph-fill ph-graduation-cap mr-1"></i> Curso: {{ $event->eventCourse->course_name ?? 'Sem Curso' }}
                                            </p>
                                        @endif
                                    </div>
                                </div>

                                {{-- Coordenador --}}
                                <div class="flex items-start gap-3">
                                    <i class="ph-fill ph-user-circle text-2xl text-red-600 mt-0.5"></i>
                                    <div>
                                        <p class="text-sm font-bold text-gray-500">Promovido por</p>
                                        <p class="text-gray-800 font-semibold">
                                            @if (!$event->eventCoordinator || $event->eventCoordinator->coordinator_type !== $event->event_type)
                                                <span class="text-gray-500">Coordenador não informado</span>
                                            @else
                                                {{ $event->eventCoordinator?->userAccount?->name ?? 'N/A' }}
                                            @endif
                                        </p>
                                    </div>
                                </div>

                                {{-- Categorias (Largura Total) --}}
                                <div class="sm:col-span-2 border-t border-gray-200 pt-4 mt-4">
                                    <p class="text-sm font-bold text-gray-500 mb-2 flex items-center gap-1">
                                        <i class="ph-fill ph-hash text-base text-red-600"></i> Categorias:
                                    </p>
                                    <div class="flex flex-wrap gap-2">
                                        @forelse($event->eventCategories as $category)
                                            <span class="inline-block bg-blue-100 text-red-800 text-xs font-medium px-3 py-1 rounded-full shadow-sm">
                                                {{ $category->category_name }}
                                            </span>
                                        @empty
                                            <span class="text-gray-400 text-sm">Nenhuma categoria atribuída.</span>
                                        @endforelse
                                    </div>
                                </div>

                            </div> {{-- FIM do grid de detalhes --}}

                            {{-- Ações do Coordenador --}}
                            @if (auth()->check() &&
                                auth()->user()->user_type === 'coordinator' &&
                                auth()->user()->coordinator->id === $event->coordinator_id)
                                <div class="pt-4 mt-4 border-t border-gray-200 flex flex-wrap gap-2">
                                    <a href="{{ route('events.edit', $event->id) }}"
                                        class="flex items-center gap-2 px-4 py-2 bg-yellow-400 text-yellow-900 rounded-lg font-semibold hover:bg-yellow-500 transition-colors">
                                        <i class="ph-fill ph-pencil-simple-line"></i> Editar Evento
                                    </a>
                                    {{-- Botão de Excluir que abre o modal --}}
                                    <button onclick="openModal('deleteModal-{{ $event->id }}')"
                                        class="flex items-center gap-2 px-4 py-2 bg-red-500 text-white rounded-lg font-semibold hover:bg-red-600 transition-colors">
                                        <i class="ph-fill ph-trash"></i> Excluir Evento
                                    </button>
                                </div>
                            @endif
                        </div>
                    </div> {{-- FIM do CARTÃO PRINCIPAL --}}
                </div> {{-- FIM da COLUNA PRINCIPAL (6/12) --}}

                {{-- COLUNA DA DIREITA (COMENTÁRIOS - 6/12 da Largura) --}}
                <div class="lg:col-span-6">
                    {{-- Torna a coluna de comentários fixa em telas grandes. --}}
                    <div class="lg:sticky lg:top-10 bg-white rounded-3xl shadow-xl p-6 border border-gray-100 space-y-4" style="max-height: calc(100vh - 4rem); overflow-y: auto;">
                        <h2 class="text-xl font-bold text-gray-900 mb-4 flex items-center gap-2">
                            <i class="ph-fill ph-chats text-red-600"></i> Comentários ({{ $event->eventComments->count() }})
                        </h2>
                        {{-- O Livewire Component de comentários se expandirá para essa largura --}}
                        @livewire('event-comments', ['event' => $event])
                    </div>
                </div>

            </div> {{-- FIM do MAIN CONTENT GRID --}}

        </div> {{-- FIM do container amplo --}}
    </div> {{-- FIM do Main Container --}}

    {{-- MODAIS (Ajustando Z-index para prevenir vazamento) --}}

    {{-- Modal de Zoom (Agora em z-[500]) --}}
    <div id="zoomModal" class="fixed inset-0 bg-black/90 flex items-center justify-center z-[500] hidden">
        <div class="relative w-full h-full p-4 flex items-center justify-center">
            <img id="zoomImg" src="" class="max-w-full max-h-full object-contain rounded-xl">
            <button id="closeZoom"
                class="absolute top-5 right-5 text-white/80 text-4xl font-light hover:text-white transition-colors p-2 rounded-full bg-black/30">
                <i class="ph ph-x text-2xl"></i>
            </button>
        </div>
    </div>

    {{-- Modal de Exclusão (Agora em z-[1000] - O mais alto) --}}
    <div id="deleteModal-{{ $event->id }}"
        class="modal hidden fixed inset-0 z-[1000] flex items-center justify-center bg-black bg-opacity-70 backdrop-blur-sm transition-opacity duration-300">
        <div class="bg-white p-8 rounded-xl shadow-2xl w-full max-w-sm transform transition-all">
            <div class="text-center">
                <i class="ph-fill ph-warning-circle text-6xl text-red-500 mx-auto mb-4"></i>
                <h2 class="text-xl font-bold mb-2 text-gray-800">Confirmar Exclusão</h2>
                <p class="text-gray-600 text-sm">Tem certeza que deseja excluir o evento **"{{ $event->event_name }}"**? Esta ação não poderá ser desfeita.</p>
            </div>

            <div class="mt-6 flex justify-center space-x-3">
                <button onclick="closeModal('deleteModal-{{ $event->id }}')"
                    class="px-5 py-2 text-sm font-semibold rounded-lg bg-gray-200 hover:bg-gray-300 transition-colors">
                    Cancelar
                </button>
                <form action="{{ route('events.destroy', $event->id) }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="px-5 py-2 text-sm font-semibold rounded-lg text-white bg-red-600 hover:bg-red-700 transition-colors shadow-md">
                        <i class="ph-fill ph-trash text-base mr-1"></i> Confirmar Exclusão
                    </button>
                </form>
            </div>
        </div>
    </div>

    {{-- Toast simples --}}
    <div id="toast" class="fixed bottom-5 right-5 bg-blue-600 text-white px-4 py-2 rounded-lg shadow-xl hidden z-50 transition-all duration-300">
        <span id="toast-message" class="font-medium"></span>
    </div>

</x-app-layout>

<script>
    
    // Função showToast (necessária para o event-reactions.js funcionar)
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

{{-- Chamada para os scripts externos --}}
@vite('resources/js/app.js')