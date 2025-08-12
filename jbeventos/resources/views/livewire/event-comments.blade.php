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
        @php
            $profileRoute = ($comment->user_id === auth()->id()) ? 'profile.show' : 'profile.view';
        @endphp
        <div class="p-4 bg-white shadow-sm rounded-lg border" x-data="{ showReplies:false }">
            <div class="flex items-start gap-3">

                {{-- Foto com popover --}}
                <div class="relative"
                     x-data="{ open:false }"
                     @mouseenter="open=true" @mouseleave="open=false">

                    <a href="{{ route($profileRoute, $comment->user->id) }}">
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
                                <p class="text-xs text-gray-500">{{ $comment->user->email }}</p>
                            </div>
                        </div>
                        <p class="text-sm text-gray-600 mt-2 line-clamp-3">
                            {{ $comment->user->bio ?? 'Este usuário ainda não escreveu uma biografia.' }}
                        </p>
                        <a href="{{ route($profileRoute, $comment->user->id) }}"
                           class="block mt-2 text-center text-sm bg-blue-500 hover:bg-blue-600 text-white py-1 rounded">
                            Ver perfil completo
                        </a>
                    </div>
                </div>

                {{-- Conteúdo do comentário --}}
                <div class="flex-1">
                    <div class="flex items-center gap-2">
                        <a href="{{ route($profileRoute, $comment->user->id) }}"
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
                    <div class="text-sm text-gray-600 mt-2 flex gap-4 items-center">
                        {{-- Likes e Dislikes --}}
                        <div class="flex items-center gap-3">
                            <button wire:click="reactToComment({{ $comment->id }}, 'like')"
                                    class="flex items-center gap-1 hover:underline text-green-600">
                                👍 <span>{{ $comment->reactions->where('type', 'like')->count() }}</span>
                            </button>
                            <button wire:click="reactToComment({{ $comment->id }}, 'dislike')"
                                    class="flex items-center gap-1 hover:underline text-red-600">
                                👎 <span>{{ $comment->reactions->where('type', 'dislike')->count() }}</span>
                            </button>
                        </div>

                        <button wire:click="setReply({{ $comment->id }})" class="hover:underline">Responder</button>

                        {{-- Contador de respostas --}}
                        @if($comment->replies->count())
                            <button @click="showReplies=!showReplies" class="hover:underline text-blue-600">
                                💬 {{ $comment->replies->count() }} resposta{{ $comment->replies->count()>1?'s':'' }}
                            </button>
                        @endif

                        @if($comment->user_id === auth()->id())
                            <button wire:click="editComment({{ $comment->id }})"
                                    class="hover:underline">Editar</button>
                            <button wire:click="deleteComment({{ $comment->id }})"
                                    class="hover:underline text-red-500">Excluir</button>
                        @endif

                        @if(auth()->check() && auth()->user()->user_type === 'coordinator') 
                        {{-- Só permite continuar se o usuário estiver logado e for coordenador --}}

                            @php
                                $loggedCoordinator = auth()->user()->coordinator;
                                // Pega o coordenador vinculado ao usuário logado
                            @endphp

                            @if($loggedCoordinator && $loggedCoordinator->id === $event->coordinator_id)
                            {{-- Garante que o coordenador logado é o responsável pelo evento --}}

                                {{-- Formulário para ocultar o comentário --}}
                                <form action="{{ route('events.updateComment', $comment->id) }}" method="POST"
                                onsubmit="return confirm('Tem certeza que deseja ocultar este comentário?')" class="inline">
                                    @csrf {{-- Proteção contra CSRF --}}
                                    @method('PATCH') {{-- Requisição será do tipo PATCH --}}
                                    <button type="submit" class="hover:underline text-gray-700">
                                        Ocultar
                                    </button>
                                </form>
                            @endif
                        @endif
                    </div>

                    {{-- Respostas (collapse) --}}
                    <div x-show="showReplies" x-transition>
                        @foreach($comment->replies as $reply)
                            @include('partials.comment-reply', ['reply' => $reply])
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    @empty
        <p class="text-gray-500 text-sm">Nenhum comentário ainda. Seja o primeiro a comentar!</p>
    @endforelse
</div>
