<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Events\MessageSent;
use App\Events\MessageEdited; // NOVO: Evento de Edição
use App\Events\MessageDeleted; // NOVO: Evento de Exclusão
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Message;

class Chat extends Component
{
    use WithFileUploads;

    public User $otherUser;
    public $message = '';
    public $messages = [];
    public $selectedMessage = null;
    public $showDeleteModal = false;
    public $showEditInput = false; // NOVO: Controla a exibição do input de edição
    public $editingMessageId = null; // NOVO: ID da mensagem que está sendo editada
    public $editedMessageContent = ''; // NOVO: Conteúdo da mensagem que está sendo editada
    public $attachment;
    public $isOnline = false;

    public function mount(User $otherUser)
    {
        $this->otherUser = $otherUser;
        $this->loadMessages();
    }
    
    public function getListeners()
    {
        $ids = [auth()->id(), $this->otherUser->id];
        sort($ids);
        $channelName = 'chat.' . implode('.', $ids);

        return [
            "echo-presence:{$channelName},here" => 'here',
            "echo-presence:{$channelName},joining" => 'joining',
            "echo-presence:{$channelName},leaving" => 'leaving',
            "echo-presence:{$channelName},MessageSent" => 'addMessageFromBroadcast',
            "echo-presence:{$channelName},MessageEdited" => 'updateMessageFromBroadcast', // NOVO: Listener para edição
            "echo-presence:{$channelName},MessageDeleted" => 'removeMessageFromBroadcast', // NOVO: Listener para exclusão
        ];
    }

    public function here($users)
    {
        foreach ($users as $user) {
            if ($user['id'] === $this->otherUser->id) {
                $this->isOnline = true;
                return;
            }
        }
        $this->isOnline = false;
    }
    
    public function joining($user)
    {
        if ($user['id'] === $this->otherUser->id) {
            $this->isOnline = true;
        }
    }

    public function leaving($user)
    {
        if ($user['id'] === $this->otherUser->id) {
            $this->isOnline = false;
        }
    }

    public function loadMessages()
    {
        $this->messages = Message::where(function($query) {
            $query->where('sender_id', auth()->id())
                  ->where('receiver_id', $this->otherUser->id);
        })->orWhere(function($query) {
            $query->where('receiver_id', auth()->id())
                  ->where('sender_id', $this->otherUser->id);
        })->orderBy('created_at')->get()->map(function($msg) {
            return [
                'id' => $msg->id,
                'sender_id' => $msg->sender_id,
                'message' => $msg->message,
                'attachment_path' => $msg->attachment_path,
                'attachment_mime' => $msg->attachment_mime,
                'attachment_name' => $msg->attachment_name,
                'created_at' => $msg->created_at->format('H:i')
            ];
        })->toArray();
    }

    public function sendMessage()
    {
        $this->validate([
            'message' => 'nullable|string|max:255',
            'attachment' => 'nullable|file|max:10240|mimes:jpg,jpeg,png,gif,webp,mp4,mov,avi,doc,docx,pdf,txt,zip',
        ]);

        if (empty($this->message) && !$this->attachment) {
            return;
        }

        $user = Auth::user();

        $attachmentPath = null;
        $attachmentMime = null;
        $attachmentName = null;

        if ($this->attachment) {
            $attachmentPath = $this->attachment->store('attachments', 'public');
            $attachmentMime = $this->attachment->getMimeType();
            $attachmentName = $this->attachment->getClientOriginalName();
        }

        $msg = Message::create([
            'sender_id' => $user->id,
            'receiver_id' => $this->otherUser->id,
            'message' => $this->message,
            'attachment_path' => $attachmentPath,
            'attachment_mime' => $attachmentMime,
            'attachment_name' => $attachmentName,
        ]);
        
        event(new MessageSent($msg));

        $this->addMessage([
            'id' => $msg->id,
            'sender_id' => $user->id,
            'message' => $this->message,
            'attachment_path' => $attachmentPath,
            'attachment_mime' => $attachmentMime,
            'attachment_name' => $attachmentName,
            'created_at' => $msg->created_at->format('H:i')
        ]);

        $this->message = '';
        $this->attachment = null;
    }
    
