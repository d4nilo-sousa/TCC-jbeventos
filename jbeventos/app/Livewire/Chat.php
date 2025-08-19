<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Events\MessageSent;
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
    public $attachment;

    protected $listeners = ['messageReceived' => 'addMessage'];

    public function mount(User $otherUser)
    {
        $this->otherUser = $otherUser;
        $this->loadMessages();
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
            ];
        })->toArray();
    }

    public function sendMessage()
    {
        $this->validate([
            'message' => 'nullable|string|max:255',
            'attachment' => 'nullable|file|max:10240|mimes:jpg,jpeg,png,gif,mp4,mov,avi,doc,docx,pdf,txt',
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

        event(new MessageSent($user, $this->message, $this->otherUser->id, $attachmentPath, $attachmentMime, $attachmentName));

        $this->addMessage([
            'id' => $msg->id,
            'sender_id' => $user->id,
            'message' => $this->message,
            'attachment_path' => $attachmentPath,
            'attachment_mime' => $attachmentMime,
            'attachment_name' => $attachmentName,
        ]);

        $this->message = '';
        $this->attachment = null;
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

    public function deleteSelectedMessage()
    {
        $message = Message::find($this->selectedMessage);

        if ($message && $message->sender_id == auth()->id()) {
            $message->delete();
        }

        $this->selectedMessage = null;
        $this->showDeleteModal = false;
        $this->loadMessages();
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