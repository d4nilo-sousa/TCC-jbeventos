<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use App\Models\Message;
use App\Models\User;
use Illuminate\Support\Collection;

class ConversationList extends Component
{
    public Collection $conversations;

    public function mount()
    {
        $userId = Auth::id();

        // Buscar todas as conversas (última mensagem com cada usuário)
        $this->conversations = Message::where('sender_id', $userId)
            ->orWhere('receiver_id', $userId)
            ->orderBy('created_at', 'desc')
            ->get()
            ->groupBy(function ($msg) use ($userId) {
                // Agrupar pelo "outro" usuário
                return $msg->sender_id === $userId ? $msg->receiver_id : $msg->sender_id;
            })
            ->map(function ($msgs, $otherUserId) {
                $lastMessage = $msgs->first();
                $otherUser = User::find($otherUserId);

                return [
                    'user' => $otherUser,
                    'last_message' => $lastMessage->message,
                    'last_message_time' => $lastMessage->created_at->diffForHumans()
                ];
            })
            ->values(); // resetar índices
    }

    public function render()
    {
        return view('livewire.conversation-list');
    }
}
