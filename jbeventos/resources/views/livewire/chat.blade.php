{{-- Container pai do chat --}}
<div id="chat-container" class="flex justify-center items-center min-h-screen p-6 bg-gray-100">

    {{-- Chat principal --}}
    <div class="flex flex-col border rounded-xl shadow-lg bg-white w-full max-w-[95%] h-[90vh]">

        {{-- Topo da conversa --}}
        <div class="flex items-center p-4 border-b bg-white rounded-t-xl">
            <a href="{{ route('profile.view', $receiver->id) }}">
            <img src="{{ $receiver->user_icon ? asset('storage/' . $receiver->user_icon) : 'https://ui-avatars.com/api/?name=' . urlencode($receiver->name) }}"
                 alt="Foto de perfil"
                 class="w-12 h-12 rounded-full mr-4 border-2 border-red-500"></a>
            <div>
                <span class="font-semibold text-gray-800 text-lg">{{ $receiver->name }}</span>
                <p class="text-sm text-gray-500">Online</p>
            </div>
        </div>

        {{-- Lista de mensagens --}}
        <div id="messages" class="flex-1 overflow-y-auto p-4 space-y-3 bg-gray-50 scroll-smooth">
            @foreach ($messages as $msg)
                @php
                    // Gera hora atual ou use algum campo da sua array
                    $time = isset($msg['created_at']) ? $msg['created_at'] : date('H:i');
                @endphp
                <div class="flex {{ $msg['sender_id'] === auth()->id() ? 'justify-end' : 'justify-start' }} relative group">
                    <div class="flex flex-col items-end">
                        <span class="inline-block p-3 rounded-2xl max-w-xs break-words shadow
                                     {{ $msg['sender_id'] === auth()->id() ? 'bg-blue-500 text-white' : 'bg-gray-200 text-gray-800' }}">
                            {{ $msg['message'] }}
                        </span>
                        <div class="text-xs text-gray-400 mt-1 flex items-center space-x-1">
                            <span>{{ $time }}</span>
                            @if($msg['sender_id'] === auth()->id())
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 {{ isset($msg['read']) && $msg['read'] ? 'text-blue-500' : 'text-gray-400' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                            @endif
                        </div>
                    </div>

                    {{-- Menu de ação da mensagem --}}
                    <div class="absolute top-0 {{ $msg['sender_id'] === auth()->id() ? 'right-0 -translate-y-full' : 'left-0 -translate-y-full' }} opacity-0 group-hover:opacity-100 transition-all duration-200 z-10 flex space-x-1">
                        <button wire:click="copyMessage({{ $msg['id'] }})"
                                class="p-1 rounded hover:bg-gray-300 transition" title="Copiar">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16h8M8 12h8M8 8h8" />
                            </svg>
                        </button>
                        <button wire:click="selectMessage({{ $msg['id'] }})"
                                class="p-1 rounded hover:bg-gray-300 transition" title="Mais opções">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 12H6.01M12 12h.01M18 12h.01" />
                            </svg>
                        </button>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Input de envio --}}
        <div class="flex items-center border-t bg-white p-3 rounded-b-xl">
            <input type="text" wire:model.defer="message" wire:keydown.enter="sendMessage"
                   placeholder="Digite sua mensagem..."
                   class="flex-1 border border-gray-300 rounded-full px-4 py-2 mr-3 focus:outline-none focus:ring-2 focus:ring-blue-400 focus:border-transparent">
            <button wire:click="sendMessage"
                    class="bg-blue-500 text-white px-5 py-2 rounded-full hover:bg-blue-600 transition shadow">
                Enviar
            </button>
        </div>
    </div>
</div>

<script>
    document.addEventListener("livewire:init", () => {
        // Copiar mensagem para área de transferência
        Livewire.on("copy-message", (data) => {
            navigator.clipboard.writeText(data.message)
                .then(() => alert("Mensagem copiada!"))
                .catch(err => console.error("Erro ao copiar: ", err));
        });

        // Scroll automático suave
        Livewire.hook("message.processed", (message, component) => {
            const container = document.getElementById("messages");
            if (container) container.scrollTo({ top: container.scrollHeight, behavior: 'smooth' });
        });
    });
</script>
