<div class="space-y-6">
    {{-- MENSAGEM DE SUCESSO/ERRO --}}
    @if (session()->has('success'))
        <div class="p-4 bg-green-100 border border-green-400 text-green-700 rounded-md" role="alert">
            {{ session('success') }}
        </div>
    @endif
    @if (session()->has('error'))
        <div class="p-4 bg-red-100 border border-red-400 text-red-700 rounded-md" role="alert">
            {{ session('error') }}
        </div>
    @endif

    {{-- FORMULÁRIO DE CRIAÇÃO DE POST (Apenas para Coordenadores) --}}
    @if ($isCoordinator)
        <div class="bg-white rounded-xl shadow border border-gray-100 p-6">
            <h3 class="text-xl font-semibold text-gray-800 mb-4">Criar Novo Post</h3>
            <form wire:submit.prevent="createPost" enctype="multipart/form-data">
                {{-- CAMPO DE CONTEÚDO --}}
                <textarea 
                    wire:model.defer="newPostContent" 
                    rows="4" 
                    class="w-full border-gray-300 rounded-md shadow-sm focus:border-red-500 focus:ring focus:ring-red-500 focus:ring-opacity-50" 
                    placeholder="O que você tem para compartilhar com o curso {{ optional($coordinatorCourses->first())->course_name }}?"
                ></textarea>
                @error('newPostContent') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                
                {{-- UPLOAD DE IMAGENS --}}
                <div class="mt-4">
                    <input 
                        type="file" 
                        wire:model="newlyUploadedImages" 
                        multiple 
                        accept="image/*"
                        class="block w-full text-sm text-gray-500
                                file:mr-4 file:py-2 file:px-4
                                file:rounded-full file:border-0
                                file:text-sm file:font-semibold
                                file:bg-red-50 file:text-red-700
                                hover:file:bg-red-100"
                    >
                    @error('images.*') <span class="text-red-500 text-sm mt-2 block">{{ $message }}</span> @enderror
                </div>

                {{-- PREVIEW DE IMAGENS --}}
                @if ($images)
                    <div class="mt-4 flex flex-wrap gap-2">
                        @foreach ($images as $index => $image)
                            <div class="relative w-20 h-20">
                                {{-- Verifica se o objeto é uma TemporaryUploadedFile para usar temporaryUrl() --}}
                                @if (is_object($image) && method_exists($image, 'temporaryUrl'))
                                    <img src="{{ $image->temporaryUrl() }}" alt="Preview" class="w-full h-full object-cover rounded">
                                @endif

                                <button type="button" wire:click="removeImage({{ $index }})" 
                                        class="absolute -top-1 -right-1 bg-red-600 text-white rounded-full p-1 leading-none text-xs hover:bg-red-700">
                                    &times;
                                </button>
                            </div>
                        @endforeach
                    </div>
                @endif

                <div class="flex justify-end mt-4">
                    <button 
                        type="submit" 
                        class="px-4 py-2 bg-red-600 text-white font-semibold rounded-md shadow-md hover:bg-red-700 transition duration-150"
                        wire:loading.attr="disabled"
                    >
                        <span wire:loading.remove wire:target="createPost">Publicar Post</span>
                        <span wire:loading wire:target="createPost">Publicando...</span>
                    </button>
                </div>
            </form>
        </div>
    @endif

    {{-- LISTAGEM DE POSTS PAGINADOS --}}
    @forelse ($posts as $post)
        {{-- CARTÃO DE POST --}}
        <div wire:key="post-{{ $post->id }}" class="feed-card bg-white rounded-xl shadow border border-gray-100 p-6 space-y-4">
            
            {{-- BLOCU DE GERENCIAMENTO (Editar/Excluir) --}}
            @if (Auth::id() === $post->user_id)
                <div class="flex justify-end space-x-2 -mt-3 -mr-3">
                    
                    {{-- Botão de Excluir --}}
                    <button 
                        wire:click="deletePost({{ $post->id }})" 
                        wire:confirm="Tem certeza que deseja excluir este post?" 
                        class="p-2 text-sm text-red-500 hover:text-red-700 hover:bg-red-50 rounded-full transition duration-150"
                        title="Excluir Post"
                    >
                        <i class="ph ph-trash text-xl"></i>
                    </button>

                    {{-- Botão de Editar --}}
                    <button 
                        wire:click="startEditPost({{ $post->id }})" 
                        class="p-2 text-sm text-gray-500 hover:text-blue-700 hover:bg-blue-50 rounded-full transition duration-150"
                        title="Editar Post"
                    >
                        <i class="ph ph-pencil-simple text-xl"></i>
                    </button>
                </div>
            @endif
            {{-- FIM DO BLOCO DE GERENCIAMENTO --}}
            
            <div class="flex items-start space-x-3">
                <div class="flex-shrink-0">
                    {{-- Avatar --}}
                    <div class="h-10 w-10 bg-gray-200 rounded-full flex items-center justify-center text-gray-600">
                        <img src ="{{ Auth::user()->user_icon_url }}" alt="{{ Auth::user()->name }}" class="size-9 rounded-full object-cover border-2 border-gray-300 hover:border-red-500 transition shadow-md">
                    </div>
                </div>
                <div class="flex-1 min-w-0">
                    <div class="flex items-center space-x-2">
                        <span class="font-semibold text-gray-900 truncate">{{ $post->author->name }}</span>
                        @if ($post->author->user_type === 'coordinator')
                            <span class="text-xs bg-red-100 text-red-600 px-2 py-0.5 rounded-full font-medium">
                                Coordenador
                            </span>
                        @endif
                    </div>
                    <div class="text-sm text-gray-500">
                        {{ $post->created_at->diffForHumans() }} 
                        <span class="mx-1">•</span> 
                        <span class="font-medium text-red-600">{{ optional($post->course)->course_name ?? 'Geral' }}</span>
                    </div>
                </div>
            </div>

            <p class="text-gray-800 whitespace-pre-wrap">
                {{ $post->content }}
            </p>
            
            {{-- IMAGENS DO POST --}}
            @if ($post->images && count($post->images) > 0)
                <div class="grid gap-2 {{ count($post->images) > 1 ? 'grid-cols-2' : 'grid-cols-1' }}">
                    @foreach ($post->images as $imagePath)
                        <img src="{{ asset('storage/' . $imagePath) }}" alt="Post Image" 
                             class="w-full h-auto object-cover rounded-lg shadow-md max-h-80 cursor-pointer"
                             onclick="window.open(this.src)"
                             loading="lazy"
                        >
                    @endforeach
                </div>
            @endif
            
            {{-- BOTÕES DE INTERAÇÃO --}}
            <div class="flex justify-between items-center border-t border-gray-100 pt-4">
                <button wire:click="openPostModal({{ $post->id }})" class="text-sm font-medium text-gray-600 hover:text-red-600 transition">
                    Ver {{ $post->replies->count() }} Respostas
                </button>
            </div>
        </div>
    @empty
        {{-- MENSAGEM DE QUANDO NÃO HÁ POSTS --}}
        @if (!$isCoordinator)
             <div class="p-4 bg-white rounded-xl shadow border border-gray-100 text-center text-gray-500">Nenhum post para exibir.</div>
        @else
            <div class="p-4 bg-white rounded-xl shadow border border-gray-100 text-center text-gray-500">
                Seja o primeiro a publicar um post!
            </div>
        @endif
    @endforelse

    {{-- PAGINAÇÃO --}}
    <div class="mt-6">
        {{ $posts->links() }}
    </div>


    {{-- ------------------------------------------------------------------------------------------------ --}}
    {{-- MODAL DE EXPANSÃO (RESPOSTAS) --}}
    {{-- ------------------------------------------------------------------------------------------------ --}}
    @if ($selectedPostId)
        @include('livewire.feed-posts-expanded') {{-- Assumindo que este arquivo existe e lida com o $expandedPost --}}
    @endif
    
    {{-- ------------------------------------------------------------------------------------------------ --}}
    {{-- MODAL DE EDIÇÃO DE POST --}}
    {{-- ------------------------------------------------------------------------------------------------ --}}
    @if ($editingPostId)
        <div x-data="{ open: @entangle('editingPostId').not(null) }" x-show="open" 
            x-transition:enter="ease-out duration-300"
            x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
            x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
            x-transition:leave="ease-in duration-200"
            x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
            x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
            class="fixed inset-0 z-[100] overflow-y-auto" role="dialog" aria-modal="true" aria-labelledby="edit-modal-title">
            
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div x-on:click="open = false; $wire.resetEditModal()" class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>

                <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                    <form wire:submit.prevent="saveEditPost">
                        <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                            <h3 class="text-xl leading-6 font-medium text-gray-900 border-b pb-2" id="edit-modal-title">
                                Editar Post
                            </h3>
                            <div class="mt-4">
                                <textarea wire:model.defer="editingPostContent" rows="5" class="w-full border-gray-300 rounded-md shadow-sm focus:border-red-500 focus:ring focus:ring-red-500 focus:ring-opacity-50" placeholder="O que você tem para compartilhar?"></textarea>
                                @error('editingPostContent') <span class="text-red-500 text-sm block mt-1">{{ $message }}</span> @enderror

                                <div class="mt-4 flex flex-wrap gap-2">
                                    @foreach ($editingPostImages as $index => $image)
                                        <div class="relative w-20 h-20">
                                            @php
                                                // Exibe imagem do storage ou do upload temporário
                                                $src = is_string($image) ? asset('storage/' . $image) : (is_object($image) ? $image->temporaryUrl() : '');
                                            @endphp
                                            <img src="{{ $src }}" 
                                                alt="Post Image" class="w-full h-full object-cover rounded">
                                            
                                            <button type="button" wire:click="removeEditingImage({{ $index }})" 
                                                    class="absolute -top-1 -right-1 bg-red-600 text-white rounded-full p-1 leading-none text-xs hover:bg-red-700">
                                                &times;
                                            </button>
                                        </div>
                                    @endforeach
                                </div>
                                
                                {{-- Campo de Upload Adicional --}}
                                <div class="mt-4">
                                    <input 
                                        type="file" 
                                        wire:model="newlyUploadedEditingImages" 
                                        multiple 
                                        accept="image/*"
                                        class="block w-full text-sm text-gray-500
                                                file:mr-4 file:py-2 file:px-4
                                                file:rounded-full file:border-0
                                                file:text-sm file:font-semibold
                                                file:bg-blue-50 file:text-blue-700
                                                hover:file:bg-blue-100"
                                    >
                                    @error('newlyUploadedEditingImages.*') <span class="text-red-500 text-sm mt-2 block">{{ $message }}</span> @enderror
                                </div>
                            </div>
                        </div>
                        
                        <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                            <button type="submit" 
                                    class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none sm:ml-3 sm:w-auto sm:text-sm"
                                    wire:loading.attr="disabled">
                                Salvar Alterações
                            </button>
                            <button type="button" wire:click="resetEditModal" 
                                    class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                                Cancelar
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
</div>