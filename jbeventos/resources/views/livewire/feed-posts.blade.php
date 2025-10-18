<div class="space-y-6">
    {{-- ALERTAS --}}
    @foreach (['success' => 'green', 'error' => 'red', 'error_edit_image' => 'yellow'] as $type => $color)
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
                    placeholder="Compartilhe algo com o curso {{ optional($coordinatorCourses->first())->course_name }}..."
                    {{-- ADICIONADO: Envia o formulário (chama createPost) ao apertar ENTER --}} wire:keydown.enter.prevent="createPost" {{-- ADICIONADO: Permite a quebra de linha normal ao apertar SHIFT + ENTER --}} wire:keydown.shift.enter>
    </textarea>
                @error('newPostContent')
                    <span class="text-red-500 text-sm">{{ $message }}</span>
                @enderror

                <div class="flex items-center gap-3">

                    {{-- Campo de upload de mídia --}}
                    <label for="media-upload" class="cursor-pointer">
                        <div
                            class="flex items-center gap-2 text-sm text-gray-600 px-4 py-2 border border-gray-300 rounded-full hover:bg-gray-100 transition-colors shadow-sm">
                            <i class="ph-fill ph-paperclip text-lg"></i>
                            <span
                                class="truncate max-w-[150px] font-medium">{{ $media ? $media->getClientOriginalName() : 'Adicionar arquivo' }}</span>
                        </div>
                        <input type="file" id="media-upload" wire:model="media" class="hidden">
                    </label>

                    @if ($media)
                        <span class="text-sm text-gray-500 whitespace-nowrap">
                            ({{ number_format($media->getSize() / 1024 / 1024, 2) }} MB)
                        </span>
                        <button type="button" wire:click="$set('media', null)"
                            class="text-red-400 hover:text-red-600 text-xs transition-colors" title="Remover arquivo">
                            <i class="ph-fill ph-x-circle text-lg"></i>
                        </button>
                    @endif
                    @error('media')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
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

    {{-- LISTAGEM DE POSTS COM SCROLL --}}
    <div @class([
        'space-y-6',
        'overflow-y-auto' => true,
        'max-h-[70vh] min-h-[30vh]' => $isCoordinator,
        'max-h-[115vh] min-h-[85vh]' => !$isCoordinator,
    ])>
        @forelse ($posts as $post)
            <div wire:key="post-{{ $post->id }}"
                class="feed-card bg-white rounded-2xl shadow-md border border-gray-100 p-5 space-y-4 relative transition hover:shadow-lg hover:border-red-300">

                {{-- SEÇÃO DE EDIÇÃO --}}
                @if ($editingPostId === $post->id)
                    <form wire:submit.prevent="saveEditPost" class="space-y-4">
                        <h3 class="text-lg font-bold text-red-600">Editando Post</h3>
                        <div>
                            <textarea wire:model.defer="editingPostContent" rows="4"
                                class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-red-500 focus:border-red-500 text-sm p-3"
                                placeholder="Qual é a novidade?"></textarea>
                            @error('editingPostContent')
                                <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="flex items-center gap-3">
                            <label for="edit-media-upload" class="cursor-pointer">
                                <div
                                    class="flex items-center gap-2 text-sm text-gray-600 px-4 py-2 border border-gray-300 rounded-full hover:bg-gray-100 transition-colors shadow-sm">
                                    <i class="ph-fill ph-paperclip text-lg"></i>
                                    <span class="truncate max-w-[150px] font-medium">
                                        @if ($editingMedia)
                                            {{ $editingMedia->getClientOriginalName() }}
                                        @elseif ($originalMediaPath)
                                            Arquivo atual: {{ pathinfo($originalMediaPath, PATHINFO_BASENAME) }}
                                        @else
                                            Adicionar arquivo
                                        @endif
                                    </span>
                                </div>
                                <input type="file" id="edit-media-upload" wire:model="editingMedia" class="hidden">
                            </label>

                            @if ($editingMedia || $originalMediaPath)
                                <span class="text-sm text-gray-500 whitespace-nowrap">
                                    @if ($editingMedia)
                                        ({{ number_format($editingMedia->getSize() / 1024 / 1024, 2) }} MB)
                                    @else
                                        Anexado
                                    @endif
                                </span>
                                <button type="button" wire:click="removeEditingMedia"
                                    class="text-red-400 hover:text-red-600 text-xs transition-colors"
                                    title="Remover arquivo">
                                    <i class="ph-fill ph-x-circle text-lg"></i>
                                </button>
                            @endif
                        </div>
                        @error('editingMedia')
                            <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                        @enderror
                        <div class="flex justify-end gap-2 pt-2 border-t border-gray-100">
                            <button type="button" wire:click="resetEditModal"
                                class="px-4 py-2 text-sm text-gray-600 font-semibold rounded-full hover:bg-gray-100 transition">
                                Cancelar
                            </button>
                            <button type="submit"
                                class="px-4 py-2 text-sm bg-red-600 text-white font-semibold rounded-full hover:bg-red-700 transition"
                                wire:loading.attr="disabled">
                                <span wire:loading.remove wire:target="saveEditPost">Salvar Alterações</span>
                                <span wire:loading wire:target="saveEditPost">Salvando...</span>
                            </button>
                        </div>
                    </form>
                @else
                    {{-- 2. VISUALIZAÇÃO NORMAL DO POST (EXIBIDO SE NÃO ESTIVER EM EDIÇÃO) --}}
                    <div wire:click="openPostModal({{ $post->id }})" class="cursor-pointer">

                        {{-- Botões de Ação (Editar e Excluir) --}}
                        @if (Auth::id() === $post->user_id)
                            <div class="absolute top-4 right-4 flex gap-2 z-10">
                                {{-- Botão Editar --}}
                                <button wire:click.stop="startEditPost({{ $post->id }})"
                                    class="text-sm text-blue-600 hover:text-blue-800 transition p-1 bg-white rounded-full shadow-md"
                                    title="Editar Post">

                                    <i class="ph ph-pencil-simple text-lg"></i>
                                </button>

                                {{-- Botão Excluir --}}
                                <button wire:click.stop="confirmPostDeletion({{ $post->id }})"
                                    class="text-sm text-red-600 hover:text-red-800 transition p-1 bg-white rounded-full shadow-md"
                                    title="Excluir Post">
                                    <i class="ph ph-trash text-lg"></i>
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

                        <div class="flex justify-between items-center border-t border-gray-100 pt-5 mt-2">
                            <p class="text-gray-800 whitespace-pre-wrap leading-relaxed mb-2">{{ $post->content }}</p>
                        </div>

                        {{-- Mídia do Post --}}
                        @if (!empty($post->images) && count($post->images) > 0)
                            @php
                                $mediaPath = $post->images[0];
                                $extension = strtolower(pathinfo($mediaPath, PATHINFO_EXTENSION));
                            @endphp

                            <div class="mt-3">
                                @if (in_array($extension, ['jpg', 'jpeg', 'png', 'gif', 'webp']))
                                    {{-- Exibe Imagem --}}
                                    <div class="max-w-full rounded-xl shadow-md cursor-pointer group">
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
                            <button type="button"
                                class="text-sm font-medium text-gray-600 hover:text-red-600 transition"
                                wire:click.stop="openPostModal({{ $post->id }})">
                                Ver {{ $post->replies->count() }} respostas
                            </button>
                        </div>
                    </div>
                @endif
            </div>
        @empty
            <div class="p-5 bg-white rounded-2xl shadow text-center text-gray-500">
                {{ $isCoordinator ? 'Seja o primeiro a publicar um post!' : 'Nenhum post para exibir.' }}
            </div>
        @endforelse
    </div>

    {{-- =========================================================== --}}
    {{-- MODAL DE CONFIRMAÇÃO DE EXCLUSÃO --}}
    {{-- ============================================================ --}}
    @if ($confirmingPostDeletionId)
        <div x-data="{ show: true }" x-show="show" x-transition.opacity.duration.300ms
            class="fixed inset-0 bg-gray-900/70 backdrop-blur-sm z-[60] flex items-center justify-center p-4 sm:p-6"
            aria-modal="true" role="dialog" wire:ignore.self
            x-on:keydown.escape.window="$wire.confirmingPostDeletionId = null">

            <div x-transition
                class="bg-white rounded-xl shadow-2xl w-full max-w-sm p-6 space-y-4 transform transition-all border border-gray-100">

                <h3 class="text-xl font-bold text-red-600 flex items-center gap-2">
                    <i class="ph-bold ph-warning text-2xl"></i>
                    Confirmar Exclusão
                </h3>
                <p class="text-gray-700">Tem certeza que deseja excluir este post? Essa ação é **irreversível**.</p>

                <div class="flex justify-end gap-3">
                    <button type="button" wire:click="$set('confirmingPostDeletionId', null)"
                        class="px-4 py-2 text-sm text-gray-600 font-semibold rounded-full hover:bg-gray-100 transition">
                        Cancelar
                    </button>
                    <button type="button" wire:click="deletePost"
                        class="px-4 py-2 text-sm bg-red-600 text-white font-semibold rounded-full hover:bg-red-700 transition">
                        Excluir Post
                    </button>
                </div>
            </div>
        </div>
    @endif

    {{-- ============================================================ --}}
    {{-- MODAL DE EXPANSÃO (RESPOSTAS) --}}
    {{-- ============================================================ --}}
    @if ($selectedPostId && $expandedPost)
        <div x-data="{ show: true }" x-show="show" x-transition.opacity.duration.300ms x-init="window.addEventListener('close-post-modal', () => show = false);"
            class="fixed inset-0 bg-gray-900/70 backdrop-blur-sm z-50 flex items-center justify-center p-6 sm:p-8"
            aria-modal="true" role="dialog" wire:key="post-modal-{{ $expandedPost->id }}" wire:ignore.self
            x-on:keydown.escape.window="$wire.closePostModal()">

            <div x-transition
                class="bg-white rounded-2xl shadow-2xl w-full max-w-6xl max-h-[95vh] overflow-y-auto transform transition-all border border-gray-100">

                {{-- Cabeçalho permanece igual --}}
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

                {{-- Corpo aumentado --}}
                <div class="p-6 space-y-8">
                    {{-- Respostas --}}
                    <h4 class="text-xl font-semibold text-gray-800">
                        {{ $expandedPost->replies->count() }}
                        Resposta{{ $expandedPost->replies->count() != 1 ? 's' : '' }}
                    </h4>

                    {{-- Formulário de Nova Resposta aumentado --}}
                    <form wire:submit="createReply({{ $expandedPost->id }})" class="space-y-3">
                        <div class="flex gap-3">
                            <img src="{{ Auth::user()->user_icon_url }}" alt="{{ Auth::user()->name }}"
                                class="w-10 h-10 rounded-full object-cover border border-gray-200 flex-shrink-0">
                            <div class="flex-1">
                                <textarea wire:model="newReplyContent.{{ $expandedPost->id }}" rows="3"
                                    class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-red-500 focus:border-red-500 text-base p-3 resize-none"
                                    placeholder="Escreva sua resposta..." required wire:keydown.enter.prevent="createReply({{ $expandedPost->id }})"
                                    wire:keydown.shift.enter></textarea>
                                @error("newReplyContent.{$expandedPost->id}")
                                    <p class="text-sm text-red-500 mt-1">{{ $message }}</p>
                                @enderror
                                <div class="text-right mt-2">
                                    <button type="submit"
                                        class="px-4 py-2 text-sm bg-red-600 text-white font-semibold rounded-full hover:bg-red-700 transition">
                                        Responder
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>

                    {{-- Lista de respostas aumentada --}}
                    <div class="space-y-4">
                        @forelse ($expandedPost->replies->sortByDesc('created_at') as $reply)
                            <div wire:key="reply-{{ $reply->id }}"
                                class="p-5 bg-gray-50 rounded-xl border border-gray-100 shadow-sm relative">

                                {{-- Botões de Ação (Editar e Excluir) --}}
                                <div class="absolute top-3 right-3 flex gap-3 z-10">
                                    {{-- Botão Editar (apenas autor da resposta) --}}
                                    @if (Auth::id() === $reply->user_id)
                                        <button wire:click="startEditReply({{ $reply->id }})"
                                            class="text-base text-blue-600 hover:text-blue-800 transition p-3 rounded-full hover:bg-gray-100"
                                            title="Editar Resposta">
                                            <i class="ph ph-pencil-simple text-lg"></i>
                                        </button>
                                    @endif

                                    {{-- Botão Excluir (autor da resposta ou dono do post) --}}
                                    @if (Auth::id() === $reply->user_id || Auth::id() === $expandedPost->user_id)
                                        <button wire:click="confirmReplyDeletion({{ $reply->id }})"
                                            class="text-base text-red-600 hover:text-red-800 transition p-3 rounded-full hover:bg-gray-100"
                                            title="Excluir Resposta">
                                            <i class="ph ph-trash text-lg"></i>
                                        </button>
                                    @endif
                                </div>

                                <div class="flex items-start gap-4 mb-3">
                                    <img src="{{ $reply->author->user_icon_url }}" alt="{{ $reply->author->name }}"
                                        class="w-10 h-10 rounded-full object-cover border border-gray-200">
                                    <div>
                                        <span
                                            class="font-semibold text-gray-900 text-base">{{ $reply->author->name }}</span>
                                        <p class="text-sm text-gray-500">{{ $reply->created_at->diffForHumans() }}</p>
                                    </div>
                                </div>

                                {{-- Conteúdo da resposta ou edição --}}
                                @if ($editingReplyId === $reply->id)
                                    <form wire:submit.prevent="saveEditReply" class="space-y-3 ml-12 mt-3">
                                        <textarea wire:model.defer="editingReplyContent" rows="4"
                                            class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-red-500 focus:border-red-500 text-base p-3 resize-none"
                                            placeholder="Edite sua resposta..." wire:keydown.enter.prevent="saveEditReply" wire:keydown.shift.enter></textarea>
                                        @error('editingReplyContent')
                                            <p class="text-sm text-red-500 mt-1">{{ $message }}</p>
                                        @enderror
                                        <div class="flex justify-end gap-3">
                                            <button type="button" wire:click="resetEditReply"
                                                class="px-5 py-2 text-sm text-gray-600 font-semibold rounded-full hover:bg-gray-200 transition">
                                                Cancelar
                                            </button>
                                            <button type="submit"
                                                class="px-5 py-2 text-sm bg-red-600 text-white font-semibold rounded-full hover:bg-red-700 transition"
                                                wire:loading.attr="disabled">
                                                Salvar
                                            </button>
                                        </div>
                                    </form>
                                @else
                                    <div>
                                        {{-- Linha divisória com leve recuo à esquerda --}}
                                        <hr class="border-gray-200 w-[98%] mb-3 mt-1">

                                        {{-- Conteúdo da resposta alinhado normal --}}
                                        <p class="text-base text-gray-700 pt-2 pb-2">
                                            {{ $reply->content }}
                                        </p>
                                    </div>
                                @endif

                            </div>
                        @empty
                            <p class="text-center text-gray-500 text-base py-4">
                                Nenhuma resposta ainda. Seja o primeiro a responder!
                            </p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- ============================================================ --}}
    {{-- NOVO: MODAL DE CONFIRMAÇÃO DE EXCLUSÃO DE RESPOSTA (FORA DO MODAL DE POST) --}}
    {{-- Este modal deve ser colocado ao lado do Modal de Exclusão de POST original --}}
    {{-- ============================================================ --}}
    @if ($confirmingReplyDeletionId)
        <div x-data="{ show: true }" x-show="show" x-transition.opacity.duration.300ms
            class="fixed inset-0 bg-gray-900/70 backdrop-blur-sm z-[70] flex items-center justify-center p-4 sm:p-6"
            aria-modal="true" role="dialog" wire:ignore.self
            x-on:keydown.escape.window="$wire.confirmingReplyDeletionId = null">

            <div x-transition
                class="bg-white rounded-xl shadow-2xl w-full max-w-sm p-6 space-y-4 transform transition-all border border-gray-100">

                <h3 class="text-xl font-bold text-red-600 flex items-center gap-2">
                    <i class="ph-bold ph-warning text-2xl"></i>
                    Confirmar Exclusão
                </h3>
                <p class="text-gray-700">Tem certeza que deseja excluir esta resposta? Essa ação é **irreversível**.
                </p>

                <div class="flex justify-end gap-3">
                    <button type="button" wire:click="$set('confirmingReplyDeletionId', null)"
                        class="px-4 py-2 text-sm text-gray-600 font-semibold rounded-full hover:bg-gray-100 transition">
                        Cancelar
                    </button>
                    <button type="button" wire:click="deleteReply"
                        class="px-4 py-2 text-sm bg-red-600 text-white font-semibold rounded-full hover:bg-red-700 transition">
                        Excluir Resposta
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
