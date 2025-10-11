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

    // Propriedades de Criação
    public Course $course;
    public $newPostContent = '';
    public $newReplyContent = [];
    public $images = [];
    public $newlyUploadedImages = [];

    // PROPRIEDADES DE EDIÇÃO
    public $editingPostId = null;
    public $editingPostContent = '';
    public $editingPostCurrentImages = []; // Imagens JÁ SALVAS no DB 
    public $editingPostNewImages = []; // Imagens NOVAS enviadas


    // Método rules() para validação dinâmica
    protected function rules()
    {
        // Calcula o total de imagens durante a edição
        $totalEditingImages = count($this->editingPostCurrentImages) + count(is_array($this->editingPostNewImages) ? $this->editingPostNewImages : []);
        
        // Define as regras base
        $rules = [
            'newPostContent' => 'nullable|string|max:500', 
            'images' => 'array|max:5',
            'images.*' => 'image|max:2048', 
            'newReplyContent.*' => 'required|string|min:2|max:100', 
            
            // Regras para Edição
            'editingPostContent' => 'nullable|string|max:300',
            'editingPostNewImages' => 'array', // Array de novos uploads
            'editingPostNewImages.*' => 'image|max:2048', // Regra para cada nova imagem
        ];

        // Se o total de imagens na edição exceder 5, retorna um erro de validação (embora a lógica de `updatedEditingPostNewImages` tente evitar isso)
        if ($totalEditingImages > 5) {
             // Esta checagem é mais para garantir que a validação não passe,
             // mas a lógica de limite é controlada principalmente por updatedEditingPostNewImages.
        }

        return $rules;
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

    // --- MÉTODOS DE CRIAÇÃO DE POSTS ---

    // Lógica para múltiplas imagens
    public function updatedNewlyUploadedImages()
    {
        $newFiles = is_array($this->newlyUploadedImages) ? $this->newlyUploadedImages : [$this->newlyUploadedImages];
        $currentImages = collect($this->images);
        $updatedImages = $currentImages->merge($newFiles);
        $totalAllowed = 5;

        if ($updatedImages->count() > $totalAllowed) {
            $take = $totalAllowed - $currentImages->count();
            // Pega apenas o que falta para completar o limite de 5
            $this->images = $currentImages->merge(collect($newFiles)->take($take))->all(); 
            session()->flash('error', "Você só pode enviar até 5 imagens por post. Apenas {$take} foram adicionadas desta vez.");
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

        // Checagem Lógica: O post precisa de CONTEÚDO OU IMAGENS
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
            // Garante que se o conteúdo for só espaços vazios, seja salvo como null
            'content' => trim($this->newPostContent) ?: null, 
            'images' => $imagePaths,
        ]);

        $this->newPostContent = '';
        $this->images = [];
        $this->resetPage();

        session()->flash('success', 'Post criado com sucesso!');
        $this->dispatch('postCreated');
    }

    // --- MÉTODOS DE EDIÇÃO DE POSTS ---

    // Inicializa o formulário de edição
    public function startEdit($postId) 
    {
        $post = Post::findOrFail($postId);

        $isCoordinator = optional(optional($this->course->courseCoordinator)->userAccount)->id === auth()->id();
        if (auth()->id() !== $post->user_id && !$isCoordinator) {
             session()->flash('error', 'Você não tem permissão para editar este post.');
             return;
        }

        $this->editingPostId = $postId;
        $this->editingPostContent = $post->content;
        
        // Carrega as imagens ATUAIS do post para o array de edição
        $this->editingPostCurrentImages = $post->images ?? [];
        $this->editingPostNewImages = []; // Zera as novas imagens de upload
        $this->resetErrorBag();
    }
    
    // Lógica para NOVAS imagens durante a edição
    public function updatedEditingPostNewImages()
    {
        $newFiles = is_array($this->editingPostNewImages) ? $this->editingPostNewImages : [$this->editingPostNewImages];
        $currentCount = count($this->editingPostCurrentImages);
        $totalAllowed = 5;
        $newCount = count($newFiles);

        // Verifica o limite total (atuais + novas)
        if ($newCount + $currentCount > $totalAllowed) {
            // Se o upload exceder o limite, pega apenas o que falta para 5
            $take = $totalAllowed - $currentCount;
            // Pega as 'take' primeiras da lista de uploads
            $this->editingPostNewImages = collect($newFiles)->take($take)->all(); 
            
            // Se `take` for menor que 1, significa que o limite de 5 já foi atingido, 
            // então não deve adicionar nada.
            if ($take < 1) {
                $this->editingPostNewImages = [];
                session()->flash('error', "O post já atingiu o limite de 5 imagens. Remova alguma foto para adicionar uma nova.");
            } else {
                 session()->flash('error', "Você só pode ter um total de 5 imagens. Apenas $take foram adicionadas.");
            }
        }
    }

    // Remove imagem (existente ou nova) durante a edição
    public function removeEditingImage($index, $isNew = false)
    {
        if ($isNew) {
            // Remove do array de novas imagens (UploadedFiles)
            if (isset($this->editingPostNewImages[$index])) {
                unset($this->editingPostNewImages[$index]);
                $this->editingPostNewImages = array_values($this->editingPostNewImages);
            }
        } else {
            // Remove do array de imagens atuais (paths no DB)
            if (isset($this->editingPostCurrentImages[$index])) {
                unset($this->editingPostCurrentImages[$index]);
                $this->editingPostCurrentImages = array_values($this->editingPostCurrentImages);
            }
        }
    }


    public function cancelEdit()
    {
        $this->editingPostId = null;
        $this->editingPostContent = '';
        $this->editingPostCurrentImages = [];
        $this->editingPostNewImages = [];
        $this->resetErrorBag();
    }

    // Atualização de Post
    public function updatePost()
    {
        // Valida as novas imagens e o conteúdo de texto
        $this->validate(); 

        // Checagem Lógica: O post precisa de CONTEÚDO OU IMAGENS
        if (empty(trim($this->editingPostContent)) && empty($this->editingPostCurrentImages) && empty($this->editingPostNewImages)) {
             $this->addError('editingPostContent', 'O post deve ter conteúdo de texto ou pelo menos uma imagem.');
             return; 
        }

        $post = Post::findOrFail($this->editingPostId);
        $isCoordinator = optional(optional($this->course->courseCoordinator)->userAccount)->id === auth()->id();

        if (auth()->id() !== $post->user_id && !$isCoordinator) {
             session()->flash('error', 'Você não tem permissão para atualizar este post.');
             $this->cancelEdit();
             return;
        }

        // 1. Processa e armazena as NOVAS imagens
        $newImagePaths = [];
        if (!empty($this->editingPostNewImages)) {
            foreach ($this->editingPostNewImages as $image) {
                if (method_exists($image, 'store')) {
                    $newImagePaths[] = $image->store('post-images', 'public');
                }
            }
        }

        // 2. Combina as imagens antigas que restaram (após remoção) com as novas
        $finalImages = array_merge($this->editingPostCurrentImages, $newImagePaths);

        // 3. Atualiza o Post
        $post->update([
            // Garante que se o conteúdo for só espaços vazios, seja salvo como null
            'content' => trim($this->editingPostContent) ?: null, 
            'images' => $finalImages,
        ]);

        session()->flash('success', 'Post atualizado com sucesso!');
        $this->cancelEdit();
    }
    
    // --- MÉTODOS DE RESPOSTAS E EXCLUSÃO ---

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

    // Excluir Resposta
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