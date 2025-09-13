<?php

namespace App\Livewire;

use App\Models\Course;
use App\Models\Post;
use Livewire\Component;
use Livewire\WithPagination;

class CoursePosts extends Component
{
    use WithPagination;

    protected $paginationTheme = 'tailwind';

    public Course $course;
    public bool $overview = false; // define se é overview
    public $newPostContent = '';
    public $newReplyContent = [];
    public $editingPostId = null;
    public $editingPostContent = '';

    protected $rules = [
        'newPostContent' => 'required|string|min:5',
        'newReplyContent.*' => 'required|string|min:2',
        'editingPostContent' => 'required|string|min:5',
    ];

    public function mount(Course $course, bool $overview = false)
    {
        $this->course = $course;
        $this->overview = $overview;
    }

    public function render()
    {
        if ($this->overview) {
            $posts = $this->course->posts()->latest()->take(2)->get();
        } else {
            $posts = $this->course->posts()->with('author', 'replies.author')->latest()->paginate(5);
        }

        $isCoordinator = auth()->check() && optional(optional($this->course->courseCoordinator)->userAccount)->id === auth()->id();

        return view('livewire.course-posts', [
            'posts' => $posts,
            'isCoordinator' => $isCoordinator,
        ]);
    }

    // Criar novo post
    public function createPost()
    {
        $this->validateOnly('newPostContent');

        $coordinatorUserId = optional(optional($this->course->courseCoordinator)->userAccount)->id;

        if (auth()->id() !== $coordinatorUserId) {
            session()->flash('error', 'Somente o coordenador pode criar posts.');
            return;
        }

        $this->course->posts()->create([
            'user_id' => auth()->id(),
            'content' => $this->newPostContent,
        ]);

        $this->newPostContent = '';
        $this->resetPage();

        session()->flash('success', 'Post criado com sucesso!');
    }

    // Editar post
    public function editPost(Post $post)
    {
        if (auth()->id() !== $post->user_id) {
            session()->flash('error', 'Você não pode editar este post.');
            return;
        }

        $this->editingPostId = $post->id;
        $this->editingPostContent = $post->content;
    }

    public function updatePost(Post $post)
    {
        $this->validateOnly('editingPostContent');

        if (auth()->id() !== $post->user_id) {
            session()->flash('error', 'Você não pode atualizar este post.');
            return;
        }

        $post->update(['content' => $this->editingPostContent]);
        $this->editingPostId = null;
        session()->flash('success', 'Post atualizado com sucesso!');
    }

    // Excluir post
    public function deletePost(Post $post)
    {
        $coordinatorUserId = optional(optional($this->course->courseCoordinator)->userAccount)->id;

        if (auth()->id() === $post->user_id || auth()->id() === $coordinatorUserId) {
            $post->delete();
            $this->resetPage();
            session()->flash('success', 'Post excluído com sucesso!');
        } else {
            session()->flash('error', 'Você não tem permissão para excluir este post.');
        }
    }

    // Resposta
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
        $this->resetPage();
        session()->flash('success', 'Resposta enviada com sucesso!');
    }

    public function deleteReply($replyId)
    {
        $reply = \App\Models\Reply::findOrFail($replyId);
        $coordinatorUserId = optional(optional($this->course->courseCoordinator)->userAccount)->id;

        if (auth()->id() === $reply->author->id || auth()->id() === $coordinatorUserId) {
            $reply->delete();
            $this->resetPage();
            session()->flash('success', 'Resposta excluída com sucesso.');
        } else {
            session()->flash('error', 'Você não tem permissão para excluir esta resposta.');
        }
    }
}
