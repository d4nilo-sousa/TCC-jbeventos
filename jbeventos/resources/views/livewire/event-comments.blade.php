<div class="space-y-4">
    {{-- Formulário de Comentário --}}
    <div class="bg-gray-50 p-6 rounded-lg border shadow-sm">
        @if ($replyTo)
            <div class="mb-3 text-sm text-gray-600 flex items-center justify-between p-2 bg-blue-100 rounded">
                <span>
                    Respondendo a:
                    <span class="font-semibold text-blue-800">
                        {{ \App\Models\Comment::find($replyTo)->user->name }}
                    </span>
                </span>
                <button wire:click="cancelReply" class="text-xs text-gray-500 hover:text-gray-800 hover:underline">
                    Cancelar
                </button>
            </div>
        @endif
        
        <textarea wire:model.defer="commentText"
            class="w-full p-3 border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 transition-colors"
            placeholder="Escreva um comentário...">
        </textarea>
        
        @error('commentText')
            <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
        @enderror

        <div class="flex items-center gap-3 mt-4">
            <label for="media-upload" class="cursor-pointer">
                <div class="flex items-center gap-2 text-sm text-gray-600 px-4 py-2 border rounded-full hover:bg-gray-100 transition-colors">
                    <i class="fas fa-paperclip"></i>
                    <span>{{ $media ? $media->getClientOriginalName() : 'Adicionar arquivo' }}</span>
                </div>
                <input type="file" id="media-upload" wire:model="media" class="hidden">
            </label>

            @if ($media)
                <span class="text-sm text-gray-500">
                    {{ number_format($media->getSize() / 1024 / 1024, 2) }} MB
                </span>
            @endif

            <div class="flex-1"></div>

            @if($editingCommentId)
                <button wire:click="updateComment"
                    class="bg-yellow-500 hover:bg-yellow-600 text-white font-semibold px-4 py-2 rounded-lg transition-colors">
                    <i class="fas fa-check"></i> Atualizar
                </button>
                <button wire:click="$set('editingCommentId', null)"
                    class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-semibold px-4 py-2 rounded-lg transition-colors">
                    Cancelar
                </button>
            @else
                <button wire:click="addComment"
                    class="bg-blue-600 hover:bg-blue-700 text-white font-semibold px-4 py-2 rounded-lg transition-colors">
                    <span wire:loading.remove wire:target="addComment">Comentar</span>
                    <span wire:loading wire:target="addComment">Enviando...</span>
                </button>
            @endif
        </div>
    </div>

    {{-- Lista de Comentários --}}
    @forelse($comments as $comment)
        @php
            $profileRoute = ($comment->user_id === auth()->id()) ? 'profile.show' : 'profile.view';
        @endphp
        
        <div class="p-4 bg-white shadow-md rounded-lg border">
            {{-- Comentário principal --}}
            <div class="flex items-start gap-4">
                {{-- Avatar e Popover --}}
                <a href="{{ route($profileRoute, $comment->user->id) }}">
                    <img src="{{ $comment->user->user_icon_url }}" class="w-10 h-10 rounded-full shadow-sm">
                </a>

                {{-- Conteúdo --}}
                <div class="flex-1 space-y-2">
                    <div class="flex items-center gap-2">
                        <a href="{{ route($profileRoute, $comment->user->id) }}" class="font-bold text-gray-800 hover:underline">
                            {{ $comment->user->name }}
                        </a>
                        @if ($comment->user->user_type === 'coordinator')
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                Coordenador
                            </span>
                        @endif
                        <span class="text-sm text-gray-500">• {{ $comment->created_at->diffForHumans() }}</span>
                        @if($comment->isEdited())
                            <span class="text-xs text-gray-400 italic">(editado)</span>
                        @endif
                    </div>

                    <p class="text-gray-700">{{ $comment->comment }}</p>

                    {{-- Mídia do comentário --}}
                    @if($comment->media_path)
                        <div class="mt-2">
                            @if(Str::endsWith($comment->media_path, ['jpg','jpeg','png','webp']))
                                <img src="{{ asset('storage/'.$comment->media_path) }}" class="max-w-xs rounded-md shadow-sm border">
                            @elseif(Str::endsWith($comment->media_path, 'mp4'))
                                <video controls class="max-w-xs rounded-md shadow-sm border">
                                    <source src="{{ asset('storage/'.$comment->media_path) }}" type="video/mp4">
                                </video>
                            @else
                                <a href="{{ asset('storage/'.$comment->media_path) }}" target="_blank"
                                    class="text-blue-600 underline text-sm flex items-center gap-1">
                                    <i class="fas fa-file-alt"></i> Ver arquivo
                                </a>
                            @endif
                        </div>
                    @endif

                    {{-- Botões de Ação do Comentário --}}
                    <div class="flex items-center gap-4 text-sm mt-2">
                        {{-- Reações --}}
                        <button wire:click="reactToComment({{ $comment->id }}, 'like')"
                            class="flex items-center gap-1 p-1 px-2 rounded-full transition-colors 
                                {{ auth()->user()->commentReactions->where('comment_id', $comment->id)->where('type', 'like')->count() > 0 
                                    ? 'bg-blue-100 text-blue-600 font-semibold' 
                                    : 'text-gray-600 hover:bg-gray-100' }}">
                            <i class="far fa-thumbs-up"></i>
                            <span>{{ $comment->likes_count }}</span>
                        </button>
                        <button wire:click="reactToComment({{ $comment->id }}, 'dislike')"
                            class="flex items-center gap-1 p-1 px-2 rounded-full transition-colors 
                                {{ auth()->user()->commentReactions->where('comment_id', $comment->id)->where('type', 'dislike')->count() > 0 
                                    ? 'bg-red-100 text-red-600 font-semibold' 
                                    : 'text-gray-600 hover:bg-gray-100' }}">
                            <i class="far fa-thumbs-down"></i>
                            <span>{{ $comment->dislikes_count }}</span>
                        </button>
                        
                        {{-- Botões de Ação --}}
                        <button wire:click="setReply({{ $comment->id }})" class="text-gray-600 hover:text-blue-600">Responder</button>

                        @if($comment->user_id === auth()->id())
                            <button wire:click="editComment({{ $comment->id }})" class="text-gray-600 hover:text-yellow-600">Editar</button>
                            <button wire:click="deleteComment({{ $comment->id }})" onclick="return confirm('Tem certeza que deseja excluir?')" class="text-red-500 hover:text-red-700">Excluir</button>
                        @endif

                        @if (auth()->check() && auth()->user()->user_type === 'coordinator' && $event->coordinator_id === auth()->user()->coordinator->id)
                            <button wire:click="hideComment({{ $comment->id }})" onclick="return confirm('Tem certeza que deseja ocultar este comentário?')"
                                class="text-red-500 hover:text-red-700">
                                Ocultar
                            </button>
                        @endif
                    </div>

                    {{-- Respostas (collapse) --}}
                    <div x-show="showReplies" x-transition>
                        @foreach($comment->replies as $reply)
                            @include('partials.comments.comment-reply', ['reply' => $reply])
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- Respostas aninhadas --}}
            @if ($comment->replies->count())
                <div class="mt-4 pl-12 border-l border-gray-200 space-y-4">
                    @foreach($comment->replies as $reply)
                        @php
                            $replyProfileRoute = ($reply->user_id === auth()->id()) ? 'profile.show' : 'profile.view';
                        @endphp
                        <div class="flex items-start gap-4">
                             <a href="{{ route($replyProfileRoute, $reply->user->id) }}">
                                <img src="{{ $reply->user->user_icon_url }}" class="w-8 h-8 rounded-full shadow-sm">
                            </a>
                            <div class="flex-1 space-y-1">
                                <div class="flex items-center gap-2">
                                    <a href="{{ route($replyProfileRoute, $reply->user->id) }}" class="font-bold text-gray-800 hover:underline">
                                        {{ $reply->user->name }}
                                    </a>
                                    @if ($reply->user->user_type === 'coordinator')
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                            Coordenador
                                        </span>
                                    @endif
                                    <span class="text-sm text-gray-500">• {{ $reply->created_at->diffForHumans() }}</span>
                                    @if($reply->isEdited())
                                        <span class="text-xs text-gray-400 italic">(editado)</span>
                                    @endif
                                </div>
                                <p class="text-gray-700 text-sm">{{ $reply->comment }}</p>

                                {{-- Botões de Ação para a Resposta --}}
                                <div class="flex items-center gap-4 text-xs mt-2">
                                    {{-- Reações --}}
                                    <button wire:click="reactToComment({{ $reply->id }}, 'like')"
                                        class="flex items-center gap-1 p-1 px-2 rounded-full transition-colors 
                                            {{ auth()->user()->commentReactions->where('comment_id', $reply->id)->where('type', 'like')->count() > 0 
                                                ? 'bg-blue-100 text-blue-600 font-semibold' 
                                                : 'text-gray-600 hover:bg-gray-100' }}">
                                        <i class="far fa-thumbs-up"></i>
                                        <span>{{ $reply->reactions->where('type', 'like')->count() }}</span>
                                    </button>
                                    <button wire:click="reactToComment({{ $reply->id }}, 'dislike')"
                                        class="flex items-center gap-1 p-1 px-2 rounded-full transition-colors 
                                            {{ auth()->user()->commentReactions->where('comment_id', $reply->id)->where('type', 'dislike')->count() > 0 
                                                ? 'bg-red-100 text-red-600 font-semibold' 
                                                : 'text-gray-600 hover:bg-gray-100' }}">
                                        <i class="far fa-thumbs-down"></i>
                                        <span>{{ $reply->reactions->where('type', 'dislike')->count() }}</span>
                                    </button>
                                    
                                    {{-- Botões de Ação --}}
                                    @if($reply->user_id === auth()->id())
                                        <button wire:click="editComment({{ $reply->id }})" class="text-gray-600 hover:text-yellow-600">Editar</button>
                                        <button wire:click="deleteComment({{ $reply->id }})" onclick="return confirm('Tem certeza que deseja excluir?')" class="text-red-500 hover:text-red-700">Excluir</button>
                                    @endif
                                    
                                    @if (auth()->check() && auth()->user()->user_type === 'coordinator' && $event->coordinator_id === auth()->user()->coordinator->id)
                                        <button wire:click="hideComment({{ $reply->id }})" onclick="return confirm('Tem certeza que deseja ocultar este comentário?')"
                                            class="text-red-500 hover:text-red-700">
                                            Ocultar
                                        </button>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    @empty
        <p class="text-gray-500 text-center text-sm p-4">Nenhum comentário ainda. Seja o primeiro a comentar!</p>
    @endforelse
</div>