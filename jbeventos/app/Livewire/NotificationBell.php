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
        return Auth::user()->notifications()
            ->whereNull('read_at')
            ->latest()   // opcional, para trazer as mais recentes primeiro
            ->take(5)
            ->get();
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

        $user->unreadNotifications()->update(['read_at' => now()]);

        // Atualiza contagem e lista de notificações
        $this->unreadCount = $this->getDbUnreadCount();
        $this->notifications = $this->getDbNotifications();

        // Se não houver notificações não lidas, força lista vazia
        if ($this->unreadCount === 0) {
            $this->notifications = collect([]);
        }

        $this->dispatchUpdate();
    }

    /**
     * Marca UMA notificação como lida e redireciona, se necessário
     */
    public function markOneAsRead($id) // Remova o parâmetro $url
    {
        $user = Auth::user();
        // Encontra a notificação
        $notification = $user->notifications()->find($id);

        // Inicializa a URL para ser segura
        $url = '#';

        if ($notification) {
            // Tenta buscar a URL dos dados, garantindo um valor seguro como fallback
            $url = data_get($notification->data, 'event_url', '#');

            if (is_null($notification->read_at)) {
                $notification->markAsRead();
            }
        }

        $this->unreadCount = $this->getDbUnreadCount();
        $this->notifications = $this->getDbNotifications();
        $this->dispatchUpdate();

        // Redireciona apenas se houver URL válida (diferente de #)
        if ($url && $url !== '#') {
            $this->dispatch('navigateToUrl', ['url' => $url]);
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
