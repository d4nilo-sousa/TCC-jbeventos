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

                <div class="flex items-center gap-3">

                    {{-- Campo de upload de mídia --}}
                    <label for="media-upload" class="cursor-pointer">
                        <div
                            class="flex items-center gap-2 text-sm text-gray-600 px-4 py-2 border border-gray-300 rounded-full hover:bg-gray-100 transition-colors shadow-sm">
                            <i class="ph-fill ph-paperclip text-lg"></i>
                            {{-- Exibe o nome do arquivo ou o texto padrão --}}
                            <span
                                class="truncate max-w-[150px] font-medium">{{ $media ? $media->getClientOriginalName() : 'Adicionar arquivo' }}</span>
                        </div>
                        {{-- Input real do Livewire --}}
                        <input type="file" id="media-upload" wire:model="media" class="hidden">
                    </label>

                    {{-- Informações do Arquivo e Botão de Remover --}}
                    @if ($media)
                        <span class="text-sm text-gray-500 whitespace-nowrap">
                            ({{ number_format($media->getSize() / 1024 / 1024, 2) }} MB)
                        </span>
                        <button type="button" wire:click="$set('media', null)"
                            class="text-red-400 hover:text-red-600 text-xs transition-colors" title="Remover arquivo">
                            <i class="ph-fill ph-x-circle text-lg"></i>
                        </button>
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

                <div class="flex justify-between items-center border-t border-gray-100 pt-3 mt-5">
                    {{-- A div de borda e justify-between original não fazia sentido sem itens no meio, 
                    então simplifiquei a margem/padding para o conteúdo --}}
                    <p class="text-gray-800 whitespace-pre-wrap leading-relaxed mb-2">{{ $post->content }}</p>
                </div>

                {{-- -------------------------------------------------------------------------------- --}}
                {{-- Mídia do Post (Imagens, Vídeos ou Arquivos) --}}
                {{-- Assumimos que o caminho do arquivo único está em $post->images[0] --}}
                {{-- -------------------------------------------------------------------------------- --}}
                @if (!empty($post->images) && count($post->images) > 0)
                    @php
                        // Pega o caminho do primeiro item do array de imagens
                        $mediaPath = $post->images[0];
                        $extension = strtolower(pathinfo($mediaPath, PATHINFO_EXTENSION));
                    @endphp

                    <div class="mt-3">
                        @if (in_array($extension, ['jpg', 'jpeg', 'png', 'gif', 'webp']))
                            {{-- Exibe Imagem --}}
                            <div class="max-w-full rounded-xl shadow-md cursor-pointer group">
                                {{-- ALTERAÇÃO AQUI: `w-full` e removemos `object-cover` e `h-full` fixo --}}
                                <img src="{{ asset('storage/' . $mediaPath) }}"
                                    class="w-full h-auto rounded-xl shadow-md border transition duration-300"
                                    alt="Imagem anexada">
                            </div>
                        @elseif($extension === 'mp4')
                            {{-- Exibe Vídeo --}}
                            <video controls class="max-w-full rounded-xl shadow-md border">
                                <source src="{{ asset('storage/' . $mediaPath) }}" type="video/mp4">
                                Seu navegador não suporta a tag de vídeo.
                            </video>
                        @else
                            {{-- Exibe Link para Outros Arquivos (PDF, DOC, ZIP, etc.) --}}
                            <a href="{{ asset('storage/' . $mediaPath) }}" target="_blank"
                                class="text-blue-600 hover:text-red-600 underline text-sm flex items-center gap-1 bg-gray-50 p-3 rounded-xl max-w-max transition-colors shadow-sm">
                                <i class="ph-fill ph-file-text text-base"></i> Ver arquivo:
                                **{{ pathinfo($mediaPath, PATHINFO_BASENAME) }}**
                            </a>
                        @endif
                    </div>
                @endif

                <div class="flex justify-between items-center border-t border-gray-100 pt-3 mt-3">
                    <button type="button" class="text-sm font-medium text-gray-600 hover:text-red-600 transition"
                        wire:click.stop="openPostModal({{ $post->id }})">
                        Ver {{ $post->replies->count() }} respostas
                    </button>
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
        {{-- ALTERAÇÃO CRÍTICA: x-data="{ show: true }" e x-init simplificado --}}
        <div x-data="{ show: true }" x-show="show" x-transition.opacity.duration.300ms x-init="window.addEventListener('close-post-modal', () => show = false);"
            class="fixed inset-0 bg-gray-900/70 backdrop-blur-sm z-50 flex items-center justify-center p-4 sm:p-6"
            aria-modal="true" role="dialog" wire:key="post-modal-{{ $expandedPost->id }}" wire:ignore.self
            x-on:keydown.escape.window="$wire.closePostModal()">

            <div x-transition
                class="bg-white rounded-2xl shadow-2xl w-full max-w-4xl max-h-[90vh] overflow-y-auto transform transition-all border border-gray-100">

                {{-- Cabeçalho --}}
                <div
                    class="sticky top-0 bg-white/95 backdrop-blur-sm p-4 border-b border-gray-100 z-10 flex justify-between items-center">
                    <h3 class="text-xl font-semibold text-gray-900 flex items-center gap-2">
                        <i class="ph ph-chats text-red-600 text-2xl"></i>
                        Respostas do Post
                    </h3>
                    <button wire:click="closePostModal"
                        class="text-gray-500 hover:text-red-600 transition p-2 rounded-full hover:bg-gray-100">
                        <i class="ph-bold ph-x text-2xl"></i>
                    </button>
                </div>

                {{-- Corpo --}}
                <div class="p-4 space-y-6">
                    {{-- Respostas --}}
                    <h4 class="text-lg font-semibold text-gray-800">
                        {{-- A linha e a cor da borda foram removidas --}}
                        {{ $expandedPost->replies->count() }}
                        Resposta{{ $expandedPost->replies->count() != 1 ? 's' : '' }}
                    </h4>

                    {{-- Formulário --}}
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
</div>
