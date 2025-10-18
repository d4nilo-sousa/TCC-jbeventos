<?php

namespace App\Livewire;

use App\Models\Post;
use App\Models\Course;
use App\Models\Reply; 
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\On;
use Livewire\WithFileUploads;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

class FeedPosts extends Component
{
    use WithPagination, WithFileUploads;

    protected $paginationTheme = 'tailwind';

    public $newReplyContent = [];
    public ?int $selectedPostId = null;
    public ?Post $expandedPost = null;

    public $isCarouselOpen = false;
    public $currentImageIndex = 0;

    // --- PROPRIEDADES DE CRIAÇÃO DE POST ---
    public $isCoordinator = false;
    public $newPostContent = '';
    /** @var TemporaryUploadedFile|null */
    public $media = null;
    public $newPostCourseId = null;
    public Collection $coordinatorCourses;

    // --- PROPRIEDADES DE EDIÇÃO DE POST ---
    public ?int $editingPostId = null;
    public $editingPostContent = '';
    /** @var TemporaryUploadedFile|null */
    public $editingMedia = null;
    public $originalMediaPath = null;

    public ?int $confirmingPostDeletionId = null;

    // --- PROPRIEDADES NOVAS PARA EDIÇÃO/EXCLUSÃO DE RESPOSTA ---
    public ?int $editingReplyId = null;
    public $editingReplyContent = '';
    public ?int $confirmingReplyDeletionId = null;
    // -----------------------------------------------------------

    protected function rules()
    {
        $rules = [
            'newReplyContent.*' => 'required|string|min:2|max:500',
        ];

        // NOVO: Regra de validação para o conteúdo da edição da resposta
        if ($this->editingReplyId) {
            $rules['editingReplyContent'] = 'required|string|min:2|max:500';
        }

        if ($this->isCoordinator) {
            $rules['newPostContent'] = 'nullable|string|max:5000';
            $rules['media'] = 'nullable|file|max:5120|mimes:jpeg,png,jpg,gif,pdf,doc,docx,zip';
        }

        if ($this->editingPostId) {
            $rules['editingPostContent'] = 'nullable|string|max:5000';
            $rules['editingMedia'] = 'nullable|file|max:5120|mimes:jpeg,png,jpg,gif,pdf,doc,docx,zip';
        }

        return $rules;
    }

    protected function validationAttributes()
    {
        return [
            'media' => 'arquivo anexado',
            'editingMedia' => 'novo arquivo anexado',
            'editingReplyContent' => 'conteúdo da resposta', // NOVO
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
                    $this->coordinatorCourses = Course::all();
                } else {
                    $course = $coordinator->coordinatedCourse;
                    $this->coordinatorCourses = $course ? collect([$course]) : collect();
                }
            }

