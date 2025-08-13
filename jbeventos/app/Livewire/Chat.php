<?php

namespace App\Livewire;

use Livewire\Component;
use App\Events\MessageSent;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use function event;
use App\Models\Message;

class Chat extends Component
{
    public User $otherUser;
    public $message = '';
    public $messages = [];

    protected $listeners = ['messageReceived' => 'addMessage'];

    public function mount(User $otherUser)
    {
        $this->otherUser = $otherUser;

         $this->messages = Message::where(function($query) {
        $query->where('sender_id', auth()->id())
              ->where('receiver_id', $this->otherUser->id);
    })->orWhere(function($query) {
        $query->where('receiver_id', auth()->id())
              ->where('sender_id', $this->otherUser->id);
    })->orderBy('created_at')->get()->map(function($msg) {
        return [
            'user' => $msg->sender->name,
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

        //salvar no banco
        Message::create([
            'sender_id' => $user->id,
            'receiver_id' => $this->otherUser->id,
            'message' => $this->message,
        ]);

        event(new MessageSent($user, $this->message, $this->otherUser->id));

        $this->message = '';
    }

    public function addMessage($messageData)
    {
        $this->messages[] = $messageData;
    }

    public function render()
    {
        return view('livewire.chat');
    }
}
