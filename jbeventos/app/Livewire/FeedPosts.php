<?php

namespace App\Livewire;

use App\Models\Post;
use App\Models\Course; 
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\On; 
use Livewire\WithFileUploads;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class FeedPosts extends Component
{
    use WithPagination, WithFileUploads;

    protected $paginationTheme = 'tailwind';
    
    // --- PROPRIEDADES ORIGINAIS (Respostas e Modal) ---
    public $newReplyContent = []; 
    public ?int $selectedPostId = null;
    public ?Post $expandedPost = null; 

    // --- PROPRIEDADES PARA CRIAÇÃO DE POSTS (Novas) ---
    public $isCoordinator = false;
    public $newPostContent = '';
    public $newlyUploadedImages = []; // Arquivos temporários do input
    public $images = []; // Arquivos prontos para upload/preview
    // ID do curso de destino, definido no mount
    public $newPostCourseId = null; 
    public Collection $coordinatorCourses;
    // ------------------------------------------

    protected function rules()
    {
        $rules = [
            'newReplyContent.*' => 'required|string|min:2|max:500', 
        ];

        if ($this->isCoordinator) {
            $rules['newPostContent'] = 'required|string|max:5000';
            $rules['images.*'] = 'nullable|image|max:1024';
        }

        return $rules;
    }
    
    public function mount()
    {
        $user = Auth::user();
        
        $this->isCoordinator = ($user && $user->user_type === 'coordinator');
        $this->coordinatorCourses = collect();

        if ($this->isCoordinator) {
            $coordinator = $user->coordinatorRole;

            if ($coordinator) {
                if ($coordinator->coordinator_type === 'general') {
                     // Coordenador Geral: Carrega todos os cursos
                     $this->coordinatorCourses = Course::all(); 
                } else {
                    // Coordenador de Curso Específico
                    $course = $coordinator->coordinatedCourse;
                    $this->coordinatorCourses = $course ? collect([$course]) : collect();
                }
            }
            
            // Define o curso de destino como o primeiro curso associado
            if ($this->coordinatorCourses->isNotEmpty()) {
                $this->newPostCourseId = $this->coordinatorCourses->first()->id;
            }
        }
    }
    
    // --- MÉTODOS DE UPLOAD/IMAGEM ---
    public function updatedNewlyUploadedImages()
    {
        // Adiciona as imagens recém-selecionadas ao array de preview
        $this->images = array_merge($this->images, $this->newlyUploadedImages);
        $this->newlyUploadedImages = [];
    }

    public function removeImage($index)
    {
        if (isset($this->images[$index])) {
            unset($this->images[$index]);
            $this->images = array_values($this->images); 
        }
    }
    
    // --- MÉTODO DE CRIAÇÃO DO POST ---
    public function createPost()
    {
        if (!$this->isCoordinator || !$this->newPostCourseId) {
             session()->flash('error', 'Você não tem permissão ou curso associado para criar posts.');
             return;
        }
        
        // Validação
        $this->validate([
            'newPostContent' => $this->rules()['newPostContent'],
            'images.*' => $this->rules()['images.*'],
        ]);

        // 1. Processar upload das imagens
        $imagePaths = [];
        foreach ($this->images as $image) {
            $imagePaths[] = $image->store('posts', 'public');
        }

        // 2. Criar o Post
        Post::create([
            'user_id' => Auth::id(),
            'course_id' => $this->newPostCourseId, // Usa o ID pré-definido
            'content' => $this->newPostContent,
            'images' => $imagePaths,
        ]);

        // 3. Limpar formulário e emitir evento
        $this->reset('newPostContent', 'newlyUploadedImages', 'images');
        $this->dispatch('postCreated'); 
        $this->resetPage();

        session()->flash('success', 'Post criado com sucesso!');
    }
    // ------------------------------------
    
    // --- MÉTODOS ORIGINAIS (Modal) ---
    public function openPostModal(int $postId)
    {
        $this->selectedPostId = $postId;
        $this->expandedPost = Post::with(['course.courseCoordinator.userAccount', 'author', 'replies.author'])
            ->findOrFail($postId);
        $this->dispatch('openPostModal');
    }

    public function closePostModal()
    {
        $this->selectedPostId = null;
        $this->expandedPost = null;
        $this->resetPage(); // Mantido resetPage()
    }

    public function createReply($postId)
    {
        if (!isset($this->newReplyContent[$postId])) {
             $this->newReplyContent[$postId] = '';
        }

        $this->validate([
            "newReplyContent.{$postId}" => $this->rules()['newReplyContent.*'] 
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

        $this->newReplyContent[$postId] = '';

        if ($this->selectedPostId === $postId) {
            $this->expandedPost = $this->expandedPost->fresh(['replies.author']);
        }

        $this->resetPage(); 
        session()->flash('success', 'Resposta enviada com sucesso!');
        $this->dispatch('replyCreated');
    }

    // --- MÉTODO RENDER ---
    #[On('postCreated')] 
    public function render()
    {
        $posts = Post::with(['course.courseCoordinator.userAccount', 'author', 'replies']) 
            ->latest() 
            ->paginate(5);

        $feedItems = $posts->map(function ($post) {
            $post->type = 'post';
            $post->sort_date = $post->created_at; 
            return $post;
        });
        
        if ($this->selectedPostId && !$this->expandedPost) {
            $this->expandedPost = Post::with(['course.courseCoordinator.userAccount', 'author', 'replies.author'])
                ->findOrFail($this->selectedPostId);
        }

        return view('livewire.feed-posts', [
            'feedItems' => $feedItems,
            'posts' => $posts, // Variável usada DENTRO do livewire/feed-posts.blade.php
        ]);
    }
}