<div class="space-y-4" x-data>
    {{-- Formulário de comentário / edição --}}
    <div class="bg-gray-50 p-4 rounded-lg border">
        <textarea wire:model.defer="commentText"
                  class="w-full p-2 border rounded-md focus:ring focus:ring-blue-200"
                  placeholder="{{ $replyTo ? 'Respondendo a um comentário...' : 'Escreva um comentário...' }}"></textarea>

        <input type="file" wire:model="media" class="mt-2 text-sm">

        <div class="flex gap-2 mt-3">
            @if($editingCommentId)
                <button wire:click="updateComment"
                        class="bg-yellow-500 hover:bg-yellow-600 text-white px-3 py-1 rounded-md text-sm">
                    ✏ Atualizar
                </button>
            @else
                <button wire:click="addComment"
                        class="bg-blue-500 hover:bg-blue-600 text-white px-3 py-1 rounded-md text-sm">
                    💬 Comentar
                </button>
            @endif

            @if($replyTo)
                <button wire:click="cancelReply"
                        class="text-sm text-gray-600 hover:underline">
                    Cancelar resposta
                </button>
            @endif
        </div>
    </div>

    {{-- Listagem de comentários --}}
    @forelse($comments as $comment)
        <div class="p-4 bg-white shadow-sm rounded-lg border">
            <div class="flex items-start gap-3">

                {{-- Foto com popover --}}
                <div class="relative" 
                     x-data="{ open:false }" 
                     @mouseenter="open=true" @mouseleave="open=false">
                    
                    <a href="{{ route('profile.show', $comment->user->id) }}">
                        <img src="{{ $comment->user->user_icon_url }}" 
                             class="w-10 h-10 rounded-full shadow-sm cursor-pointer">
                    </a>

                    {{-- Popover --}}
                    <div x-show="open"
                         x-transition
                         class="absolute top-12 left-0 bg-white border rounded-lg shadow-lg p-3 w-56 z-10"
                         @mouseenter="open=true" @mouseleave="open=false">
                        <div class="flex items-center gap-2">
                            <img src="{{ $comment->user->user_icon_url }}" class="w-10 h-10 rounded-full">
                            <div>
                                <p class="font-semibold text-gray-800">{{ $comment->user->name }}</p>
                                <p class="text-xs text-gray-500">
                                    {{ $comment->user->email }}
                                </p>
                            </div>
                        </div>
                        <p class="text-sm text-gray-600 mt-2 line-clamp-3">
                            {{ $comment->user->bio ?? 'Este usuário ainda não escreveu uma biografia.' }}
                        </p>
                        <a href="{{ route('profile.show', $comment->user->id) }}"
                           class="block mt-2 text-center text-sm bg-blue-500 hover:bg-blue-600 text-white py-1 rounded">
                            Ver perfil completo
                        </a>
                    </div>
                </div>

                {{-- Conteúdo do comentário --}}
                <div class="flex-1">
                    <div class="flex items-center gap-2">
                        <a href="{{ route('profile.show', $comment->user->id) }}"
                           class="font-semibold text-blue-600 hover:underline">
                            {{ $comment->user->name }}
                        </a>
                        <span class="text-xs text-gray-400">
                            {{ $comment->created_at->diffForHumans() }}
                        </span>
                        @if($comment->isEdited())
                            <span class="text-xs text-gray-500">(editado)</span>
                        @endif
                    </div>

                    <p class="mt-1 text-gray-700">{{ $comment->comment }}</p>

                    {{-- Mídia --}}
                    @if($comment->media_path)
                        <div class="mt-2">
                            @if(Str::endsWith($comment->media_path, ['jpg','jpeg','png','webp']))
                                <img src="{{ asset('storage/'.$comment->media_path) }}" class="w-40 rounded-md shadow">
                            @elseif(Str::endsWith($comment->media_path, 'mp4'))
                                <video controls class="w-40 rounded-md shadow">
                                    <source src="{{ asset('storage/'.$comment->media_path) }}" type="video/mp4">
                                </video>
                            @else
                                <a href="{{ asset('storage/'.$comment->media_path) }}" target="_blank"
                                   class="text-blue-600 underline">📎 Ver arquivo</a>
                            @endif
                        </div>
                    @endif

                    {{-- Botões --}}
                    <div class="text-sm text-gray-600 mt-2 flex gap-3">
                        <button wire:click="setReply({{ $comment->id }})"
                                class="hover:underline">Responder</button>

                        @if($comment->user_id === auth()->id())
                            <button wire:click="editComment({{ $comment->id }})"
                                    class="hover:underline">Editar</button>
                            <button wire:click="deleteComment({{ $comment->id }})"
                                    class="hover:underline text-red-500">Excluir</button>
                        @endif
                    </div>

                    {{-- Respostas --}}
                    @foreach($comment->replies as $reply)
                        <div class="mt-4 ml-6 p-3 rounded-md bg-gray-50 border-l-4 border-blue-100 flex gap-2">

                            {{-- Foto com popover --}}
                            <div class="relative" 
                                 x-data="{ open:false }" 
                                 @mouseenter="open=true" @mouseleave="open=false">
                                
                                <a href="{{ route('profile.show', $reply->user->id) }}">
                                    <img src="{{ $reply->user->user_icon_url }}" class="w-8 h-8 rounded-full cursor-pointer">
                                </a>

                                {{-- Popover --}}
                                <div x-show="open"
                                     x-transition
                                     class="absolute top-10 left-0 bg-white border rounded-lg shadow-lg p-3 w-56 z-10"
                                     @mouseenter="open=true" @mouseleave="open=false">
                                    <div class="flex items-center gap-2">
                                        <img src="{{ $reply->user->user_icon_url }}" class="w-10 h-10 rounded-full">
                                        <div>
                                            <p class="font-semibold text-gray-800">{{ $reply->user->name }}</p>
                                            <p class="text-xs text-gray-500">{{ $reply->user->email }}</p>
                                        </div>
                                    </div>
                                    <p class="text-sm text-gray-600 mt-2 line-clamp-3">
                                        {{ $reply->user->bio ?? 'Este usuário ainda não escreveu uma biografia.' }}
                                    </p>
                                    <a href="{{ route('profile.show', $reply->user->id) }}"
                                       class="block mt-2 text-center text-sm bg-blue-500 hover:bg-blue-600 text-white py-1 rounded">
                                        Ver perfil completo
                                    </a>
                                </div>
                            </div>

                            {{-- Conteúdo da resposta --}}
                            <div>
                                <a href="{{ route('profile.show', $reply->user->id) }}"
                                   class="font-semibold text-blue-600 hover:underline text-sm">
                                    {{ $reply->user->name }}
                                </a>
                                <p class="text-sm text-gray-700">{{ $reply->comment }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    @empty
        <p class="text-gray-500 text-sm">Nenhum comentário ainda. Seja o primeiro a comentar!</p>
    @endforelse
</div>
