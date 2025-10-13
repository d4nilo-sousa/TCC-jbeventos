<?php

namespace App\Livewire;

use App\Models\Course;
use App\Models\Post;
use App\Models\Reply;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;

class CoursePosts extends Component
{
    use WithPagination, WithFileUploads; // traits para paginação e upload de arquivos

    protected $paginationTheme = 'tailwind'; // tema do Tailwind para paginação

    // Propriedades de Criação (Post)
    public Course $course;
    public $newPostContent = '';
    public $images = []; // Imagens do POST
    public $newlyUploadedImages = []; // Upload temporário do POST

    // Propriedades de Resposta (Reply)
    public $newReplyContent = [];
    public $newReplyImage = []; // Upload temporário de imagem para a nova resposta

    // PROPRIEDADES DE EDIÇÃO (Post)
    public $editingPostId = null;
    public $editingPostContent = '';
    public $editingPostCurrentImages = []; // Imagens JÁ SALVAS no DB 
    public $editingPostNewImages = []; // Imagens NOVAS enviadas

    // PROPRIEDADES DE EDIÇÃO (Reply)
    public $editingReplyId = null;
    public $editingReplyContent = '';
    public $editingReplyCurrentImage = null; // Caminho no DB da imagem atual
    public $editingReplyNewImage = null; // Upload temporário da nova imagem


    // Método rules() para validação dinâmica
    protected function rules()
    {
        // Define as regras base
        $rules = [
            // Regras de Criação de Post
            'newPostContent' => 'nullable|string|max:500', 
            'images' => 'array|max:5',
            'images.*' => 'image|max:2048', // 2MB

            // Regras de Edição de Post
            'editingPostContent' => 'nullable|string|max:300',
            'editingPostNewImages' => 'array', 
            'editingPostNewImages.*' => 'image|max:2048', 

            // Regras de Criação de Resposta
            'newReplyContent.*' => 'nullable|string|min:2|max:300', // Aumentei o limite
            'newReplyImage.*' => 'nullable|image|max:512', // Max 1 imagem por resposta, 512KB

            // Regras de Edição de Resposta
            'editingReplyContent' => 'nullable|string|max:300',
            'editingReplyNewImage' => 'nullable|image|max:512',
        ];
        
        // Regra de checagem condicional para Edição de Resposta:
        // O conteúdo é obrigatório SE não houver imagem atual ou nova.
        if ($this->editingReplyId) {
            $rules['editingReplyContent'] = 'required_without_all:editingReplyCurrentImage,editingReplyNewImage|string|max:300';
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

    // -------------------------------------------------------------------------
    // --- MÉTODOS DE CRIAÇÃO DE POSTS ---
    // -------------------------------------------------------------------------

    // Lógica para múltiplas imagens no post
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

    // Remove imagem do array de imagens do Post
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

    // -------------------------------------------------------------------------
    // --- MÉTODOS DE EDIÇÃO DE POSTS ---
    // -------------------------------------------------------------------------

    // Inicializa o formulário de edição do Post
    public function startEdit($postId) 
    {
        $post = Post::findOrFail($postId);

        $isCoordinator = optional(optional($this->course->courseCoordinator)->userAccount)->id === auth()->id();
        if (auth()->id() !== $post->user_id && !$isCoordinator) {
             session()->flash('error', 'Você não tem permissão para editar este post.');
             return;
        }
        
        // Cancela a edição de resposta, se estiver ativa
        $this->cancelEditReply(); 

        $this->editingPostId = $postId;
        $this->editingPostContent = $post->content;
        $this->editingPostCurrentImages = $post->images ?? [];
        $this->editingPostNewImages = []; 
        $this->resetErrorBag();
    }
    
    // Lógica para NOVAS imagens durante a edição do Post
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
            
            if ($take < 1) {
                $this->editingPostNewImages = [];
                session()->flash('error', "O post já atingiu o limite de 5 imagens. Remova alguma foto para adicionar uma nova.");
            } else {
                 session()->flash('error', "Você só pode ter um total de 5 imagens. Apenas $take foram adicionadas.");
            }
        }
    }

