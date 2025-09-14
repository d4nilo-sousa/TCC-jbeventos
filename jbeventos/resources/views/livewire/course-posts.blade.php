<div>
    {{-- Formulário de criação de post --}}
    @if ($isCoordinator)
        <div class="mb-6 p-6 bg-gray-50 rounded-2xl border border-gray-200 shadow-md">
            <h4 class="text-md font-bold mb-3 text-stone-700">Criar Novo Post</h4>
            <form wire:submit.prevent="createPost">
                <textarea wire:model.defer="newPostContent" rows="4"
                    class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500 resize-none"
                    placeholder="O que há de novo no curso?"></textarea>
                @error('newPostContent') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror

                {{-- Seção de upload e preview de imagens --}}
                <div class="flex items-center justify-between mt-3">
                    <input type="file" wire:model="newlyUploadedImages" multiple accept="image/*"
                        class="hidden" id="file-upload">
                    <label for="file-upload" class="bg-blue-100 text-blue-600 px-4 py-2 rounded-full cursor-pointer hover:bg-blue-200 transition text-sm font-semibold flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
                            <circle cx="8.5" cy="8.5" r="1.5"></circle>
                            <polyline points="21 15 16 10 5 21"></polyline>
                        </svg>
                        Adicionar Fotos
                    </label>

                    <button type="submit"
                        class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-6 rounded-lg shadow-md transition">
                        Postar
                    </button>
                </div>
                
                {{-- Preview --}}
                @if(!empty($images) && count($images) > 0)
                    <div class="flex flex-wrap mt-3 gap-3">
                        @foreach($images as $index => $image)
                            <div class="relative w-24 h-24 border rounded-lg overflow-hidden">
                                <img src="{{ $image->temporaryUrl() }}" class="object-cover w-full h-full">
                                <button type="button" wire:click="removeImage({{ $index }})"
                                    class="absolute top-1 right-1 bg-red-500 text-white rounded-full w-5 h-5 flex items-center justify-center text-xs">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </button>
                            </div>
                        @endforeach
                    </div>
                @endif
                @error('newlyUploadedImages.*') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
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
            {{-- Card de Post --}}
            <div class="bg-white rounded-2xl p-6 shadow-md border border-gray-100 hover:border-blue-300 transition-colors duration-200">
                <div class="flex items-center gap-4 mb-4">
                    <img src="{{ $post->author->user_icon ? asset('storage/' . $post->author->user_icon) : asset('images/default-icon.png') }}"
                            class="w-12 h-12 rounded-full object-cover">
                    <div class="flex-1">
                        <div class="flex justify-between items-center">
                            <a href="#" class="text-stone-800 font-semibold text-lg hover:underline">{{ $post->author->name }}</a>
                            <span class="text-xs text-gray-500">{{ $post->created_at->diffForHumans() }}</span>
                        </div>
                        @if($isCoordinator && auth()->id() === $post->user_id)
                            <div class="mt-1 flex gap-2">
                                <a href="#" class="text-blue-500 hover:underline text-xs font-semibold flex items-center gap-1">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                    </svg>
                                    Editar
                                </a>
                                <button wire:click="deletePost({{ $post->id }})"
                                        class="text-red-500 hover:underline text-xs font-semibold flex items-center gap-1">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                    Excluir
                                </button>
                            </div>
                        @endif
                    </div>
                </div>

                <p class="text-gray-700 mb-4 whitespace-pre-line">{{ $post->content }}</p>

                @if(!empty($post->images))
                    <div class="mt-4 mb-4">
                        @if(count($post->images) === 1)
                            <div class="rounded-lg overflow-hidden border border-gray-200">
                                <img src="{{ asset('storage/' . $post->images[0]) }}" alt="Imagem do post" class="object-cover w-full h-auto max-h-96">
                            </div>
                        @else
                            <div class="grid grid-cols-2 gap-3">
                                @foreach($post->images as $img)
                                    <div class="w-full h-48 rounded-lg overflow-hidden border border-gray-200">
                                        <img src="{{ asset('storage/' . $img) }}" alt="Imagem do post" class="object-cover w-full h-full">
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                @endif

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

                    <form wire:submit.prevent="createReply({{ $post->id }})" class="mt-4">
                        <textarea wire:model.defer="newReplyContent.{{ $post->id }}" rows="2"
                            class="w-full border-gray-300 rounded-lg shadow-sm text-sm focus:border-blue-500 focus:ring-blue-500 resize-none"
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
                <p class="text-gray-500">Nenhum post foi criado neste curso ainda. O coordenador pode criar o primeiro!</p>
            </div>
        @endforelse
    </div>

    {{-- Paginação --}}
    <div class="mt-8">
        {{ $posts->links() }}
    </div>
</div>