@php
    $replyProfileRoute = ($reply->user_id === auth()->id()) ? 'profile.show' : 'profile.view';
@endphp

<div class="mt-4 ml-6 p-3 rounded-md bg-gray-50 border-l-4 border-blue-100 flex gap-2" x-data="{ showReplies:false }">

    {{-- Foto com popover --}}
    <div class="relative"
         x-data="{ open:false }"
         @mouseenter="open=true" @mouseleave="open=false">

        <a href="{{ route($replyProfileRoute, $reply->user->id) }}">
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
            <a href="{{ route($replyProfileRoute, $reply->user->id) }}"
               class="block mt-2 text-center text-sm bg-blue-500 hover:bg-blue-600 text-white py-1 rounded">
                Ver perfil completo
            </a>
        </div>
    </div>

    {{-- Conteúdo da resposta --}}
    <div class="flex-1">
        <div class="flex items-center gap-2">
            <a href="{{ route($replyProfileRoute, $reply->user->id) }}"
               class="font-semibold text-blue-600 hover:underline text-sm">
                {{ $reply->user->name }}
            </a>
            <span class="text-xs text-gray-400">
                {{ $reply->created_at->diffForHumans() }}
            </span>
            @if($reply->isEdited())
                <span class="text-xs text-gray-500">(editado)</span>
            @endif
        </div>

        <p class="text-sm text-gray-700 mt-1">{{ $reply->comment }}</p>

        {{-- Mídia da resposta --}}
        @if($reply->media_path)
            <div class="mt-2">
                @if(Str::endsWith($reply->media_path, ['jpg','jpeg','png','webp']))
                    <img src="{{ asset('storage/'.$reply->media_path) }}" class="w-32 rounded-md shadow">
                @elseif(Str::endsWith($reply->media_path, 'mp4'))
                    <video controls class="w-32 rounded-md shadow">
                        <source src="{{ asset('storage/'.$reply->media_path) }}" type="video/mp4">
                    </video>
                @else
                    <a href="{{ asset('storage/'.$reply->media_path) }}" target="_blank"
                       class="text-blue-600 underline">📎 Ver arquivo</a>
                @endif
            </div>
        @endif

        {{-- Botões --}}
        <div class="flex items-center gap-4 text-xs text-gray-600 mt-2">
            <button wire:click="reactToComment({{ $reply->id }}, 'like')"
                    class="flex items-center gap-1 hover:underline text-green-600">
                👍 <span>{{ $reply->reactions->where('type', 'like')->count() }}</span>
            </button>
            <button wire:click="reactToComment({{ $reply->id }}, 'dislike')"
                    class="flex items-center gap-1 hover:underline text-red-600">
                👎 <span>{{ $reply->reactions->where('type', 'dislike')->count() }}</span>
            </button>

            <button wire:click="setReply({{ $reply->id }})" class="hover:underline">Responder</button>

            {{-- Contador de respostas --}}
            @if($reply->replies->count())
                <button @click="showReplies=!showReplies" class="hover:underline text-blue-600">
                    💬 {{ $reply->replies->count() }} resposta{{ $reply->replies->count()>1?'s':'' }}
                </button>
            @endif

            @if($reply->user_id === auth()->id())
                <button wire:click="editComment({{ $reply->id }})" class="hover:underline">Editar</button>
                <button wire:click="deleteComment({{ $reply->id }})"
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
                    <form action="{{ route('events.updateComment', $reply->id) }}" method="POST"
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

        {{-- Respostas aninhadas (collapse) --}}
        <div x-show="showReplies" x-transition>
            @foreach($reply->replies as $nestedReply)
                @include('partials.comment-reply', ['reply' => $nestedReply])
            @endforeach
        </div>
    </div>
</div>
