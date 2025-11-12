<div class="hidden sm:block" x-data="{ open: false }" @click.away="open = false" @keydown.escape.window="open = false"
    x-init='
        let count = @js($unreadCount);
        document.querySelector("#notification-count").textContent = count > 9 ? "9+" : count;

        Livewire.on("notificationsUpdated", count => {
            document.querySelector("#notification-count").textContent = count > 9 ? "9+" : count;
        });

        // ✅ CORREÇÃO: Escuta segura do evento Livewire dentro do x-init, que resolve o erro de PHP.
        // O evento Livewire 3 (dispatch) é capturado, e o payload é acessado via (event).url.
        Livewire.on("navigateToUrl", (event) => window.location.href = event.url);
     '
    wire:poll.100ms="refreshUnreadCount">

    {{-- Ícone do Sino --}}
    <button @click="open = !open" :class="open ? 'ring-2 ring-red-500 shadow-sm' : ''"
        class="relative flex items-center size-9 rounded-full justify-center text-gray-700 bg-gray-100 hover:bg-gray-200 transition">
        <i class="ph-fill ph-bell text-lg"></i>

        {{-- Badge --}}
        <span id="notification-count"
            class="absolute top-0 right-0 inline-flex items-center justify-center w-4 h-4 text-[10px] font-bold leading-none text-white bg-red-600 rounded-full ring-2 ring-white">
            {{ $unreadCount > 9 ? '9+' : $unreadCount }}
        </span>
    </button>

    {{-- Dropdown --}}
    <div x-show="open" x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 scale-100"
        x-transition:leave-end="opacity-0 scale-95"
        class="absolute right-0 mt-2 !w-[600px] min-w-[600px] max-w-[700px]
                bg-white rounded-2xl border border-gray-200 shadow-2xl p-2
                overflow-hidden z-50"
        style="transform: translateX(-4rem); transform-origin: top right;">


        {{-- Cabeçalho --}}
        <div class="mt-2 mb-2 pl-2 pr-4">
            <p class="text-xl sm:text-2xl font-extrabold text-stone-800 tracking-tight drop-shadow-sm">
                Minhas Notificações
            </p>
            <div class="w-16 h-1 bg-red-500 rounded-full mt-1 shadow-lg"></div>
        </div>

        {{-- Corpo --}}
        <div class="max-h-[500px] overflow-y-auto custom-scrollbar p-1">
            @forelse ($notifications as $notification)
                @php
                    $message = $notification->data['message'] ?? 'Notificação sem conteúdo.';
                    $url = $notification->data['event_url'] ?? '#';
                    $type = $notification->data['type'] ?? 'default';
                    $isUnread = is_null($notification->read_at);
                @endphp
                <div
                    class="flex items-start gap-4 p-4 my-2 mx-1 rounded-xl transition-all duration-200 shadow-sm
                @if ($isUnread) bg-red-50 border-l-4 border-red-500 hover:bg-red-100/70
                @else bg-white border border-gray-100 hover:bg-gray-50 @endif
                focus:outline-none focus:ring-2 focus:ring-red-400 focus:ring-offset-2">

                    @if ($isUnread)
                        <div class="shrink-0 pt-1">
                            <span class="block size-2 bg-red-600 rounded-full animate-pulse"></span>
                        </div>
                    @else
                        <div class="shrink-0 pt-1 size-2"></div>
                    @endif

                    {{-- Ícone --}}
                    <div class="shrink-0 mt-[1px]">
                        <i
                            class="ph-duotone ph-bell-simple text-xl @if ($isUnread) text-red-600 @else text-gray-500 @endif"></i>
                    </div>

                    {{-- Texto e Data --}}
                    <div class="flex flex-col min-w-0 flex-1">
                        <p
                            class="text-sm font-semibold @if ($isUnread) text-red-800 @else text-gray-800 @endif leading-snug">
                            {!! $message !!}
                        </p>
                        <p
                            class="text-xs @if ($isUnread) text-red-500 @else text-gray-400 @endif mt-1">
                            {{ \Carbon\Carbon::parse($notification->created_at)->diffForHumans() }}
                        </p>
                    </div>
                </div>
            @empty
                <div class="pb-4 pt-1 text-center text-base text-gray-500">
                    🎉 Tudo em dia! Nenhuma notificação nova.
                </div>
            @endforelse
        </div>

        {{-- Rodapé --}}
        @unless ($notifications->isEmpty())
            <div class="p-3 text-center border-t border-gray-100 bg-gray-50 rounded-b-2xl">
                <button wire:click="markAsRead"
                    class="w-full inline-flex items-center justify-center px-4 py-2 border border-transparent text-sm font-bold rounded-lg shadow-md
                    text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500
                    transition ease-in-out duration-150 transform hover:scale-[1.01] active:scale-[0.99]">
                    <i class="ph-fill ph-check-circle mr-2 text-lg"></i>
                    Limpar Notificações
                </button>
            </div>
        @endunless

        {{-- Scrollbar --}}
        <style>
            .custom-scrollbar::-webkit-scrollbar {
                width: 8px;
            }

            .custom-scrollbar::-webkit-scrollbar-track {
                background: transparent;
            }

            .custom-scrollbar::-webkit-scrollbar-thumb {
                background-color: rgba(156, 163, 175, 0.6);
                border-radius: 4px;
            }

            .custom-scrollbar::-webkit-scrollbar-thumb:hover {
                background-color: rgba(107, 114, 128, 0.8);
                border-radius: 4px;
            }
        </style>

    </div>
</div>
