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
                    $time = isset($msg['created_at']) ? $msg['created_at'] : date('H:i');
                @endphp
                <div class="flex {{ $msg['sender_id'] === auth()->id() ? 'justify-end' : 'justify-start' }} relative group">
                    <div class="flex flex-col">
                        <div class="flex items-center">
                            {{-- Menu de ação da mensagem --}}
                            <div class="relative inline-block text-left opacity-0 group-hover:opacity-100 transition-opacity duration-200">
                                <button wire:click="selectMessage({{ $msg['id'] }})"
                                        class="p-1 rounded-full hover:bg-gray-300 transition" title="Mais opções">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 12h.01M12 12h.01M18 12h.01" />
                                    </svg>
                                </button>
                                @if($selectedMessage === $msg['id'])
                                <div class="origin-top-right absolute {{ $msg['sender_id'] === auth()->id() ? 'right-0' : 'left-0' }} mt-2 w-48 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 focus:outline-none z-20">
                                    <div class="py-1" role="menu" aria-orientation="vertical" aria-labelledby="options-menu">
                                        <button wire:click="copyMessage({{ $msg['id'] }})" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100" role="menuitem">Copiar</button>
                                        @if($msg['sender_id'] === auth()->id())
                                            <button wire:click="confirmDelete" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100" role="menuitem">Excluir</button>
                                        @endif
                                        <button wire:click="clearSelection" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100" role="menuitem">Cancelar</button>
                                    </div>
                                </div>
                                @endif
                            </div>

                            <span class="inline-block p-3 rounded-2xl max-w-xs break-words shadow
                                        {{ $msg['sender_id'] === auth()->id() ? 'bg-blue-500 text-white' : 'bg-gray-200 text-gray-800' }}">
                                {{ $msg['message'] }}
                            </span>
                        </div>
                        <div class="text-xs text-gray-400 mt-1 flex items-center space-x-1">
                            <span>{{ $time }}</span>
                            @if($msg['sender_id'] === auth()->id())
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 {{ isset($msg['read']) && $msg['read'] ? 'text-blue-500' : 'text-gray-400' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                            @endif
                        </div>
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

    {{-- O modal agora está DENTRO da div principal controlada pelo Livewire --}}
    @if ($showDeleteModal)
        <div class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full flex justify-center items-center z-50">
            <div class="bg-white p-6 rounded-lg shadow-xl max-w-sm w-full">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Confirmar Exclusão</h3>
                <p class="text-sm text-gray-600 mb-6">Tem certeza de que deseja excluir esta mensagem? Esta ação não pode ser desfeita.</p>
                <div class="flex justify-end space-x-4">
                    <button wire:click="cancelDelete" class="px-4 py-2 bg-gray-200 text-gray-800 rounded-lg hover:bg-gray-300 transition">
                        Cancelar
                    </button>
                    <button wire:click="deleteSelectedMessage" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition">
                        Excluir
                    </button>
                </div>
            </div>
        </div>
    @endif
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