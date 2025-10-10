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
    use WithPagination, WithFileUploads; // Adiciona suporte a paginação e upload de arquivos

    protected $paginationTheme = 'tailwind'; // Usa o tema Tailwind para paginação

    // Propriedades do componente
    public Course $course;
    public $newPostContent = '';
    public $newReplyContent = [];
    public $images = [];
    public $newlyUploadedImages = [];

    // Regras de validação
    protected $rules = [
        'newPostContent' => 'required|string|min:5',
        'images.*' => 'image|max:2048',
        'newReplyContent.*' => 'required|string|min:2',
    ];

    // Montagem inicial do componente com o curso
    public function mount(Course $course)
    {
        $this->course = $course;
    }

    // Renderização do componente
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

    // Corrigido para ser um listener explícito de mudança (mesmo que wire:model já chame)
    public function updatedNewlyUploadedImages()
    {
        $newFiles = is_array($this->newlyUploadedImages) ? $this->newlyUploadedImages : [$this->newlyUploadedImages];
        $currentImages = collect($this->images);

        // Adiciona os novos arquivos à coleção de imagens
        $updatedImages = $currentImages->merge($newFiles);

        if ($updatedImages->count() > 5) {
            $this->images = $updatedImages->take(5)->all();
            session()->flash('error', 'Você só pode enviar até 5 imagens por post.');
        } else {
            $this->images = $updatedImages->all();
        }

        // Limpa a propriedade de upload para permitir novos uploads
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
            'newPostContent' => 'required|string|min:5', 
            'images.*' => 'image|max:2048'
        ]);

        $coordinatorUserId = optional(optional($this->course->courseCoordinator)->userAccount)->id;

        if (auth()->id() !== $coordinatorUserId) {
            session()->flash('error', 'Somente o coordenador pode criar posts.');
            return;
        }

        $imagePaths = [];
        if (!empty($this->images)) {
            foreach ($this->images as $image) {
                // Certifica-se de que é uma instância de UploadedFile antes de armazenar
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

        // Evita que o campo de reply de outros posts seja limpo
        $this->newReplyContent[$postId] = ''; 
        // Não é necessário resetPage, pois replies estão aninhados. 
        // Apenas recarrega o estado do componente.
        
        session()->flash('success', 'Resposta enviada com sucesso!');
        $this->dispatch('replyCreated');
    }

    public function deleteReply($replyId)
    {
        $reply = \App\Models\Reply::findOrFail($replyId);
        $coordinatorUserId = optional(optional($this->course->courseCoordinator)->userAccount)->id;

        // Autor da resposta OU Coordenador do Curso
        if (auth()->id() === $reply->user_id || auth()->id() === $coordinatorUserId) {
            $reply->delete();
            session()->flash('success', 'Resposta excluída com sucesso.');
            $this->dispatch('replyDeleted'); // Dispara evento para atualizar a interface
        } else {
            session()->flash('error', 'Você não tem permissão para excluir esta resposta.');
        }
    }


    // Excluir Post (somente para o autor ou coordenador)
    public function deletePost($postId)
    {
        $post = Post::findOrFail($postId);
        $coordinatorUserId = optional(optional($this->course->courseCoordinator)->userAccount)->id;

        // Autor do Post OU Coordenador do Curso
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