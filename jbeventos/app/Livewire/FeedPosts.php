<?php

namespace App\Livewire;

use App\Models\Post;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\On; // Adicionado para ouvir eventos, se necessário

class FeedPosts extends Component
{
    use WithPagination;

    protected $paginationTheme = 'tailwind';
    
    // Armazena o conteúdo da nova resposta, indexado pelo ID do Post
    public $newReplyContent = []; 

    protected $rules = [
        'newReplyContent.*' => 'required|string|min:2|max:500', 
    ];

    #[On('postCreated')] // Ouve o evento 'postCreated' para recarregar a página
    public function render()
    {
        // 1. Busca todos os posts do sistema, com Eager Loading para as relações
        $posts = Post::with(['course.courseCoordinator.userAccount', 'author', 'replies.author'])
            ->latest() // Ordena pelo mais recente
            ->paginate(5);

        // 2. Criação do mapa de posts (necessário pois o FeedController combina Posts e Events)
        // No componente FeedPosts, lidamos APENAS com Posts.
        $feedItems = $posts->map(function ($post) {
            $post->type = 'post';
            $post->sort_date = $post->created_at; 
            return $post;
        });

        return view('livewire.feed-posts', [
            'feedItems' => $feedItems,
            'posts' => $posts, // Mantendo $posts para a paginação
        ]);
    }

    /**
     * Cria uma resposta para um Post. Lógica baseada em app/Livewire/CoursePosts, mas simplificada.
     */
    public function createReply($postId)
    {
        // Garante que o campo existe no array e faz a validação específica
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

        // Recarrega a página de forma reativa para mostrar a nova resposta
        $this->resetPage(); 
        session()->flash('success', 'Resposta enviada com sucesso!');
        $this->dispatch('replyCreated');
    }
}