{{-- Container principal do componente --}}
<div class="flex flex-col flex-1 p-0 sm:px-4 md:px-8 bg-gray-100 w-full h-full">

    {{-- Container do Chat --}}
    <div class="flex flex-col mx-auto mt-6 bg-white shadow-xl rounded-2xl w-full max-h-[80vh] lg:max-w-4xl xl:max-w-5xl">

        {{-- Topo Fixo da Conversa --}}
        <div class="flex items-center p-4 sm:p-5 border-b bg-white sticky top-0 z-20 flex-shrink-0">
            <a href="javascript:history.back()" class="text-gray-500 hover:text-red-500 mr-3 transition duration-150"
                title="Voltar">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
            </a>
            <a href="{{ route('profile.view', $receiver->id) }}" class="flex items-center">
                <img src="{{ $receiver->user_icon ? asset('storage/' . $receiver->user_icon) : 'https://ui-avatars.com/api/?name=' . urlencode($receiver->name) . '&background=F87171&color=ffffff&size=128' }}"
                    alt="Foto de perfil" class="w-12 h-12 rounded-full mr-3 border-2 border-red-500 object-cover">
                <div>
                    <span class="font-bold text-gray-800 text-lg block">{{ $receiver->name }}</span>
                    <p class="text-xs font-semibold">
                        @if ($isOnline)
                            <span class="text-green-500 flex items-center">
                                <span class="h-2 w-2 bg-green-500 rounded-full mr-1"></span> Online
                            </span>
                        @else
                            <span class="text-gray-500 flex items-center">
                                <span class="h-2 w-2 bg-gray-400 rounded-full mr-1"></span> Offline
                            </span>
                        @endif
                    </p>
                </div>
            </a>
        </div>

        {{-- LISTA DE MENSAGENS --}}
        <div id="messages" class="flex-1 overflow-y-auto p-4 sm:p-6 space-y-4 bg-gray-50 scroll-smooth min-h-[50vh]">
            @foreach ($messages as $msg)
                @php
                    $time = isset($msg['created_at']) ? $msg['created_at'] : date('H:i');
                    $isSender = $msg['sender_id'] === auth()->id();
                    $isEditing = $isSender && $editingMessageId === $msg['id'];
                    $isSelected = $isSender && $selectedMessage === $msg['id'];

                    $bubbleClasses = 'inline-block p-3 rounded-xl max-w-sm lg:max-w-md break-words shadow-sm relative';
                    $senderClasses = 'bg-red-500 text-white rounded-br-none';
                    $receiverClasses = 'bg-gray-200 text-gray-800 rounded-tl-none';
                @endphp

                <div class="flex {{ $isSender ? 'justify-end' : 'justify-start' }} group transition duration-100 z-10">

                    @if ($isSender)

                        {{-- Menu de Opções --}}
                        <div class="relative flex items-center order-1 self-center mr-1 z-20">

                            <button wire:click="selectMessage({{ $msg['id'] }})"
                                class="p-1 self-center rounded-full transition-opacity {{ $isSelected ? 'bg-gray-200 opacity-100' : 'opacity-0 group-hover:opacity-100 hover:bg-gray-200' }}"
                                title="Mais opções">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-600" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M6 12h.01M12 12h.01M18 12h.01" />
                                </svg>
                            </button>

                            @if ($isSelected)
                                <div wire:click.away="clearSelection"
                                    class="absolute right-full top-1/2 -translate-y-1/2 mr-1 w-40 rounded-lg shadow-lg bg-white ring-1 ring-black ring-opacity-5 z-50">
                                    <div class="py-1">
                                        @if (isset($msg['message']) && !empty($msg['message']))
                                            <button wire:click="copyMessage({{ $msg['id'] }})"
                                                class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Copiar</button>
                                        @endif

                                        {{-- SÓ MOSTRA EDITAR SE HOUVER MENSAGEM DE TEXTO --}}
                                        @if (isset($msg['message']) && !empty($msg['message']))
                                            <button wire:click="startEditing({{ $msg['id'] }})"
                                                class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Editar</button>
                                        @endif

                                        <button wire:click="confirmDelete"
                                            class="block w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50">Excluir</button>
                                    </div>
                                </div>
                            @endif
                        </div>

                        {{-- Conteúdo Principal --}}
                        <div class="flex flex-col space-y-1 max-w-full items-end order-2">

                            @if ($isEditing)
                                <div class="flex items-center space-x-2 w-full">
                                    <input type="text" wire:model.defer="editedMessageContent"
                                        wire:keydown.enter="saveEditedMessage"
                                        class="flex-1 rounded-lg px-3 py-2 border-gray-300 focus:ring-red-500 focus:border-red-500 text-sm" />
                                    <button wire:click="saveEditedMessage"
                                        class="bg-green-500 text-white p-2 rounded-full hover:bg-green-600 transition"
                                        title="Salvar">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20"
                                            fill="currentColor">
                                            <path fill-rule="evenodd"
                                                d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                                clip-rule="evenodd" />
                                        </svg>
                                    </button>
                                    <button wire:click="$set('editingMessageId', null)"
                                        class="text-gray-500 p-2 rounded-full hover:bg-gray-200 transition"
                                        title="Cancelar">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                                            viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                    </button>
                                </div>
                            @else
                                {{-- Anexo --}}
                                @if (isset($msg['attachment_path']))
                                    <a href="{{ asset('storage/' . $msg['attachment_path']) }}" target="_blank"
                                        class="block rounded-xl overflow-hidden shadow mb-1 border {{ $isSender ? 'border-red-400' : 'border-gray-300' }}">
                                        @if (\Illuminate\Support\Str::startsWith($msg['attachment_mime'], 'image'))
                                            <img src="{{ asset('storage/' . $msg['attachment_path']) }}"
                                                class="max-h-60 rounded-xl object-cover w-full" alt="Anexo de imagem">
                                        @elseif (\Illuminate\Support\Str::startsWith($msg['attachment_mime'], 'video'))
                                            <video src="{{ $msg['attachment_path'] }}"
                                                class="max-h-60 rounded-xl w-full" controls></video>
                                        @else
                                            <div
                                                class="bg-white p-4 flex items-center space-x-2 text-sm text-gray-700 w-64">
                                                <svg xmlns="http://www.w3.org/2000/svg"
                                                    class="h-6 w-6 text-red-500 flex-shrink-0" fill="none"
                                                    viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                                </svg>
                                                <span class="truncate"
                                                    title="{{ $msg['attachment_name'] }}">{{ $msg['attachment_name'] }}</span>
                                            </div>
                                        @endif
                                    </a>
                                @endif

                                {{-- Texto da Mensagem (Se houver texto) --}}
                                @if (isset($msg['message']) && !empty($msg['message']))
                                    <div class="flex flex-col">
                                        <span class="{{ $bubbleClasses }} {{ $senderClasses }}">
                                            {!! nl2br(e($msg['message'])) !!}
                                        </span>
                                        <div class="flex items-center text-xs text-gray-500 self-end mt-1 space-x-1">
                                            @if (isset($msg['is_edited']) && $msg['is_edited'])
                                                <span class="italic">Editada</span>
                                                <span class="mx-[1px]">•</span>
                                            @endif
                                            <span>{{ $time }}</span>

                                            @if ($isSender)
                                                {{-- Ícone de check(s) --}}
                                                @if (isset($msg['is_read']) && $msg['is_read'])
                                                    <!-- Dois checks azuis bem próximos -->
                                                    <div class="flex space-x-[-7px]">
                                                        <svg xmlns="http://www.w3.org/2000/svg"
                                                            class="h-4 w-4 text-blue-500" fill="none"
                                                            viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2" d="M5 13l4 4L19 7" />
                                                        </svg>
                                                        <svg xmlns="http://www.w3.org/2000/svg"
                                                            class="h-4 w-4 text-blue-500" fill="none"
                                                            viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2" d="M5 13l4 4L19 7" />
                                                        </svg>
                                                    </div>
                                                @else
                                                    <!-- Dois checks cinza bem próximos -->
                                                    <div class="flex space-x-[-7px]">
                                                        <svg xmlns="http://www.w3.org/2000/svg"
                                                            class="h-4 w-4 text-gray-400" fill="none"
                                                            viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2" d="M5 13l4 4L19 7" />
                                                        </svg>
                                                        <svg xmlns="http://www.w3.org/2000/svg"
                                                            class="h-4 w-4 text-gray-400" fill="none"
                                                            viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2" d="M5 13l4 4L19 7" />
                                                        </svg>
                                                    </div>
                                                @endif
                                            @endif
                                        </div>
                                    </div>
                                @elseif(isset($msg['attachment_path']))
                                    <div class="flex items-center text-xs text-gray-500 self-end space-x-1">
                                        <span>{{ $time }}</span>

                                        @if ($isSender)
                                            {{-- Ícone de check(s) --}}
                                            @if (isset($msg['is_read']) && $msg['is_read'])
                                                <!-- Dois checks azuis -->
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-blue-500"
                                                    fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2" d="M4 13l4 4L20 6" />
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2" d="M9 13l3 3L22 6" />
                                                </svg>
                                            @else
                                                <!-- Um check cinza -->
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-400"
                                                    fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2" d="M5 13l4 4L19 7" />
                                                </svg>
                                            @endif
                                        @endif
                                    </div>
                                @endif

                            @endif

                        </div>
                    @else
                        {{-- Destinatário --}}
                        <div class="flex flex-col space-y-1 max-w-full items-start">
                            @if (isset($msg['attachment_path']))
                                <a href="{{ asset('storage/' . $msg['attachment_path']) }}" target="_blank"
                                    class="block rounded-xl overflow-hidden shadow mb-1 border border-gray-300">
                                    @if (\Illuminate\Support\Str::startsWith($msg['attachment_mime'], 'image'))
                                        <img src="{{ asset('storage/' . $msg['attachment_path']) }}"
                                            class="max-h-60 rounded-xl object-cover w-full" alt="Anexo de imagem">
                                    @elseif (\Illuminate\Support\Str::startsWith($msg['attachment_mime'], 'video'))
                                        <video src="{{ $msg['attachment_path'] }}" class="max-h-60 rounded-xl w-full"
                                            controls></video>
                                    @else
                                        <div
                                            class="bg-white p-4 flex items-center space-x-2 text-sm text-gray-700 w-64">
                                            <svg xmlns="http://www.w3.org/2000/svg"
                                                class="h-6 w-6 text-red-500 flex-shrink-0" fill="none"
                                                viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                            </svg>
                                            <span class="truncate"
                                                title="{{ $msg['attachment_name'] }}">{{ $msg['attachment_name'] }}</span>
                                        </div>
                                    @endif
                                </a>
                            @endif

                            @if (isset($msg['message']) && !empty($msg['message']))
                                <div class="flex flex-col">
                                    <span class="{{ $bubbleClasses }} {{ $receiverClasses }}">
                                        {!! nl2br(e($msg['message'])) !!}
                                    </span>
                                    <div class="flex items-center text-xs text-gray-500 self-start mt-1 space-x-1">
                                        @if (isset($msg['is_edited']) && $msg['is_edited'])
                                            <span class="italic">Editada</span> <span class="mx-[1px]">•</span>
                                        @endif
                                        <span>{{ $time }}</span>
                                    </div>
                                </div>
                            @elseif(isset($msg['attachment_path']))
                                <div class="flex items-center text-xs text-gray-500 self-start space-x-1">
                                    <span>{{ $time }}</span>
                                </div>
                            @endif
                        </div>
                    @endif
                </div>
            @endforeach

            {{-- Status de digitação --}}
            @if ($isTyping)
                <div class="flex justify-start">
                    <div
                        class="flex items-center space-x-2 bg-gray-200 px-4 py-2 rounded-2xl rounded-tl-none shadow-sm">
                        <span class="text-sm text-gray-600 italic">{{ $receiver->name }} está digitando</span>
                        <div class="flex space-x-1">
                            <span class="w-1.5 h-1.5 bg-gray-400 rounded-full animate-pulse delay-100"></span>
                            <span class="w-1.5 h-1.5 bg-gray-400 rounded-full animate-pulse delay-200"></span>
                            <span class="w-1.5 h-1.5 bg-gray-400 rounded-full animate-pulse delay-300"></span>
                        </div>
                    </div>
                </div>
            @endif
        </div>

        {{-- Input de envio --}}
        <form wire:submit.prevent="sendMessage"
            class="flex flex-col border-t bg-white p-4 sm:p-5 rounded-b-2xl z-20 flex-shrink-0">

            {{-- Pré-visualização do anexo --}}
            @if ($attachment)
                <div
                    class="flex items-center space-x-3 p-3 rounded-xl bg-gray-100 mb-3 border border-gray-200 flex-shrink-0">
                    <span class="font-semibold text-sm text-gray-700">Anexo:</span>
                    @if (\Illuminate\Support\Str::startsWith($attachment->getMimeType(), 'image'))
                        <img src="{{ $attachment->temporaryUrl() }}" class="h-10 w-10 rounded-md object-cover"
                            alt="Pré-visualização">
                    @elseif (\Illuminate\Support\Str::startsWith($attachment->getMimeType(), 'video'))
                        <video src="{{ $attachment->temporaryUrl() }}"
                            class="h-10 w-10 rounded-md object-cover"></video>
                    @else
                        <div class="flex items-center space-x-1 text-sm text-gray-700 truncate max-w-[200px]">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-red-500 flex-shrink-0"
                                fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            <span class="truncate">{{ $attachment->getClientOriginalName() }}</span>
                        </div>
                    @endif
                    <button type="button" wire:click="$set('attachment', null)"
                        class="text-gray-500 hover:text-red-600 transition ml-auto" title="Remover anexo">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            @endif

            {{-- Input de digitação (Textarea auto-ajustável) --}}
            <div class="flex items-end space-x-2">

                {{-- O rótulo e o input de arquivo agora são desabilitados durante o carregamento --}}
                <label for="attachment-input" wire:loading.attr="disabled" wire:target="sendMessage, attachment"
                    class="p-2 cursor-pointer text-gray-500 hover:text-red-500 transition-colors flex-shrink-0 disabled:opacity-50 disabled:cursor-not-allowed"
                    title="Adicionar anexo">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.414a4 4 0 00-5.656-5.656l-6.415 6.415a2 2 0 102.828 2.828l6.414-6.414" />
                    </svg>
                </label>
                <input id="attachment-input" type="file" wire:model="attachment" class="hidden"
                    accept="image/*,video/*,application/pdf,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document,text/plain,application/zip">

                {{-- O Textarea agora é desabilitado durante o carregamento --}}
                <textarea x-data="{
                    resize: () => {
                        $el.style.height = '42px';
                        $el.style.height = $el.scrollHeight + 'px'
                    }
                }" x-init="resize()" @input="resize()" rows="1"
                    wire:model.live="message" wire:keydown.enter.prevent="sendMessage" wire:keydown="typing"
                    wire:keyup.debounce.1500ms="stopTyping" wire:loading.attr="disabled" wire:target="sendMessage, attachment"
                    placeholder="Digite sua mensagem..."
                    class="flex-1 border border-gray-300 rounded-full px-4 py-2 pt-2.5 overflow-hidden resize-none focus:outline-none focus:ring-2 focus:ring-red-400 focus:border-red-400 text-sm min-h-[42px] max-h-40 disabled:bg-gray-100 disabled:opacity-75 disabled:cursor-not-allowed">
