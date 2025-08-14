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

    protected $listeners = ['messageReceived' => 'addMessage'];

    public function mount(User $otherUser)
    {
        $this->otherUser = $otherUser;

        // Carregar histórico de mensagens entre os dois usuários
        $this->messages = Message::where(function($query) {
            $query->where('sender_id', auth()->id())
                  ->where('receiver_id', $this->otherUser->id);
        })->orWhere(function($query) {
            $query->where('receiver_id', auth()->id())
                  ->where('sender_id', $this->otherUser->id);
        })->orderBy('created_at')->get()->map(function($msg) {
            return [
                'user_id' => $msg->sender->id,
                'user_name' => $msg->sender->name,
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

        // Salvar mensagem no banco
        $msg = Message::create([
            'sender_id' => $user->id,
            'receiver_id' => $this->otherUser->id,
            'message' => $this->message,
        ]);

        // Broadcast para Pusher
        event(new MessageSent($user, $this->message, $this->otherUser->id));

        // Adicionar a mensagem localmente sem recarregar
        $this->addMessage([
            'user_id' => $user->id,
            'user_name' => $user->name,
            'message' => $this->message,
        ]);

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
