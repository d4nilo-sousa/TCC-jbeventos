<div>
    {{-- Poll a cada 10s, mas eventos broadcast também atualizam --}}
    <div wire:poll.100ms="getUnreadCount">
        <span id="notification-count"
            class="absolute top-0 right-0 inline-flex items-center justify-center w-4 h-4 text-[10px] font-bold leading-none text-white bg-red-600 rounded-full ring-2 ring-white">
            {{ $unreadCount > 9 ? '9+' : $unreadCount }}
        </span>
    </div>

    {{-- CSS simples para a animação de pulso --}}
    @once
        <style>
            @keyframes pulse-slow {
                0%, 100% {
                    opacity: 1;
                    transform: scale(1);
                }
                50% {
                    opacity: 0.8;
                    transform: scale(1.1);
                }
            }

            .animate-pulse-slow {
                animation: pulse-slow 2s infinite ease-in-out;
            }
        </style>
    @endonce
</div>
