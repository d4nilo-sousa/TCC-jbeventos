<div>
    <div class="max-h-[500px] overflow-y-auto custom-scrollbar p-1">
        @forelse ($conversations as $conversation)

            {{-- Cartão de Conversa --}}
            <a href="{{ route('chat.show', ['user' => $conversation['user']->id]) }}"
                class="grid grid-cols-[auto_1fr] items-center gap-3 p-3 my-1 rounded-xl transition-all duration-300 ease-in-out cursor-pointer
                    border border-transparent hover:border-red-200
                    hover:bg-red-50 hover:shadow-lg
                    @if($conversation['unread_count'] > 0) bg-red-50 border-red-100 @else bg-white @endif"
            >

                <div class="relative shrink-0">
                    {{-- ÍCONE/AVATAR DO USUÁRIO com Fallback --}}
                    @if(isset($conversation['user']->profile_photo_url) && !empty($conversation['user']->profile_photo_url))
                        <img src="{{ $conversation['user']->user_icon_url }}"
                            alt="{{ $conversation['user']->name }}"
                            class="size-12 rounded-full object-cover border-2 border-gray-100 shadow-sm">
                    @elseif(isset($conversation['user']->name))
                        {{-- Fallback em caso de ausência de foto de perfil --}}
                        <div class="size-12 rounded-full bg-gray-200 flex items-center justify-center border-2 border-gray-100 shadow-sm">
                            {{-- Exibe as duas primeiras letras do nome --}}
                            <span class="text-xl font-semibold text-gray-500">{{ strtoupper(substr($conversation['user']->name, 0, 2)) }}</span>
                        </div>
                    @else
                        {{-- Fallback genérico se nem o nome estiver disponível --}}
                         <div class="size-12 rounded-full bg-gray-200 flex items-center justify-center border-2 border-gray-100 shadow-sm">
                            <i class="ph-fill ph-user text-xl text-gray-500"></i>
                        </div>
                    @endif

                    {{-- Ponto de Online/Status --}}
                    {{-- <span class="absolute bottom-0 right-0 block h-3 w-3 rounded-full ring-2 ring-white bg-green-400"></span> --}}
                </div>

                <div class="flex-1 min-w-0">
                    <div class="flex justify-between items-center gap-2">

                        {{-- Nome do usuário --}}
                        <p class="text-sm font-semibold truncate 
                            @if($conversation['unread_count'] > 0) text-red-700 @else text-gray-800 @endif">
                            {{ $conversation['user']->name }}
                        </p>

                        {{-- Timestamp ou Contagem de Não Lidas --}}
                        @if ($conversation['unread_count'] > 0)
                            {{-- Contagem (Balão de Mensagens Não Lidas) - Cor mais vibrante --}}
                            <span class="inline-flex items-center justify-center h-5 min-w-5 px-1.5 text-xs font-bold rounded-full text-white bg-red-600 shadow-md shrink-0">
                                {{ $conversation['unread_count'] > 9 ? '9+' : $conversation['unread_count'] }}
                            </span>
                        @else
                            {{-- Tempo da Última Mensagem --}}
                            <span class="text-xs text-gray-400 whitespace-nowrap shrink-0">
                                {{ str_replace([' hours', ' hour', ' minutes', ' minute', ' days', ' day'], ['h', 'h', 'm', 'm', 'd', 'd'], $conversation['last_message_time']) }}
                            </span>
                        @endif
                    </div>

                    {{-- Última Mensagem --}}
                    <p class="text-xs truncate mt-1
                        @if($conversation['unread_count'] > 0) text-gray-700 font-bold @else text-gray-500 @endif">
                        {{ \Illuminate\Support\Str::limit($conversation['last_message'], 60) }}
                    </p>

                </div>
            </a>
        @empty
            <div class="p-4 text-center text-sm text-gray-500">
                🎉 Tudo em dia! Nenhuma conversa nova.
            </div>
        @endforelse
    </div>

    {{-- Estilos da Scrollbar --}}
    <style>
        .custom-scrollbar::-webkit-scrollbar {
            width: 8px;
        }
        .custom-scrollbar::-webkit-scrollbar-track {
            background: transparent;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb {
            background-color: rgba(156, 163, 175, 0.6); /* gray-400 */
            border-radius: 4px;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background-color: rgba(107, 114, 128, 0.8); /* gray-500 */
        }
    </style>
</div>