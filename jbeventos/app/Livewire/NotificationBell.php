<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Illuminate\Notifications\DatabaseNotification; 
use Illuminate\Support\Facades\DB;

class NotificationBell extends Component
{
    public $unreadCount;
    public $notifications;

    // Função auxiliar para obter a contagem SEMPRE do banco (ignora cache)
    private function getDbUnreadCount()
    {
        // 🛑 NOVO: Consulta direta ao relacionamento, forçando o DB
        return Auth::user()->notifications()->whereNull('read_at')->count();
    }
    
    // Função auxiliar para recarregar a lista (sempre do DB)
    private function getDbNotifications()
    {
        return Auth::user()->notifications->take(5);
    }
    
    // Método para disparar o evento para o Alpine
    private function dispatchUpdate()
    {
         $this->dispatch('notificationsUpdated', count: $this->unreadCount);
    }

    public function getListeners()
    {
        $userId = Auth::id();

        return [
            "echo-private:users.{$userId},Illuminate\\Notifications\\Events\\BroadcastNotificationCreated" 
                 => 'broadcastUpdate',
            'refreshBell' => '$refresh',
        ];
    }

    public function mount()
    {
        $user = Auth::user();
        if ($user) {
            // Usa a função auxiliar para garantir a contagem correta
            $this->unreadCount = $this->getDbUnreadCount();
            $this->notifications = $this->getDbNotifications();
        }
    }

    public function broadcastUpdate($event)
    {
        // Recalcula o estado com a nova contagem
        $this->unreadCount = $this->getDbUnreadCount();
        $this->notifications = $this->getDbNotifications();
        
        $this->dispatchUpdate();
    }

    /**
     * ✅ Marcar todas como lidas ao abrir o sino
     */
    public function markAsRead()
    {
        $user = Auth::user();
        $userId = $user->id;
        
        // 1. Marca todas como lidas diretamente no DB
        DB::table('notifications')
            ->where('notifiable_type', get_class($user)) // Garante que é o modelo User
            ->where('notifiable_id', $userId)
            ->whereNull('read_at')
            ->update(['read_at' => now()]); // Força o timestamp de leitura no DB

        // 2. Recalcula o estado com a nova contagem (que deve ser 0 agora)
        $this->unreadCount = $this->getDbUnreadCount(); 
        $this->notifications = $this->getDbNotifications();
        
        $this->dispatchUpdate(); 
    }

    /**
     * ✅ Marcar uma específica como lida e redirecionar
     */
    public function markOneAsRead($id, $url = '#')
    {
        $user = Auth::user();
        $notification = $user->notifications()->where('id', $id)->first();

        if ($notification) {
            $notification->markAsRead();
        }
        
        // Recalcula o estado após a marcação (Contagem e Lista)
        $this->unreadCount = $this->getDbUnreadCount(); 
        $this->notifications = $this->getDbNotifications();
        
        $this->dispatchUpdate();
        
        // Redireciona via Livewire
        if ($url !== '#') {
            $this->dispatch('navigateToUrl', url: $url);
        }
    }

    /**
     * ✅ Fallback para Polling
     */
    public function refreshUnreadCount()
    {
        $newCount = $this->getDbUnreadCount();
        
        if ($this->unreadCount !== $newCount) {
            $this->unreadCount = $newCount;
            $this->notifications = $this->getDbNotifications();
            $this->dispatchUpdate();
        }
    }

    public function render()
    {
        return view('livewire.notification-bell');
    }
}