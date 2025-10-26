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

    public function getListeners()
    {
        $userId = Auth::id();

        return [
            "echo:private-user.{$userId},MessageSent" => 'refreshListOnNewMessage',
            'refreshConversationList' => '$refresh',
        ];
    }

    public function refreshListOnNewMessage($event)
    {
        $this->mount(); // Recarrega a lista de conversas
    }


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
            ->map(function ($msgs, $otherUserId) use ($userId) {
                $lastMessage = $msgs->first();
                $otherUser = User::find($otherUserId);

                $unreadCount = $msgs->where('receiver_id', $userId)
                                    ->where('is_read', false)
                                    ->count();

                return [
                    'user' => $otherUser,
                    'last_message' => $lastMessage->message,
                    'last_message_time' => $lastMessage->created_at->diffForHumans(),
                    'unread_count' => $unreadCount,
                ];
            })
            ->values(); // resetar índices
    }

    public function render()
    {
        return view('livewire.conversation-list');
    }
}
