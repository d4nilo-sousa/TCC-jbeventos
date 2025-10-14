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
        <div wire:key="post-{{ $post->id }}" 
             class="feed-card bg-white rounded-xl shadow border border-gray-100 p-6 space-y-4 relative 
                    transition duration-150 ease-in-out hover:shadow-lg hover:border-red-300">
            {{-- DIV CLICÁVEL PARA ABRIR O MODAL --}}
            <div wire:click="openPostModal({{ $post->id }})" class="cursor-pointer"> 
                
                {{-- BLOCO DE GERENCIAMENTO (Editar/Excluir) --}}
                @if (Auth::id() === $post->user_id)
                    <div class="flex justify-end space-x-2 -mt-3 -mr-3 absolute top-0 right-0 z-10 p-4">
                        
                        {{-- Botão de Excluir --}}
                        <button 
                            wire:click.stop="confirmPostDeletion({{ $post->id }})" {{-- Chama o método para abrir o modal --}}
                            class="p-2 text-sm text-red-500 hover:text-red-700 hover:bg-red-50 rounded-full transition duration-150"
                            title="Excluir Post"
                        >
                            <i class="ph ph-trash text-xl"></i>
                        </button>

                        {{-- Botão de Editar --}}
                        <button 
                            wire:click.stop="startEditPost({{ $post->id }})" {{-- .stop impede o clique de propagar para o card --}}
                            class="p-2 text-sm text-gray-500 hover:text-blue-700 hover:bg-blue-50 rounded-full transition duration-150"
                            title="Editar Post"
                        >
                            <i class="ph ph-pencil-simple text-xl"></i>
                        </button>
                    </div>
                @endif
                {{-- FIM DO BLOCO DE GERENCIAMENTO --}}
                
                <div class="flex items-start space-x-3 {{ Auth::id() === $post->user_id ? 'pt-6' : '' }}">
                    <div class="flex-shrink-0">
                        {{-- Avatar --}}
                        <div class="h-10 w-10 bg-gray-200 rounded-full flex items-center justify-center text-gray-600">
                            <img src ="{{ $post->author->user_icon_url }}" alt="{{ $post->author->name }}" class="size-9 rounded-full object-cover border-2 border-gray-300 hover:border-red-500 transition shadow-md">
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
                    {{-- Grid com 1 ou 2 colunas, forçando altura fixa para miniaturas --}}
                    <div class="grid gap-2 {{ count($post->images) == 1 ? 'grid-cols-1' : 'grid-cols-2' }} mt-3">
                        @foreach ($post->images as $imagePath)
                            {{-- Wrapper com altura fixa (h-48) e overflow-hidden para padronizar o bloco --}}
                            <div class="relative w-full h-48 overflow-hidden rounded-lg shadow-md cursor-pointer"
                                onclick="event.stopPropagation(); window.open('{{ asset('storage/' . $imagePath) }}')"
                            >
                                <img src="{{ asset('storage/' . $imagePath) }}" alt="Post Image" 
                                        class="absolute inset-0 w-full h-full object-cover" {{-- object-cover para preencher e cortar o excesso --}}
                                        loading="lazy"
                                >
                            </div>
                        @endforeach
                    </div>
                @endif
                
                {{-- BOTÕES DE INTERAÇÃO (Substituído por um span para manter o card clicável) --}}
                <div class="flex justify-between items-center border-t border-gray-100 pt-4 mt-4"> {{-- Adicionei mt-4 para espaçamento --}}
                    <span class="text-sm font-medium text-gray-600 hover:text-red-600 transition cursor-pointer">
                        Ver {{ $post->replies->count() }} Respostas
                    </span>
                </div>

            </div> {{-- FIM DO DIV CLICÁVEL --}}
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
    @if ($selectedPostId && $expandedPost)
        <div 
            x-data="{ show: @entangle('selectedPostId').not(null) }" 
            x-show="show" 
            x-transition:enter="ease-out duration-300" 
            x-transition:enter-start="opacity-0" 
            x-transition:enter-end="opacity-100" 
            x-transition:leave="ease-in duration-200" 
            x-transition:leave-start="opacity-100" 
            x-transition:leave-end="opacity-0"
            class="fixed inset-0 bg-gray-900 bg-opacity-75 z-50 flex items-center justify-center p-4 sm:p-6" 
            aria-modal="true" role="dialog"
            wire:key="post-modal-{{ $expandedPost->id }}"
            x-on:keydown.escape.window="$wire.closePostModal()"
        >
            
            <div 
                x-transition:enter="ease-out duration-300" 
                x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" 
                x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" 
                x-transition:leave="ease-in duration-200" 
                x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" 
                x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                class="bg-white rounded-xl shadow-2xl w-full max-w-4xl max-h-[90vh] overflow-y-auto transform transition-all">
                
                {{-- Botão de Fechar/Voltar --}}
                <div class="sticky top-0 bg-white p-4 border-b border-gray-100 z-10 flex justify-between items-center">
                    <h3 class="text-xl font-bold text-gray-900">Detalhes do Post</h3>
                    <button wire:click="closePostModal" type="button" class="text-gray-500 hover:text-red-600 transition p-2 rounded-full hover:bg-gray-100">
                        <i class="ph-bold ph-x text-2xl"></i>
                    </button>
                </div>

                {{-- Conteúdo do Post Expandido --}}
                <div class="p-6">
                    
                    {{-- Dados do Autor --}}
                    <div class="flex items-center space-x-3 mb-6">
                        <img src="{{ $expandedPost->author->user_icon_url }}" alt="{{ $expandedPost->author->name }}" class="w-12 h-12 rounded-full object-cover">
                        <div>
                            <span class="text-lg font-semibold text-gray-900 block">{{ $expandedPost->author->name }}</span>
                            <p class="text-sm text-gray-500">
                                Postado em {{ $expandedPost->created_at->format('d/m/Y H:i') }}
                                @if($expandedPost->course)
                                    em <span class="font-medium text-red-600">{{ $expandedPost->course->course_name }}</span>
                                @endif
                            </p>
                        </div>
                    </div>

                    {{-- Conteúdo Principal --}}
                    <p class="text-gray-800 text-lg mb-6 whitespace-pre-wrap">
                        {{ $expandedPost->content }}
                    </p>

                    {{-- Imagens do Post (Todas as imagens) --}}
                    @if (!empty($expandedPost->images) && count($expandedPost->images) > 0)
                        {{-- Contêiner principal: max-h + overflow-y-auto + grid --}}
                        <div class="grid {{ count($expandedPost->images) == 1 ? 'grid-cols-1' : 'grid-cols-2' }} gap-4 mb-6 max-h-[70vh] overflow-y-auto p-1 -m-1">
                            @foreach($expandedPost->images as $imagePath)
                                {{-- Wrapper da Imagem: Altura fixa (h-64) com flex para centralizar o objeto --}}
                                <div class="max-w-full h-64 mx-auto rounded-lg overflow-hidden shadow-md bg-gray-100 flex items-center justify-center">
                                <img src="{{ asset('storage/' . $imagePath) }}" 
                                        class="object-cover w-full h-full"
                                        alt="Imagem do Post">
                                </div>
                            @endforeach
                        </div>
                    @endif

                    {{-- Seção de Respostas --}}
                    <h4 class="text-xl font-bold text-gray-800 border-t border-gray-100 pt-6 mb-4">{{ $expandedPost->replies->count() }} Respostas</h4>
                    
                    {{-- Formulário de Resposta no Modal --}}
                    <form wire:submit="createReply({{ $expandedPost->id }})" class="mb-6">
                        <div class="flex items-start space-x-2">
                            <img src="{{ Auth::user()->user_icon_url }}" alt="{{ Auth::user()->name }}" class="w-8 h-8 rounded-full object-cover flex-shrink-0">
                            <div class="flex-grow">
                                <textarea 
                                    wire:model="newReplyContent.{{ $expandedPost->id }}"
                                    rows="2"
                                    placeholder="Escreva sua resposta..." 
                                    class="w-full text-sm border-gray-300 focus:border-red-500 focus:ring-red-500 rounded-lg shadow-sm resize-none p-2 @error("newReplyContent.{$expandedPost->id}") border-red-500 @enderror"
                                    required
                                ></textarea>
                                @error("newReplyContent.{$expandedPost->id}")
                                    <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                                @enderror
                                <div class="text-right mt-1">
                                    <button type="submit" class="text-xs font-semibold px-3 py-1 bg-red-600 text-white rounded-full hover:bg-red-700 transition" wire:loading.attr="disabled">
                                        Responder
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                    
                    {{-- Lista Completa de Respostas --}}
                    <div class="space-y-4">
                        @forelse ($expandedPost->replies->sortByDesc('created_at') as $reply)
                            <div class="p-4 bg-gray-50 rounded-lg shadow-sm border border-gray-100">
                                <div class="flex items-start space-x-3 mb-2">
                                    <img src="{{ $reply->author->user_icon_url }}" alt="{{ $reply->author->name }}" class="w-8 h-8 rounded-full object-cover flex-shrink-0">
                                    <div>
                                        <span class="font-semibold text-gray-900 text-sm">{{ $reply->author->name }}</span>
                                        <p class="text-xs text-gray-500">{{ $reply->created_at->diffForHumans() }}</p>
                                    </div>
                                </div>
                                <p class="text-sm text-gray-700 ml-11 whitespace-pre-wrap">{{ $reply->content }}</p>
                            </div>
                        @empty
                            <p class="text-center text-gray-500 text-sm py-4">Nenhuma resposta ainda. Seja o primeiro a responder!</p>
                        @endforelse
                    </div>

                </div>
                
            </div>
        </div>
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
            class="fixed inset-0 z-[100] overflow-y-auto" role="dialog" aria-modal="true" aria-labelledby="edit-modal-title"
            x-on:keydown.escape.window="$wire.resetEditModal()">
            
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div x-on:click="$wire.resetEditModal()" class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>

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
                                <span wire:loading.remove wire:target="saveEditPost">Salvar Alterações</span>
                                <span wire:loading wire:target="saveEditPost">Salvando...</span>
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

    {{-- MODAL DE CONFIRMAÇÃO DE EXCLUSÃO --}}
    @if ($confirmingPostDeletionId)
        <div x-data="{ open: @entangle('confirmingPostDeletionId').not(null) }" x-show="open" 
            x-transition:enter="ease-out duration-300"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="ease-in duration-200"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="fixed inset-0 bg-gray-900 bg-opacity-75 z-[200] flex items-center justify-center p-4 sm:p-6"
            aria-modal="true" role="dialog"
            x-on:keydown.escape.window="$wire.set('confirmingPostDeletionId', null)">

            <div x-transition:enter="ease-out duration-300"
                 x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave="ease-in duration-200"
                 x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 class="bg-white rounded-xl shadow-2xl w-full max-w-sm transform transition-all">

                <div class="bg-white p-6 sm:p-8 rounded-xl">
                    <div class="sm:flex sm:items-start">
                        <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                            <i class="ph ph-trash text-red-600 text-2xl"></i> {{-- Ícone de Lixeira --}}
                        </div>
                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                            <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-headline">
                                Confirmar Exclusão
                            </h3>
                            <div class="mt-2">
                                <p class="text-sm text-gray-500">
                                    Tem certeza que deseja excluir este post? Esta ação não pode ser desfeita.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse rounded-b-xl">
                    <button wire:click="deletePost" type="button" 
                            class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto sm:text-sm"
                            wire:loading.attr="disabled">
                        <span wire:loading.remove wire:target="deletePost">Excluir</span>
                        <span wire:loading wire:target="deletePost">Excluindo...</span>
                    </button>
                    <button wire:click="$set('confirmingPostDeletionId', null)" type="button" 
                            class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-100 sm:mt-0 sm:w-auto sm:text-sm">
                        Cancelar
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>