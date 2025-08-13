<div>
    <div class="max-h-80 overflow-y-auto custom-scrollbar">
        @forelse ($conversations as $conversation)
            <a href="{{ route('chat.show', ['user' => $conversation['user']->id]) }}"
               class="flex items-center gap-3 p-3 hover:bg-gray-100 rounded-xl transition-colors">
                <img src="{{ $conversation['user']->profile_photo_url ?? 'https://ui-avatars.com/api/?name=' . urlencode($conversation['user']->name) }}"
                     alt="{{ $conversation['user']->name }}"
                     class="w-12 h-12 rounded-full object-cover border border-gray-200 shadow-sm">

                <div class="flex-1 min-w-0">
                    <p class="text-sm font-semibold truncate text-gray-800">
                        {{ $conversation['user']->name }}
                    </p>
                    <p class="text-xs text-gray-500 truncate">
                        {{ $conversation['last_message'] }}
                    </p>
                </div>

                <span class="text-xs text-gray-400 whitespace-nowrap">
                    {{ $conversation['last_message_time'] }}
                </span>
            </a>
        @empty
            <div class="p-4 text-center text-sm text-gray-500">
                Nenhuma conversa encontrada
            </div>
        @endforelse
    </div>

    <style>
        .custom-scrollbar::-webkit-scrollbar {
            width: 6px;
        }
        .custom-scrollbar::-webkit-scrollbar-track {
            background: transparent;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb {
            background-color: rgba(156, 163, 175, 0.5);
            border-radius: 3px;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background-color: rgba(107, 114, 128, 0.7);
        }
    </style>
</div>
