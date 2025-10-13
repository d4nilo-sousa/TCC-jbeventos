<div x-data="{ 
    // Variáveis Alpine para controle dos modais
    showPostModal: false, 
    showReplyModal: false,
    postIdToDelete: null,
    replyIdToDelete: null,
    postContentToDelete: '',
    replyContentToDelete: '',

    // VARIÁVEIS PARA LIGHTBOX DE CARROSSEL
    showLightbox: false,
    lightboxImageUrls: [], // Lista de todas as URLs do post
    currentImageIndex: 0,  // Índice da imagem atual no carrossel

    // Função que abre o Lightbox e inicializa o carrossel
    openLightbox(urls, initialIndex) {
        // urls é um array de strings [url1, url2, ...]
        this.lightboxImageUrls = urls;
        this.currentImageIndex = initialIndex;
        this.showLightbox = true;
    },

    // Ações de Navegação
    nextImage() {
        if (this.currentImageIndex < this.lightboxImageUrls.length - 1) {
            this.currentImageIndex++;
        }
    },

    prevImage() {
        if (this.currentImageIndex > 0) {
            this.currentImageIndex--;
        }
    },
    
    // Propriedades Computadas para desativação dos botões
    isFirstImage() {
        return this.currentImageIndex === 0;
    },
    isLastImage() {
        return this.currentImageIndex === (this.lightboxImageUrls.length - 1);
    }
}"
@keydown.arrow-right.window="nextImage()"
@keydown.arrow-left.window="prevImage()"
>
    
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
                    placeholder="Publique uma novidade, um lembrete, etc. (Opcional se houver foto)"></textarea>
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
                        @php
                            // Mapeia as URLs temporárias das novas imagens para o lightbox
                            $tempUrls = collect($images)->map(fn($img) => $img->temporaryUrl())->toArray();
                            $tempUrlsJson = json_encode($tempUrls);
                        @endphp
                        @foreach($images as $index => $image)
                            <div class="relative w-24 h-24 border-2 border-red-300 rounded-lg overflow-hidden shadow-sm">
                                <img src="{{ $image->temporaryUrl() }}" alt="Preview da imagem {{ $index + 1 }}" 
                                    class="object-contain w-full h-full bg-gray-100 cursor-pointer"
                                    @click="openLightbox({{ $tempUrlsJson }}, {{ $index }})"> {{-- Lightbox inicializa no índice clicado --}}
                                
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

    {{-- Feedback --}}
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
                
                {{-- Cabeçalho do Post / Modo de Edição (Imagens) --}}
                {{-- Ele deve aparecer sempre que o post NÃO estiver em modo de edição --}}
                    @if ($editingPostId !== $post->id)
                        <div class="flex items-start gap-3 mb-4">
                            {{-- Foto de Perfil do Autor do Post --}}
                            <a href="{{ route('profile.view', $post->author) }}">
                            <img src="{{ $post->author->user_icon_url }}"
                                class="w-10 h-10 rounded-full object-cover border-2 border-red-500 shadow-md"> </a>
                            <div class="flex-1 min-w-0"> 
                                <p class="text-sm font-bold text-gray-900 truncate">
                                    {{ $post->author->name }}
                                    {{-- Indicador de Coordenador (se for o coordenador) --}}
                                    @if($post->user_id === optional(optional($course->courseCoordinator)->userAccount)->id)
                                        <span class="text-red-600 text-xs font-bold ml-1">(Coordenador)</span>
                                    @endif
                                </p>
                                {{-- Data de Criação do Post --}}
                                <p class="text-xs text-gray-500">{{ $post->created_at->diffForHumans() }}</p>
                            </div>
                             {{-- Botões de Ação para o Post --}}
                            <div class="flex gap-2">
                                {{-- Botão de Edição --}}
                                @if(auth()->id() === $post->user_id || $isCoordinator)
                                    @if ($editingPostId !== $post->id && $editingReplyId === null)
                                        <button type="button" wire:click="startEdit({{ $post->id }})"
                                            class="text-black hover:text-red-800 text-xs font-semibold flex items-center gap-1 transition">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" /></svg>
                                            Editar
                                        </button>
                                    @endif
                                @endif
                                
                                {{-- BOTÃO DE EXCLUSÃO --}}
                                @if(auth()->id() === $post->user_id || $isCoordinator)
                                    <button type="button" 
                                        @click="
                                            postIdToDelete = {{ $post->id }};
                                            postContentToDelete = '{{ Str::limit(str_replace(["\n", "\r"], ' ', addslashes($post->content)), 40, '...') }}';
                                            showPostModal = true;
                                        "
                                        class="text-red-600 hover:text-red-800 text-xs font-semibold flex items-center gap-1 transition">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                        Excluir
                                    </button>
                                @endif
                            </div>
                        </div>
                    @endif

                {{-- Conteúdo do Post / Formulário de Edição do Post --}}
                @if ($editingPostId === $post->id)
                    {{-- MODO DE EDIÇÃO DO POST --}}
                    <form wire:submit.prevent="updatePost" class="mb-4">
                        <textarea wire:model.defer="editingPostContent" rows="4"
                            class="w-full border-red-300 rounded-lg shadow-inner text-sm p-3 focus:border-red-500 focus:ring-red-500 resize-none placeholder-gray-500 mb-2"
                            placeholder="Edite seu conteúdo aqui (Opcional se houver foto)"></textarea>
                        @error('editingPostContent') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                        
                        {{-- SEÇÃO DE EDIÇÃO DE IMAGENS DO POST --}}
                        <div class="border-t pt-3 mt-3">
                            <h5 class="text-sm font-semibold mb-2 text-gray-700">Imagens (Máx. 5 no total)</h5>

                            @php
                                // Cria a lista de URLs combinadas para o Lightbox no modo de edição
                                $currentUrls = collect($editingPostCurrentImages)->map(fn($img) => asset('storage/' . $img))->toArray();
                                $newUrls = collect($editingPostNewImages)->map(fn($img) => $img->temporaryUrl())->toArray();
                                $allEditingUrls = array_merge($currentUrls, $newUrls);
                                $allEditingUrlsJson = json_encode($allEditingUrls);

                                $currentImageCount = count($editingPostCurrentImages);
                            @endphp

                            <div class="flex flex-wrap gap-3 mb-4">
                                {{-- 1. Preview de IMAGENS ATUAIS (já salvas no DB) --}}
                                @foreach($editingPostCurrentImages as $index => $imageUrl)
                                    <div class="relative w-24 h-24 border-2 border-red-500 rounded-lg overflow-hidden shadow-sm">
                                        @php $fullUrl = asset('storage/' . $imageUrl); @endphp
                                        <img src="{{ $fullUrl }}" alt="Imagem atual" 
                                            class="object-contain w-full h-full bg-gray-100 cursor-pointer"
                                            @click="openLightbox({{ $allEditingUrlsJson }}, {{ $index }})"> {{-- Lightbox inicializa no índice clicado (0 a N) --}}
                                        
                                        {{-- Botão para Remover Imagem ATUAL --}}
                                        <button type="button" wire:click="removeEditingImage({{ $index }}, false)"
                                            class="absolute top-0 right-0 transform translate-x-1 -translate-y-1 bg-red-600 text-white rounded-full p-1 shadow-md hover:bg-red-700 transition"
                                            title="Remover Imagem Atual">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                                            </svg>
                                        </button>
                                    </div>
                                @endforeach

                                {{-- 2. Preview de NOVAS IMAGENS (upload temporário) --}}
                                @foreach($editingPostNewImages as $index => $image)
                                    <div class="relative w-24 h-24 border-2 border-green-500 rounded-lg overflow-hidden shadow-sm">
                                        @php $tempUrl = $image->temporaryUrl(); @endphp
                                        <img src="{{ $tempUrl }}" alt="Nova imagem" 
                                            class="object-contain w-full h-full bg-gray-100 cursor-pointer"
                                            @click="openLightbox({{ $allEditingUrlsJson }}, {{ $currentImageCount + $index }})"> {{-- Índice correto = atuais + novas --}}
                                        
                                        {{-- Botão para Remover Nova Imagem --}}
                                        <button type="button" wire:click="removeEditingImage({{ $index }}, true)"
                                            class="absolute top-0 right-0 transform translate-x-1 -translate-y-1 bg-red-600 text-white rounded-full p-1 shadow-md hover:bg-red-700 transition"
                                            title="Remover Nova Imagem">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                                            </svg>
                                        </button>
                                    </div>
                                @endforeach
                            </div>
                            
                            {{-- 3. Botão de Upload de Mais Fotos --}}
                            @php
                                $totalImages = count($editingPostCurrentImages) + count($editingPostNewImages);
                                $remaining = 5 - $totalImages;
                            @endphp

                            @if($remaining > 0)
                                <input type="file" wire:model="editingPostNewImages" multiple accept="image/*"
                                    class="hidden" id="edit-file-upload-{{ $post->id }}">
                                <label for="edit-file-upload-{{ $post->id }}"
                                    class="inline-flex items-center gap-2 bg-gray-700 text-white px-3 py-1.5 rounded-full cursor-pointer hover:bg-red-600 transition text-xs font-semibold shadow-md">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none"
                                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
                                        <circle cx="8.5" cy="8.5" r="1.5"></circle>
                                        <polyline points="21 15 16 10 5 21"></polyline>
                                    </svg>
                                    Adicionar Fotos (Máx. {{ $remaining }})
                                </label>
                            @else
                                <p class="text-sm text-red-500 font-medium">Limite máximo de 5 fotos atingido.</p>
                            @endif
                            @error('editingPostNewImages.*') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                        </div>
                        {{-- FIM SEÇÃO DE EDIÇÃO DE IMAGENS DO POST --}}

                        <div class="flex justify-end gap-2 mt-4">
                            <button type="button" wire:click="cancelEdit"
                                class="px-3 py-1 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 text-xs font-semibold transition">
                                Cancelar
                            </button>
                            <button type="submit"
                                class="px-3 py-1 bg-red-600 text-white font-semibold rounded-lg hover:bg-red-700 text-xs transition">
                                Salvar Edição
                            </button>
                        </div>
                    </form>
                @else
                    {{-- MODO DE VISUALIZAÇÃO DO POST --}}
                    @if ($post->content)
                        <p class="text-gray-700 text-sm mb-4 whitespace-pre-line">{{ $post->content }}</p>
                    @endif
                @endif
                
                {{-- Imagens do Post (apenas em modo de visualização) --}}
                @if(!empty($post->images) && $editingPostId !== $post->id)
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
                        
                        // Mapeia todas as URLs do post para o lightbox
                        $postUrls = collect($post->images)->map(fn($img) => asset('storage/' . $img))->toArray();
                        $postUrlsJson = json_encode($postUrls);
                    @endphp
                    <div class="mb-4 grid {{ $gridClass }} gap-3">
                        @foreach($post->images as $index => $img)
                            <div class="w-full {{ $heightClass }} rounded-lg overflow-hidden shadow-md border border-gray-200 cursor-pointer hover:shadow-lg transition">
                                <img src="{{ asset('storage/' . $img) }}" alt="Imagem do post" 
                                    class="w-full h-full object-contain bg-gray-100 cursor-pointer"
                                    @click="openLightbox({{ $postUrlsJson }}, {{ $index }})"> {{-- Lightbox: passa TODAS as URLs e o índice clicado --}}
                            </div>
                        @endforeach
                    </div>
                @endif
                
                {{-- Rodapé / Ações do Post - REMOVIDO os botões de ação duplicados (movidos para o cabeçalho) --}}
                <div class="flex justify-end items-center border-t pt-4">
                    <p class="text-sm font-semibold text-gray-600">{{ $post->replies->count() }} Respostas</p>
                </div>

                {{-- Seção de Respostas --}}
                <div class="mt-5 pt-3 border-t border-gray-100">
                    <div class="space-y-4">
                        @foreach ($post->replies as $reply)
                            {{-- MODO DE EDIÇÃO DE RESPOSTA (INLINE) --}}
                            @if ($editingReplyId === $reply->id)
                                <div class="p-3 bg-red-50 rounded-xl border-2 border-red-300">
                                    <form wire:submit.prevent="updateReply">
                                        {{-- Área de Texto da Edição --}}
                                        <textarea wire:model.defer="editingReplyContent" rows="2"
                                            class="w-full border-red-400 rounded-lg shadow-inner text-sm p-2 focus:border-red-500 focus:ring-red-500 resize-none placeholder-gray-500 mb-2"
                                            placeholder="Edite sua resposta..."></textarea>
                                        @error('editingReplyContent') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                                        
                                        {{-- Edição/Preview da Imagem da Resposta --}}
                                        <div class="flex items-center gap-3">
                                            @if($editingReplyNewImage)
                                                {{-- Nova Imagem (Preview) --}}
                                                <div class="relative w-20 h-20 border-2 border-green-500 rounded-lg overflow-hidden shadow-sm flex-shrink-0">
                                                    <img src="{{ $editingReplyNewImage->temporaryUrl() }}" alt="Nova imagem de resposta" class="object-contain w-full h-full bg-gray-100">
                                                    <button type="button" wire:click="$set('editingReplyNewImage', null)"
                                                        class="absolute top-0 right-0 transform translate-x-1 -translate-y-1 bg-red-600 text-white rounded-full p-1 shadow-md hover:bg-red-700 transition"
                                                        title="Remover Nova Imagem">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" /></svg>
                                                    </button>
                                                </div>
                                                <p class="text-xs text-green-700 font-semibold flex-1">Nova foto pronta para upload.</p>
                                            @elseif($editingReplyCurrentImage)
                                                {{-- Imagem Atual (Remoção) --}}
                                                <div class="relative w-20 h-20 border-2 border-red-500 rounded-lg overflow-hidden shadow-sm flex-shrink-0">
                                                    <img src="{{ asset('storage/' . $editingReplyCurrentImage) }}" alt="Imagem de resposta atual" class="object-contain w-full h-full bg-gray-100">
                                                    <button type="button" wire:click="removeReplyImage"
                                                        class="absolute top-0 right-0 transform translate-x-1 -translate-y-1 bg-red-600 text-white rounded-full p-1 shadow-md hover:bg-red-700 transition"
                                                        title="Remover Imagem Atual">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" /></svg>
                                                    </button>
                                                </div>
                                                <p class="text-xs text-gray-700 flex-1">Imagem atual. Clique no "X" para remover.</p>
                                            @else
                                                {{-- Input de Nova Imagem --}}
                                                <input type="file" wire:model="editingReplyNewImage" accept="image/*" class="hidden" id="edit-reply-file-{{ $reply->id }}">
                                                <label for="edit-reply-file-{{ $reply->id }}"
                                                    class="inline-flex items-center gap-2 bg-gray-700 text-white px-3 py-1.5 rounded-full cursor-pointer hover:bg-red-600 transition text-xs font-semibold shadow-md">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none"
                                                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                        <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
                                                        <circle cx="8.5" cy="8.5" r="1.5"></circle>
                                                        <polyline points="21 15 16 10 5 21"></polyline>
                                                    </svg>
                                                    Adicionar Foto (Máx. 512KB)
                                                </label>
                                            @endif
                                            @error('editingReplyNewImage') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                                        </div>

                                        <div class="mt-3 flex justify-end gap-2">
                                            <button type="button" wire:click="cancelEditReply"
                                                class="px-3 py-1 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 text-xs font-semibold transition">
                                                Cancelar
                                            </button>
                                            <button type="submit"
                                                class="px-3 py-1 bg-red-600 text-white font-semibold rounded-lg hover:bg-red-700 text-xs transition">
                                                Salvar Resposta
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            @else
                                {{-- MODO DE VISUALIZAÇÃO DE RESPOSTA --}}
                                <div class="flex items-start gap-3 p-3 bg-gray-50 rounded-xl border border-gray-200">
                                    <a href="{{ route('profile.view', $reply->author) }}">
                                    <img src="{{ $reply->author->user_icon_url }}" 
                                        class="w-7 h-7 rounded-full object-cover border border-white shadow-sm flex-shrink-0"> </a>
                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-center justify-between">
                                            <p class="text-sm font-semibold text-gray-800 truncate">
                                                {{ $reply->author->name }}
                                                @if($reply->user_id === optional(optional($course->courseCoordinator)->userAccount)->id)
                                                    <span class="text-red-600 text-xs font-bold ml-1">(Coord.)</span>
                                                @endif
                                                <span class="text-gray-500 font-normal ml-2 text-xs">{{ $reply->created_at->diffForHumans() }}</span>
                                            </p>
                                            <div class="flex gap-1 items-center">
                                                {{-- BOTÃO DE EDIÇÃO DA RESPOSTA --}}
                                                @if(auth()->id() === $reply->author->id) <!-- se for o autor da resposta -->
                                                    <button type="button" wire:click="startEditReply({{ $reply->id }})"
                                                        class="text-gray-500 hover:text-red-700 text-xs font-semibold p-1 transition"
                                                        title="Editar Resposta">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" /></svg>
                                                    </button>
                                                @endif

                                                {{-- BOTÃO DE EXCLUSÃO DA RESPOSTA --}}
                                                @if(auth()->id() === $reply->author->id || $isCoordinator)
                                                    <button type="button" 
                                                        @click="
                                                            replyIdToDelete = {{ $reply->id }};
                                                            replyContentToDelete = '{{ Str::limit(str_replace(["\n", "\r"], ' ', addslashes($reply->content)), 25, '...') }}';
                                                            showReplyModal = true;
                                                        "
                                                        class="text-red-500 hover:text-red-700 text-xs font-semibold p-1 transition">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" /></svg>
                                                    </button>
                                                @endif
                                            </div>
                                        </div>
                                        <p class="text-sm text-gray-700 mt-1 whitespace-pre-line">{{ $reply->content }}</p>
                                        
                                        {{-- Exibição da Imagem da Resposta --}}
                                        @if($reply->image)
                                            <div class="mt-2 w-32 h-32 rounded-lg overflow-hidden border border-gray-300 shadow-sm cursor-pointer hover:shadow-md transition flex-shrink-0"
                                                @click="openLightbox(['{{ asset('storage/' . $reply->image) }}'], 0)">
                                                <img src="{{ asset('storage/' . $reply->image) }}" alt="Imagem da Resposta" 
                                                    class="w-full h-full object-contain bg-gray-100">
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @endif
                        @endforeach
                    </div>

                    {{-- Formulário de Nova Resposta (MODIFICADO para incluir upload) --}}
                    <form wire:submit.prevent="createReply({{ $post->id }})" class="mt-5 pt-3 border-t border-gray-100">
                        <textarea wire:model.defer="newReplyContent.{{ $post->id }}" rows="2"
                            class="w-full border-gray-300 rounded-lg shadow-inner text-sm p-3 focus:border-red-500 focus:ring-red-500 resize-none placeholder-gray-500"
                            placeholder="Deixe sua resposta para este post..."></textarea>
                        @error("newReplyContent.{$post->id}") <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                        
                        <div class="mt-2 flex justify-between items-end">
                            {{-- Input de Imagem para Resposta --}}
                            <div>
                                <input type="file" wire:model.defer="newReplyImage.{{ $post->id }}" accept="image/*"
                                    class="hidden" id="reply-file-upload-{{ $post->id }}">
                                <label for="reply-file-upload-{{ $post->id }}"
                                    class="inline-flex items-center gap-2 bg-black text-white px-3 py-1.5 rounded-full cursor-pointer hover:bg-red-600 transition text-xs font-semibold shadow-md">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none"
                                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
                                        <circle cx="8.5" cy="8.5" r="1.5"></circle>
                                        <polyline points="21 15 16 10 5 21"></polyline>
                                    </svg>
                                    Foto (Máx. 512KB)
                                </label>
                                @if(isset($newReplyImage[$post->id]) && $newReplyImage[$post->id] instanceof \Livewire\Features\SupportFileUploads\TemporaryUploadedFile)
                                    <span class="text-green-600 text-xs ml-2 font-medium">Foto selecionada!</span>
                                @endif
                                @error("newReplyImage.{$post->id}") <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                            </div>
                            
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


    {{-------------------------------------}}
    {{-- MODAIS DE EXCLUSÃO (INALTERADOS) --}}
    {{-------------------------------------}}
    
    {{-- Modal de Exclusão de POST --}}
    <div x-show="showPostModal" x-cloak
        class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
    >
        <div class="bg-white p-6 rounded-xl shadow-2xl w-full max-w-md mx-4"
            @click.away="showPostModal = false"
            x-transition:enter="transition ease-out duration-300 transform"
            x-transition:enter-start="scale-95"
            x-transition:enter-end="scale-100"
            x-transition:leave="transition ease-in duration-200 transform"
            x-transition:leave-start="scale-100"
            x-transition:leave-end="scale-95">
            <h2 class="text-xl font-bold mb-4 text-red-600">Confirmar Exclusão do Post</h2>
            
            <p class="text-gray-700">Tem certeza que deseja excluir o post 
                <strong x-text="postContentToDelete ? ('"' + postContentToDelete + '"') : 'selecionado'"></strong>?
            </p>
            <p class="text-sm text-red-500 mt-2 font-semibold">
                Esta ação é irreversível e todas as respostas associadas serão perdidas.
            </p>
            
            <div class="mt-6 flex justify-end space-x-3">
                <button @click="showPostModal = false"
                    class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition font-semibold">
                    Cancelar
                </button>
                
                {{-- Ação Livewire para exclusão --}}
                <button 
                    wire:click="deletePost(postIdToDelete)" 
                    @click="showPostModal = false"
                    class="px-4 py-2 bg-red-600 text-white font-semibold rounded-lg hover:bg-red-700 transition">
                    Excluir Post
                </button>
            </div>
        </div>
    </div>

    {{-- Modal de Exclusão de RESPOSTA --}}
    <div x-show="showReplyModal" x-cloak
        class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
    >
        <div class="bg-white p-6 rounded-xl shadow-2xl w-full max-w-sm mx-4"
            @click.away="showReplyModal = false"
            x-transition:enter="transition ease-out duration-300 transform"
            x-transition:enter-start="scale-95"
            x-transition:enter-end="scale-100"
            x-transition:leave="transition ease-in duration-200 transform"
            x-transition:leave-start="scale-100"
            x-transition:leave-end="scale-95">
            <h2 class="text-xl font-bold mb-4 text-red-600">Confirmar Exclusão da Resposta</h2>
            
            <p class="text-gray-700">
                Tem certeza que deseja excluir a resposta 
                <strong x-text="replyContentToDelete ? ('"' + replyContentToDelete + '"') : 'selecionada'"></strong>?
            </p>
            <p class="text-sm text-red-500 mt-2 font-semibold">Esta ação é irreversível.</p>
            
            <div class="mt-6 flex justify-end space-x-3">
                <button @click="showReplyModal = false"
                    class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition font-semibold">
                    Cancelar
                </button>
                
                {{-- Ação Livewire para exclusão --}}
                <button 
                    wire:click="deleteReply(replyIdToDelete)" 
                    @click="showReplyModal = false"
                    class="px-4 py-2 bg-red-600 text-white font-semibold rounded-lg hover:bg-red-700 transition">
                    Excluir Resposta
                </button>
            </div>
        </div>
    </div>
    
    
    {{-------------------------------------}}
    {{-- LIGHTBOX COM CARROSSEL (INALTERADO) --}}
    {{-------------------------------------}}
    <div x-show="showLightbox" x-cloak
        class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-90"
        @click.self="showLightbox = false" 
        @keydown.window.escape="showLightbox = false" {{-- Adicionado ESC para fechar --}}
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
    >
        <div class="relative w-full h-full p-4 flex items-center justify-center">
            
            {{-- Botão Anterior --}}
            <button @click.prevent="prevImage()" :disabled="isFirstImage()"
                class="absolute left-4 top-1/2 transform -translate-y-1/2 text-white bg-black bg-opacity-40 p-3 rounded-full hover:bg-red-600 transition z-50 disabled:opacity-30 disabled:hover:bg-black"
                :class="{'cursor-not-allowed': isFirstImage()}"
                title="Imagem Anterior (Seta Esquerda)">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
                </svg>
            </button>
            
            {{-- Imagem Atual (Conteúdo do Carrossel) --}}
            <div class="max-w-full max-h-full">
                <template x-for="(url, index) in lightboxImageUrls" :key="index">
                    <img x-show="index === currentImageIndex" :src="url" alt="Imagem expandida" 
                        class="max-w-full max-h-full object-contain rounded-lg shadow-2xl transition-opacity duration-300"
                        x-transition:enter="transition ease-out duration-300"
                        x-transition:enter-start="opacity-0"
                        x-transition:enter-end="opacity-100"
                        x-transition:leave="transition ease-in duration-200"
                        x-transition:leave-start="opacity-100"
                        x-transition:leave-end="opacity-0"
                    >
                </template>
            </div>
            
            {{-- Botão Próximo --}}
            <button @click.prevent="nextImage()" :disabled="isLastImage()"
                class="absolute right-4 top-1/2 transform -translate-y-1/2 text-white bg-black bg-opacity-40 p-3 rounded-full hover:bg-red-600 transition z-50 disabled:opacity-30 disabled:hover:bg-black"
                :class="{'cursor-not-allowed': isLastImage()}"
                title="Próxima Imagem (Seta Direita)">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                </svg>
            </button>
            
            {{-- Contador de Imagens --}}
            <div x-show="lightboxImageUrls.length > 1"
                    class="absolute bottom-4 left-1/2 transform -translate-x-1/2 text-white bg-black bg-opacity-50 px-3 py-1 rounded-full text-sm font-semibold z-50">
                <span x-text="currentImageIndex + 1"></span> de <span x-text="lightboxImageUrls.length"></span>
            </div>

            {{-- Botão de Fechar --}}
            <button @click="showLightbox = false"
                class="absolute top-4 right-4 text-white bg-black bg-opacity-50 p-2 rounded-full hover:bg-red-600 transition z-50"
                title="Fechar (Esc)">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
    </div>
</div>