<div>
    {{-- Poll de backup a cada 10s, mas eventos broadcast já atualizam --}}
    <div wire:poll.10000ms="getUnreadCount">
        @if ($unreadCount > 0)
            <span
                class="absolute top-0 right-0 -mt-1 -mr-1 size-5 rounded-full flex items-center justify-center text-xs font-semibold text-white bg-red-500 ring-2 ring-white transform scale-100 transition-transform duration-300 animate-pulse-slow z-10"
                style="animation-duration: 2s;">
                {{ $unreadCount > 9 ? '9+' : $unreadCount }}
            </span>
        @endif
    </div>

    {{-- CSS simples para a animação de pulso --}}
    @once
        <style>
            @keyframes pulse-slow {

                0%,
                100% {
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
