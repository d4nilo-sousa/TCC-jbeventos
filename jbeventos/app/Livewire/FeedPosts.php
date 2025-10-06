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
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class FeedPosts extends Component
{
    use WithPagination, WithFileUploads;

    protected $paginationTheme = 'tailwind';
    
    // --- PROPRIEDADES DE INTERAÇÃO (Respostas e Modal) ---
    public $newReplyContent = []; 
    public ?int $selectedPostId = null;
    public ?Post $expandedPost = null; 

    // --- PROPRIEDADES PARA CRIAÇÃO DE POSTS ---
    public $isCoordinator = false;
    public $newPostContent = '';
    public $newlyUploadedImages = []; // Arquivos temporários do input
    public $images = []; // Arquivos prontos para upload/preview
    public $newPostCourseId = null; 
    public Collection $coordinatorCourses;

    // --- PROPRIEDADES PARA EDIÇÃO DE POSTS ---
    public ?int $editingPostId = null; 
    public $editingPostContent = '';
    public $newlyUploadedEditingImages = []; // Novos uploads no modal de edição
    public $editingPostImages = []; // Array que mistura caminhos de storage e temporários
    public $originalPostImages = []; // Usado para saber quais imagens deletar ao salvar
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
        
        // Regras para edição
        if ($this->editingPostId) {
             $rules['editingPostContent'] = 'required|string|max:5000';
             $rules['newlyUploadedEditingImages.*'] = 'nullable|image|max:1024';
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
    
    // --- UPLOAD/IMAGEM (Criação) ---
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

    // --- UPLOAD/IMAGEM (Edição) ---
    public function updatedNewlyUploadedEditingImages()
    {
        $this->editingPostImages = array_merge($this->editingPostImages, $this->newlyUploadedEditingImages);
        $this->newlyUploadedEditingImages = [];
    }

    public function removeEditingImage($index)
    {
        if (isset($this->editingPostImages[$index])) {
            unset($this->editingPostImages[$index]);
            $this->editingPostImages = array_values($this->editingPostImages);
        }
    }
    
    // --- MÉTODO DE CRIAÇÃO DO POST ---
    public function createPost()
    {
        if (!$this->isCoordinator || !$this->newPostCourseId) {
             session()->flash('error', 'Você não tem permissão ou curso associado para criar posts.');
             return;
        }
        
        $this->validate([
            'newPostContent' => $this->rules()['newPostContent'],
            'images.*' => $this->rules()['images.*'],
        ]);

        // 1. Processar upload das imagens
        $imagePaths = [];
        foreach ($this->images as $image) {
            // Verifica se é um arquivo temporário antes de tentar store()
            if (is_object($image) && method_exists($image, 'store')) {
                $imagePaths[] = $image->store('posts', 'public');
            }
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
    
    // --- MÉTODOS DE EDIÇÃO ---
    public function startEditPost(int $postId)
    {
        // Garante que APENAS o usuário criador possa editar
        $post = Post::where('user_id', Auth::id())->findOrFail($postId);
        
        $this->editingPostId = $post->id;
        $this->editingPostContent = $post->content;
        $this->editingPostImages = $post->images ?? [];
        $this->originalPostImages = $post->images ?? []; 
        
        $this->dispatch('openEditModal');
    }

    public function saveEditPost()
    {
        $post = Post::where('user_id', Auth::id())->findOrFail($this->editingPostId);

        $this->validate([
            'editingPostContent' => $this->rules()['editingPostContent'],
            'newlyUploadedEditingImages.*' => $this->rules()['newlyUploadedEditingImages.*'], 
        ]);
        
        $currentImages = [];
        $newUploads = [];

        // 1. Processar novas imagens e separar caminhos existentes
        foreach ($this->editingPostImages as $image) {
            if (is_object($image) && method_exists($image, 'store')) {
                // É um novo arquivo temporário, fazer upload
                $newUploads[] = $image->store('posts', 'public');
            } elseif (is_string($image)) {
                // É um caminho de arquivo existente no Storage
                $currentImages[] = $image;
            }
        }
        
        // 2. Combinar imagens novas e antigas que não foram removidas
        $finalImages = array_merge($currentImages, $newUploads);

        // 3. Deletar imagens antigas que foram removidas do array
        $imagesToDelete = array_diff($this->originalPostImages, $currentImages);
        if (!empty($imagesToDelete)) {
            Storage::disk('public')->delete($imagesToDelete);
        }

        // 4. Atualizar o Post
        $post->update([
            'content' => $this->editingPostContent,
            'images' => $finalImages,
        ]);
        
        session()->flash('success', 'Post atualizado com sucesso!');
        $this->resetEditModal();
        $this->resetPage();
    }

    public function resetEditModal()
    {
        $this->reset([
            'editingPostId', 
            'editingPostContent', 
            'editingPostImages',
            'originalPostImages',
            'newlyUploadedEditingImages',
        ]);
        $this->dispatch('closeEditModal');
    }

    /**
     * Deleta um post.
     */
    public function deletePost(int $postId)
    {
        // Garante que APENAS o usuário criador possa deletar
        $post = Post::where('user_id', Auth::id())->findOrFail($postId);
        
        // Deleta as imagens do storage
        if ($post->images) {
            Storage::disk('public')->delete($post->images);
        }
        
        $post->delete();

        session()->flash('success', 'Post excluído com sucesso!');
        $this->resetPage(); 
        if ($this->selectedPostId === $postId) {
            $this->closePostModal();
        }
    }
    // ------------------------------------
    
    // --- MÉTODOS DE RESPOSTAS/MODAL ---
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
        $this->resetPage();
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

        // Mapeamento mantido para compatibilidade, embora não seja usado para mistura
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
            'posts' => $posts,
        ]);
    }
}