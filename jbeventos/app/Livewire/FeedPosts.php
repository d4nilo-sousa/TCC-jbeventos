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
use Illuminate\Database\Eloquent\Builder;

class FeedPosts extends Component
{
    use WithPagination, WithFileUploads;

    protected $paginationTheme = 'tailwind';
    
    // --- PROPRIEDADES DE INTERAÇÃO (Respostas e Modal) ---
    public $newReplyContent = []; 
    public ?int $selectedPostId = null;
    public ?Post $expandedPost = null; 
    
    // --- PROPRIEDADES PARA CARROSSEL ---
    public $isCarouselOpen = false;
    public $currentImageIndex = 0; 
    // ------------------------------------------

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
    public $newlyUploadedEditingImages = []; 
    public $editingPostImages = []; 
    public $originalPostImages = []; 
    // ------------------------------------------

    // --- PROPRIEDADES PARA EXCLUSÃO DE POSTS ---
    public ?int $confirmingPostDeletionId = null; 
    // ----------------------------------------------


    protected function rules()
    {
        $rules = [
            'newReplyContent.*' => 'required|string|min:2|max:500', 
        ];

        if ($this->isCoordinator) {
            $rules['newPostContent'] = 'required|string|max:5000';
            // REGRA: Limita a 2 imagens.
            $rules['images'] = 'nullable|array|max:2'; 
            $rules['images.*'] = 'nullable|image|max:1024';
        }
        
        // Regras para edição
        if ($this->editingPostId) {
             $rules['editingPostContent'] = 'required|string|max:5000';
             // REGRA: A soma total de imagens (existentes + novas) não deve exceder 2.
             $rules['editingPostImages'] = 'nullable|array|max:2';
             $rules['newlyUploadedEditingImages.*'] = 'nullable|image|max:1024';
        }

        return $rules;
    }
    
    protected function validationAttributes()
    {
        return [
            'images' => 'imagens do post',
            'images.*' => 'arquivo de imagem',
            'editingPostImages' => 'imagens do post',
        ];
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
        // Limita a adicionar imagens apenas se o total não exceder 2
        $totalImages = count($this->images);
        $newImagesCount = count($this->newlyUploadedImages);
        
        if ($totalImages + $newImagesCount > 2) {
             session()->flash('error_image', 'Você só pode adicionar um máximo de 2 imagens por post.');
             $this->newlyUploadedImages = []; // Limpa o upload
             return;
        }
        
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
        // Limita a adicionar imagens apenas se o total não exceder 2
        $totalImages = count($this->editingPostImages);
        $newImagesCount = count($this->newlyUploadedEditingImages);
        
        if ($totalImages + $newImagesCount > 2) {
             session()->flash('error_edit_image', 'Você só pode ter um máximo de 2 imagens no post.');
             $this->newlyUploadedEditingImages = []; // Limpa o upload
             return;
        }

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
            'images' => $this->rules()['images'],
            'images.*' => $this->rules()['images.*'],
        ]);

        $imagePaths = [];
        foreach ($this->images as $image) {
            if (is_object($image) && method_exists($image, 'store')) {
                $imagePaths[] = $image->store('posts', 'public');
            }
        }

        Post::create([
            'user_id' => Auth::id(),
            'course_id' => $this->newPostCourseId, 
            'content' => $this->newPostContent,
            'images' => $imagePaths,
        ]);

        $this->reset('newPostContent', 'newlyUploadedImages', 'images');
        $this->dispatch('postCreated'); 
        $this->resetPage();

        session()->flash('success', 'Post criado com sucesso!');
    }
    
    // --- MÉTODOS DE EDIÇÃO ---
    public function startEditPost(int $postId)
    {
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
            'editingPostImages' => $this->rules()['editingPostImages'], // Validação do limite de 2
            'newlyUploadedEditingImages.*' => $this->rules()['newlyUploadedEditingImages.*'], 
        ]);
        
        $currentImages = [];
        $newUploads = [];

        foreach ($this->editingPostImages as $image) {
            if (is_object($image) && method_exists($image, 'store')) {
                $newUploads[] = $image->store('posts', 'public');
            } elseif (is_string($image)) {
                $currentImages[] = $image;
            }
        }
        
        $finalImages = array_merge($currentImages, $newUploads);

        $imagesToDelete = array_diff($this->originalPostImages, $currentImages);
        if (!empty($imagesToDelete)) {
            Storage::disk('public')->delete($imagesToDelete);
        }

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

    // --- MÉTODOS DE EXCLUSÃO ---

    /**
     * Abre o modal de confirmação de exclusão.
     */
    public function confirmPostDeletion(int $postId)
    {
        $this->confirmingPostDeletionId = $postId;
    }

    /**
     * Deleta um post após a confirmação do modal.
     */
    public function deletePost()
    {
        $postId = $this->confirmingPostDeletionId; // Armazena o ID antes de limpar

        // Se nenhum post está selecionado, retorna
        if (!$postId) {
            return;
        }

        $post = Post::where('user_id', Auth::id())->findOrFail($postId);
        
        if ($post->images) {
            Storage::disk('public')->delete($post->images);
        }
        
        $post->delete();

        session()->flash('success', 'Post excluído com sucesso!');
        
        // Limpa o estado da deleção e recarrega
        $this->confirmingPostDeletionId = null;
        $this->resetPage(); 
        
        // Fecha o modal de post expandido se o post deletado estava aberto
        if ($this->selectedPostId === $postId) {
            $this->closePostModal();
        }
    }

    // ------------------------------------
    
    // --- MÉTODOS DE RESPOSTAS/MODAL ---

    /**
     * Define o post selecionado, carrega o post expandido e abre o modal.
     */
    public function openPostModal(int $postId)
    {
        $this->selectedPostId = $postId;
        $this->expandedPost = Post::with(['course.courseCoordinator.userAccount', 'author', 'replies.author'])
            ->findOrFail($postId);
            
        // Garante que o carrossel esteja fechado ao abrir o modal principal
        $this->isCarouselOpen = false;
        $this->currentImageIndex = 0; 
        
        // O evento JS é útil para o Alpine.js garantir que o modal seja exibido.
        $this->dispatch('openPostModal');
    }

    /**
     * Fecha o modal, limpando as propriedades reativas.
     */
    public function closePostModal()
    {
        $this->selectedPostId = null;
        $this->expandedPost = null;
        $this->isCarouselOpen = false; // Garante que o carrossel feche junto
    }
    
    /**
     * Abre o modal do carrossel no índice da imagem clicada.
     */
    public function openCarousel(int $imageIndex)
    {
        // Esta checagem garante que só abrimos se houver um post expandido válido
        if (!$this->expandedPost || empty($this->expandedPost->images)) {
            return;
        }
        
        // Define o índice inicial e abre o modal do carrossel
        $this->currentImageIndex = $imageIndex;
        $this->isCarouselOpen = true;
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
            // Recarrega apenas as respostas para mostrar a nova resposta no modal
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
        
        // Garante que o post expandido seja recarregado se o ID estiver definido (para evitar desserialização incompleta)
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