</textarea>

                {{-- BOTÃO DE ENVIAR: Adicionamos o wire:loading e o spinner --}}
                <button type="submit" wire:loading.attr="disabled" wire:target="sendMessage, attachment"
                    {{-- Condição: desabilitado apenas se $message estiver vazio E não houver $attachment. --}} @if (empty($message) && !$attachment) disabled @endif
                    class="bg-red-500 text-white p-3 rounded-full hover:bg-red-600 transition shadow-lg flex-shrink-0
                        {{ empty($message) && !$attachment ? 'opacity-50 cursor-not-allowed' : '' }}
                        disabled:bg-red-400 disabled:opacity-70 disabled:cursor-wait">

                    {{-- Spinner de Carregamento (mostra APENAS durante o carregamento) --}}
                    <div wire:loading wire:target="sendMessage, attachment" class="flex items-center justify-center">
                        <svg class="animate-spin h-6 w-6 pt-2" xmlns="http://www.w3.org/2000/svg" fill="none"
                            viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor"
                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                            </path>
                        </svg>
                    </div>

                    {{-- Ícone Normal (mostra APENAS quando não está carregando) --}}
                    <div wire:loading.remove wire:target="sendMessage, attachment">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 transform rotate-90" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                        </svg>
                    </div>
                </button>
            </div>
            @error('attachment')
                <span class="text-red-500 text-xs mt-1 ml-4">{{ $message }}</span>
            @enderror
        </form>
    </div>

    {{-- Modal de exclusão de mensagem --}}
    @if ($showDeleteModal)
        <div
            class="fixed inset-0 bg-gray-900 bg-opacity-75 overflow-y-auto h-full w-full flex justify-center items-center z-50 transition-opacity duration-300 ease-out">
            <div
                class="bg-white p-6 rounded-xl shadow-2xl max-w-sm w-full transform transition-all duration-300 ease-out scale-100">
                <h3 class="text-xl font-bold text-gray-900 mb-4">Excluir Mensagem</h3>
                <p class="text-sm text-gray-600 mb-6">Tem certeza de que deseja **excluir permanentemente** esta
                    mensagem? Esta ação não pode ser desfeita.</p>
                <div class="flex justify-end space-x-3">
                    <button wire:click="cancelDelete"
                        class="px-4 py-2 bg-gray-200 text-gray-800 rounded-lg font-medium hover:bg-gray-300 transition">
                        Cancelar
                    </button>
                    <button wire:click="deleteSelectedMessage"
                        class="px-4 py-2 bg-red-600 text-white rounded-lg font-medium hover:bg-red-700 transition">
                        Confirmar Exclusão
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>

