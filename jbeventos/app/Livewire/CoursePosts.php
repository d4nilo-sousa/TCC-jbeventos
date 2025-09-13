<?php

namespace App\Livewire;

use App\Models\Course;
use App\Models\Post;
use Livewire\Component;
use Livewire\WithPagination;

class CoursePosts extends Component
{
    use WithPagination;

    public Course $course;
    public $newPostContent = '';
    public $newReplyContent = [];

    protected $rules = [
        'newPostContent' => 'required|string|min:5',
        'newReplyContent.*' => 'required|string|min:2',
    ];

    // Para o Livewire, esse é o método que carrega a view.
    public function render()
    {
        $posts = $this->course->posts()->with('author', 'replies.author')->latest()->paginate(5);
        
        $isCoordinator = auth()->user()->id === $this->course->courseCoordinator->userAccount->id;

        return view('livewire.course-posts', [
            'posts' => $posts,
            'isCoordinator' => $isCoordinator,
        ]);
    }

    public function createPost()
    {
        $this->validate(['newPostContent' => 'required|string|min:5']);

        if (auth()->user()->id !== $this->course->courseCoordinator->userAccount->id) {
            session()->flash('error', 'Somente o coordenador pode criar posts.');
            return;
        }

        $this->course->posts()->create([
            'user_id' => auth()->id(),
            'content' => $this->newPostContent,
        ]);

        $this->newPostContent = '';
        session()->flash('success', 'Post criado com sucesso!');
    }

    public function createReply($postId)
    {
        $this->validate([
            "newReplyContent.{$postId}" => 'required|string|min:2'
        ]);

        $post = Post::findOrFail($postId);
        $post->replies()->create([
            'user_id' => auth()->id(),
            'content' => $this->newReplyContent[$postId],
        ]);

        $this->newReplyContent[$postId] = '';
        session()->flash('success', 'Resposta enviada com sucesso!');
    }

    public function deleteReply($replyId)
    {
        $reply = \App\Models\Reply::findOrFail($replyId);
        
        // Permite ao autor da resposta ou ao coordenador do curso excluí-la
        if (auth()->user()->id === $reply->author->id || auth()->user()->id === $this->course->courseCoordinator->userAccount->id) {
            $reply->delete();
            session()->flash('success', 'Resposta excluída com sucesso.');
        } else {
            session()->flash('error', 'Você não tem permissão para excluir esta resposta.');
        }
    }
}