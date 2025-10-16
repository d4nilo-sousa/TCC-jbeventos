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

class FeedPosts extends Component
{
    use WithPagination, WithFileUploads;

    protected $paginationTheme = 'tailwind';

    public $newReplyContent = [];
    public ?int $selectedPostId = null;
    public ?Post $expandedPost = null;

    public $isCarouselOpen = false;
    public $currentImageIndex = 0;

    public $isCoordinator = false;
    public $newPostContent = '';
    public $newlyUploadedImages = [];
    public $images = [];
    public $newPostCourseId = null;
    public Collection $coordinatorCourses;

    public ?int $editingPostId = null;
    public $editingPostContent = '';
    public $newlyUploadedEditingImages = [];
    public $editingPostImages = [];
    public $originalPostImages = [];

    public ?int $confirmingPostDeletionId = null;

    protected function rules()
    {
        $rules = [
            'newReplyContent.*' => 'required|string|min:2|max:500',
        ];

        if ($this->isCoordinator) {
            $rules['newPostContent'] = 'nullable|string|max:5000';
            $rules['images'] = 'nullable|array|max:5';
            $rules['images.*'] = 'nullable|image|max:1024';
        }

        if ($this->editingPostId) {
            $rules['editingPostContent'] = 'nullable|string|max:5000';
            $rules['editingPostImages'] = 'nullable|array|max:5';
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

    public function updatedNewlyUploadedImages()
    {
        $totalImages = count($this->images);
        $newImagesCount = count($this->newlyUploadedImages);

        if ($totalImages + $newImagesCount > 5) {
            session()->flash('error_image', 'Você só pode adicionar um máximo de 5 imagens por post.');
            $this->newlyUploadedImages = [];
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

    public function updatedNewlyUploadedEditingImages()
    {
        $totalImages = count($this->editingPostImages);
        $newImagesCount = count($this->newlyUploadedEditingImages);

        if ($totalImages + $newImagesCount > 5) {
            session()->flash('error_edit_image', 'Você só pode ter um máximo de 5 imagens no post.');
            $this->newlyUploadedEditingImages = [];
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

    public function createPost()
    {
        if (!$this->isCoordinator || !$this->newPostCourseId) {
            session()->flash('error', 'Você não tem permissão ou curso associado para criar posts.');
            return;
        }

        $this->validate();

        if (empty(trim($this->newPostContent)) && empty($this->images)) {
            session()->flash('error', 'O post deve ter texto ou imagem.');
            return;
        }

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
            'editingPostImages' => $this->rules()['editingPostImages'],
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

    public function openPostModal(int $postId)
    {
        $this->selectedPostId = $postId;
        $this->expandedPost = Post::with(['course.courseCoordinator.userAccount', 'author', 'replies.author'])
            ->findOrFail($postId);

        $this->isCarouselOpen = false;
        $this->currentImageIndex = 0;
        $this->dispatch('openPostModal');
    }

    public function closePostModal()
    {
        $this->selectedPostId = null;
        $this->expandedPost = null;
        $this->isCarouselOpen = false;
    }

    public function openCarousel(int $imageIndex)
    {
        if (!$this->expandedPost || empty($this->expandedPost->images)) {
            return;
        }

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
            'posts' => $posts,
        ]);
    }
}