{{-- Scripts para Scroll, Copiar e Notificações --}}
<script>
    document.addEventListener("livewire:init", () => {
        const initialScroll = () => {
            const container = document.getElementById("messages");
            if (container) {
                container.scrollTop = container.scrollHeight;
            }
        };

        Livewire.hook("message.processed", (message, component) => {
            const container = document.getElementById("messages");
            const hasMessageChange = (
                message.response.serverMemo.data.messages.length !== (component.data.messages ?
                    component.data.messages.length : 0) ||
                message.response.effects.dispatches.some(d => d.event === 'copy-message') ||
                message.response.effects.html.includes('<div class="flex flex-col">')
            );

            if (container && hasMessageChange) {
                // Verifica se o usuário está perto do final (dentro de 50px)
                const isScrolledToBottom = container.scrollHeight - container.clientHeight <= container
                    .scrollTop + 50;

                // Rola para o final se: (a) o usuário já estava perto do final OU (b) uma nova mensagem foi adicionada (ou seja, o total de mensagens aumentou)
                if (isScrolledToBottom || message.response.serverMemo.data.messages.length > (component
                        .data.messages ? component.data.messages.length : 0)) {
                    setTimeout(() => {
                        container.scrollTo({
                            top: container.scrollHeight,
                            behavior: 'smooth'
                        });
                    }, 100);
                }
            }
        });

        initialScroll();

        Livewire.on("copy-message", (data) => {
            navigator.clipboard.writeText(data.message)
                .then(() => {
                    // CORREÇÃO: Usar um modal personalizado ou console.log em vez de alert()
                    console.log("Mensagem copiada para a área de transferência!");
                    // Se precisar de feedback visual sem alert:
                    // window.dispatchEvent(new CustomEvent('notify', { detail: { message: 'Mensagem copiada!' } }));
                })
                .catch(err => console.error("Erro ao copiar: ", err));

            Livewire.getByName('chat').call('clearSelection');
        });
    });
</script>

<script>
    document.addEventListener('livewire:initialized', () => {
        // Escuta o evento 'clearFileUpload' disparado pelo componente
        Livewire.on('clearFileUpload', () => {
            const input = document.getElementById('attachment-file-input');
            if (input) {
                // Isso é o que realmente limpa o input file no navegador
                input.value = '';
                // Se você tem algum preview de imagem, limpe-o aqui também
            }
        });
    });
</script>