            if ($this->coordinatorCourses->isNotEmpty()) {
                $this->newPostCourseId = $this->coordinatorCourses->first()->id;
            }
        }
    }

    // Método para CRIAR POST (mantido)
    public function createPost()
    {
        if (!$this->isCoordinator || !$this->newPostCourseId) {
            session()->flash('error', 'Você não tem permissão ou curso associado para criar posts.');
            return;
        }

        $this->validate();

        if (empty(trim($this->newPostContent)) && is_null($this->media)) {
            session()->flash('error', 'O post deve ter texto ou um arquivo anexado.');
            return;
        }

        $imagePaths = [];
        $filePath = null;

        if ($this->media) {
            $filePath = $this->media->store('posts', 'public');

            $imagePaths[] = $filePath;
        }

        Post::create([
            'user_id' => Auth::id(),
            'course_id' => $this->newPostCourseId,
            'content' => $this->newPostContent,
            'images' => $imagePaths,
        ]);

        $this->reset('newPostContent', 'media');
        $this->dispatch('postCreated');
        $this->resetPage();

        session()->flash('success', 'Post criado com sucesso!');
    }

    // --- MÉTODOS DE EDIÇÃO ATUALIZADOS PARA ARQUIVO ÚNICO ---

    public function startEditPost(int $postId)
    {
        $post = Post::where('user_id', Auth::id())->findOrFail($postId);

        $this->editingPostId = $post->id;
        $this->editingPostContent = $post->content;

        $this->originalMediaPath = !empty($post->images) ? $post->images[0] : null;

        $this->editingMedia = null;

        $this->dispatch('openEditModal');
    }

    public function saveEditPost()
    {
        $post = Post::where('user_id', Auth::id())->findOrFail($this->editingPostId);

        $this->validate([
            'editingPostContent' => 'nullable|string|max:5000',
            'editingMedia' => 'nullable|file|max:5120|mimes:jpeg,png,jpg,gif,pdf,doc,docx,zip',
        ]);

        $finalImages = [];
        $shouldDeleteOriginal = false;

        if ($this->editingMedia) {
            $newPath = $this->editingMedia->store('posts', 'public');
            $finalImages = [$newPath];
            $shouldDeleteOriginal = !is_null($this->originalMediaPath);
        } elseif (!is_null($this->originalMediaPath)) {
            $finalImages = [$this->originalMediaPath];
        }

        if ($shouldDeleteOriginal && $this->originalMediaPath) {
            Storage::disk('public')->delete($this->originalMediaPath);
        } elseif (is_null($this->originalMediaPath) && !is_null($post->images)) {
            Storage::disk('public')->delete($post->images);
        }

        $post->update([
            'content' => $this->editingPostContent,
            'images' => $finalImages,
        ]);

        session()->flash('success', 'Post atualizado com sucesso!');
        $this->resetEditModal();
        $this->resetPage();
    }

    public function removeEditingMedia()
    {
        $this->originalMediaPath = null;
        $this->editingMedia = null;
    }

    public function resetEditModal()
    {
        $this->reset([
            'editingPostId',
            'editingPostContent',
            'editingMedia',
            'originalMediaPath',
        ]);
        $this->dispatch('closeEditModal');
    }

    // --- MÉTODOS DE EXCLUSÃO DE POST ---

    public function confirmPostDeletion(int $postId)
    {
        $this->confirmingPostDeletionId = $postId;
    }

    public function deletePost()
    {
        $postId = $this->confirmingPostDeletionId;
        if (!$postId) return;

        $post = Post::where('user_id', Auth::id())->findOrFail($postId);

        if ($post->images) {
            Storage::disk('public')->delete($post->images);
        }

        $post->delete();

        session()->flash('success', 'Post excluído com sucesso!');
        $this->confirmingPostDeletionId = null;
        $this->resetPage();

        if ($this->selectedPostId === $postId) {
            $this->closePostModal();
        }
    }

    // --- MÉTODOS DE MODAL DE POST ---

    public function openPostModal(int $postId)
    {
        $this->selectedPostId = $postId;
        $this->expandedPost = Post::with(['course.courseCoordinator.userAccount', 'author', 'replies.author'])
            ->findOrFail($postId);

        $this->isCarouselOpen = false;
        $this->currentImageIndex = 0;

        // NOVO: Reseta o estado de edição/exclusão da resposta ao abrir o modal
        $this->resetEditReply();
        $this->confirmingReplyDeletionId = null;
    }

    public function closePostModal()
    {
        $this->selectedPostId = null;
        $this->expandedPost = null;
        $this->isCarouselOpen = false;

        // NOVO: Reseta o estado de edição/exclusão da resposta ao fechar
        $this->resetEditReply();
        $this->confirmingReplyDeletionId = null;

        $this->dispatch('close-post-modal');
    }

    public function openCarousel(int $imageIndex)
    {
        if (!$this->expandedPost || empty($this->expandedPost->images)) {
            return;
        }

        $this->currentImageIndex = $imageIndex;
        $this->isCarouselOpen = true;
    }

    // --- MÉTODOS DE RESPOSTA (REPLY) ---

    public function createReply($postId)
    {
        if (!isset($this->newReplyContent[$postId])) {
            $this->newReplyContent[$postId] = '';
        }

        $this->validate([
            "newReplyContent.{$postId}" => $this->rules()['newReplyContent.*']
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

    // ==========================================================
    // MÉTODOS NOVOS PARA EDIÇÃO E EXCLUSÃO DE RESPOSTA (REPLY)
    // ==========================================================

    /**
     * Inicia o modo de edição para uma resposta específica.
     */
    public function startEditReply(int $replyId)
    {
        $reply = Reply::where('user_id', Auth::id())->findOrFail($replyId);

        $this->editingReplyId = $replyId;
        $this->editingReplyContent = $reply->content;
    }

    /**
     * Salva o conteúdo editado da resposta.
     */
    public function saveEditReply()
    {
        $this->validate([
            'editingReplyContent' => 'required|string|min:2|max:500',
        ]);

        $reply = Reply::where('user_id', Auth::id())->findOrFail($this->editingReplyId);

        $reply->content = $this->editingReplyContent;
        $reply->save();

        session()->flash('success', 'Resposta editada com sucesso!');

        // Atualiza a lista de respostas no modal
        $this->expandedPost->refresh();

        $this->resetEditReply();
    }

    /**
     * Reseta as propriedades de edição de resposta.
     */
    public function resetEditReply()
    {
        $this->reset([
            'editingReplyId',
            'editingReplyContent',
        ]);
    }

    /**
     * Abre o modal de confirmação para exclusão de resposta.
     */
    public function confirmReplyDeletion(int $replyId)
    {
        $this->confirmingReplyDeletionId = $replyId;
    }

    /**
     * Executa a exclusão da resposta.
     * Permite que o dono da resposta ou o dono do post excluam.
     */
    public function deleteReply()
    {
        $replyId = $this->confirmingReplyDeletionId;
        if (!$replyId) return;

        $reply = Reply::with('post')->find($replyId);

        if (!$reply) {
            session()->flash('error', 'Resposta não encontrada.');
            $this->confirmingReplyDeletionId = null;
            return;
        }

        // Permite deletar se for dono da resposta ou dono do post
        if ($reply->user_id !== auth()->id() && $reply->post->user_id !== auth()->id()) {
            abort(403);
        }

        $reply->delete();

        session()->flash('success', 'Resposta excluída com sucesso!');

        $this->confirmingReplyDeletionId = null;

        // Atualiza a lista de respostas no modal
        if ($this->expandedPost) {
            $this->expandedPost = $this->expandedPost->fresh(['replies.author']);
        }

        $this->resetPage();
    }
    // ==========================================================

    #[On('postCreated')]
    public function render()
    {
        $posts = Post::with(['course.courseCoordinator.userAccount', 'author', 'replies'])
            ->latest()
            ->get();

        $feedItems = $posts->map(function ($post) {
            $post->type = 'post';
            $post->sort_date = $post->created_at;
            return $post;
        });

        if ($this->selectedPostId && !$this->expandedPost) {
            $this->expandedPost = Post::with(['course.courseCoordinator.userAccount', 'author', 'replies.author'])
                ->findOrFail($this->selectedPostId);
        }

        // NOVO: Garantir que a propriedade editingReplyContent seja definida se estiver editando
        if ($this->editingReplyId && empty($this->editingReplyContent)) {
            $this->startEditReply($this->editingReplyId);
        }

        return view('livewire.feed-posts', [
            'feedItems' => $feedItems,
            'posts' => $posts,
        ]);
    }
}
