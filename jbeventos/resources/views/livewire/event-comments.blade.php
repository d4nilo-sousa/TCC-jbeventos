<div class="space-y-4" x-data="{
    deletingId: @entangle('deletingId').live,
    isModalOpen: false,
    openModal(id) {
        // Alpine chama o PHP para definir o ID de exclusão e abre o modal
        $wire.setDeletingId(id);
        this.isModalOpen = true;
    },
    closeModal() {
        this.isModalOpen = false;
        // Limpar o ID no PHP é feito automaticamente após a exclusão bem-sucedida,
        // mas é bom ter uma forma de fechar e limpar caso o usuário cancele.
        // O Livewire já reseta o 'deletingId' após a exclusão, mas mantemos
        // o reset no PHP para garantir o cancelamento.
        $wire.setDeletingId(null);
    }
}">

    {{-- Formulário de Comentário Principal (Novo Comentário - SEMPRE NO TOPO) --}}

    <div class="bg-white p-6 rounded-xl border shadow-lg sticky top-0 z-10 transition-shadow hover:shadow-xl"
        id="main-comment-form">

        <textarea wire:model.defer="commentText" id="comment-textarea"
            class="w-full p-4 border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 transition-colors resize-none text-base shadow-inner"
            placeholder="Escreva um comentário ou anexe um arquivo...">
    </textarea>

        @error('commentText')
            <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span>
        @enderror

        <div class="flex items-center gap-3 mt-4">
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
                <button wire:click="$set('media', null)"
                    class="text-red-400 hover:text-red-600 text-xs transition-colors" title="Remover arquivo">
                    <i class="ph-fill ph-x-circle text-lg"></i>
                </button>
            @endif

            <div class="flex-1"></div>

            {{-- Botão de Comentar --}}
            <button wire:click="addComment"
                class="bg-red-600 hover:bg-red-700 text-white font-semibold px-6 py-2 rounded-lg transition-colors shadow-lg"
                wire:loading.attr="disabled" wire:target="addComment, media">
                <span wire:loading.remove wire:target="addComment, media">Comentar</span>
                <span wire:loading wire:target="addComment, media">
                    <i class="ph-fill ph-spinner-gap animate-spin mr-1"></i> Enviando...
                </span>
            </button>
        </div>

    </div>

    {{-- Lista de Comentários --}}
    @forelse($comments as $comment)
        @php
            $profileRoute = $comment->user_id === auth()->id() ? 'profile.show' : 'profile.view';
            $isEditing = $openEditFormId === $comment->id;
        @endphp

        <div class="p-5 bg-white shadow-md rounded-xl border transition-all duration-300 hover:shadow-lg"
            id="comment-{{ $comment->id }}">

            {{-- Formulário de Edição Inline para Comentário Principal/Resposta --}}
            {{-- ATENÇÃO: Se $isEditing for TRUE, o conteúdo de exibição abaixo NÃO será mostrado. --}}
            @if ($isEditing)
                <div class="mt-0" id="edit-form-{{ $comment->id }}">
                    <div class="bg-green-50 p-4 rounded-lg border border-green-300 shadow-inner">
                        <div class="mb-3 text-sm text-gray-600 flex items-center justify-between">
                            <span class="font-bold text-yellow-800 flex items-center">
                                <i class="ph-fill ph-pencil-simple text-lg mr-1"></i>
                                Editando seu comentário
                            </span>
                            <button wire:click="cancelEdit"
                                class="text-xs text-gray-500 hover:text-gray-800 hover:underline flex items-center font-medium transition-colors">
                                <i class="ph-fill ph-x text-base mr-1"></i> Cancelar Edição
                            </button>
                        </div>
                        <textarea wire:model.defer="editText"
                            class="w-full p-3 border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-500 transition-colors resize-none text-sm"
                            placeholder="Edite seu comentário...">
                    </textarea>

                        @error('editText')
                            <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span>
                        @enderror

                        <div class="flex justify-end mt-2">
                            <button wire:click="updateComment"
                                class="bg-yellow-600 hover:bg-yellow-700 text-white text-sm font-semibold px-4 py-2 rounded-lg transition-colors shadow-md disabled:bg-yellow-300"
                                wire:loading.attr="disabled" wire:target="updateComment">
                                <span wire:loading.remove wire:target="updateComment">
                                    <i class="ph-fill ph-check-circle mr-1"></i> Salvar Edição
                                </span>
                                <span wire:loading wire:target="updateComment">
                                    <i class="ph-fill ph-spinner-gap animate-spin mr-1"></i> Atualizando...
                                </span>
                            </button>
                        </div>
                    </div>
                </div>
            @endif

            {{-- Conteúdo do Comentário Principal (Visível apenas se NÃO estiver em edição) --}}
            @if (!$isEditing)
                <div class="flex items-start gap-4">
                    {{-- Avatar --}}
                    <a href="{{ route($profileRoute, $comment->user->id) }}">
                        <img src="{{ $comment->user->user_icon_url }}" alt="{{ $comment->user->name }}"
                            class="w-10 h-10 rounded-full shadow-sm object-cover">
                    </a>

                    {{-- Conteúdo --}}
                    <div class="flex-1 space-y-2">
                        <div class="flex items-center gap-2 flex-wrap">
                            <a href="{{ route($profileRoute, $comment->user->id) }}"
                                class="font-bold text-gray-800 hover:underline">
                                {{ $comment->user->name }}
                            </a>
                            @if ($comment->user->user_type === 'coordinator')
                                <span
                                    class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                    <i class="ph-fill ph-crown-simple text-sm mr-1"></i> Coordenador
                                </span>
                            @endif
                            <span class="text-sm text-gray-500">• {{ $comment->created_at->diffForHumans() }}</span>
                            @if ($comment->isEdited())
                                <span class="text-xs text-gray-400 italic ml-1">(editado)</span>
                            @endif
                        </div>

                        {{-- O comentário é exibido aqui --}}
                        <p class="text-gray-700 whitespace-pre-wrap">{{ $comment->comment }}</p>

                        {{-- Mídia do comentário --}}
                        @if ($comment->media_path)
                            <div class="mt-3">
                                @if (Str::endsWith($comment->media_path, ['jpg', 'jpeg', 'png', 'webp']))
                                    <img src="{{ asset('storage/' . $comment->media_path) }}"
                                        class="max-w-full sm:max-w-md rounded-lg shadow-md border object-cover"
                                        alt="Imagem anexada">
                                @elseif(Str::endsWith($comment->media_path, 'mp4'))
                                    <video controls class="max-w-full sm:max-w-md rounded-lg shadow-md border">
                                        <source src="{{ asset('storage/' . $comment->media_path) }}" type="video/mp4">
                                        Seu navegador não suporta a tag de vídeo.
                                    </video>
                                @else
                                    <a href="{{ asset('storage/' . $comment->media_path) }}" target="_blank"
                                        class="text-blue-600 hover:text-blue-800 underline text-sm flex items-center gap-1 bg-blue-50 p-2 rounded-lg max-w-max transition-colors shadow-sm">
                                        <i class="ph-fill ph-file-text text-base"></i> Ver arquivo:
                                        {{ pathinfo($comment->media_path, PATHINFO_BASENAME) }}
                                    </a>
                                @endif
                            </div>
                        @endif

                        {{-- Botões de Ação do Comentário --}}
                        <div class="flex items-center gap-4 text-sm mt-3 pt-1">
                            {{-- Reações (sempre visíveis) --}}
                            @php
                                $userLike =
                                    auth()
                                        ->user()
                                        ->commentReactions->where('comment_id', $comment->id)
                                        ->where('type', 'like')
                                        ->count() > 0;
                                $userDislike =
                                    auth()
                                        ->user()
                                        ->commentReactions->where('comment_id', $comment->id)
                                        ->where('type', 'dislike')
                                        ->count() > 0;
                            @endphp
                            <button wire:click="reactToComment({{ $comment->id }}, 'like')"
                                class="flex items-center gap-1 p-1 px-3 rounded-full transition-colors font-medium 
                                {{ $userLike ? 'bg-blue-100 text-blue-600 shadow-inner' : 'text-gray-600 hover:bg-gray-100' }}">
                                <i class="ph-fill ph-thumbs-up text-base"></i>
                                <span>{{ $comment->reactions->where('type', 'like')->count() }}</span>
                            </button>
                            <button wire:click="reactToComment({{ $comment->id }}, 'dislike')"
                                class="flex items-center gap-1 p-1 px-3 rounded-full transition-colors font-medium
                                {{ $userDislike ? 'bg-red-100 text-red-600 shadow-inner' : 'text-gray-600 hover:bg-gray-100' }}">
                                <i class="ph-fill ph-thumbs-down text-base"></i>
                                <span>{{ $comment->reactions->where('type', 'dislike')->count() }}</span>
                            </button>

                            {{-- Ações --}}
                            <button wire:click="setReply({{ $comment->id }})"
                                class="text-gray-600 hover:text-blue-600 flex items-center gap-1 font-medium transition-colors">
                                <i class="ph-fill ph-chat-circle-dots text-base"></i> Responder
                            </button>

                            @if ($comment->user_id === auth()->id())
                                <button wire:click="editComment({{ $comment->id }})"
                                    class="text-gray-600 hover:text-yellow-600 flex items-center gap-1 font-medium transition-colors">
                                    <i class="ph-fill ph-pencil text-base"></i> Editar
                                </button>
                                {{-- Abertura do Modal via Alpine --}}
                                <button @click="openModal({{ $comment->id }})"
                                    class="text-red-500 hover:text-red-700 flex items-center gap-1 font-medium transition-colors">
                                    <i class="ph-fill ph-trash text-base"></i> Excluir
                                </button>
                            @endif
                        </div>
                    </div>
                </div>
            @endif {{-- Fim do bloco de exibição do comentário --}}

            {{-- Formulário de Resposta Inline (Injetado) --}}
            @if ($openReplyFormId === $comment->id)
                <div class="mt-4 pl-12 pt-4 border-l border-gray-200 transition-all duration-300"
                    id="reply-form-{{ $comment->id }}">
                    <div class="bg-gray-50 p-4 rounded-lg border shadow-inner">
                        <div class="mb-3 text-sm text-gray-600 flex items-center justify-between">
                            <span>
                                <i class="ph-fill ph-arrow-u-up-left text-blue-800 mr-1"></i>
                                Respondendo a <span
                                    class="font-semibold text-blue-800">{{ $comment->user->name }}</span>
                            </span>
                            <button wire:click="$set('openReplyFormId', null)"
                                class="text-xs text-gray-500 hover:text-gray-800 hover:underline flex items-center font-medium transition-colors">
                                <i class="ph-fill ph-x text-base mr-1"></i> Cancelar
                            </button>
                        </div>
                        <textarea wire:model.defer="replyText"
                            class="w-full p-3 border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 transition-colors resize-none text-sm"
                            placeholder="Escreva sua resposta...">
                    </textarea>

                        @error('replyText')
                            <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span>
                        @enderror

                        <div class="flex justify-end mt-2">
                            <button wire:click="addReply({{ $comment->id }})"
                                class="bg-blue-500 hover:bg-blue-600 text-white text-sm font-semibold px-4 py-2 rounded-lg transition-colors shadow-md disabled:bg-blue-300"
                                wire:loading.attr="disabled" wire:target="addReply({{ $comment->id }})">
                                <span wire:loading.remove wire:target="addReply({{ $comment->id }})">Enviar
                                    Resposta</span>
                                <span wire:loading wire:target="addReply({{ $comment->id }})">
                                    <i class="ph-fill ph-spinner-gap animate-spin mr-1"></i> Enviando...
                                </span>
                            </button>
                        </div>
                    </div>
                </div>
            @endif

            {{-- Respostas aninhadas (Sub-comentários) --}}
            @if ($comment->replies->count())
                <div class="mt-4 pl-12 border-l border-gray-200 space-y-4" x-data="{ repliesHidden: true }"
                    x-persist="{{ $comment->id }}-replies">
                    {{-- Botão para mostrar/ocultar respostas --}}
                    <button class="text-blue-600 text-sm hover:underline font-medium mb-2"
                        @click="repliesHidden = !repliesHidden"
                        x-text="repliesHidden ? 'Mostrar Respostas (' + {{ $comment->replies->count() }} + ')' : 'Ocultar Respostas (' + {{ $comment->replies->count() }} + ')'">
                        Mostrar Respostas ({{ $comment->replies->count() }})
                    </button>
                    <div class="space-y-4" x-show="!repliesHidden" x-cloak> {{-- Usa x-show em vez de hidden --}}
                        @foreach ($comment->replies as $reply)
                            @php
                                $replyProfileRoute = $reply->user_id === auth()->id() ? 'profile.show' : 'profile.view';
                                $isReplyEditing = $openEditFormId === $reply->id;
                            @endphp
                            <div class="flex items-start gap-3" id="comment-{{ $reply->id }}">
                                <a href="{{ route($replyProfileRoute, $reply->user->id) }}">
                                    <img src="{{ $reply->user->user_icon_url }}" alt="{{ $reply->user->name }}"
                                        class="w-8 h-8 rounded-full shadow-sm object-cover">
                                </a>
                                <div class="flex-1 space-y-1">

                                    {{-- Formulário de Edição Inline para Resposta --}}
                                    @if ($isReplyEditing)
                                        <div class="mt-0" id="edit-form-{{ $reply->id }}">
                                            <div
                                                class="bg-yellow-50 p-4 rounded-lg border border-yellow-300 shadow-inner">
                                                <div
                                                    class="mb-3 text-sm text-gray-600 flex items-center justify-between">
                                                    <span class="font-bold text-yellow-800 flex items-center">
                                                        <i class="ph-fill ph-pencil-simple text-lg mr-1"></i>
                                                        Editando sua resposta
                                                    </span>
                                                    <button wire:click="cancelEdit"
                                                        class="text-xs text-gray-500 hover:text-gray-800 hover:underline flex items-center font-medium transition-colors">
                                                        <i class="ph-fill ph-x text-base mr-1"></i> Cancelar Edição
                                                    </button>
                                                </div>
                                                <textarea wire:model.defer="editText"
                                                    class="w-full p-3 border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-500 transition-colors resize-none text-sm"
                                                    placeholder="Edite sua resposta...">
                                            </textarea>

                                                @error('editText')
                                                    <span
                                                        class="text-red-500 text-sm mt-1 block">{{ $message }}</span>
                                                @enderror

                                                <div class="flex justify-end mt-2">
                                                    <button wire:click="updateComment"
                                                        class="bg-yellow-600 hover:bg-yellow-700 text-white text-sm font-semibold px-4 py-2 rounded-lg transition-colors shadow-md disabled:bg-yellow-300"
                                                        wire:loading.attr="disabled" wire:target="updateComment">
                                                        <span wire:loading.remove wire:target="updateComment">
                                                            <i class="ph-fill ph-check-circle mr-1"></i> Salvar Edição
                                                        </span>
                                                        <span wire:loading wire:target="updateComment">
                                                            <i class="ph-fill ph-spinner-gap animate-spin mr-1"></i>
                                                            Atualizando...
                                                        </span>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    @endif {{-- Fim do Formulário de Edição Inline para Resposta --}}

                                    {{-- Conteúdo da Resposta (Visível apenas se NÃO estiver em edição) --}}
                                    @if (!$isReplyEditing)
                                        <div class="flex items-center gap-2 flex-wrap">
                                            <a href="{{ route($replyProfileRoute, $reply->user->id) }}"
                                                class="font-bold text-gray-800 hover:underline text-sm">
                                                {{ $reply->user->name }}
                                            </a>
                                            @if ($reply->user->user_type === 'coordinator')
                                                <span
                                                    class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                                    <i class="ph-fill ph-crown-simple text-xs mr-1"></i> Coord.
                                                </span>
                                            @endif
                                            <span class="text-xs text-gray-500">•
                                                {{ $reply->created_at->diffForHumans() }}</span>
                                            @if ($reply->isEdited())
                                                <span class="text-xs text-gray-400 italic ml-1">(editado)</span>
                                            @endif
                                        </div>
                                        <p class="text-gray-700 text-sm whitespace-pre-wrap">{{ $reply->comment }}</p>

                                        {{-- Botões de Ação para a Resposta --}}
                                        <div class="flex items-center gap-3 text-xs mt-2 pt-1">
                                            {{-- Reações --}}
                                            @php
                                                $replyLike =
                                                    auth()
                                                        ->user()
                                                        ->commentReactions->where('comment_id', $reply->id)
                                                        ->where('type', 'like')
                                                        ->count() > 0;
                                                $replyDislike =
                                                    auth()
                                                        ->user()
                                                        ->commentReactions->where('comment_id', $reply->id)
                                                        ->where('type', 'dislike')
                                                        ->count() > 0;
                                            @endphp
                                            <button wire:click="reactToComment({{ $reply->id }}, 'like')"
                                                class="flex items-center gap-1 p-1 px-3 rounded-full transition-colors font-medium
                                                {{ $replyLike ? 'bg-blue-100 text-blue-600 shadow-inner' : 'text-gray-600 hover:bg-gray-100' }}">
                                                <i class="ph-fill ph-thumbs-up text-sm"></i>
                                                <span>{{ $reply->reactions->where('type', 'like')->count() }}</span>
                                            </button>
                                            <button wire:click="reactToComment({{ $reply->id }}, 'dislike')"
                                                class="flex items-center gap-1 p-1 px-3 rounded-full transition-colors font-medium
                                                {{ $replyDislike ? 'bg-red-100 text-red-600 shadow-inner' : 'text-gray-600 hover:bg-gray-100' }}">
                                                <i class="ph-fill ph-thumbs-down text-sm"></i>
                                                <span>{{ $reply->reactions->where('type', 'dislike')->count() }}</span>
                                            </button>

                                            {{-- Ações --}}
                                            <button wire:click="setReply({{ $comment->id }})"
                                                class="text-gray-600 hover:text-blue-600 flex items-center gap-1 font-medium transition-colors">
                                                <i class="ph-fill ph-chat-circle-dots text-sm"></i> Responder
                                            </button>

                                            @if ($reply->user_id === auth()->id())
                                                <button wire:click="editComment({{ $reply->id }})"
                                                    class="text-gray-600 hover:text-yellow-600 flex items-center gap-1 font-medium transition-colors">
                                                    <i class="ph-fill ph-pencil text-sm"></i> Editar
                                                </button>
                                                <button @click="openModal({{ $reply->id }})"
                                                    class="text-red-500 hover:text-red-700 flex items-center gap-1 font-medium transition-colors">
                                                    <i class="ph-fill ph-trash text-sm"></i> Excluir
                                                </button>
                                            @endif
                                        </div>
                                    @endif {{-- Fim do bloco de exibição da resposta --}}
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>

    @empty
        <p class="text-gray-500 text-center text-sm p-6 bg-white rounded-xl shadow-inner border border-dashed">
            <i class="ph-fill ph-chat-circle-dots text-xl block mb-2"></i>
            Nenhum comentário ainda. Seja o primeiro a iniciar a conversa!
        </p>
    @endforelse

    {{-- Modal de Confirmação de Exclusão (Alpine.js) --}}

    <div x-show="isModalOpen" x-transition:enter="ease-out duration-300"
        x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
        x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" x-transition:leave="ease-in duration-200"
        x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
        x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" style="display: none;"
        class="fixed inset-0 z-50 overflow-y-auto">
        {{-- Overlay --}}
        <div x-show="isModalOpen" x-transition.opacity @click="closeModal()"
            class="fixed inset-0 bg-gray-900 bg-opacity-75"></div>

        {{-- Conteúdo do Modal --}}
        <div x-show="isModalOpen" class="flex items-center justify-center min-h-screen p-4"
            @click.away="closeModal()">
            <div class="bg-white rounded-xl shadow-2xl p-6 max-w-sm w-full z-10 mx-auto transform transition-all">
                <div class="text-center">
                    <i class="ph-fill ph-warning text-6xl text-red-500 mx-auto mb-4 animate-pulse-slow"></i>
                    <h3 class="text-xl font-bold text-gray-800 mb-2">Confirmação de Exclusão</h3>
                    <p class="text-sm text-gray-600">
                        Tem certeza que deseja excluir este comentário? Esta ação é irreversível e excluirá também todas
                        as suas respostas.
                    </p>
                </div>

                <div class="mt-6 flex justify-center gap-4">
                    <button @click="closeModal()"
                        class="px-5 py-2 text-sm font-semibold rounded-lg bg-gray-200 hover:bg-gray-300 transition-colors">
                        Cancelar
                    </button>
                    {{-- Ação CRÍTICA: Chama o método deleteComment no Livewire --}}
                    <button wire:click="deleteComment" @click="closeModal()" {{-- Fecha o modal imediatamente para melhor UX --}}
                        class="px-5 py-2 text-sm font-semibold rounded-lg text-white bg-red-600 hover:bg-red-700 transition-colors shadow-md disabled:bg-red-400"
                        wire:loading.attr="disabled" wire:target="deleteComment">
                        <span wire:loading.remove wire:target="deleteComment">Sim, Excluir</span>
                        <span wire:loading wire:target="deleteComment">
                            <i class="ph-fill ph-spinner-gap animate-spin mr-1"></i> Excluindo...
                        </span>
                    </button>
                </div>
            </div>
        </div>

    </div>

    <script>
        document.addEventListener('livewire:initialized', () => {
            // Função para rolar para o elemento e focar
            const scrollToElement = (id) => {
                const element = document.getElementById(id);
                if (element) {
                    element.scrollIntoView({
                        behavior: 'smooth',
                        block: 'center'
                    });
                    // Tenta focar na textarea se for um formulário
                    const textarea = element.querySelector('textarea');
                    if (textarea) {
                        textarea.focus();
                    }
                }
            };

            // Rola para o formulário de resposta recém-aberto
            Livewire.on( & #39;replyFormOpened&# 39;, ({
                id
            }) = & gt;
            {
                scrollToElement(id);
            });

            // Rola para o formulário de edição recém-aberto
            Livewire.on( & #39;editFormOpened&# 39;, ({
                id
            }) = & gt;
            {
                scrollToElement(id);
            });

            // Rola para o comentário recém-adicionado/editado/pai
            Livewire.on( & #39;scrollToComment&# 39;, ({
                id
            }) = & gt;
            {
                // Rola para o comentário pai ou para o formulário principal
                if (id === & #39;main-comment-form&# 39;) {
                    // Rola para o topo (novo comentário)
                    document.getElementById(id).scrollIntoView({
                        behavior: & #39;smooth&# 39;,
                        block: & #39;start&# 39;
                    });
                } else {
                    scrollToElement(id);
                }
            });

        });
    </script>
