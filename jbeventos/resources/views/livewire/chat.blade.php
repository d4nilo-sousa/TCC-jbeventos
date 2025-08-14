<div class="flex flex-col h-full">
    <div class="flex-1 overflow-y-auto p-4 space-y-2">
        @foreach ($messages as $msg)
            <div class="{{ $msg['user_id'] === auth()->id() ? 'text-right' : 'text-left' }}">
                <span class="inline-block p-2 rounded-lg {{ $msg['user_id'] === auth()->id() ? 'bg-blue-500 text-white' : 'bg-gray-200 text-gray-800' }}">
                    {{ $msg['message'] }}
                </span>
            </div>
        @endforeach
    </div>

    <div class="p-4 border-t flex">
        <input type="text" wire:model.defer="message" placeholder="Digite sua mensagem..."
               class="flex-1 border rounded p-2 mr-2">
        <button wire:click="sendMessage" class="bg-blue-500 text-white px-4 py-2 rounded">Enviar</button>
    </div>
</div>


<script>
    Livewire.hook('message.processed', (message, component) => {
        const container = document.getElementById('messages');
        container.scrollTop = container.scrollHeight; // rola automaticamente para baixo
    });
</script>
