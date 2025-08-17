<?php

namespace App\Livewire;

use Livewire\Component;
use App\Events\MessageSent;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Message;

class Chat extends Component
{
    public User $otherUser;
    public $message = '';
    public $messages = [];
    public $selectedMessage = null;
    public $showDeleteModal = false;

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
            ];
        })->toArray();
    }

    public function sendMessage()
    {
        $this->validate([
            'message' => 'required|string|max:255',
        ]);

        $user = Auth::user();

        $msg = Message::create([
            'sender_id' => $user->id,
            'receiver_id' => $this->otherUser->id,
            'message' => $this->message,
        ]);

        event(new MessageSent($user, $this->message, $this->otherUser->id));

        $this->addMessage([
            'id' => $msg->id,
            'sender_id' => $user->id,
            'message' => $this->message,
        ]);

        $this->message = '';
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
    
    public function copyMessage($message)
    {
        $this->dispatch('copy-message', message: $message);
        $this->clearSelection();
    }


    public function render()
    {
        return view('livewire.chat', ['receiver' => $this->otherUser]);
    }
}