    // Remove imagem (existente ou nova) durante a edição do Post
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

        // Armazena a lista ORIGINAL de imagens para posterior exclusão
        $originalImages = $post->images ?? [];

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

        // 4. Lógica de Limpeza: Exclui as imagens antigas que NÃO estão mais no array final
        $imagesToDelete = array_diff($originalImages, $this->editingPostCurrentImages);

        foreach ($imagesToDelete as $path) {
            if (Storage::disk('public')->exists($path)) {
                Storage::disk('public')->delete($path);
            }
        }

        session()->flash('success', 'Post atualizado com sucesso!');
        $this->cancelEdit();
    }
    
    // -------------------------------------------------------------------------
    // --- MÉTODOS DE RESPOSTAS (CRIAÇÃO/EDIÇÃO/EXCLUSÃO) ---
    // -------------------------------------------------------------------------

    // Criação de Respostas (MODIFICADO para imagem única)
    public function createReply($postId)
    {
        $contentKey = "newReplyContent.{$postId}";
        $imageKey = "newReplyImage.{$postId}";

        // Valida o conteúdo e a imagem (se houver)
        $this->validate([
            $contentKey => 'required_without:' . $imageKey . '|string|min:2|max:300',
            $imageKey => 'nullable|image|max:512', // 512KB
        ]);
        
        // Checagem Lógica: Precisa de CONTEÚDO OU IMAGEM
        if (empty(trim(Arr::get($this->newReplyContent, $postId))) && empty(Arr::get($this->newReplyImage, $postId))) {
             $this->addError($contentKey, 'A resposta deve ter conteúdo de texto ou uma imagem.');
             return; 
        }

        $imagePath = null;
        $imageFile = Arr::get($this->newReplyImage, $postId);
        
        if ($imageFile) {
            $imagePath = $imageFile->store('reply-images', 'public');
        }

        $post = Post::findOrFail($postId);
        $post->replies()->create([
            'user_id' => auth()->id(),
            'content' => trim(Arr::get($this->newReplyContent, $postId)) ?: null,
            'image' => $imagePath,
        ]);

        // Limpa o formulário
        Arr::forget($this->newReplyContent, $postId); 
        Arr::forget($this->newReplyImage, $postId);
        $this->newReplyImage[$postId] = null; // Garante que o input de file seja resetado
        
        session()->flash('success', 'Resposta enviada com sucesso!');
        $this->dispatch('replyCreated');
    }

    // Inicializa o formulário de edição de Resposta
    public function startEditReply($replyId) 
    {
        $reply = \App\Models\Reply::findOrFail($replyId);

        $isCoordinator = optional(optional($this->course->courseCoordinator)->userAccount)->id === auth()->id();
        if (auth()->id() !== $reply->user_id && !$isCoordinator) {
             session()->flash('error', 'Você não tem permissão para editar esta resposta.');
             return;
        }
        
        // Redefine a edição de post, se estiver ativa
        $this->cancelEdit();
        
        $this->editingReplyId = $replyId;
        $this->editingReplyContent = $reply->content;
        $this->editingReplyCurrentImage = $reply->image; // Caminho do storage
        $this->editingReplyNewImage = null;
        $this->resetErrorBag();
    }
    
    // Cancela a Edição de Resposta
    public function cancelEditReply()
    {
        $this->editingReplyId = null;
        $this->editingReplyContent = '';
        $this->editingReplyCurrentImage = null;
        $this->editingReplyNewImage = null;
        $this->resetErrorBag();
    }
    
    // Remove a Imagem Atual da Resposta durante a edição
    public function removeReplyImage()
    {
        // Apenas remove do array de edição. A exclusão do arquivo será em updateReply.
        $this->editingReplyCurrentImage = null; 
    }

    // Atualiza a Resposta
    public function updateReply()
    {
        $this->validate(); 

        $reply = \App\Models\Reply::findOrFail($this->editingReplyId);
        $isCoordinator = optional(optional($this->course->courseCoordinator)->userAccount)->id === auth()->id();

        if (auth()->id() !== $reply->user_id && !$isCoordinator) {
             session()->flash('error', 'Você não tem permissão para atualizar esta resposta.');
             $this->cancelEditReply();
             return;
        }
        
        // Checagem Lógica: Precisa de CONTEÚDO OU IMAGEM
        if (empty(trim($this->editingReplyContent)) && empty($this->editingReplyCurrentImage) && empty($this->editingReplyNewImage)) {
             $this->addError('editingReplyContent', 'A resposta deve ter conteúdo de texto ou uma imagem.');
             return; 
        }

        $originalImage = $reply->image;
        $newImagePath = $originalImage;

        // 1. Processa nova imagem de upload (se houver)
        if ($this->editingReplyNewImage) {
            // Se houver uma nova imagem, armazena
            $newImagePath = $this->editingReplyNewImage->store('reply-images', 'public');
            
            // Exclui a imagem antiga (se houver)
            if ($originalImage && Storage::disk('public')->exists($originalImage)) {
                Storage::disk('public')->delete($originalImage);
            }
        } 
        // 2. Lógica para exclusão de imagem
        elseif ($originalImage && is_null($this->editingReplyCurrentImage)) {
            // Se a imagem original existia, e a atual foi removida manualmente (editingReplyCurrentImage = null)
            $newImagePath = null;
            if (Storage::disk('public')->exists($originalImage)) {
                Storage::disk('public')->delete($originalImage);
            }
        }
        // 3. Se não houver nova imagem e a imagem atual não foi removida, mantém o caminho original do DB.
        // Se `editingReplyCurrentImage` for nulo, mas o upload de `editingReplyNewImage` também for nulo, `newImagePath` será nulo.

        // 4. Atualiza a Resposta
        $reply->update([
            'content' => trim($this->editingReplyContent) ?: null, 
            'image' => $newImagePath,
        ]);

        session()->flash('success', 'Resposta atualizada com sucesso!');
        $this->cancelEditReply();
    }


    // Excluir Resposta (MODIFICADO para incluir exclusão de imagem)
    public function deleteReply($replyId)
    {
        $reply = \App\Models\Reply::findOrFail($replyId);
        $coordinatorUserId = optional(optional($this->course->courseCoordinator)->userAccount)->id;

        if (auth()->id() === $reply->user_id || auth()->id() === $coordinatorUserId) {
             // Lógica de Limpeza: Exclui a imagem associada à resposta, se houver
            if ($reply->image && Storage::disk('public')->exists($reply->image)) {
                Storage::disk('public')->delete($reply->image);
            }
            
            $reply->delete();
            session()->flash('success', 'Resposta excluída com sucesso.');
            $this->dispatch('replyDeleted');
        } else {
            session()->flash('error', 'Você não tem permissão para excluir esta resposta.');
        }
    }

    // Excluir Post (MODIFICADO para incluir exclusão de todas as imagens do post E das respostas)
    public function deletePost($postId)
    {
        $post = Post::findOrFail($postId);
        $coordinatorUserId = optional(optional($this->course->courseCoordinator)->userAccount)->id;

        if (auth()->id() === $post->user_id || auth()->id() === $coordinatorUserId) {
            
            // Lógica de Limpeza: Exclui as imagens do post
            if (!empty($post->images)) {
                foreach ($post->images as $path) {
                    if (Storage::disk('public')->exists($path)) {
                        Storage::disk('public')->delete($path);
                    }
                }
            }
            
            // Lógica de Limpeza: Exclui as imagens das respostas
            foreach ($post->replies as $reply) {
                 if ($reply->image && Storage::disk('public')->exists($reply->image)) {
                    Storage::disk('public')->delete($reply->image);
                }
            }
            
            $post->delete();
            session()->flash('success', 'Post excluído com sucesso.');
            $this->resetPage();
            $this->dispatch('postDeleted');
        } else {
            session()->flash('error', 'Você não tem permissão para excluir este post.');
        }
    }
}