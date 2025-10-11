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
    use WithPagination, WithFileUploads; // Traits para paginação e upload de arquivos

    protected $paginationTheme = 'tailwind'; // Tema de paginação Tailwind CSS

    // Propriedades do componente
    public Course $course;
    public $newPostContent = '';
    public $newReplyContent = [];
    public $images = [];
    public $newlyUploadedImages = [];

    public $editingPostId = null;
    public $editingPostContent = ''; 

    // Método rules() para validação dinâmica
    protected function rules()
    {
        return [
            'newPostContent' => 'nullable|string|max:500', 
            'images' => 'array|max:5',
            'images.*' => 'image|max:2048', 
            'newReplyContent.*' => 'required|string|min:2|max:100', 
            'editingPostContent' => 'required|string|min:2|max:300', 
        ];
    }

    // Montagem inicial do componente com o curso
    public function mount(Course $course)
    {
        $this->course = $course;
    }

    // Renderização do componente
    public function render()
    {
        $posts = $this->course->posts()->with('author', 'replies.author')->latest()->paginate(5);
        $isCoordinator = auth()->check() && optional(optional($this->course->courseCoordinator)->userAccount)->id === auth()->id();

        return view('livewire.course-posts', [
            'posts' => $posts,
            'isCoordinator' => $isCoordinator,
        ]);
    }

    // Lógica para múltiplas imagens 
    public function updatedNewlyUploadedImages()
    {
        $newFiles = is_array($this->newlyUploadedImages) ? $this->newlyUploadedImages : [$this->newlyUploadedImages];
        $currentImages = collect($this->images);
        $updatedImages = $currentImages->merge($newFiles);

        if ($updatedImages->count() > 5) {
            $this->images = $updatedImages->take(5)->all();
            session()->flash('error', 'Você só pode enviar até 5 imagens por post.');
        } else {
            $this->images = $updatedImages->all();
        }

        $this->newlyUploadedImages = [];
    }

    // Remove imagem do array de imagens
    public function removeImage($index)
    {
        if (isset($this->images[$index])) {
            unset($this->images[$index]);
            $this->images = array_values($this->images);
        }
    }

    // Criação de Posts
    public function createPost()
    {
        $this->validate([
            'newPostContent' => 'nullable|string|max:500', 
            'images.*' => 'image|max:2048'
        ]);

        if (empty(trim($this->newPostContent)) && empty($this->images)) {
             $this->addError('newPostContent', 'O post deve ter conteúdo de texto ou pelo menos uma imagem.');
             return; 
        }

        $coordinatorUserId = optional(optional($this->course->courseCoordinator)->userAccount)->id;
        if (auth()->id() !== $coordinatorUserId) {
            session()->flash('error', 'Somente o coordenador pode criar posts.');
            return;
        }

        $imagePaths = [];
        if (!empty($this->images)) {
            foreach ($this->images as $image) {
                if (method_exists($image, 'store')) {
                    $imagePaths[] = $image->store('post-images', 'public');
                }
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

    // --- MÉTODOS DE EDIÇÃO ---

    public function startEdit($postId) 
    {
        $post = Post::findOrFail($postId);

        // Verifica se o usuário tem permissão para editar (autor ou coordenador)
        $isCoordinator = optional(optional($this->course->courseCoordinator)->userAccount)->id === auth()->id();
        if (auth()->id() !== $post->user_id && !$isCoordinator) {
             session()->flash('error', 'Você não tem permissão para editar este post.');
             return;
        }

        // Inicia a edição
        $this->editingPostId = $postId;
        $this->editingPostContent = $post->content;
    }

    public function cancelEdit()
    {
        $this->editingPostId = null;
        $this->editingPostContent = '';
        $this->resetErrorBag();
    }

    // Atualização de Post
    public function updatePost()
    {
        $this->validate([
            'editingPostContent' => 'required|string|min:2|max:300',
        ]);

        $post = Post::findOrFail($this->editingPostId);

        // Verifica se o usuário tem permissão para editar (autor ou coordenador)
        $isCoordinator = optional(optional($this->course->courseCoordinator)->userAccount)->id === auth()->id();
        if (auth()->id() !== $post->user_id && !$isCoordinator) {
             session()->flash('error', 'Você não tem permissão para atualizar este post.');
             $this->cancelEdit();
             return;
        }

        $post->update([
            // Garante que se o conteúdo for apagado, ele seja salvo como '' (string vazia)
            'content' => trim($this->editingPostContent) ?: null, 
        ]);

        session()->flash('success', 'Post atualizado com sucesso!');
        $this->cancelEdit();
    }
    
    // Criação de Respostas
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
        $this->dispatch('replyCreated');
    }

    //
    public function deleteReply($replyId)
    {
        $reply = \App\Models\Reply::findOrFail($replyId);
        $coordinatorUserId = optional(optional($this->course->courseCoordinator)->userAccount)->id;

        if (auth()->id() === $reply->user_id || auth()->id() === $coordinatorUserId) {
            $reply->delete();
            session()->flash('success', 'Resposta excluída com sucesso.');
            $this->dispatch('replyDeleted');
        } else {
            session()->flash('error', 'Você não tem permissão para excluir esta resposta.');
        }
    }

    // Excluir Post
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