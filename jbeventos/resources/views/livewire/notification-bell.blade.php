<div class="hidden sm:block" x-data="{ open: false }" 
     @click.away="open = false" 
     @keydown.escape.window="open = false" 
     wire:poll.5000ms="refreshUnreadCount">

    <button @click="open = !open; if(open) $wire.markAsRead()" 
            :class="open ? 'ring-2 ring-red-500 shadow-sm' : ''"
            class="relative flex items-center size-9 rounded-full justify-center text-gray-700 bg-gray-100 hover:bg-gray-200 transition">
        
        <i class="ph-fill ph-bell text-lg"></i>

        {{-- Badge de notificação não lida --}}
        @if ($unreadCount > 0)
            <span class="absolute top-0 right-0 inline-flex items-center justify-center px-2 py-1 text-xs font-bold leading-none text-white transform translate-x-1/2 -translate-y-1/2 bg-red-600 rounded-full">
                {{ $unreadCount > 9 ? '9+' : $unreadCount }}
            </span>
        @endif
    </button>

    {{-- Conteúdo do dropdown (Notificações) --}}
    <div x-show="open" 
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-95"
         class="absolute right-0 mt-2 !w-[400px] bg-white rounded-2xl border border-gray-200 shadow-2xl p-2 overflow-hidden z-50"
         style="transform: translateX(-4rem); transform-origin: top right;">
         
        <div class="px-4 py-2 font-bold text-gray-800 border-b border-gray-100">
            Minhas Notificações
        </div>
        
        @forelse ($notifications as $notification)
            @php
                // Os campos 'message' e 'event_url' vêm do seu método toArray() nas Notifications
                $message = $notification->data['message'] ?? 'Notificação sem conteúdo.';
                $url = $notification->data['event_url'] ?? '#';
                $isUnread = is_null($notification->read_at);
            @endphp
            
            <a href="#" wire:click.prevent="markOneAsRead('{{ $notification->id }}', '{{ $url }}')"
               class="flex items-start p-3 transition duration-150 ease-in-out hover:bg-gray-50 border-b border-gray-100 {{ $isUnread ? 'bg-red-50/50' : '' }}">
                <i class="ph-duotone ph-bell-simple text-xl mr-3 mt-1 {{ $isUnread ? 'text-red-600' : 'text-gray-500' }}"></i>
                <div>
                    {{-- O conteúdo da mensagem (HTML pode ser renderizado) --}}
                    <p class="text-sm font-medium text-gray-700">
                        {!! $message !!}
                    </p>
                    <p class="text-xs text-gray-400 mt-1">
                        {{ \Carbon\Carbon::parse($notification->created_at)->diffForHumans() }}
                    </p>
                </div>
            </a>
        @empty
            <div class="text-center p-4 text-gray-500 text-sm">
                Nenhuma notificação por enquanto.
            </div>
        @endforelse

        <div class="py-2 text-center text-xs border-t border-gray-100">
            <a href="#" class="text-red-500 hover:text-red-600">Ver todas</a>
        </div>
    </div>
</div>