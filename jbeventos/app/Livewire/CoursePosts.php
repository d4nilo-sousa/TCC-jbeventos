<?php

namespace App\Livewire;

use App\Models\Course;
use App\Models\Post;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;

class CoursePosts extends Component
{
    use WithPagination, WithFileUploads;

    protected $paginationTheme = 'tailwind';

    public Course $course;
    public $newPostContent = '';
    public $newReplyContent = [];
    public $images = [];
    public $newlyUploadedImages = [];

    protected $rules = [
        'newPostContent' => 'required|string|min:5',
        'images.*' => 'image|max:2048',
        'newReplyContent.*' => 'required|string|min:2',
    ];

    public function mount(Course $course)
    {
        $this->course = $course;
    }

    public function render()
    {
        // Agora, o componente busca APENAS os posts e os pagina.
        $posts = $this->course->posts()->with('author', 'replies.author')->latest()->paginate(5);

        $isCoordinator = auth()->check() && optional(optional($this->course->courseCoordinator)->userAccount)->id === auth()->id();

        // Passa a variável $posts para a view, corrigindo o erro
        return view('livewire.course-posts', [
            'posts' => $posts, // Variável corrigida
            'isCoordinator' => $isCoordinator,
        ]);
    }

    // ... (restante dos métodos Updated, createPost, createReply, deleteReply, deletePost permanecem os mesmos)

    public function updatedNewlyUploadedImages()
    {
        if (is_array($this->newlyUploadedImages)) {
            $this->images = array_merge($this->images, $this->newlyUploadedImages);
        } else {
            $this->images[] = $this->newlyUploadedImages;
        }

        if (count($this->images) > 5) {
            $this->images = array_slice($this->images, 0, 5);
            session()->flash('error', 'Você só pode enviar até 5 imagens por post.');
        }

        $this->newlyUploadedImages = [];
    }

    public function removeImage($index)
    {
        unset($this->images[$index]);
        $this->images = array_values($this->images);
    }

    public function createPost()
    {
        $this->validate(['newPostContent' => 'required|string|min:5', 'images.*' => 'image|max:2048']);

        $coordinatorUserId = optional(optional($this->course->courseCoordinator)->userAccount)->id;

        if (auth()->id() !== $coordinatorUserId) {
            session()->flash('error', 'Somente o coordenador pode criar posts.');
            return;
        }

        $imagePaths = [];
        if (!empty($this->images)) {
            foreach ($this->images as $image) {
                $imagePaths[] = $image->store('post-images', 'public');
            }
        }

        $this->course->posts()->create([
            'user_id' => auth()->id(),
            'content' => $this->newPostContent,
            'images' => $imagePaths,
        ]);

        $this->newPostContent = '';
        $this->images = [];
        $this->resetPage();

        session()->flash('success', 'Post criado com sucesso!');
        $this->dispatch('postCreated');
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

    public function deletePost($postId)
    {
        $post = Post::findOrFail($postId);
        $coordinatorUserId = optional(optional($this->course->courseCoordinator)->userAccount)->id;

        if (auth()->id() === $post->user_id || auth()->id() === $coordinatorUserId) {
            $post->delete();
            session()->flash('success', 'Post excluído com sucesso.');
            $this->resetPage();
            $this->dispatch('postDeleted');
        } else {
            session()->flash('error', 'Você não tem permissão para excluir este post.');
        }
    }
}