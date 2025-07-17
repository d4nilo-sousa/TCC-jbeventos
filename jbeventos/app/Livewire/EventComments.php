<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\Comment;
use Illuminate\Support\Facades\Auth;

class EventComments extends Component
{
    use WithFileUploads; // Permite subir arquivos

    // Propriedades
    public $event;
    public $commentText = '';
    public $media;
    public $replyTo = null;
    public $editingCommentId = null;

    // Validações
    protected $rules = [
        'commentText' => 'required|string|max:1000',
        'media' => 'nullable|file|max:2048|mimes:jpg,jpeg,png,webp,mp4,pdf'
    ];

    // Renderiza o componente
    public function render()
    {
        $comments = Comment::with(['user', 'replies.user']) // Carrega os relacionamentos
            ->where('event_id', $this->event->id) // Filtra pelo ID do evento
            ->whereNull('parent_id') // Filtra os comentários principais
            ->orderBy('created_at', 'desc') // Ordena os comentários por data de criação
            ->get();

        return view('livewire.event-comments', compact('comments'));
    }

    // Adiciona um comentário
    public function addComment()
    {
        $this->validate(); // Valida as regras

        // Armazena o caminho do arquivo
        $mediaPath = $this->media ? $this->media->store('comments', 'public') : null; 

        // Cria o comentário
        Comment::create([
            'comment' => $this->commentText, // Armazena o comentário
            'user_id' => Auth::id(), // Armazena o ID do usuário
            'event_id' => $this->event->id, // Armazena o ID do evento
            'parent_id' => $this->replyTo, // Armazena o ID do comentário de resposta
            'media_path' => $mediaPath, // Armazena o caminho do arquivo
        ]);

        // Limpa as propriedades
        $this->reset(['commentText', 'media', 'replyTo']);
        session()->flash('message', 'Comentário adicionado!'); // Exibe uma mensagem
    }

    // Define o comentário de resposta
    public function setReply($commentId)
    {
        // Armazena o ID do comentário de resposta
        $this->replyTo = $commentId;
    }

    // Cancela a resposta
    public function cancelReply()
    {
        $this->reset('replyTo'); // Limpa o ID do comentário de resposta
    }

    // Edita um comentário
    public function editComment($id)
    {
        $comment = Comment::find($id); // Busca o comentário

        // Verifica se o comentário existe e se o usuário logado é o dono
        if ($comment && $comment->user_id === Auth::id()) {
            $this->editingCommentId = $id;
            $this->commentText = $comment->comment;
        }
    }

    // Atualiza um comentário
    public function updateComment()
    {
        $this->validate();

        $comment = Comment::find($this->editingCommentId); // Busca o comentário

        // Verifica se o comentário existe e se o usuário logado é o dono
        if ($comment && $comment->user_id === Auth::id()) {
            $comment->update([
                'comment' => $this->commentText,
                'edited_at' => now(),
            ]);
        }

        $this->reset(['commentText', 'editingCommentId']); // Limpa as propriedades
    }

    // Exclui um comentário
    public function deleteComment($id)
    {
        $comment = Comment::find($id); // Busca o comentário

        // Verifica se o comentário existe e se o usuário logado é o dono
        if ($comment && $comment->user_id === Auth::id()) {
            $comment->delete();
        }
    }
}
