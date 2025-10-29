<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\Comment;
use Illuminate\Support\Facades\Auth;
use App\Models\CommentReaction;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str; // Adiciona Str para geração de ID único

class EventComments extends Component
{
    use WithFileUploads;

    public $event;
    public $commentText = ''; // Usado APENAS para o formulário de novo comentário no topo
    public $media;
    
    // Estado para Resposta Inline
    public $openReplyFormId = null; 
    public $replyText = ''; 
    
    // Estado para Edição Inline
    public $openEditFormId = null; 
    public $editText = ''; 

    // Estado para Exclusão
    public $deletingId = null; 
    public $comments = [];

    // Variável para armazenar o ID do comentário recém-criado/editado/deletado (para rolagem)
    public $lastActionId = null; 

    // Regras de validação para o formulário principal
    protected $rules = [
        'commentText' => 'nullable|string|max:1000',
        'media' => 'nullable|file|max:2048|mimes:jpg,jpeg,png,webp,mp4,pdf,gif'
    ];
    
    // Regras para o formulário de resposta inline
    protected $replyRules = [
        'replyText' => 'required|string|max:1000',
    ];
    
    // Regras para o formulário de edição inline
    protected $editRules = [
        'editText' => 'required|string|max:1000',
    ];

    // Listeners para atualizar os comentários em tempo real
    protected $listeners = ['commentAdded' => 'loadComments', 'commentDeleted' => 'loadComments'];

    public function mount($event)
    {
        $this->event = $event;
        $this->loadComments();
    }

    public function render()
    {
        return view('livewire.event-comments');
    }

    public function loadComments()
    {
        // Limpa apenas o estado de interação (mantém $deletingId para o modal fechar)
        $this->reset(['openReplyFormId', 'replyText', 'openEditFormId', 'editText']);
        
        $this->comments = Comment::with([
            'user',
            'replies' => function ($query) {
                $query->with(['user', 'reactions'])
                      ->orderBy('created_at', 'asc'); 
            },
            'reactions'
        ])
        ->withCount([
            'reactions as likes_count' => function ($q) {
                $q->where('type', 'like');
            },
            'reactions as dislikes_count' => function ($q) {
                $q->where('type', 'dislike');
            },
        ])
        ->where('event_id', $this->event->id)
        ->whereNull('parent_id')
        ->orderBy('created_at', 'desc')
        ->get();
        
        // Rola para a última ação se houver um ID definido
        if ($this->lastActionId) {
             $this->dispatch('scrollToComment', id: 'comment-' . $this->lastActionId);
             $this->lastActionId = null; // Limpa após o uso
        }
    }

    /**
     * Adiciona um novo comentário principal (top-level)
     */
    public function addComment()
    {
        $this->validate();

        if (empty(trim($this->commentText)) && is_null($this->media)) {
            $this->addError('commentText', 'O texto ou o arquivo é obrigatório.');
            return;
        }

        $mediaPath = $this->media ? $this->media->store('comments', 'public') : null;

        $newComment = Comment::create([
            'comment' => $this->commentText,
            'user_id' => Auth::id(),
            'event_id' => $this->event->id,
            'parent_id' => null, 
            'media_path' => $mediaPath,
        ]);

        $this->lastActionId = $newComment->id; // Define o ID para rolagem
        $this->reset(['commentText', 'media']); 
        $this->loadComments();
    }
    
    /**
     * Alterna a exibição do formulário de resposta inline.
     */
    public function setReply($commentId)
    {
        $this->openReplyFormId = ($this->openReplyFormId === $commentId) ? null : $commentId;
        
        $this->replyText = ''; 
        $this->reset(['openEditFormId', 'editText']);
        
        if ($this->openReplyFormId) {
            $this->dispatch('replyFormOpened', id: 'reply-form-' . $commentId);
        }
    }

    /**
     * Adiciona uma nova resposta a um comentário existente.
     */
    public function addReply($parentId)
    {
        $this->validate($this->replyRules, [], [
            'replyText' => 'resposta',
        ]);

        $newReply = Comment::create([
            'comment' => $this->replyText,
            'user_id' => Auth::id(),
            'event_id' => $this->event->id,
            'parent_id' => $parentId, 
            'media_path' => null,
        ]);

        $this->lastActionId = $parentId; // Rola para o comentário pai
        $this->reset('replyText', 'openReplyFormId');
        $this->loadComments();
    }

    /**
     * Inicia a Edição Inline.
     */
    public function editComment($id)
    {
        $comment = Comment::find($id);
        if ($comment && $comment->user_id === Auth::id()) {
            $this->openEditFormId = $id;
            $this->editText = $comment->comment;
            
            $this->reset(['openReplyFormId', 'replyText', 'commentText', 'media']); 

            // Rola para o formulário de edição
            $this->dispatch('editFormOpened', id: 'edit-form-' . $id);
        }
    }

    public function cancelEdit()
    {
        $this->reset(['openEditFormId', 'editText']);
        // Não precisamos recarregar os comentários aqui, apenas limpar o estado.
    }

    /**
     * Atualiza o comentário usando o estado de edição inline.
     */
    public function updateComment()
    {
        $this->validate($this->editRules, [], [
            'editText' => 'comentário',
        ]);
        
        $comment = Comment::find($this->openEditFormId);

        if ($comment && $comment->user_id === Auth::id()) {
            $comment->update([
                'comment' => $this->editText,
            ]);
            $this->lastActionId = $comment->parent_id ?? $comment->id; // Rola para a resposta ou o comentário principal
        }

        $this->reset(['editText', 'openEditFormId']);
        $this->loadComments();
    }
    
    /**
     * Define o ID do comentário a ser excluído para abrir o modal.
     */
    public function setDeletingId($id)
    {
        // Garante que apenas usuários com permissão possam setar o ID para exclusão
        $comment = Comment::find($id);
        if ($comment && ($comment->user_id === Auth::id() || (Auth::user()->user_type === 'coordinator' && $this->event->coordinator_id === Auth::user()->coordinator->id))) {
             $this->deletingId = $id;
        }
    }

    /**
     * Realiza a exclusão após a confirmação do modal.
     */
    public function deleteComment()
    {
        // VERIFICAÇÃO CRÍTICA
        if (!$this->deletingId) {
            $this->addError('deleteError', 'ID de exclusão não definido.');
            return;
        }

        $comment = Comment::find($this->deletingId);
        
        // Verifica se o usuário tem permissão antes de excluir
        if ($comment && $comment->user_id === Auth::id()) {
            // Se houver mídia, exclui do storage
            if ($comment->media_path) {
                Storage::disk('public')->delete($comment->media_path);
            }
            
            $comment->delete(); 
            // Define o ID de rolagem para o pai, ou usa um UUID se for um comentário principal, para rolar para a área geral
            $this->lastActionId = $comment->parent_id ?? 'main-comment-form'; 
            
        } else {
             $this->addError('deleteError', 'Você não tem permissão para excluir este comentário.');
        }

        // Reseta o ID para fechar o modal.
        $this->reset('deletingId');
        $this->loadComments();
    }

    public function reactToComment($commentId, $type)
    {
        // Lógica de reação (mantida)
        $userId = auth()->id();
        $reaction = CommentReaction::where('comment_id', $commentId)
            ->where('user_id', $userId)
            ->first();

        if ($reaction) {
            if ($reaction->type === $type) {
                $reaction->delete();
            } else {
                $reaction->update(['type' => $type]);
            }
        } else {
            CommentReaction::create([
                'user_id' => $userId,
                'comment_id' => $commentId,
                'type' => $type,
            ]);
        }

        $this->loadComments(); 
    }
}
