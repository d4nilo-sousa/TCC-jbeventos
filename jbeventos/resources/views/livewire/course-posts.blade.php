<div class="space-y-6">

    {{-- Formulário para criar novo post --}}
    @if ($isCoordinator && !$overview)
        <div class="bg-white rounded-xl shadow-md p-6 border border-gray-200">
            <h4 class="text-lg font-bold text-stone-700 mb-3">Criar Novo Post</h4>
            <form wire:submit.prevent="createPost" class="space-y-3">
                <textarea wire:model.defer="newPostContent" rows="4"
                    class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-blue-400 focus:border-blue-500 p-3 text-sm placeholder-gray-400"
                    placeholder="Compartilhe uma atualização com o curso..."></textarea>
                @error('newPostContent') <span class="text-red-500 text-sm block">{{ $message }}</span> @enderror
                <div class="text-right">
                    <button type="submit"
                        class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-6 rounded-lg shadow-md transition">
                        Postar
                    </button>
                </div>
            </form>
        </div>
    @endif

    {{-- Mensagens de feedback --}}
    @if (session()->has('success'))
        <div class="bg-green-100 text-green-800 p-4 rounded-lg shadow-md border border-green-200">
            {{ session('success') }}
        </div>
    @endif
    @if (session()->has('error'))
        <div class="bg-red-100 text-red-800 p-4 rounded-lg shadow-md border border-red-200">
            {{ session('error') }}
        </div>
    @endif

    {{-- Lista de posts --}}
    @forelse ($posts as $post)
        <div class="bg-white rounded-xl shadow-md p-6 border border-gray-200">
            {{-- Header do post --}}
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center gap-4">
                    <img src="{{ $post->author->user_icon ? asset('storage/' . $post->author->user_icon) : asset('images/default-icon.png') }}"
                        class="w-12 h-12 rounded-full object-cover border-2 border-blue-400">
                    <div>
                        <p class="text-stone-800 font-semibold">{{ $post->author->name }}</p>
                        <p class="text-xs text-gray-500">{{ $post->created_at->diffForHumans() }}</p>
                    </div>
                </div>

                {{-- Botões de ação --}}
                @if(auth()->id() === $post->user_id || $isCoordinator)
                    <div class="flex gap-2">
                        <button wire:click="editPost({{ $post->id }})"
                                class="text-blue-600 hover:text-blue-800 text-sm font-medium">Editar</button>
                        <button wire:click="deletePost({{ $post->id }})"
                                class="text-red-600 hover:text-red-800 text-sm font-medium">Excluir</button>
                    </div>
                @endif
            </div>

            {{-- Conteúdo do post --}}
            @if($editingPostId === $post->id)
                <form wire:submit.prevent="updatePost({{ $post->id }})" class="space-y-3 mb-4">
                    <textarea wire:model.defer="editingPostContent" rows="3"
                        class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-blue-400 focus:border-blue-500 p-2 text-sm"></textarea>
                    <div class="text-right">
                        <button type="submit"
                            class="bg-green-500 hover:bg-green-600 text-white text-sm font-semibold py-1.5 px-4 rounded-lg transition">
                            Salvar
                        </button>
                        <button type="button" wire:click="$set('editingPostId', null)"
                            class="bg-gray-200 hover:bg-gray-300 text-gray-700 text-sm font-semibold py-1.5 px-4 rounded-lg transition">
                            Cancelar
                        </button>
                    </div>
                </form>
            @else
                <p class="text-gray-700 mb-4 whitespace-pre-line">{{ $post->content }}</p>
            @endif

            {{-- Respostas --}}
            <div class="bg-gray-50 rounded-lg p-4 border border-gray-100 space-y-3">
                <p class="text-sm font-semibold text-gray-600">Respostas ({{ $post->replies->count() }})</p>

                @foreach ($post->replies as $reply)
                    <div class="flex items-start gap-3 bg-white p-3 rounded-lg shadow-sm border border-gray-100">
                        <img src="{{ $reply->author->user_icon ? asset('storage/' . $reply->author->user_icon) : asset('images/default-icon.png') }}"
                            class="w-8 h-8 rounded-full object-cover border-2 border-gray-300">
                        <div class="flex-1">
                            <div class="flex justify-between items-start">
                                <p class="text-xs font-medium text-gray-800">{{ $reply->author->name }}
                                    <span class="text-gray-500 ml-2 font-normal">{{ $reply->created_at->diffForHumans() }}</span>
                                </p>
                                @if(auth()->id() === $reply->author->id || $isCoordinator)
                                    <button wire:click="deleteReply({{ $reply->id }})"
                                            class="text-red-500 hover:text-red-700 text-xs font-semibold">Excluir</button>
                                @endif
                            </div>
                            <p class="text-sm text-gray-700 mt-1 whitespace-pre-line">{{ $reply->content }}</p>
                        </div>
                    </div>
                @endforeach

                {{-- Formulário para nova resposta --}}
                <form wire:submit.prevent="createReply({{ $post->id }})" class="mt-3 space-y-2">
                    <textarea wire:model.defer="newReplyContent.{{ $post->id }}" rows="2"
                        class="w-full border-gray-300 rounded-lg shadow-sm text-sm focus:ring-2 focus:ring-blue-400 focus:border-blue-500 p-2"
                        placeholder="Deixe sua resposta..."></textarea>
                    @error("newReplyContent.{$post->id}") <span class="text-red-500 text-xs block">{{ $message }}</span> @enderror
                    <div class="text-right">
                        <button type="submit"
                            class="bg-blue-500 hover:bg-blue-600 text-white text-sm font-semibold py-1.5 px-4 rounded-lg transition">
                            Responder
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @empty
        <div class="bg-gray-50 text-center p-10 rounded-lg shadow-md">
            <p class="text-gray-500">Nenhum post ainda. O coordenador pode criar o primeiro!</p>
        </div>
    @endforelse
</div>
