<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class NotificationBell extends Component
{
    // A propriedade que armazena a contagem de notificações não lidas
    public $unreadCount;
    // A propriedade que armazena as últimas notificações
    public $notifications;

    // Propriedade para definir os listeners de Broadcast (WebSockets)
    protected $listeners = [];

    // Método de inicialização para carregar os dados
    public function mount()
    {
        $user = Auth::user();

        // 1. Configura o Listener de Broadcast
        if ($user) {
            $this->listeners = [
                'echo-private:users.' . $user->id . ',.Illuminate\\Notifications\\Events\\BroadcastNotificationCreated' => 'broadcastUpdate',
            ];
        }
        
        // 2. Carrega as notificações
        $this->unreadCount = $user->unreadNotifications->count();
        $this->notifications = $user->notifications->take(5); // Últimas 5 notificações
    }

    // Método para lidar com a atualização em tempo real via Broadcast
    public function broadcastUpdate($event)
    {
        // $event contém o payload que definimos em toBroadcast()
        
        // Recarrega as notificações e a contagem. 
        $user = Auth::user();
        $this->unreadCount = $user->unreadNotifications->count();
        $this->notifications = $user->notifications->take(5);
    }

    // Marca todas as notificações como lidas quando o sino é aberto
    public function markAsRead()
    {
        Auth::user()->unreadNotifications->markAsRead();
        $this->unreadCount = 0; // Zera o contador
        $this->notifications = Auth::user()->notifications->take(5); // Recarrega para mostrar 'lidas'
    }

    // Marca uma notificação específica como lida e redireciona
    public function markOneAsRead($id, $url = '#')
    {
        $notification = Auth::user()->notifications()->where('id', $id)->first();
        if ($notification) {
            $notification->markAsRead();
        }
        return redirect()->to($url);
    }
    
    // Para polling (atualização a cada 5 segundos) - Mantido como fallback
    public function refreshUnreadCount()
    {
        $this->unreadCount = Auth::user()->unreadNotifications->count();
    }


    public function render()
    {
        return view('livewire.notification-bell');
    }
}