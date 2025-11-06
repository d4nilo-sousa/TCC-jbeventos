<div>
    {{-- Cabeçalho com título e linha vermelha --}}
    <div class="mt-2 mb-2 pl-2 pr-4">
        <p class="text-xl sm:text-2xl font-extrabold text-stone-800 tracking-tight drop-shadow-sm">
            Mensagens
        </p>
        <div class="w-16 h-1 bg-red-500 rounded-full mt-1 shadow-lg"></div>
    </div>
    <div class="max-h-[500px] overflow-y-auto custom-scrollbar p-1">
        
        {{-- Adicione esta linha: Defina o ID do chat ativo. Assuma 'null' se não houver chat aberto. --}}
        @php
            // Este valor deve ser passado para o view (e.g., vindo do controller ou da URL)
            $currentChatUserId = $currentChatUserId ?? null; 
        @endphp

        @forelse ($conversations as $conversation)
            
            {{-- Nova Condição: A conversa está "ativa" (usuário está nela)? --}}
            @php
                $isCurrentlyActive = $conversation['user']->id === $currentChatUserId;
                $hasUnreadMessages = $conversation['unread_count'] > 0;
                
                // A conversa é considerada "não lida" se: 
                // 1. Tiver mensagens não lidas E 
                // 2. O usuário NÃO estiver nela no momento.
                $isUnread = $hasUnreadMessages && !$isCurrentlyActive;
            @endphp

            <a href="{{ route('chat.show', ['user' => $conversation['user']->id]) }}"
                class="grid grid-cols-[auto_1fr] items-center gap-3 p-3 my-1 rounded-xl transition-all duration-300 ease-in-out cursor-pointer
                border border-gray-200 text-gray-800
                @if ($isUnread) 
                    bg-red-50 border-red-100 
                @else 
                    bg-gray-100 hover:bg-gray-200 
                    {{-- Adiciona estilo especial se for a conversa ATIVA, mesmo que já lida --}}
                    @if ($isCurrentlyActive) border-red-400 bg-red-100/50 hover:bg-red-100/70 @endif
                @endif">

                <div class="relative shrink-0">
                    {{-- ÍCONE/AVATAR DO USUÁRIO com Fallback (Seção omitida para brevidade, sem alteração) --}}
                    @if (isset($conversation['user']->profile_photo_url) && !empty($conversation['user']->profile_photo_url))
                        <img src="{{ $conversation['user']->user_icon_url }}" alt="{{ $conversation['user']->name }}"
                            class="size-12 rounded-full object-cover border-2 border-red-500 shadow-sm">
                    @elseif(isset($conversation['user']->name))
                        <div
                            class="size-12 rounded-full bg-gray-200 flex items-center justify-center border-2 border-red-500 shadow-sm">
                            <span
                                class="text-2xl font-semibold text-gray-500">{{ strtoupper(substr($conversation['user']->name, 0, 2)) }}</span>
                        </div>
                    @else
                        <div
                            class="size-12 rounded-full bg-gray-200 flex items-center justify-center border-2 border-gray-100 shadow-sm">
                            <i class="ph-fill ph-user text-2xl text-gray-500"></i>
                        </div>
                    @endif
                    
                    {{-- ADICIONA INDICADOR DE ATIVO: --}}
                    @if ($isCurrentlyActive)
                        <span class="absolute top-0 right-0 block h-3 w-3 rounded-full ring-2 ring-white bg-green-500" title="Conversa Ativa"></span>
                    @endif
                </div>

                <div class="flex-1 min-w-0">
                    <div class="flex justify-between items-center gap-2">
                        {{-- Nome do usuário --}}
                        <p
                            class="text-base font-semibold truncate 
                            @if ($isUnread) text-red-700 @else text-gray-800 @endif">
                            {{ $conversation['user']->name }}
                        </p>

                        {{-- Timestamp ou Contagem de Não Lidas --}}
                        @if ($isUnread)
                            <span
                                class="inline-flex items-center justify-center h-6 min-w-6 px-2 text-sm font-bold rounded-full text-white bg-red-600 shadow-md shrink-0 relative top-2.5">
                                {{ $conversation['unread_count'] > 9 ? '9+' : $conversation['unread_count'] }}
                            </span>
                        @else
                            <span class="text-sm text-gray-400 whitespace-nowrap shrink-0 relative top-2.5">
                                {{ str_replace([' hours', ' hour', ' minutes', ' minute', ' days', ' day'], ['h', 'h', 'm', 'm', 'd', 'd'], $conversation['last_message_time']) }}
                            </span>
                        @endif
                    </div>

                    {{-- Última Mensagem MODIFICADA (Seção omitida para brevidade, sem alteração) --}}
                    <p
                        class="text-sm truncate mt-1
                        @if ($isUnread) text-gray-700 font-bold @else text-gray-500 @endif">

                        @php
                            $message = $conversation['last_message'];
                        @endphp

                        @if (str_starts_with($message, '📷'))
                            <span class="text-gray-500 italic">📷 Imagem enviada</span>
                        @elseif (str_starts_with($message, '🎬'))
                            <span class="text-gray-500 italic">🎬 Vídeo enviado</span>
                        @elseif (str_starts_with($message, '🎞️'))
                            <span class="text-gray-500 italic">🎞️ GIF enviado</span>
                        @elseif (str_starts_with($message, '📄'))
                            <span class="text-gray-500 italic">📄 Documento enviado</span>
                        @else
                            {{ \Illuminate\Support\Str::limit($message, 60) }}
                        @endif

                    </p>
                </div>
            </a>
        @empty
            <div class="pb-4 pt-1 text-center text-base text-gray-500">
                🎉 Tudo em dia! Nenhuma conversa nova.
            </div>
        @endforelse
    </div>

    {{-- Estilos da Scrollbar (Omitida para brevidade) --}}
    <style>
        .custom-scrollbar::-webkit-scrollbar {
            width: 8px;
        }

        .custom-scrollbar::-webkit-scrollbar-track {
            background: transparent;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb {
            background-color: rgba(156, 163, 175, 0.6);
            /* gray-400 */
            border-radius: 4px;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background-color: rgba(107, 114, 128, 0.8);
            /* gray-500 */
            border-radius: 4px;
        }
    </style>
</div>