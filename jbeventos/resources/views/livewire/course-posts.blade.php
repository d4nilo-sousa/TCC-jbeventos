<div>
    {{-- Formulário de criação de post --}}
    @if ($isCoordinator)
        <div class="mb-6 p-6 bg-gray-50 rounded-lg border border-gray-200">
            <h4 class="text-md font-bold mb-3 text-stone-700">Criar Novo Post</h4>
            <form wire:submit.prevent="createPost">
                <textarea wire:model.defer="newPostContent" rows="4"
                    class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500"
                    placeholder="O que há de novo no curso?"></textarea>
                @error('newPostContent') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror

                {{-- Upload de imagens --}}
                {{-- O wire:model agora aponta para a propriedade temporária --}}
                <input type="file" wire:model="newlyUploadedImages" multiple accept="image/*"
                       class="w-full mt-3 border-gray-300 rounded-lg shadow-sm text-sm">
                @error('images.*') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror

                {{-- Preview --}}
                @if(!empty($images))
                    <div class="flex flex-wrap mt-3 gap-3">
                        @foreach($images as $index => $image)
                            <div class="w-24 h-24 border rounded-lg overflow-hidden relative">
                                <img src="{{ $image->temporaryUrl() }}" class="object-cover w-full h-full">
                                <button type="button" wire:click="removeImage({{ $index }})"
                                        class="absolute top-1 right-1 bg-red-500 text-white rounded-full w-5 h-5 flex items-center justify-center text-xs">×</button>
                            </div>
                        @endforeach
                    </div>
                @endif

                <div class="mt-3 text-right">
                    <button type="submit"
                        class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-6 rounded-lg shadow-md transition">
                        Postar
                    </button>
                </div>
            </form>
        </div>
    @endif

    {{-- Feedback --}}
    @if (session()->has('success'))
        <div class="mb-4 p-4 bg-green-100 text-green-700 rounded-lg">{{ session('success') }}</div>
    @endif
    @if (session()->has('error'))
        <div class="mb-4 p-4 bg-red-100 text-red-700 rounded-lg">{{ session('error') }}</div>
    @endif

    {{-- Lista de posts --}}
    <div class="space-y-8">
        @forelse ($posts as $post)
            <div class="bg-white rounded-lg p-6 shadow-md border border-gray-100">
                <div class="flex items-center gap-4 mb-4">
                    <img src="{{ $post->author->user_icon ? asset('storage/' . $post->author->user_icon) : asset('images/default-icon.png') }}"
                            class="w-12 h-12 rounded-full object-cover">
                    <div class="flex-1">
                        <div class="flex justify-between items-center">
                            <a href="#" class="text-stone-800 font-semibold text-lg hover:underline">{{ $post->author->name }}</a>
                            <span class="text-xs text-gray-500">{{ $post->created_at->diffForHumans() }}</span>
                        </div>
                        {{-- Botões --}}
                        @if($isCoordinator && auth()->id() === $post->user_id)
                            <div class="mt-1 flex gap-2">
                                <button class="text-blue-500 hover:underline text-xs font-semibold">Editar</button>
                                <button wire:click="deletePost({{ $post->id }})"
                                        class="text-red-500 hover:underline text-xs font-semibold">Excluir</button>
                            </div>
                        @endif
                    </div>
                </div>

                <p class="text-gray-700 mb-4 whitespace-pre-line">{{ $post->content }}</p>

                {{-- Imagens do post --}}
                @if(!empty($post->images))
                    <div class="flex flex-wrap gap-3 mb-4">
                        @foreach($post->images as $img)
                            <div class="w-32 h-32 border rounded-lg overflow-hidden">
                                <img src="{{ asset('storage/' . $img) }}" class="object-cover w-full h-full">
                            </div>
                        @endforeach
                    </div>
                @endif

                {{-- Respostas --}}
                <div class="border-t pt-4">
                    <h5 class="text-sm font-semibold mb-3 text-gray-600">Respostas ({{ $post->replies->count() }})</h5>
                    <div class="space-y-4">
                        @foreach ($post->replies as $reply)
                            <div class="flex items-start gap-3 bg-gray-50 p-3 rounded-lg border border-gray-200">
                                <img src="{{ $reply->author->user_icon ? asset('storage/' . $reply->author->user_icon) : asset('images/default-icon.png') }}"
                                    class="w-8 h-8 rounded-full object-cover">
                                <div class="flex-1">
                                    <div class="flex items-center justify-between">
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
                    </div>

                    {{-- Formulário de reply --}}
                    <form wire:submit.prevent="createReply({{ $post->id }})" class="mt-4">
                        <textarea wire:model.defer="newReplyContent.{{ $post->id }}" rows="2"
                            class="w-full border-gray-300 rounded-lg shadow-sm text-sm focus:border-blue-500 focus:ring-blue-500"
                            placeholder="Deixe sua resposta..."></textarea>
                        @error("newReplyContent.{$post->id}") <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                        <div class="mt-2 text-right">
                            <button type="submit"
                                class="bg-blue-500 hover:bg-blue-600 text-white text-sm font-semibold py-1.5 px-4 rounded-lg transition">Responder</button>
                        </div>
                    </form>
                </div>
            </div>
        @empty
            <div class="text-center p-10 bg-gray-50 rounded-lg shadow-md">
                <p class="text-gray-500">Nenhum post ainda. O coordenador pode criar o primeiro!</p>
            </div>
        @endforelse
    </div>

    {{-- Paginação --}}
    <div class="mt-8">
        {{ $posts->links() }}
    </div>
</div>