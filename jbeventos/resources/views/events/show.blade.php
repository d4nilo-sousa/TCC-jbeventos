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

            {{-- Main Content Grid --}}
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

                {{-- Image Carousel Section --}}
                <div class="lg:col-span-2 relative">
                    <div class="bg-white rounded-2xl shadow-lg overflow-hidden border">
                        @if ($event->images->count())
                            <div class="relative aspect-video w-full" id="carouselContainer">
                                {{-- Carousel Images --}}
                                <div class="w-full h-full relative" id="carousel">
                                    @foreach ($event->images as $img)
                                        <img src="{{ asset('storage/' . $img->image_path) }}"
                                             class="absolute top-0 left-0 w-full h-full object-cover transition-opacity duration-500 ease-in-out carousel-img {{ $loop->first ? 'opacity-100' : 'opacity-0 absolute' }}">
                                    @endforeach
                                </div>

                                {{-- Carousel Controls & Indicator --}}
                                @if ($event->images->count() > 1)
                                    <button id="prevBtn"
                                            class="absolute left-4 top-1/2 -translate-y-1/2 bg-white/50 text-gray-800 p-2 rounded-full hover:bg-white/80 transition-colors shadow-md">
                                        <i class="fas fa-chevron-left w-4 h-4"></i>
                                    </button>
                                    <button id="nextBtn"
                                            class="absolute right-4 top-1/2 -translate-y-1/2 bg-white/50 text-gray-800 p-2 rounded-full hover:bg-white/80 transition-colors shadow-md">
                                        <i class="fas fa-chevron-right w-4 h-4"></i>
                                    </button>
                                    <div id="indicator" class="absolute bottom-4 right-4 bg-black/60 text-white text-sm px-3 py-1 rounded-full">
                                        1 / {{ $event->images->count() }}
                                    </div>
                                @endif
                                
                                {{-- Zoom Button --}}
                                <button id="zoomBtn"
                                        class="absolute bottom-4 left-4 bg-white/50 text-gray-800 p-2 rounded-full hover:bg-white/80 transition-colors shadow-md">
                                    <i class="fas fa-expand w-4 h-4"></i>
                                </button>
                            </div>
                        @else
                            <div class="aspect-video w-full flex items-center justify-center bg-gray-100 rounded-2xl">
                                <span class="text-gray-400 text-lg">Sem imagem de capa.</span>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Event Info & Details --}}
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
                                <span class="font-bold">Data:</span> {{ \Carbon\Carbon::parse($event->event_scheduled_at)->isoFormat('D MMMM YYYY') }}
                            </p>
                        </div>
                        <div class="flex items-center gap-3">
                            <i class="far fa-clock text-blue-600"></i>
                            <p class="text-gray-800 text-base">
                                <span class="font-bold">Horário:</span> {{ \Carbon\Carbon::parse($event->event_scheduled_at)->isoFormat('HH:mm') }}
                            </p>
                        </div>

                        {{-- Coordinator's Actions --}}
                        @if (auth()->check() && auth()->user()->user_type === 'coordinator' && auth()->user()->coordinator->id === $event->coordinator_id)
                            <div class="pt-4 mt-4 border-t border-gray-200 flex flex-wrap gap-2">
                                <a href="{{ route('events.edit', $event->id) }}"
                                    class="flex items-center gap-2 px-4 py-2 bg-yellow-400 text-yellow-900 rounded-lg font-semibold hover:bg-yellow-500 transition-colors">
                                    <i class="fas fa-edit"></i> Editar Evento
                                </a>
                                <form action="{{ route('events.destroy', $event->id) }}" method="POST"
                                    onsubmit="return confirm('Tem certeza que deseja excluir este evento? Esta ação é irreversível.');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                        class="flex items-center gap-2 px-4 py-2 bg-red-500 text-white rounded-lg font-semibold hover:bg-red-600 transition-colors">
                                        <i class="fas fa-trash-alt"></i> Excluir Evento
                                    </button>
                                </form>
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
                            <p>{{ $event->event_type === 'general' ? 'Evento Geral' : ($event->event_type === 'course' ? 'Evento de Curso' : 'N/A') }}</p>
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
                                    <span class="inline-block bg-blue-100 text-blue-800 text-xs font-semibold px-2.5 py-0.5 rounded-full">
                                        {{ $category->category_name }}
                                    </span>
                                @empty
                                    <span class="text-gray-400 text-sm">Nenhuma categoria atribuída.</span>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Description Section --}}
            <div class="bg-white rounded-2xl shadow-lg p-6 border">
                <h2 class="text-2xl font-bold text-gray-900 mb-4">Sobre o Evento</h2>
                <p class="text-gray-700 leading-relaxed">{{ $event->event_description }}</p>
            </div>
            
            {{-- Reactions Section --}}
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
                        <form class="reaction-form" method="POST" action="{{ route('events.react', ['event' => $event->id]) }}">
                            @csrf
                            <input type="hidden" name="reaction_type" value="{{ $type }}">
                            <button type="submit" data-type="{{ $type }}" data-count="{{ $count }}"
                                class="reaction-btn flex items-center gap-2 px-4 py-2 rounded-full border transition-colors
                                {{ $isActive ? 'bg-blue-600 text-white border-blue-600' : 'bg-white text-blue-600 border-blue-500 hover:bg-blue-50' }}">
                                <i class="fas fa-{{ $type == 'like' ? 'thumbs-up' : ($type == 'save' ? 'bookmark' : 'bell') }}"></i>
                                {{ $label }}
                                <span class="reaction-count text-xs font-semibold px-2 py-1 rounded-full {{ $isActive ? 'bg-white text-blue-600' : 'bg-blue-100' }}">
                                    {{ $count }}
                                </span>
                            </button>
                        </form>
                    @endforeach
                </div>
            </div>

            {{-- Comments Section (Livewire) --}}
            <div class="bg-white rounded-2xl shadow-lg p-6 border">
                <h2 class="text-2xl font-bold text-gray-900 mb-4">
                    Comentários
                </h2>
                @livewire('event-comments', ['event' => $event])
            </div>

        </div>
    </div>

    {{-- Modal de Zoom (fora do card para sobrepor tudo) --}}
    <div id="zoomModal" class="fixed inset-0 bg-black/90 flex items-center justify-center z-[100] hidden">
        <div class="relative w-full h-full p-4 flex items-center justify-center">
            <img id="zoomImg" src="" class="max-w-full max-h-full object-contain rounded-lg">
            <button id="closeZoom" class="absolute top-5 right-5 text-white text-4xl font-light hover:text-gray-300 transition-colors">
                &times;
            </button>
        </div>
    </div>
