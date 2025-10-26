<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Message;
use Illuminate\Support\Facades\Auth;

class UnreadMessages extends Component
{
    public $unreadCount = 0;

    protected $listeners = [
        'refreshUnread' => 'getUnreadCount', // Evento para forçar a atualização (se necessário)
        'echo:private-user.{userId},MessageSent' => 'handleNewMessage', // Listener para mensagens recebidas
        'messageRead', // Listener chamado pelo Chat.php para decrementar a contagem
    ];

    public function mount()
    {
        $this->getUnreadCount();
    }

    // O Livewire preenche automaticamente {userId} com o ID do usuário autenticado
    public function getListeners()
    {
        return [
            'refreshUnread' => 'getUnreadCount',
            "echo:private-user." . Auth::id() . ",MessageSent" => 'handleNewMessage',
            'messageRead',
        ];
    }
    
    /**
     * Calcula o total de mensagens não lidas para o usuário logado.
     */
    public function getUnreadCount()
    {
        $this->unreadCount = Message::where('receiver_id', Auth::id())
                                    ->where('is_read', false)
                                    ->count();
    }

    /**
     * Incrementa a contagem quando uma nova mensagem é recebida via Broadcast.
     * @param array $event
     */
    public function handleNewMessage(array $event)
    {
        // Garante que a mensagem seja realmente para o usuário logado e não lida
        if ($event['receiver_id'] === Auth::id()) {
            $this->unreadCount++;
            $this->dispatch('refreshConversationList'); // Opcional: para atualizar a lista de conversas
        }
    }

    /**
     * Diminui a contagem quando o usuário entra no chat e marca as mensagens como lidas.
     */
    public function messageRead()
    {
        $this->getUnreadCount(); // Recalcula para garantir a precisão
    }

    public function render()
    {
        return view('livewire.unread-messages');
    }
}