    // NOVO MÉTODO: Inicia o modo de edição
    public function startEditing($id)
    {
        $messageToEdit = collect($this->messages)->firstWhere('id', $id);
        
        if ($messageToEdit && $messageToEdit['sender_id'] === auth()->id()) {
            $this->editingMessageId = $id;
            $this->editedMessageContent = $messageToEdit['message'];
            $this->showEditInput = true;
            $this->selectedMessage = null; // Fecha o menu de opções
        }
    }
    
    // NOVO MÉTODO: Salva a mensagem editada
    public function saveEditedMessage()
    {
        $this->validate([
            'editedMessageContent' => 'required|string|max:255',
        ]);
        
        $message = Message::find($this->editingMessageId);
        
        if ($message && $message->sender_id === auth()->id()) {
            $message->update(['message' => $this->editedMessageContent]);
            event(new MessageEdited($message));
            
            // Atualiza a visualização local para o usuário que editou
            $this->updateMessageFromBroadcast($message->toArray());
        }
        
        $this->editingMessageId = null;
        $this->editedMessageContent = '';
        $this->showEditInput = false;
    }

    // NOVO MÉTODO: Lida com a edição recebida por broadcast
    public function updateMessageFromBroadcast($messageData)
    {
        $index = collect($this->messages)->search(function($msg) use ($messageData) {
            return $msg['id'] === $messageData['id'];
        });
        
        if ($index !== false) {
            $this->messages[$index]['message'] = $messageData['message'];
        }
    }

    // MÉTODO AJUSTADO: Agora dispara um evento de broadcast
    public function deleteSelectedMessage()
    {
        $message = Message::find($this->selectedMessage);

        if ($message && $message->sender_id == auth()->id()) {
            $id = $message->id;
            $message->delete();
            event(new MessageDeleted($id, $message->sender_id, $message->receiver_id));
            
            // Lida com a exclusão na visualização local
            $this->removeMessageFromBroadcast(['id' => $id]);
        }

        $this->selectedMessage = null;
        $this->showDeleteModal = false;
    }
    
    // NOVO MÉTODO: Lida com a exclusão recebida por broadcast
    public function removeMessageFromBroadcast($messageData)
    {
        $this->messages = collect($this->messages)->filter(function($msg) use ($messageData) {
            return $msg['id'] !== $messageData['id'];
        })->values()->toArray();
    }

    public function addMessageFromBroadcast($messageData)
    {
        if ($messageData['sender_id'] === $this->otherUser->id) {
            $this->addMessage([
                'id' => $messageData['id'],
                'sender_id' => $messageData['sender_id'],
                'message' => $messageData['message'],
                'attachment_path' => $messageData['attachment_path'],
                'attachment_mime' => $messageData['attachment_mime'],
                'attachment_name' => $messageData['attachment_name'],
                'created_at' => now()->format('H:i')
            ]);
        }
    }

    public function addMessage($messageData)
    {
        $this->messages[] = $messageData;
    }

    public function selectMessage($id)
    {
        $this->selectedMessage = $id;
    }

    public function clearSelection()
    {
        $this->selectedMessage = null;
    }

    public function confirmDelete()
    {
        $this->showDeleteModal = true;
    }

    public function cancelDelete()
    {
        $this->showDeleteModal = false;
    }

    public function copyMessage($id)
    {
        $message = Message::find($id);

        if ($message) {
            $this->dispatch('copy-message', message: $message->message);
        }
        $this->clearSelection();
    }

    public function render()
    {
        return view('livewire.chat', ['receiver' => $this->otherUser]);
    }
}