</x-app-layout>

{{-- Scripts for Interactivity --}}
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const carousel = document.getElementById('carousel');
        const images = carousel ? carousel.querySelectorAll('.carousel-img') : [];
        const prevBtn = document.getElementById('prevBtn');
        const nextBtn = document.getElementById('nextBtn');
        const indicator = document.getElementById('indicator');
        const zoomBtn = document.getElementById('zoomBtn');
        const zoomModal = document.getElementById('zoomModal');
        const zoomImg = document.getElementById('zoomImg');
        const closeZoom = document.getElementById('closeZoom');

        let currentIndex = 0;

        function showImage(index) {
            images.forEach((img, i) => {
                img.classList.remove('opacity-100');
                img.classList.add('opacity-0', 'absolute');
            });
            images[index].classList.remove('opacity-0', 'absolute');
            images[index].classList.add('opacity-100');
            if (indicator) {
                indicator.textContent = `${index + 1} / ${images.length}`;
            }
        }

        if (images.length > 0) {
            if (prevBtn) {
                prevBtn.addEventListener('click', () => {
                    currentIndex = (currentIndex - 1 + images.length) % images.length;
                    showImage(currentIndex);
                });
            }

            if (nextBtn) {
                nextBtn.addEventListener('click', () => {
                    currentIndex = (currentIndex + 1) % images.length;
                    showImage(currentIndex);
                });
            }
            
            if (zoomBtn) {
                zoomBtn.addEventListener('click', () => {
                    zoomImg.src = images[currentIndex].src;
                    zoomModal.classList.remove('hidden');
                });
            }
        }

        if (closeZoom) {
            closeZoom.addEventListener('click', () => {
                zoomModal.classList.add('hidden');
            });
        }
        
        if (zoomModal) {
             zoomModal.addEventListener('click', (e) => {
                if(e.target === zoomModal) {
                    zoomModal.classList.add('hidden');
                }
            });
        }
        
        // --- NOVO CÓDIGO PARA AS REAÇÕES (AJAX) ---
        const reactionForms = document.querySelectorAll('.reaction-form');

        reactionForms.forEach(form => {
            form.addEventListener('submit', async (e) => {
                e.preventDefault(); // Impede o envio padrão do formulário e o redirecionamento
                
                const url = form.action;
                const formData = new FormData(form);
                const button = form.querySelector('.reaction-btn');
                const countSpan = form.querySelector('.reaction-count');
                const isCurrentlyActive = button.classList.contains('bg-blue-600');

                // Adiciona um estado de carregamento e desabilita o botão
                button.disabled = true;
                button.classList.add('opacity-50', 'cursor-not-allowed');

                try {
                    const response = await fetch(url, {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    });

                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }

                    const result = await response.json();
                    
                    if (result.status === 'added') {
                        // Atualiza para o estado 'ativo'
                        button.classList.remove('bg-white', 'text-blue-600', 'border-blue-500', 'hover:bg-blue-50');
                        button.classList.add('bg-blue-600', 'text-white', 'border-blue-600');
                        countSpan.classList.remove('bg-blue-100');
                        countSpan.classList.add('bg-white', 'text-blue-600');
                        
                        // Incrementa a contagem
                        const currentCount = parseInt(countSpan.textContent, 10);
                        countSpan.textContent = currentCount + 1;

                    } else if (result.status === 'removed') {
                        // Atualiza para o estado 'inativo'
                        button.classList.remove('bg-blue-600', 'text-white', 'border-blue-600');
                        button.classList.add('bg-white', 'text-blue-600', 'border-blue-500', 'hover:bg-blue-50');
                        countSpan.classList.remove('bg-white', 'text-blue-600');
                        countSpan.classList.add('bg-blue-100');

                        // Decrementa a contagem
                        const currentCount = parseInt(countSpan.textContent, 10);
                        countSpan.textContent = Math.max(0, currentCount - 1); // Garante que a contagem não seja negativa
                    }

                } catch (error) {
                    console.error('Erro ao enviar reação:', error);
                    alert('Erro ao processar sua reação. Tente novamente.');
                } finally {
                    // Reabilita o botão
                    button.disabled = false;
                    button.classList.remove('opacity-50', 'cursor-not-allowed');
                }
            });
        });
    });
</script>

{{-- Livewire & App scripts --}}
@vite('resources/js/app.js')