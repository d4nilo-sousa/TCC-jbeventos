<?php

namespace App\Livewire;

use App\Models\Post;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\On; 
use Illuminate\Support\Collection;

class FeedPosts extends Component
{
    use WithPagination;

    protected $paginationTheme = 'tailwind';
    
    // Armazena o conteúdo da nova resposta, indexado pelo ID do Post
    public $newReplyContent = []; 
    
    // Propriedade para armazenar o ID do post que está expandido no modal
    public ?int $selectedPostId = null;

    // Propriedade para armazenar os dados completos do post selecionado para o modal
    public ?Post $expandedPost = null; 

    // Regras de validação para respostas
    protected $rules = [
        'newReplyContent.*' => 'required|string|min:2|max:500', 
    ];

    // Listeners para eventos JS
    #[On('postCreated')] 
    public function render()
    {
        // 1. Busca todos os posts do sistema, com Eager Loading para as relações
        // Mantemos o eager loading leve para a lista principal do feed
        $posts = Post::with(['course.courseCoordinator.userAccount', 'author', 'replies']) // removi 'replies.author' para otimizar o feed
            ->latest() 
            ->paginate(5);

        // 2. Criação do mapa de posts (necessário pois o FeedController combina Posts e Events)
        $feedItems = $posts->map(function ($post) {
            $post->type = 'post';
            $post->sort_date = $post->created_at; 
            return $post;
        });
        
        // Se selectedPostId estiver definido, carregamos o expandedPost completo
        if ($this->selectedPostId && !$this->expandedPost) {
            // Carrega o post completo com todas as relações para exibição detalhada no modal
            $this->expandedPost = Post::with(['course.courseCoordinator.userAccount', 'author', 'replies.author'])
                ->findOrFail($this->selectedPostId);
        }

        return view('livewire.feed-posts', [
            'feedItems' => $feedItems,
            'posts' => $posts, 
        ]);
    }

    // Método para abrir o modal e carregar o post
    public function openPostModal(int $postId)
    {
        $this->selectedPostId = $postId;
        
        // Carrega o post e suas respostas completas assim que o modal é aberto
        // O método render() acima garantirá que expandedPost seja carregado com todas as respostas.
        $this->expandedPost = Post::with(['course.courseCoordinator.userAccount', 'author', 'replies.author'])
            ->findOrFail($postId);

        // Dispara um evento JS para garantir que o modal seja exibido
        $this->dispatch('openPostModal');
    }

    //Método para fechar o modal
    public function closePostModal()
    {
        $this->selectedPostId = null;
        $this->expandedPost = null;
        // Limpa a páginação para evitar problemas
        $this->setPage(1); 
    }

    /**
     * Cria uma resposta para um Post.
     */
    public function createReply($postId)
    {
        // ... (Lógica de validação e criação de resposta mantida) ...

        if (!isset($this->newReplyContent[$postId])) {
             $this->newReplyContent[$postId] = '';
        }

        $this->validate([
            "newReplyContent.{$postId}" => 'required|string|min:2|max:500'
        ], [
            "newReplyContent.{$postId}.required" => 'O conteúdo da resposta não pode estar vazio.',
            "newReplyContent.{$postId}.min" => 'A resposta deve ter pelo menos 2 caracteres.',
            "newReplyContent.{$postId}.max" => 'A resposta deve ter no máximo 500 caracteres.',
        ]);

        $post = Post::findOrFail($postId);
        $post->replies()->create([
            'user_id' => auth()->id(),
            'content' => $this->newReplyContent[$postId],
        ]);

        // Limpa o campo de resposta específico
        $this->newReplyContent[$postId] = '';

        // Se o modal estiver aberto, atualiza expandedPost para incluir a nova resposta
        if ($this->selectedPostId === $postId) {
            $this->expandedPost = $this->expandedPost->fresh(['replies.author']);
        }

        // Recarrega a página de forma reativa para mostrar a nova resposta
        $this->resetPage(); 
        session()->flash('success', 'Resposta enviada com sucesso!');
        $this->dispatch('replyCreated');
    }
}