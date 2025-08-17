<div class="flex flex-col h-full border rounded-lg shadow-md">
    {{-- Topo da conversa --}}
    <div class="flex items-center p-4 border-b bg-gray-100">
        <img src="{{ $receiver->user_icon ?? 'https://ui-avatars.com/api/?name=' . urlencode($receiver->name) }}"
             alt="Foto de perfil"
             class="w-10 h-10 rounded-full mr-3">
        <span class="font-semibold text-gray-800">{{ $receiver->name }}</span>
    </div>

    {{-- Lista de mensagens --}}
    <div id="messages" class="flex-1 overflow-y-auto p-4 space-y-2 bg-white relative">
        @foreach ($messages as $msg)
            <div class="flex {{ $msg['sender_id'] === auth()->id() ? 'justify-end' : 'justify-start' }} relative group">
                <span class="inline-block p-2 rounded-lg max-w-xs break-words
                             {{ $msg['sender_id'] === auth()->id() ? 'bg-blue-500 text-white' : 'bg-gray-200 text-gray-800' }}">
                    {{ $msg['message'] }}
                </span>

                {{-- Ícone de 3 pontos (menu) para mensagens do usuário --}}
                @if($msg['sender_id'] === auth()->id())
                    <div class="absolute top-0 right-0 mt-0 -translate-y-full opacity-0 group-hover:opacity-100 transition z-10">
                        <button wire:click="selectMessage({{ $msg['id'] }})"
                                class="p-1 rounded hover:bg-gray-200">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 12H6.01M12 12h.01M18 12h.01" />
                            </svg>
                        </button>
                    </div>
                @endif

                {{-- Menu flutuante ao clicar no ícone --}}
                @if($selectedMessage === $msg['id'])
                    <div class="absolute top-0 right-0 -translate-y-full bg-white shadow-lg rounded-md border z-20 flex gap-1 p-1">
                        <button wire:click="deleteSelectedMessage"
                                class="bg-red-500 text-white px-2 py-1 rounded hover:bg-red-600 transition text-sm">
                            Excluir
                        </button>
                        <button wire:click="copyMessage('{{ addslashes($msg['message']) }}')"
                                class="bg-gray-200 text-gray-800 px-2 py-1 rounded hover:bg-gray-300 transition text-sm">
                            Copiar
                        </button>
                        <button wire:click="clearSelection"
                                class="bg-gray-300 text-gray-800 px-2 py-1 rounded hover:bg-gray-400 transition text-sm">
                            Cancelar
                        </button>
                    </div>
                @endif
            </div>
        @endforeach
    </div>

    {{-- Botão de enviar --}}
    <div class="p-2 border-t flex items-center bg-gray-50">
        <input type="text" wire:model.defer="message" wire:keydown.enter="sendMessage"
               placeholder="Digite sua mensagem..."
               class="flex-1 border rounded p-2 mr-2 focus:outline-none focus:ring-2 focus:ring-blue-400">

        <button wire:click="sendMessage"
                class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600 transition">
            Enviar
        </button>
    </div>
</div>

<script>
    document.addEventListener("livewire:init", () => {
        // Copiar mensagem para a área de transferência
        Livewire.on("copy-message", (data) => {
            navigator.clipboard.writeText(data.message).then(() => {
                alert("Mensagem copiada!");
            }).catch(err => {
                console.error("Erro ao copiar: ", err);
            });
        });

        // Rola automaticamente para baixo após nova mensagem
        Livewire.hook("message.processed", (message, component) => {
            const container = document.getElementById("messages");
            if (container) {
                container.scrollTop = container.scrollHeight;
            }
        });
    });
</script>
