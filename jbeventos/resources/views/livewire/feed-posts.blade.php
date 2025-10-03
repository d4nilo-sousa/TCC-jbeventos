<div>
    @forelse ($feedItems as $item)
        {{-- Aqui você pode ter uma estrutura 'match' para diferenciar Posts e Events --}}
        @if ($item->type === 'post')
            {{-- CARTÃO DE POST --}}
            <div id="post-{{ $item->id }}" class="bg-white shadow rounded-xl p-6 border border-gray-200 transition duration-300 hover:shadow-lg mb-6">
                
                <div class="flex items-center space-x-3 mb-4">
                    <img src="{{ $item->author->user_icon_url }}" alt="{{ $item->author->name }}" class="w-10 h-10 rounded-full object-cover">
                    <div>
                        <a href="{{ route('profile.view', $item->author) }}" class="font-semibold text-gray-900 hover:text-red-600">
                            {{ $item->author->name }}
                        </a>
                        <p class="text-xs text-gray-500">
                            Postado em {{ $item->created_at->format('d/m/Y H:i') }}
                            @if($item->course)
                                em <span class="font-medium text-red-600">{{ $item->course->title }}</span>
                            @endif
                        </p>
                    </div>
                </div>

                <p class="text-gray-800 mb-4 whitespace-pre-wrap">
                    {{ $item->content }}
                </p>
                
                {{-- Imagens do Post (Simulação de exibição, se houver) --}}
                @if (!empty($item->images))
                    <div class="grid grid-cols-2 gap-2 mb-4">
                        @foreach($item->images as $imagePath)
                            <img src="{{ asset('storage/' . $imagePath) }}" class="rounded-lg object-cover w-full h-auto max-h-72" alt="Imagem do Post">
                        @endforeach
                    </div>
                @endif
                
                <div class="mt-4 border-t border-gray-100 pt-4 flex items-center justify-between text-sm text-gray-500">
                    <div class="flex items-center">
                        <i class="ph-fill ph-chat-circle text-lg mr-2 text-red-500"></i>
                        {{ $item->replies->count() }} Respostas
                    </div>
                    {{-- Aqui você pode adicionar um botão para expandir as respostas (opcional) --}}
                </div>

                {{-- FORMULÁRIO LIVEWIRE PARA RESPOSTA --}}
                <form wire:submit="createReply({{ $item->id }})" class="mt-4 pt-4 border-t border-gray-100">
                    
                    <div class="flex items-start space-x-2">
                        {{-- Ícone do Usuário Logado --}}
                        <img src="{{ Auth::user()->user_icon_url }}" alt="{{ Auth::user()->name }}" class="w-8 h-8 rounded-full object-cover flex-shrink-0">

                        <div class="flex-grow">
                            <textarea 
                                wire:model="newReplyContent.{{ $item->id }}"
                                rows="1"
                                placeholder="Escreva sua resposta..." 
                                class="w-full text-sm border-gray-300 focus:border-red-500 focus:ring-red-500 rounded-lg shadow-sm resize-none p-2 @error("newReplyContent.{$item->id}") border-red-500 @enderror"
                                required
                            ></textarea>
                            @error("newReplyContent.{$item->id}")
                                <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                            @enderror
                            <div class="text-right mt-1">
                                <button type="submit" class="text-xs font-semibold px-3 py-1 bg-red-600 text-white rounded-full hover:bg-red-700 transition" wire:loading.attr="disabled">
                                    Responder
                                </button>
                            </div>
                        </div>
                    </div>

                    {{-- Lista de Respostas (Exemplo: mostrar as 2 últimas) --}}
                    @if ($item->replies->count() > 0)
                        <div class="mt-4 space-y-3 pl-10 border-l border-gray-200">
                            @foreach ($item->replies->sortByDesc('created_at')->take(2) as $reply)
                                <div class="p-3 bg-gray-50 rounded-lg">
                                    <div class="flex items-center space-x-2 text-xs mb-1">
                                        <img src="{{ $reply->author->user_icon_url }}" alt="{{ $reply->author->name }}" class="w-5 h-5 rounded-full object-cover">
                                        <span class="font-semibold text-gray-800">{{ $reply->author->name }}</span>
                                        <span class="text-gray-500">{{ $reply->created_at->diffForHumans() }}</span>
                                    </div>
                                    <p class="text-sm text-gray-700 whitespace-pre-wrap">{{ $reply->content }}</p>
                                </div>
                            @endforeach

                            @if ($item->replies->count() > 2)
                                <p class="text-xs text-center text-red-600 hover:text-red-700 cursor-pointer">
                                    Ver todas as {{ $item->replies->count() }} respostas
                                </p>
                            @endif
                        </div>
                    @endif

                </form>
                {{-- FIM FORMULÁRIO LIVEWIRE PARA RESPOSTA --}}

            </div>
        @else 
            {{-- CARTÃO DE EVENTO (se precisar) --}}
            {{-- ... (Estrutura do Evento, se necessário, ou ele será tratado separadamente) ... --}}
        @endif
    @empty
        <p class="text-center text-gray-500 p-8">Nenhum post ou evento encontrado no feed.</p>
    @endforelse

    {{-- Paginação do Livewire (para os posts) --}}
    @if(isset($posts) && $posts instanceof \Illuminate\Pagination\LengthAwarePaginator)
        <div class="mt-6">
            {{ $posts->links() }}
        </div>
    @endif
</div>