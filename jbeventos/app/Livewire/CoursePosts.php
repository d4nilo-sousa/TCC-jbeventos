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
    public $newPostContent = '';
    public $newReplyContent = [];

    protected $rules = [
        'newPostContent' => 'required|string|min:5',
        'newReplyContent.*' => 'required|string|min:2',
    ];

    public function mount(Course $course)
    {
        $this->course = $course;
    }

    public function render()
    {
        $posts = $this->course->posts()->with('author', 'replies.author')->latest()->paginate(5);

        $isCoordinator = auth()->check() && optional(optional($this->course->courseCoordinator)->userAccount)->id === auth()->id();

        return view('livewire.course-posts', [
            'posts' => $posts,
            'isCoordinator' => $isCoordinator,
        ]);
    }

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

        // volta pra primeira página para ver o post novo
        $this->resetPage();

        session()->flash('success', 'Post criado com sucesso!');
        $this->dispatch('postCreated'); // opcional, útil se quiser escutar no JS
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

        // garante que a lista de posts/replies atualize (e volte pra primeira página se necessário)
        $this->resetPage();

        session()->flash('success', 'Resposta enviada com sucesso!');
        $this->dispatch('replyCreated');
    }

    public function deleteReply($replyId)
    {
        $reply = \App\Models\Reply::findOrFail($replyId);

        $coordinatorUserId = optional(optional($this->course->courseCoordinator)->userAccount)->id;

        if (auth()->id() === $reply->author->id || auth()->id() === $coordinatorUserId) {
            $reply->delete();
            session()->flash('success', 'Resposta excluída com sucesso.');
            $this->resetPage();
            $this->dispatch('replyDeleted');
        } else {
            session()->flash('error', 'Você não tem permissão para excluir esta resposta.');
        }
    }
}
