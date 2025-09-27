<div>
    <div class="max-h-96 overflow-y-auto custom-scrollbar p-1">
        @forelse ($conversations as $conversation)
            <a href="{{ route('chat.show', ['user' => $conversation['user']->id]) }}"
               class="grid grid-cols-[auto_1fr] items-start gap-3 p-3 my-1 rounded-xl transition-all duration-200 ease-in-out transform hover:scale-[1.02] hover:bg-gray-100 dark:hover:bg-gray-700 shadow-sm hover:shadow-md">

                <div class="relative shrink-0">
                    <img src="{{ $conversation['user']->profile_photo_url ?? 'https://ui-avatars.com/api/?name=' . urlencode($conversation['user']->name) }}"
                         alt="{{ $conversation['user']->name }}"
                         class="size-12 rounded-full object-cover border-2 border-white dark:border-gray-800">
                    <span class="absolute bottom-0 right-0 block h-3 w-3 rounded-full ring-2 ring-white dark:ring-gray-800 bg-green-400"></span>
                </div>

                <div class="flex-1 min-w-0">
                    <div class="flex justify-between items-baseline gap-2">
                        <p class="text-sm font-bold text-gray-800 dark:text-gray-200">
                            {{ $conversation['user']->name }}
                        </p>
                        <span class="text-xs text-gray-400 dark:text-gray-500 whitespace-nowrap shrink-0">
                            {{-- Formata a data de forma curta --}}
                            {{ str_replace([' hours', ' hour', ' minutes', ' minute', ' days', ' day'], ['h', 'h', 'm', 'm', 'd', 'd'], $conversation['last_message_time']) }}
                        </span>
                    </div>

                    <p class="text-xs text-gray-500 truncate dark:text-gray-400 mt-0.5">
                        {{ \Illuminate\Support\Str::limit($conversation['last_message'], 25) }}
                    </p>
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