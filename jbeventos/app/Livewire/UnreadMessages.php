<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Message;
use Illuminate\Support\Facades\Auth;

class UnreadMessages extends Component
{
    public $unreadCount = 0;

    public function mount()
    {
        $this->updateUnreadCount();
    }
    
    // CORREÇÃO: Altere a sintaxe para escutar eventos globais do Livewire 3
    protected function getListeners()
    {
        return [
            'echo-private:user.'.Auth::id().',.MessageSent' => 'updateUnreadCount',
            'messageRead' => 'updateUnreadCount',
            'messageReceived' => 'updateUnreadCount',
        ];
    }
    
    public function updateUnreadCount()
    {
        $this->unreadCount = Message::where('receiver_id', Auth::id())
                                     ->where('is_read', false)
                                     ->count();
    }

    public function render()
    {
        return view('livewire.unread-messages');
    }
}