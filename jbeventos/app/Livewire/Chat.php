<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Events\MessageSent;
use App\Events\MessageEdited;
use App\Events\MessageDeleted;
use App\Events\UserIsTyping; // Importe o evento de digitação
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
    public $showEditInput = false;
    public $editingMessageId = null;
    public $editedMessageContent = '';
    public $attachment;
    public $isOnline = false;
    public $isTyping = false; // Propriedade para rastrear o status de digitação do outro usuário

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
            "echo-presence:{$channelName},MessageEdited" => 'updateMessageFromBroadcast',
            "echo-presence:{$channelName},MessageDeleted" => 'removeMessageFromBroadcast',
            "echo-presence:{$channelName},UserIsTyping" => 'handleUserIsTyping',
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

    public function handleUserIsTyping($event)
    {
        if ($event['user_id'] === $this->otherUser->id) {
            $this->isTyping = $event['isTyping'];
        }
    }

    public function typing()
    {
        broadcast(new UserIsTyping(auth()->user()->id, $this->otherUser->id, true));
    }

    public function stopTyping()
    {
        broadcast(new UserIsTyping(auth()->user()->id, $this->otherUser->id, false));
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
                'created_at' => $msg->created_at->format('H:i'),
                'is_edited' => $msg->is_edited ?? false,
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
        $this->stopTyping(); // Garante que o status de digitação seja removido ao enviar a mensagem
    }
    
    public function startEditing($id)
    {
        $messageToEdit = collect($this->messages)->firstWhere('id', $id);
        
        if ($messageToEdit && $messageToEdit['sender_id'] === auth()->id()) {
            $this->editingMessageId = $id;
            $this->editedMessageContent = $messageToEdit['message'];
            $this->showEditInput = true;
            $this->selectedMessage = null;
        }
    }
    
    public function saveEditedMessage()
    {
        $this->validate([
            'editedMessageContent' => 'required|string|max:255',
        ]);
        
        $message = Message::find($this->editingMessageId);
        
        if ($message && $message->sender_id === auth()->id()) {
            $message->update(['message' => $this->editedMessageContent]);
            event(new MessageEdited($message));
            
            $messageArray = $message->toArray();
            $messageArray['is_edited'] = true;
            $this->updateMessageFromBroadcast($messageArray);
        }
        
        $this->editingMessageId = null;
        $this->editedMessageContent = '';
        $this->showEditInput = false;
    }

    public function updateMessageFromBroadcast($messageData)
    {
        $index = collect($this->messages)->search(function($msg) use ($messageData) {
            return $msg['id'] === $messageData['id'];
        });
        
        if ($index !== false) {
            $this->messages[$index]['message'] = $messageData['message'];
            $this->messages[$index]['is_edited'] = $messageData['is_edited'] ?? false;
        }
    }

    public function deleteSelectedMessage()
    {
        $message = Message::find($this->selectedMessage);

        if ($message && $message->sender_id == auth()->id()) {
            $id = $message->id;
            $message->delete();
            event(new MessageDeleted($id, $message->sender_id, $message->receiver_id));
            
            $this->removeMessageFromBroadcast(['id' => $id]);
        }

        $this->selectedMessage = null;
        $this->showDeleteModal = false;
    }
    
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
            $this->stopTyping(); // Garante que o status de digitação pare quando a mensagem chegar
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