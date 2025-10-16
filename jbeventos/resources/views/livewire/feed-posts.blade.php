<div class="space-y-6">
    {{-- ALERTAS --}}
    @foreach (['success' => 'green', 'error' => 'red', 'error_image' => 'yellow'] as $type => $color)
        @if (session()->has($type))
            <div
                class="p-4 bg-{{ $color }}-50 border border-{{ $color }}-300 text-{{ $color }}-700 rounded-xl shadow-sm flex items-center gap-2">
                <i class="ph ph-info text-lg"></i>
                <span>{{ session($type) }}</span>
            </div>
        @endif
    @endforeach

    {{-- FORM DE NOVO POST --}}
    @if ($isCoordinator)
        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6">
            <h3 class="text-xl font-bold text-gray-800 mb-4">Criar Novo Post</h3>

            <form wire:submit.prevent="createPost" enctype="multipart/form-data" class="space-y-4">
                <textarea wire:model.defer="newPostContent" rows="4"
                    class="w-full border-gray-300 rounded-lg shadow-sm focus:border-red-500 focus:ring-red-500 transition"
                    placeholder="Compartilhe algo com o curso {{ optional($coordinatorCourses->first())->course_name }}..."></textarea>
                @error('newPostContent')
                    <span class="text-red-500 text-sm">{{ $message }}</span>
                @enderror

                <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                    {{-- Upload --}}
                    <div>
                        <input type="file" wire:model="newlyUploadedImages" multiple accept="image/*"
                            id="file-upload" class="hidden" @if (count($images) >= 5) disabled @endif>
                        <label for="file-upload"
                            class="inline-flex items-center gap-2 bg-red-600 text-white px-4 py-2 rounded-full cursor-pointer hover:bg-red-700 transition font-semibold text-sm shadow-md">
                            <i class="ph ph-image"></i> Adicionar Fotos (Máx. 5)
                        </label>

                        @if (count($images) >= 5)
                            <p class="text-xs text-yellow-600 mt-1">Limite máximo de 5 imagens atingido.</p>
                        @endif
                    </div>

                    {{-- Preview --}}
                    @if ($images)
                        <div class="flex flex-wrap gap-2">
                            @foreach ($images as $index => $image)
                                <div class="relative w-20 h-20">
                                    @if (is_object($image) && method_exists($image, 'temporaryUrl'))
                                        <img src="{{ $image->temporaryUrl() }}" alt="Preview"
                                            class="w-full h-full object-cover rounded-lg shadow">
                                    @endif
                                    <button type="button" wire:click="removeImage({{ $index }})"
                                        class="absolute -top-1 -right-1 bg-red-600 text-white rounded-full p-1 leading-none text-xs hover:bg-red-700">
                                        &times;
                                    </button>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>

                <div class="flex justify-end">
                    <button type="submit"
                        class="px-4 py-2 bg-red-600 text-white font-semibold rounded-lg shadow-md hover:bg-red-700 transition"
                        wire:loading.attr="disabled">
                        <span wire:loading.remove wire:target="createPost">Publicar Post</span>
                        <span wire:loading wire:target="createPost">Publicando...</span>
                    </button>
                </div>
            </form>
        </div>
    @endif

    {{-- LISTAGEM DE POSTS --}}
    @forelse ($posts as $post)
        <div wire:key="post-{{ $post->id }}"
            class="feed-card bg-white rounded-2xl shadow-md border border-gray-100 p-5 space-y-4 relative transition hover:shadow-lg hover:border-red-300">
            <div wire:click="openPostModal({{ $post->id }})" class="cursor-pointer">

                {{-- Ações --}}
                @if (Auth::id() === $post->user_id)
                    <div class="absolute top-3 right-3 flex gap-2 z-10">
                        <button wire:click.stop="confirmPostDeletion({{ $post->id }})"
                            class="p-2 text-red-500 hover:text-red-700 hover:bg-red-50 rounded-full transition"
                            title="Excluir Post">
                            <i class="ph ph-trash text-lg"></i>
                        </button>
                        <button wire:click.stop="startEditPost({{ $post->id }})"
                            class="p-2 text-gray-500 hover:text-blue-700 hover:bg-blue-50 rounded-full transition"
                            title="Editar Post">
                            <i class="ph ph-pencil-simple text-lg"></i>
                        </button>
                    </div>
                @endif

                <div class="flex items-start space-x-3 {{ Auth::id() === $post->user_id ? 'pt-6' : '' }}">
                    <img src="{{ $post->author->user_icon_url }}" alt="{{ $post->author->name }}"
                        class="w-10 h-10 rounded-full object-cover border-2 border-gray-200 hover:border-red-500 transition shadow-sm">

                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-2">
                            <span class="font-semibold text-gray-900">{{ $post->author->name }}</span>
                            @if ($post->author->user_type === 'coordinator')
                                <span
                                    class="text-xs bg-red-100 text-red-600 px-2 py-0.5 rounded-full font-medium">Coordenador</span>
                            @endif
                        </div>
                        <p class="text-sm text-gray-500">
                            {{ $post->created_at->diffForHumans() }} •
                            <span
                                class="font-medium text-red-600">{{ optional($post->course)->course_name ?? 'Geral' }}</span>
                        </p>
                    </div>
                </div>

                <p class="text-gray-800 whitespace-pre-wrap leading-relaxed">{{ $post->content }}</p>

                {{-- Imagens --}}
                @if ($post->images && count($post->images) > 0)
                    <div class="grid {{ count($post->images) == 1 ? 'grid-cols-1' : 'grid-cols-2' }} gap-2 mt-2">
                        @foreach ($post->images as $imagePath)
                            <div class="relative w-full h-48 overflow-hidden rounded-xl shadow-md cursor-pointer group"
                                onclick="event.stopPropagation(); window.open('{{ asset('storage/' . $imagePath) }}')">
                                <img src="{{ asset('storage/' . $imagePath) }}"
                                    class="absolute inset-0 w-full h-full object-contain bg-gray-100 rounded-xl transition duration-300 group-hover:scale-105"
                                    alt="Imagem do Post {{ $loop->index + 1 }}">
                            </div>
                        @endforeach
                    </div>
                @endif

                <div class="flex justify-between items-center border-t border-gray-100 pt-3 mt-3">
                    <span class="text-sm font-medium text-gray-600 hover:text-red-600 transition cursor-pointer">
                        Ver {{ $post->replies->count() }} respostas
                    </span>
                </div>
            </div>
        </div>
    @empty
        <div class="p-5 bg-white rounded-2xl shadow text-center text-gray-500">
            {{ $isCoordinator ? 'Seja o primeiro a publicar um post!' : 'Nenhum post para exibir.' }}
        </div>
    @endforelse

    {{-- PAGINAÇÃO --}}
    <div class="mt-6">
        {{ $posts->links() }}
    </div>

    {{-- ============================================================ --}}
    {{-- MODAL DE EXPANSÃO (RESPOSTAS) --}}
    {{-- ============================================================ --}}
    @if ($selectedPostId && $expandedPost)
        <div x-data="{ show: @entangle('selectedPostId').not(null) }" x-show="show" x-transition.opacity.duration.300ms
            class="fixed inset-0 bg-gray-900/70 backdrop-blur-sm z-50 flex items-center justify-center p-4 sm:p-6"
            aria-modal="true" role="dialog" wire:key="post-modal-{{ $expandedPost->id }}"
            x-on:keydown.escape.window="$wire.closePostModal()">

            <div x-transition
                class="bg-white rounded-2xl shadow-2xl w-full max-w-4xl max-h-[90vh] overflow-y-auto transform transition-all border border-gray-100">

                {{-- Cabeçalho --}}
                <div
                    class="sticky top-0 bg-white/95 backdrop-blur-sm p-4 border-b border-gray-100 z-10 flex justify-between items-center">
                    <h3 class="text-xl font-semibold text-gray-900 flex items-center gap-2">
                        <i class="ph ph-chats text-red-600 text-2xl"></i>
                        Detalhes do Post
                    </h3>
                    <button wire:click="closePostModal"
                        class="text-gray-500 hover:text-red-600 transition p-2 rounded-full hover:bg-gray-100">
                        <i class="ph-bold ph-x text-2xl"></i>
                    </button>
                </div>

                {{-- Corpo do Modal --}}
                <div class="p-6 space-y-6">

                    {{-- Autor --}}
                    <div class="flex items-center gap-3">
                        <img src="{{ $expandedPost->author->user_icon_url }}" alt="{{ $expandedPost->author->name }}"
                            class="w-12 h-12 rounded-full object-cover border border-gray-200">
                        <div>
                            <h4 class="font-semibold text-gray-900 text-lg">{{ $expandedPost->author->name }}</h4>
                            <p class="text-sm text-gray-500">
                                {{ $expandedPost->created_at->format('d/m/Y H:i') }}
                                @if ($expandedPost->course)
                                    • <span
                                        class="text-red-600 font-medium">{{ $expandedPost->course->course_name }}</span>
                                @endif
                            </p>
                        </div>
                    </div>

                    {{-- Conteúdo --}}
                    <p class="text-gray-800 text-base whitespace-pre-wrap leading-relaxed">
                        {{ $expandedPost->content }}
                    </p>

                    {{-- Imagens --}}
                    @if (!empty($expandedPost->images))
                        <div
                            class="grid {{ count($expandedPost->images) == 1 ? 'grid-cols-1' : 'grid-cols-2' }} gap-3">
                            @foreach ($expandedPost->images as $index => $imagePath)
                                <div class="relative w-full h-48 overflow-hidden rounded-xl shadow-md cursor-pointer group"
                                    wire:click="openCarousel({{ $index }})">
                                    <img src="{{ asset('storage/' . $imagePath) }}"
                                        class="absolute inset-0 w-full h-full object-cover group-hover:scale-105 transition duration-300"
                                        alt="Imagem do Post {{ $index + 1 }}">
                                    <div class="absolute inset-0 bg-black/10 group-hover:bg-black/0 transition"></div>
                                </div>
                            @endforeach
                        </div>
                    @endif

                    {{-- Respostas --}}
                    <h4 class="text-lg font-semibold text-gray-800 border-t border-gray-100 pt-4">
                        {{ $expandedPost->replies->count() }}
                        Resposta{{ $expandedPost->replies->count() != 1 ? 's' : '' }}
                    </h4>

                    {{-- Formulário de resposta --}}
                    <form wire:submit="createReply({{ $expandedPost->id }})" class="space-y-2">
                        <div class="flex gap-2">
                            <img src="{{ Auth::user()->user_icon_url }}" alt="{{ Auth::user()->name }}"
                                class="w-8 h-8 rounded-full object-cover border border-gray-200 flex-shrink-0">
                            <div class="flex-1">
                                <textarea wire:model="newReplyContent.{{ $expandedPost->id }}" rows="2"
                                    class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-red-500 focus:border-red-500 text-sm p-2 resize-none"
                                    placeholder="Escreva sua resposta..." required></textarea>
                                @error("newReplyContent.{$expandedPost->id}")
                                    <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                                @enderror
                                <div class="text-right mt-1">
                                    <button type="submit"
                                        class="px-3 py-1 text-xs bg-red-600 text-white font-semibold rounded-full hover:bg-red-700 transition">
                                        Responder
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>

                    {{-- Lista de respostas --}}
                    <div class="space-y-3">
                        @forelse ($expandedPost->replies->sortByDesc('created_at') as $reply)
                            <div class="p-4 bg-gray-50 rounded-xl border border-gray-100 shadow-sm">
                                <div class="flex items-start gap-3 mb-2">
                                    <img src="{{ $reply->author->user_icon_url }}" alt="{{ $reply->author->name }}"
                                        class="w-8 h-8 rounded-full object-cover border border-gray-200">
                                    <div>
                                        <span
                                            class="font-semibold text-gray-900 text-sm">{{ $reply->author->name }}</span>
                                        <p class="text-xs text-gray-500">{{ $reply->created_at->diffForHumans() }}</p>
                                    </div>
                                </div>
                                <p class="text-sm text-gray-700 ml-11 whitespace-pre-wrap">{{ $reply->content }}</p>
                            </div>
                        @empty
                            <p class="text-center text-gray-500 text-sm py-3">
                                Nenhuma resposta ainda. Seja o primeiro a responder!
                            </p>
                        @endforelse
                    </div>

                </div>
            </div>
        </div>
    @endif

    {{-- ============================================================ --}}
    {{-- MODAL CARROSSEL (GALERIA) --}}
    {{-- ============================================================ --}}
    @if ($expandedPost && count($expandedPost->images) > 0)
        <div x-data="{
            isCarouselOpen: @entangle('isCarouselOpen'),
            currentIndex: @entangle('currentImageIndex'),
            images: @js($expandedPost->images),
            next() { this.currentIndex = (this.currentIndex + 1) % this.images.length; },
            prev() { this.currentIndex = (this.currentIndex - 1 + this.images.length) % this.images.length; },
            init() {
                document.addEventListener('keydown', (e) => {
                    if (this.isCarouselOpen) {
                        if (e.key === 'ArrowLeft') this.prev();
                        else if (e.key === 'ArrowRight') this.next();
                        else if (e.key === 'Escape') this.isCarouselOpen = false;
                    }
                });
            }
        }" x-show="isCarouselOpen" x-transition.opacity.duration.300ms
            class="fixed inset-0 bg-black/90 backdrop-blur-sm z-[150] flex items-center justify-center p-4"
            aria-modal="true" role="dialog" wire:ignore.self>

            <div x-cloak class="relative w-full h-full max-w-7xl max-h-full flex items-center justify-center">
                @foreach ($expandedPost->images as $index => $imagePath)
                    <img x-show="currentIndex === {{ $index }}" x-transition
                        src="{{ asset('storage/' . $imagePath) }}" alt="Imagem {{ $index + 1 }}"
                        class="max-w-full max-h-full object-contain absolute inset-0 m-auto transition-all duration-300">
                @endforeach

                <button @click="prev()" x-show="images.length > 1"
                    class="absolute left-4 p-3 bg-black/30 hover:bg-black/50 text-white rounded-full transition z-20">
                    <i class="ph ph-caret-left text-3xl"></i>
                </button>
                <button @click="next()" x-show="images.length > 1"
                    class="absolute right-4 p-3 bg-black/30 hover:bg-black/50 text-white rounded-full transition z-20">
                    <i class="ph ph-caret-right text-3xl"></i>
                </button>

                <button @click="isCarouselOpen = false"
                    class="absolute top-4 right-4 p-2 bg-black/40 hover:bg-black/60 text-white rounded-full transition z-30">
                    <i class="ph ph-x text-2xl"></i>
                </button>

                <div x-show="images.length > 1"
                    class="absolute bottom-4 left-1/2 -translate-x-1/2 bg-black/50 text-white text-sm px-4 py-1 rounded-full">
                    <span x-text="currentIndex + 1"></span> / <span x-text="images.length"></span>
                </div>
            </div>
        </div>
    @endif
</div>
