<div class="flex-1 flex justify-center items-center py-8">
    <div class="flex flex-col border rounded-xl shadow-lg bg-white w-full max-w-7xl h-full overflow-hidden">
        
        {{-- Topo da conversa --}}
        <div class="flex items-center p-4 border-b bg-white rounded-t-xl">
            <a href="{{ route('profile.view', $receiver->id) }}">
            <img src="{{ $receiver->user_icon ? asset('storage/' . $receiver->user_icon) : 'https://ui-avatars.com/api/?name=' . urlencode($receiver->name) }}"
                alt="Foto de perfil"
                class="w-12 h-12 rounded-full mr-4 border-2 border-red-500"></a>
            <div>
                <span class="font-semibold text-gray-800 text-lg">{{ $receiver->name }}</span>
                <p class="text-sm text-gray-500">
                    @if($isOnline)
                        <span class="text-green-500">Online</span>
                    @else
                        <span>Offline</span>
                    @endif
                </p>
            </div>
        </div>
        
        {{-- Lista de mensagens --}}
        <div id="messages" class="flex-1 overflow-y-auto p-4 space-y-3 bg-gray-50 scroll-smooth">
            @foreach ($messages as $msg)
                @php
                    $time = isset($msg['created_at']) ? $msg['created_at'] : date('H:i');
                    $isSender = $msg['sender_id'] === auth()->id();
                @endphp
                <div class="flex {{ $isSender ? 'justify-end' : 'justify-start' }} relative group">
                    <div class="flex items-center space-x-1">
                        @if($isSender)
                            @if($selectedMessage === $msg['id'])
                                <div class="relative inline-block text-left z-50">
                                    <div class="origin-top-right absolute right-0 mt-2 w-48 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 focus:outline-none">
                                        <div class="py-1">
                                            <button wire:click="copyMessage({{ $msg['id'] }})" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Copiar</button>
                                            <button wire:click="startEditing({{ $msg['id'] }})" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Editar</button>
                                            <button wire:click="confirmDelete" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Excluir</button>
                                            <button wire:click="clearSelection" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Cancelar</button>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        @endif
                    
                        @if($isSender)
                            <button wire:click="selectMessage({{ $msg['id'] }})" class="p-1 rounded-full hover:bg-gray-300 transition" title="Mais opções">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 12h.01M12 12h.01M18 12h.01" />
                                </svg>
                            </button>
                        @endif
                    
                        <div class="flex flex-col space-y-1">
                            @if(isset($msg['attachment_path']))
                                <a href="{{ asset('storage/' . $msg['attachment_path']) }}" target="_blank" class="block rounded-xl overflow-hidden shadow">
                                    @if (Str::startsWith($msg['attachment_mime'], 'image'))
                                        <img src="{{ asset('storage/' . $msg['attachment_path']) }}" class="max-h-60 rounded-xl" alt="Anexo de imagem">
                                    @elseif (Str::startsWith($msg['attachment_mime'], 'video'))
                                        <video src="{{ asset('storage/' . $msg['attachment_path']) }}" class="max-h-60 rounded-xl" controls></video>
                                    @else
                                        <div class="bg-white p-4 flex items-center space-x-2 text-sm text-gray-700">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                            </svg>
                                            <span>{{ $msg['attachment_name'] }}</span>
                                        </div>
                                    @endif
                                </a>
                            @endif
                            
                            @if($isSender && $editingMessageId === $msg['id'])
                                <div class="flex items-center space-x-2">
                                    <input type="text" wire:model.defer="editedMessageContent" 
                                        class="flex-1 rounded-lg px-3 py-2 border-gray-300 focus:ring-blue-400 focus:border-blue-400" />
                                    <button wire:click="saveEditedMessage" class="bg-green-500 text-white p-2 rounded-full hover:bg-green-600 transition">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                        </svg>
                                    </button>
                                </div>
                            @elseif(isset($msg['message']) && !empty($msg['message']))
                                <div class="flex flex-col">
                                    <span class="inline-block p-3 rounded-2xl max-w-xs break-words shadow
                                            {{ $isSender ? 'bg-blue-500 text-white' : 'bg-gray-200 text-gray-800' }}">
                                        {{ $msg['message'] }}
                                    </span>
                                    @if(isset($msg['is_edited']) && $msg['is_edited'])
                                        <span class="text-[10px] text-gray-500 mt-1 self-end">Editada</span>
                                    @endif
                                </div>
                            @endif
                        </div>
                    </div>
                    <div class="text-xs text-gray-400 mt-1 flex items-center space-x-1">
                        <span class="ml-2">{{ $time }}</span>
                        @if($isSender)
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 {{ isset($msg['read']) && $msg['read'] ? 'text-blue-500' : 'text-gray-400' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Status de digitação --}}
        @if ($isTyping && $receiver->id !== auth()->id())
            <div class="px-4 pb-2 flex {{ $receiver->id === auth()->id() ? 'justify-end' : 'justify-start' }}">
                <div class="flex items-center space-x-2 bg-gray-100 px-3 py-1 rounded-2xl">
                    <span class="text-sm text-gray-500 italic">{{ $receiver->name }} está digitando</span>
                    <div class="flex space-x-1">
                        <span class="w-2 h-2 bg-gray-400 rounded-full animate-bounce [animation-delay:-0.3s]"></span>
                        <span class="w-2 h-2 bg-gray-400 rounded-full animate-bounce [animation-delay:-0.15s]"></span>
                        <span class="w-2 h-2 bg-gray-400 rounded-full animate-bounce"></span>
                    </div>
                </div>
            </div>
        @endif

        {{-- Input de envio --}}
        <form wire:submit.prevent="sendMessage" class="flex flex-col border-t bg-white p-3 rounded-b-xl z-20">
            {{-- Pré-visualização do anexo --}}
            @if ($attachment)
                <div class="flex items-center space-x-2 p-2 rounded-lg bg-gray-100 mb-2">
                    @if (Str::startsWith($attachment->getMimeType(), 'image'))
                        <img src="{{ $attachment->temporaryUrl() }}" class="h-10 w-10 rounded-md object-cover" alt="Pré-visualização da imagem">
                    @elseif (Str::startsWith($attachment->getMimeType(), 'video'))
                        <video src="{{ $attachment->temporaryUrl() }}" class="h-10 w-10 rounded-md object-cover" controls></video>
                    @else
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        <span>{{ $attachment->getClientOriginalName() }}</span>
                    @endif
                    <button wire:click="$set('attachment', null)" class="text-red-500 hover:text-red-700">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            @endif
            {{-- Input de digitação --}}
            <div class="flex items-center">
                <label for="attachment-input" class="p-2 cursor-pointer text-gray-500 hover:text-blue-500 transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.414a4 4 0 00-5.656-5.656l-6.415 6.415a2 2 0 102.828 2.828l6.414-6.414"/>
                    </svg>
                </label>
                <input id="attachment-input" type="file" wire:model="attachment" class="hidden">
                <input 
                    type="text" 
                    wire:model.live.debounce.500ms="message" 
                    wire:keydown="typing" 
                    wire:keyup.debounce.1000ms="stopTyping"
                    placeholder="Digite sua mensagem..."
                    class="flex-1 border border-gray-300 rounded-full px-4 py-2 mr-3 focus:outline-none focus:ring-2 focus:ring-blue-400 focus:border-transparent">
                <button type="submit"
                        class="bg-blue-500 text-white px-5 py-2 rounded-full hover:bg-blue-600 transition shadow">
                    Enviar
                </button>
            </div>
        </form>
    </div>

    {{-- Modal de exclusão de mensagem --}}
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
        Livewire.hook("message.processed", (message, component) => {
            const container = document.getElementById("messages");
            if (container) {
                container.scrollTo({ top: container.scrollHeight, behavior: 'smooth' });
            }
        });
        const container = document.getElementById("messages");
        if (container) {
            container.scrollTo({ top: container.scrollHeight, behavior: 'auto' });
        }
        
        Livewire.on("copy-message", (data) => {
            navigator.clipboard.writeText(data.message)
                .then(() => alert("Mensagem copiada!"))
                .catch(err => console.error("Erro ao copiar: ", err));
        });
    });
</script>