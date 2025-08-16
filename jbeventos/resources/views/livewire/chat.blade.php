<div class="flex flex-col h-full border rounded-lg shadow-md">
    {{-- Topo da conversa --}}
    <div class="flex items-center p-4 border-b bg-gray-100">
        <img src="{{ $receiver->user_icon ?? 'https://ui-avatars.com/api/?name=' . urlencode($receiver->name) }}"
             alt="Foto de perfil"
             class="w-10 h-10 rounded-full mr-3">
        <span class="font-semibold text-gray-800">{{ $receiver->name }}</span>
    </div>

    {{-- Lista de mensagens --}}
    <div id="messages" class="flex-1 overflow-y-auto p-4 space-y-2 bg-white">
        @foreach ($messages as $msg)
            <div class="flex {{ $msg['sender_id'] === auth()->id() ? 'justify-end' : 'justify-start' }}">
                <span wire:click="selectMessage({{ $msg['id'] }})"
                      class="inline-block p-2 rounded-lg max-w-xs break-words cursor-pointer
                             {{ $msg['id'] === $selectedMessage ? 'bg-gray-300' : ($msg['sender_id'] === auth()->id() ? 'bg-blue-500 text-white' : 'bg-gray-200 text-gray-800') }}">
                    {{ $msg['message'] }}
                </span>
            </div>
        @endforeach
    </div>

    {{-- Botões de ação --}}
    <div class="p-2 border-t flex items-center bg-gray-50">
        <input type="text" wire:model.defer="message" wire:keydown.enter="sendMessage"
               placeholder="Digite sua mensagem..."
               class="flex-1 border rounded p-2 mr-2 focus:outline-none focus:ring-2 focus:ring-blue-400">

        <button wire:click="sendMessage"
                class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600 transition">
            Enviar
        </button>

        @if($selectedMessage)
            <button wire:click="confirmDelete"
                    class="ml-2 bg-red-500 text-white px-4 py-2 rounded hover:bg-red-600 transition">
                Apagar
            </button>
            <button wire:click="clearSelection"
                    class="ml-2 bg-gray-300 text-gray-800 px-4 py-2 rounded hover:bg-gray-400 transition">
                Cancelar
            </button>
        @endif
    </div>

    {{-- Modal de confirmação de exclusão --}}
    @if($showDeleteModal)
        <div class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-50">
            <div class="bg-white rounded-lg p-6 w-80">
                <h2 class="text-lg font-semibold mb-4">Excluir mensagem?</h2>
                <p class="mb-6 text-gray-600">Esta ação não pode ser desfeita.</p>
                <div class="flex justify-end gap-2">
                    <button wire:click="cancelDelete"
                            class="bg-gray-300 text-gray-800 px-4 py-2 rounded hover:bg-gray-400 transition">
                        Cancelar
                    </button>
                    <button wire:click="deleteSelectedMessage"
                            class="bg-red-500 text-white px-4 py-2 rounded hover:bg-red-600 transition">
                        Excluir
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>

<script>
    // rola automaticamente para baixo
    Livewire.hook('message.processed', (message, component) => {
        const container = document.getElementById('messages');
        container.scrollTop = container.scrollHeight;
    });
</script>
