<div>
    {{-- Feedback de sucesso/erro --}}
    @if (session()->has('success'))
        <div class="mb-5 p-3 bg-green-100 border border-green-400 text-green-700 rounded-lg text-sm">{{ session('success') }}</div>
    @endif
    @if (session()->has('error'))
        <div class="mb-5 p-3 bg-red-100 border border-red-400 text-red-700 rounded-lg text-sm">{{ session('error') }}</div>
    @endif
    
    {{-- NOVO: Formulário de criação de post - VISÍVEL APENAS PARA COORDENADORES --}}
    @if (auth()->user()->user_type === 'coordinator' && $coordinatorCourses->isNotEmpty())
        
        {{-- Define o nome do curso para exibição --}}
        @php
            $courseName = $coordinatorCourses->count() === 1 
                ? $coordinatorCourses->first()->course_name 
                : (auth()->user()->coordinatorRole->coordinator_type === 'general' ? 'Postagem Geral (Todos os Cursos)' : 'Múltiplos Cursos');
            $coordinatorName = Auth::user()->name;
        @endphp
        
        <div class="mb-6 p-5 bg-white rounded-xl shadow-lg border border-red-200">
            <h4 class="text-xl font-bold mb-3 text-gray-800 flex items-center">
                <i class="ph-bold ph-pencil-simple-line mr-2 text-red-600 text-2xl"></i> Criar Novo Post
            </h4>
            
            {{-- Substituição do Select pelo campo informativo --}}
            <div class="flex items-center space-x-2 mb-4 p-3 bg-gray-50 rounded-lg border border-gray-100">
                <i class="ph-bold ph-user-circle text-lg text-red-600"></i>
                <div class="text-sm">
                    <span class="font-semibold text-gray-900">{{ $coordinatorName }}</span> está publicando em: 
                    <span class="font-bold text-red-600">{{ $courseName }}</span>
                </div>
            </div>
            
            <form wire:submit.prevent="createPost">

                {{-- Conteúdo do Post --}}
                <div class="mb-4">
                    <textarea wire:model.defer="newPostContent" rows="3"
                        class="w-full border-gray-300 rounded-lg shadow-sm text-base focus:border-red-500 focus:ring-red-500 resize-none p-3 @error('newPostContent') border-red-500 @enderror"
                        placeholder="O que você gostaria de compartilhar com os seus alunos e seguidores?"></textarea>
                    @error('newPostContent') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>

                {{-- Botões e Upload de Imagem --}}
                <div class="flex items-center justify-between mt-2">
                    {{-- Campo de upload invisível --}}
                    <input type="file" wire:model="newlyUploadedImages" multiple accept="image/*"
                        class="hidden" id="feed-post-file-upload">
                    
                    {{-- Label estilizada para o upload --}}
                    <label for="feed-post-file-upload" class="bg-red-50 text-red-600 px-4 py-2 rounded-full cursor-pointer hover:bg-red-100 transition text-sm font-semibold flex items-center gap-2">
                         <i class="ph-bold ph-image text-lg"></i> Adicionar Fotos
                    </label>

                    {{-- Botão de Postar --}}
                    <button type="submit"
                        class="bg-red-600 hover:bg-red-700 text-white font-semibold py-2 px-6 rounded-lg shadow-md transition text-sm disabled:bg-red-300"
                        wire:loading.attr="disabled"
                        wire:target="createPost, newlyUploadedImages">
                        <span wire:loading.remove wire:target="createPost, newlyUploadedImages">Postar</span>
                        <span wire:loading wire:target="createPost, newlyUploadedImages" class="flex items-center">
                            <i class="ph-bold ph-spinner-gap animate-spin mr-2"></i> Processando...
                        </span>
                    </button>
                </div>
                
                {{-- 4. Preview das Imagens --}}
                @if(!empty($images))
                    <div class="flex flex-wrap mt-4 gap-3">
                        @foreach($images as $index => $image)
                            <div class="relative w-24 h-24 border rounded-lg overflow-hidden shadow-sm">
                                <img src="{{ $image->temporaryUrl() }}" class="object-cover w-full h-full">
                                
                                <button type="button" wire:click="removeImage({{ $index }})"
                                    class="absolute top-1 right-1 bg-black bg-opacity-50 text-white rounded-full w-5 h-5 flex items-center justify-center text-xs hover:bg-opacity-70 transition">
                                    <i class="ph-bold ph-x text-xs"></i>
                                </button>
                            </div>
                        @endforeach
                    </div>
                @endif
                @error('images.*') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
            </form>
        </div>
    @elseif(auth()->user()->user_type === 'coordinator' && $coordinatorCourses->isEmpty())
        <div class="mb-6 p-4 bg-yellow-50 rounded-lg border border-yellow-300 text-yellow-800 text-sm">
            <i class="ph-bold ph-warning-circle mr-2"></i> Você é um coordenador, mas não está vinculado a nenhum curso para postar.
        </div>
    @endif
    
    {{-- Loop Principal de Posts --}}
    @forelse ($feedItems as $item)
        {{-- ... (O restante do loop de posts e o modal de post expandido) ... --}}
        @if ($item->type === 'post')
            {{-- CARTÃO DE POST --}}
            <button wire:click="openPostModal({{ $item->id }})" 
                class="block w-full text-left bg-white shadow rounded-xl p-6 border border-gray-200 transition duration-300 hover:shadow-lg hover:border-red-300 mb-6 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2">
                
                {{-- Conteúdo resumido --}}
                <div class="flex items-center space-x-3 mb-4">
                    <img src="{{ $item->author->user_icon_url }}" alt="{{ $item->author->name }}" class="w-10 h-10 rounded-full object-cover">
                    <div>
                        <span class="font-semibold text-gray-900">
                            {{ $item->author->name }}
                        </span>
                        <p class="text-xs text-gray-500">
                            Postado em {{ $item->created_at->format('d/m/Y H:i') }}
                            @if($item->course)
                                em <span class="font-medium text-red-600">{{ $item->course->course_name }}</span>
                            @endif
                        </p>
                    </div>
                </div>

                <p class="text-gray-800 mb-4 whitespace-pre-wrap line-clamp-3"> {{ $item->content }}</p>
                
                {{-- Imagens do Post (apenas a primeira para pré-visualização) --}}
                @if (!empty($item->images) && count($item->images) > 0)
                    <div class="mb-4">
                        <img src="{{ asset('storage/' . $item->images[0]) }}" class="rounded-lg object-cover w-full h-auto max-h-48" alt="Imagem do Post">
                    </div>
                @endif
                
                <div class="mt-4 border-t border-gray-100 pt-4 flex items-center justify-between text-sm text-gray-500">
                    <div class="flex items-center">
                        <i class="ph-fill ph-chat-circle text-lg mr-2 text-red-500"></i>
                        {{ $item->replies->count() }} Respostas
                    </div>
                    <span class="text-red-600 font-medium">Ver post completo &rarr;</span>
                </div>

            </button>
        @endif
    @empty
        <p class="text-center text-gray-500 p-8">Nenhum post encontrado no feed.</p>
    @endforelse

    {{-- Paginação do Livewire (para os posts) --}}
    @if(isset($posts) && $posts instanceof \Illuminate\Pagination\LengthAwarePaginator)
        <div class="mt-6">
            {{ $posts->links() }}
        </div>
    @endif


    {{-- ESTRUTURA DO MODAL DE POST EXPANDIDO --}}
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
            wire:key="post-modal-{{ $expandedPost->id }}">
            
            <div 
                x-transition:enter="ease-out duration-300" 
                x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" 
                x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" 
                x-transition:leave="ease-in duration-200" 
                x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" 
                x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                class="bg-white rounded-xl shadow-2xl w-full max-w-4xl max-h-[90vh] overflow-y-auto transform transition-all">
                
                <div class="sticky top-0 bg-white p-4 border-b border-gray-100 z-10 flex justify-between items-center">
                    <h3 class="text-xl font-bold text-gray-900">Detalhes do Post</h3>
                    <button wire:click="closePostModal" type="button" class="text-gray-500 hover:text-red-600 transition p-2 rounded-full hover:bg-gray-100">
                        <i class="ph-bold ph-x text-2xl"></i>
                    </button>
                </div>

                <div class="p-6">
                    
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

                    <p class="text-gray-800 text-lg mb-6 whitespace-pre-wrap">
                        {{ $expandedPost->content }}
                    </p>

                    @if (!empty($expandedPost->images) && count($expandedPost->images) > 0)
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-6">
                            @foreach($expandedPost->images as $imagePath)
                                <img src="{{ asset('storage/' . $imagePath) }}" class="rounded-lg object-cover w-full h-auto max-h-96 shadow-md" alt="Imagem do Post">
                            @endforeach
                        </div>
                    @endif

                    <h4 class="text-xl font-bold text-gray-800 border-t border-gray-100 pt-6 mb-4">{{ $expandedPost->replies->count() }} Respostas</h4>
                    
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
                    
                    <div class="space-y-4">
                        @forelse ($expandedPost->replies->sortByDesc('created_at') as $reply)
                            <div class="p-4 bg-gray-50 rounded-lg shadow-sm border border-gray-100">
                                <div class="flex items-start space-x-3 mb-2">
                                    <img src="{{ $reply->author->user_icon_url }}" alt="{{ $reply->author->name }}" class="w-8 h-8 rounded-full object-cover flex-shrink-0">
                                    <div>
                                        <a href="{{ route('profile.view', $reply->author) }}" class="font-semibold text-gray-900 text-sm hover:text-red-600">{{ $reply->author->name }}</a>
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
</div>