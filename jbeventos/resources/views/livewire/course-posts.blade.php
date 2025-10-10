<div>
    {{-- Formulário de criação de post (Visível apenas para o Coordenador) --}}
    @if ($isCoordinator)
        <div class="mb-6 p-6 bg-white rounded-xl shadow-lg border border-red-100">
            <h4 class="text-lg font-extrabold mb-4 text-gray-800 border-b pb-3">
                <span class="text-red-600">|</span> O que há de novo?
            </h4>
            <form wire:submit.prevent="createPost">
                {{-- Área de Texto --}}
                <textarea wire:model.defer="newPostContent" rows="3"
                    class="w-full border-gray-300 rounded-lg shadow-inner text-base p-3 focus:border-red-500 focus:ring-red-500 resize-none placeholder-gray-500"
                    placeholder="Publique uma novidade, um lembrete, etc."></textarea>
                @error('newPostContent') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror

                {{-- Seção de upload e preview de imagens --}}
                <div class="mt-4 flex flex-col md:flex-row md:items-center justify-between gap-4">
                    {{-- Botão de Upload --}}
                    <div>
                        <input type="file" wire:model="newlyUploadedImages" multiple accept="image/*"
                            class="hidden" id="file-upload">
                        <label for="file-upload"
                            class="inline-flex items-center gap-2 bg-black text-white px-4 py-2 rounded-full cursor-pointer hover:bg-red-600 transition text-sm font-semibold shadow-md">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none"
                                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
                                <circle cx="8.5" cy="8.5" r="1.5"></circle>
                                <polyline points="21 15 16 10 5 21"></polyline>
                            </svg>
                            Adicionar Fotos (Máx. 5)
                        </label>
                        @error('images.*') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    {{-- Botão de Postar --}}
                    <button type="submit"
                        class="bg-gray-700 hover:bg-gray-800 text-white font-bold py-2 px-6 rounded-full shadow-lg transition text-base md:w-auto w-full">
                        Publicar
                    </button>
                </div>

                {{-- Preview de Imagens Selecionadas --}}
                @if(!empty($images))
                    <div class="flex flex-wrap mt-4 gap-3 border-t pt-3">
                        @foreach($images as $index => $image)
                            <div class="relative w-24 h-24 border-2 border-red-300 rounded-lg overflow-hidden shadow-sm">
                                {{-- Preview da Imagem --}}
                                <img src="{{ $image->temporaryUrl() }}" alt="Preview da imagem {{ $index + 1 }}" class="object-cover w-full h-full">
                                
                                {{-- Botão para Remover --}}
                                <button type="button" wire:click="removeImage({{ $index }})"
                                    class="absolute top-0 right-0 transform translate-x-1 -translate-y-1 bg-red-600 text-white rounded-full p-1 shadow-md hover:bg-red-700 transition">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                                    </svg>
                                </button>
                            </div>
                        @endforeach
                    </div>
                @endif
            </form>
        </div>
    @endif

    {{-- Feedback (movido para fora do loop principal, para melhor visibilidade) --}}
    @if (session()->has('success'))
        <div class="mb-4 p-4 bg-green-100 border border-green-300 text-green-800 rounded-xl text-sm font-medium shadow-sm" x-data="{ open: true }" x-show="open" x-init="setTimeout(() => { open = false }, 3000)">
            <p>{{ session('success') }}</p>
        </div>
    @endif
    @if (session()->has('error'))
        <div class="mb-4 p-4 bg-red-100 border border-red-300 text-red-800 rounded-xl text-sm font-medium shadow-sm" x-data="{ open: true }" x-show="open" x-init="setTimeout(() => { open = false }, 3000)">
            <p>{{ session('error') }}</p>
        </div>
    @endif

    {{-- Lista de posts --}}
    <div class="space-y-6">
        @forelse ($posts as $post)
            {{-- Card de Post --}}
            <div class="bg-white rounded-xl p-6 shadow-xl border border-gray-100">
                
                {{-- Cabeçalho do Post --}}
                <div class="flex items-start justify-between mb-4">
                    <div class="flex items-center gap-3">
                        {{-- CORRIGIDO: Usando o acessor user_icon_url --}}
                        <img src="{{ $post->author->user_icon_url }}"
                            class="w-10 h-10 rounded-full object-cover border border-gray-200 shadow-sm">
                        <div>
                            <a href="#" class="text-gray-900 font-bold text-base hover:text-red-600 transition">{{ $post->author->name }}</a>
                            <p class="text-xs text-red-600 font-semibold">
                                @if($post->user_id === optional(optional($course->courseCoordinator)->userAccount)->id)
                                    Coordenador do Curso
                                @else
                                    Membro do Curso
                                @endif
                            </p>
                        </div>
                    </div>
                    <span class="text-xs text-gray-500 pt-1">{{ $post->created_at->diffForHumans() }}</span>
                </div>

                {{-- Conteúdo do Post --}}
                <p class="text-gray-700 text-sm mb-4 whitespace-pre-line">{{ $post->content }}</p>

                {{-- Imagens do Post --}}
                @if(!empty($post->images))
                    @php
                        $imageCount = count($post->images);
                        $gridClass = match($imageCount) {
                            1 => 'grid-cols-1',
                            2 => 'grid-cols-2',
                            3 => 'grid-cols-3',
                            4 => 'grid-cols-2', // Layout 2x2
                            default => 'grid-cols-3', // Mais que 4, layout 3-colunas
                        };
                        $heightClass = $imageCount === 1 ? 'max-h-96' : 'h-40';
                    @endphp
                    <div class="mb-4 grid {{ $gridClass }} gap-3">
                        @foreach($post->images as $img)
                            <div class="w-full {{ $heightClass }} rounded-lg overflow-hidden shadow-md border border-gray-200 cursor-pointer hover:shadow-lg transition">
                                {{-- CORREÇÃO PRINCIPAL: Usando a variável $img para o caminho da imagem do post --}}
                                <img src="{{ asset('storage/' . $img) }}" alt="Imagem do post" class="object-cover w-full h-full">
                            </div>
                        @endforeach
                    </div>
                @endif
                
                {{-- Rodapé / Ações do Post --}}
                <div class="flex justify-between items-center border-t pt-4">
                    <div class="flex gap-4">
                        {{-- Botão de Edição (Somente para Coordenador que postou) --}}
                        @if($isCoordinator && auth()->id() === $post->user_id)
                            <a href="#" class="text-black hover:text-red-800 text-xs font-semibold flex items-center gap-1 transition">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" /></svg>
                                Editar
                            </a>
                        @endif
                        
                        {{-- Botão de Exclusão (Coordenador ou Autor) --}}
                        @if(auth()->id() === $post->user_id || $isCoordinator)
                            <button wire:click="deletePost({{ $post->id }})" wire:confirm="Tem certeza que deseja excluir este post? Todas as respostas serão perdidas."
                                class="text-red-600 hover:text-red-800 text-xs font-semibold flex items-center gap-1 transition">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                Excluir
                            </button>
                        @endif
                    </div>
                    
                    <p class="text-sm font-semibold text-gray-600">{{ $post->replies->count() }} Respostas</p>
                </div>

                {{-- Seção de Respostas --}}
                <div class="mt-5 pt-3 border-t border-gray-100">
                    <div class="space-y-4">
                        @foreach ($post->replies as $reply)
                            <div class="flex items-start gap-3 p-3 bg-gray-50 rounded-xl border border-gray-200">
                                {{-- CORRIGIDO: Usando o acessor user_icon_url --}}
                                <img src="{{ $reply->author->user_icon_url }}"
                                    class="w-7 h-7 rounded-full object-cover border border-white shadow-sm">
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center justify-between">
                                        <p class="text-sm font-semibold text-gray-800 truncate">
                                            {{ $reply->author->name }}
                                            @if($reply->user_id === optional(optional($course->courseCoordinator)->userAccount)->id)
                                                <span class="text-red-600 text-xs font-bold ml-1">(Coord.)</span>
                                            @endif
                                            <span class="text-gray-500 font-normal ml-2 text-xs">{{ $reply->created_at->diffForHumans() }}</span>
                                        </p>
                                        {{-- Botão de Exclusão da Resposta --}}
                                        @if(auth()->id() === $reply->author->id || $isCoordinator)
                                            <button wire:click="deleteReply({{ $reply->id }})" wire:confirm="Deseja realmente excluir esta resposta?"
                                                    class="text-red-500 hover:text-red-700 text-xs font-semibold p-1 transition">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" /></svg>
                                            </button>
                                        @endif
                                    </div>
                                    <p class="text-sm text-gray-700 mt-1 whitespace-pre-line">{{ $reply->content }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    {{-- Formulário de Nova Resposta --}}
                    <form wire:submit.prevent="createReply({{ $post->id }})" class="mt-5 pt-3 border-t border-gray-100">
                        <textarea wire:model.defer="newReplyContent.{{ $post->id }}" rows="2"
                            class="w-full border-gray-300 rounded-lg shadow-inner text-sm p-3 focus:border-red-500 focus:ring-red-500 resize-none placeholder-gray-500"
                            placeholder="Deixe sua resposta para este post..."></textarea>
                        @error("newReplyContent.{$post->id}") <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                        <div class="mt-2 text-right">
                            <button type="submit"
                                class="bg-black hover:bg-red-700 text-white text-sm font-bold py-1.5 px-5 rounded-full shadow transition">
                                Responder
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        @empty
            <div class="text-center p-10 bg-white rounded-xl shadow-lg border-2 border-dashed border-gray-300">
                <p class="text-gray-500 text-base font-medium">Nenhum post foi criado neste curso ainda. <br> O coordenador pode criar o primeiro!</p>
            </div>
        @endforelse
    </div>

    {{-- Paginação --}}
    <div class="mt-8">
        {{ $posts->links() }}
    </div>
</div>