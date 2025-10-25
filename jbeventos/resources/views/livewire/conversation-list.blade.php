<div>
    <div class="max-h-96 overflow-y-auto custom-scrollbar p-1">
        @forelse ($conversations as $conversation)
            <a href="{{ route('chat.show', ['user' => $conversation['user']->id]) }}"
               class="grid grid-cols-[auto_1fr] items-start gap-3 p-3 my-1 rounded-xl transition-all duration-200 ease-in-out transform hover:scale-[1.02] hover:bg-gray-100 dark:hover:bg-gray-700 shadow-sm hover:shadow-md">

                <div class="relative shrink-0">
                    </div>

                <div class="flex-1 min-w-0">
                    <div class="flex justify-between items-baseline gap-2">
                        <p class="text-sm font-bold text-gray-800 dark:text-gray-200">
                            {{ $conversation['user']->name }}
                        </p>
                        
                        @if ($conversation['unread_count'] > 0)
                        <span class="inline-flex items-center justify-center h-5 px-2 text-xs font-semibold rounded-full text-white bg-indigo-500 shrink-0">
                            {{ $conversation['unread_count'] > 9 ? '9+' : $conversation['unread_count'] }}
                        </span>
                        @else
                        <span class="text-xs text-gray-400 dark:text-gray-500 whitespace-nowrap shrink-0">
                            {{ str_replace([' hours', ' hour', ' minutes', ' minute', ' days', ' day'], ['h', 'h', 'm', 'm', 'd', 'd'], $conversation['last_message_time']) }}
                        </span>
                        @endif
                    </div>
                </div> 
            </a>
        @empty
            <div class="p-4 text-center text-sm text-gray-500 dark:text-gray-400">
                Nenhuma conversa encontrada.
            </div>
        @endforelse
    </div>

    <style>
        .custom-scrollbar::-webkit-scrollbar {
            width: 8px;
        }
        .custom-scrollbar::-webkit-scrollbar-track {
            background: transparent;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb {
            background-color: rgba(156, 163, 175, 0.4);
            border-radius: 4px;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background-color: rgba(107, 114, 128, 0.6);
        }
    </style>
</div>