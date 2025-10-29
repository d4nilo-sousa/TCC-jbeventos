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
    // 💡 NOVO: Propriedade para guardar o ID do usuário do chat ativo.
    // O valor será passado do Blade pai ou da rota.
    public $activeChatUserId = null; 

    public function getListeners()
    {
        $userId = Auth::id();

        return [
            // ... (restante dos listeners)
            "echo:private-user.{$userId},MessageSent" => 'refreshListOnNewMessage',
            'refreshConversationList' => '$refresh',
        ];
    }

    public function refreshListOnNewMessage($event)
    {
        // 💡 Ajuste: Usar $this->mount() pode ser custoso. Se o objetivo é 
        // apenas atualizar a lista na tela, $this->dispatch('$refresh') já faz isso.
        // Se a lógica do mount for necessária para reordenar, mantenha:
        $this->mount(); 
    }

    // Nenhuma alteração é necessária em mount(), pois a lógica de unread_count 
    // está correta ao buscar a contagem REAL do banco.
    // A simulação de "lido" se dará APENAS no Blade (view).

    public function mount($activeChatUserId = null) // 💡 NOVO: Recebe o ID ativo
    {
        $this->activeChatUserId = $activeChatUserId; // Define a propriedade
        $userId = Auth::id();

        // Buscar todas as mensagens enviadas ou recebidas
        $messages = Message::where('sender_id', $userId)
            ->orWhere('receiver_id', $userId)
            ->orderBy('created_at', 'desc')
            ->get();

        // Agrupar por "outro usuário" para simular conversas
        $this->conversations = $messages
            ->groupBy(function ($msg) use ($userId) {
                return $msg->sender_id === $userId ? $msg->receiver_id : $msg->sender_id;
            })
            ->map(function ($msgs, $otherUserId) use ($userId) {
                $lastMessage = $msgs->first();
                // 💡 Melhoria: Use Eager Loading se possível, ou faça o find fora do loop.
                // Mas, para o exemplo, mantemos o find.
                $otherUser = User::find($otherUserId); 

                // Contagem de mensagens não lidas
                $unreadCount = $msgs->where('receiver_id', $userId)
                    ->where('is_read', false)
                    ->count();
                // ... (restante da lógica de detecção de última mensagem)
                
                $lastMessageText = '';

                if ($lastMessage->attachment_path) {
                    $mime = $lastMessage->attachment_mime;

                    if (str_contains($mime, 'gif')) {
                        $lastMessageText = '🎞️ GIF enviado';
                    } elseif (str_starts_with($mime, 'image')) {
                        $lastMessageText = '📷 Imagem enviada';
                    } elseif (str_starts_with($mime, 'video')) {
                        $lastMessageText = '🎬 Vídeo enviado';
                    } elseif (preg_match('/pdf|word|officedocument|text|zip/', $mime)) {
                        $lastMessageText = '📄 Documento enviado';
                    } else {
                        $lastMessageText = '📎 Arquivo enviado';
                    }
                } else {
                    $lastMessageText = $lastMessage->message;
                }
                
                return [
                    'user' => $otherUser,
                    'last_message' => $lastMessageText,
                    'last_message_time' => $lastMessage->created_at->diffForHumans(),
                    'unread_count' => $unreadCount,
                    'attachment_path' => $lastMessage->attachment_path,
                    'attachment_mime' => $lastMessage->attachment_mime,
                ];
            })
            ->values();
    }

    public function render()
    {
        return view('livewire.conversation-list');
    }
}