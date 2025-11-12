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
                class="flex items-center gap-4 p-3 my-2 rounded-xl transition-all duration-300 ease-in-out cursor-pointer shadow-sm
               @if ($isUnread) {{-- ESTILO NÃO LIDA (Prioridade) --}}
                   bg-red-50 border-l-4 border-red-500 hover:bg-red-100/70
               @elseif ($isCurrentlyActive)
                   {{-- ESTILO CONVERSA ATIVA (Selecionada) --}}
                   bg-red-100/70 border-l-4 border-red-500 hover:bg-red-200/80
               @else
                   {{-- ESTILO PADRÃO (Lida) --}}
                   bg-white border border-gray-100 hover:bg-gray-50 @endif
               focus:outline-none focus:ring-2 focus:ring-red-400 focus:ring-offset-2">

                <div class="relative shrink-0">
                    {{-- ÍCONE/AVATAR DO USUÁRIO com Fallback (Melhorado o estilo da borda) --}}
                    @if (isset($conversation['user']->profile_photo_url) && !empty($conversation['user']->profile_photo_url))
                        <img src="{{ $conversation['user']->user_icon_url }}" alt="{{ $conversation['user']->name }}"
                            class="size-12 rounded-full object-cover border-2 @if ($isUnread || $isCurrentlyActive) border-red-500 @else border-gray-200 @endif shadow-sm">
                    @elseif(isset($conversation['user']->name))
                        <div
                            class="size-12 rounded-full bg-gray-200 flex items-center justify-center border-2 @if ($isUnread || $isCurrentlyActive) border-red-500 @else border-gray-200 @endif shadow-sm">
                            <span
                                class="text-2xl font-semibold text-gray-500">{{ strtoupper(substr($conversation['user']->name, 0, 2)) }}</span>
                        </div>
                    @else
                        <div
                            class="size-12 rounded-full bg-gray-200 flex items-center justify-center border-2 border-gray-100 shadow-sm">
                            <i class="ph-fill ph-user text-2xl text-gray-500"></i>
                        </div>
                    @endif

                    {{-- INDICADOR DE ATIVO: --}}
                    @if ($isCurrentlyActive)
                        <span
                            class="absolute bottom-0 right-0 block h-3 w-3 rounded-full ring-2 ring-white bg-green-500"
                            title="Conversa Ativa"></span>
                    @endif
                </div>

                <div class="flex-1 min-w-0">
                    <div class="flex justify-between items-start gap-2">
                        {{-- Nome do usuário --}}
                        <p
                            class="text-base font-bold truncate
                    @if ($isUnread) text-red-700 @else text-gray-800 @endif">
                            {{ $conversation['user']->name }}
                        </p>

                        {{-- Contagem de Não Lidas ou Timestamp --}}
                        @if ($isUnread)
                            {{-- Badge de Contagem (Melhorado) --}}
                            <span
                                class="inline-flex items-center justify-center h-5 min-w-5 px-1.5 text-xs font-bold rounded-full text-white bg-red-600 shadow-md shrink-0 mt-1">
                                {{ $conversation['unread_count'] > 9 ? '9+' : $conversation['unread_count'] }}
                            </span>
                        @else
                            {{-- Timestamp (Posicionamento e tamanho melhorados) --}}
                            <span class="text-xs text-gray-400 whitespace-nowrap shrink-0 mt-1">
                                {{ str_replace([' hours', ' hour', ' minutes', ' minute', ' days', ' day'], ['h', 'h', 'm', 'm', 'd', 'd'], $conversation['last_message_time']) }}
                            </span>
                        @endif
                    </div>

                    {{-- Última Mensagem --}}
                    <p
                        class="text-sm truncate mt-1 leading-snug
                @if ($isUnread) text-gray-700 font-semibold @else text-gray-500 @endif">

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
