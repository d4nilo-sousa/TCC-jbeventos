<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Events\MessageSent;
use App\Events\MessageEdited;
use App\Events\MessageDeleted;
use App\Events\MessageRead;
use App\Events\UserIsTyping;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Message;

class Chat extends Component
{
    use WithFileUploads;

    protected $listeners = ['messageReadUpdated' => '$refresh'];

    public User $otherUser;
    public $message = '';
    public $messages = [];
    public $selectedMessage = null;
    public $showDeleteModal = false;
    public $showEditInput = false;
    public $editingMessageId = null;
    public $editedMessageContent = '';
    public $attachment;
    public $isOnline = false;
    public $isTyping = false;
    public $systemOnlineUserIds = [];

    public function mount(User $otherUser)
    {
        $this->otherUser = $otherUser;
        $this->loadMessages();
        $this->markMessagesAsRead();
    }

    public function getListeners()
    {
        $ids = [auth()->id(), $this->otherUser->id];
        sort($ids);
        $channelName = 'chat.' . implode('.', $ids);

        return [
            "echo-presence:{$channelName},MessageSent" => 'addMessageFromBroadcast',
            "echo-presence:{$channelName},MessageEdited" => 'updateMessageFromBroadcast',
            "echo-presence:{$channelName},MessageDeleted" => 'removeMessageFromBroadcast',
            "echo-presence:{$channelName},MessageRead" => 'handleMessageRead',
            "echo-presence:{$channelName},UserIsTyping" => 'handleUserIsTyping',

            "echo-presence:online-users,here" => 'hereGlobal',
            "echo-presence:online-users,joining" => 'joiningGlobal',
            "echo-presence:online-users,leaving" => 'leavingGlobal',
        ];
    }

    // --------------------
    // Status online global
    // --------------------
    public function hereGlobal($users)
    {
        $this->systemOnlineUserIds = collect($users)->pluck('id')->toArray();
        $this->updateOnlineStatus();
    }

    public function joiningGlobal($user)
    {
        $this->systemOnlineUserIds[] = $user['id'];
        $this->updateOnlineStatus();
    }

    public function leavingGlobal($user)
    {
        $this->systemOnlineUserIds = array_filter($this->systemOnlineUserIds, fn($id) => $id !== $user['id']);
        $this->updateOnlineStatus();
    }

    public function updateOnlineStatus()
    {
        $this->isOnline = in_array($this->otherUser->id, $this->systemOnlineUserIds);
    }

    // --------------------
    // Digitação
    // --------------------
    public function handleUserIsTyping($event)
    {
        if ($event['user_id'] === $this->otherUser->id) {
            $this->isTyping = $event['isTyping'];
        }
    }

    public function typing()
    {
        // Certifica-se de que o estado é enviado
        broadcast(new UserIsTyping(auth()->user()->id, $this->otherUser->id, true))->toOthers();
    }

    public function stopTyping()
    {
        // Certifica-se de que o estado é enviado
        broadcast(new UserIsTyping(auth()->user()->id, $this->otherUser->id, false))->toOthers();
    }

    // --------------------
    // Mensagens
    // --------------------
    public function loadMessages()
    {
        $this->messages = Message::where(function ($query) {
            $query->where('sender_id', auth()->id())
                ->where('receiver_id', $this->otherUser->id);
        })->orWhere(function ($query) {
            $query->where('receiver_id', auth()->id())
                ->where('sender_id', $this->otherUser->id);
        })->orderBy('created_at')->get()->map(function ($msg) {
            return [
                'id' => $msg->id,
                'sender_id' => $msg->sender_id,
                'message' => $msg->message,
                'attachment_path' => $msg->attachment_path,
                'attachment_mime' => $msg->attachment_mime,
                'attachment_name' => $msg->attachment_name,
                'created_at' => $msg->created_at->format('H:i'),
                'is_edited' => $msg->is_edited ?? false,
                // O coalesce '?? false' é importante aqui
                'is_read' => $msg->is_read ?? false,
            ];
        })->toArray();
    }

    public function markMessagesAsRead()
    {
        $messagesToUpdate = Message::where('receiver_id', Auth::id())
            ->where('sender_id', $this->otherUser->id)
            ->where('is_read', false)
            ->get();

        $updatedIds = [];

        foreach ($messagesToUpdate as $msg) {
            $msg->update(['is_read' => true]);
            $updatedIds[] = $msg->id;

            // 🔊 Dispara broadcast para o remetente (outra pessoa)
            broadcast(new MessageRead($msg->id, $msg->sender_id, $msg->receiver_id))->toOthers();
        }

        // Atualiza a propriedade $this->messages na tela local
        if (!empty($updatedIds)) {
            $this->messages = collect($this->messages)->map(function ($m) use ($updatedIds) {
                if (in_array($m['id'], $updatedIds)) {
                    $m['is_read'] = true;
                }
                return $m;
            })->toArray();
        }

        // 🔁 Atualiza componentes locais (como contador de não lidas)
        $this->dispatch('messageRead')->to('unread-messages');

        // 🔔 Atualiza Livewire da própria página
        $this->dispatch('messageReadUpdated');
    }
    
    public function sendMessage()
    {
        $this->validate([
            'message' => 'nullable|string|max:255',
            'attachment' => 'nullable|file|max:10240|mimes:jpg,jpeg,png,gif,webp,mp4,mov,avi,doc,docx,pdf,txt,zip',
        ]);

        if (empty($this->message) && !$this->attachment) return;

        $user = auth()->user();
        $attachmentPath = $attachmentMime = $attachmentName = null;

        if ($this->attachment) {
            $attachmentPath = $this->attachment->store('attachments', 'public');
            $attachmentMime = $this->attachment->getMimeType();
            $attachmentName = $this->attachment->getClientOriginalName();
        }

        $msg = Message::create([
            'sender_id' => $user->id,
            'receiver_id' => $this->otherUser->id,
            'message' => $this->message,
            'attachment_path' => $attachmentPath,
            'attachment_mime' => $attachmentMime,
            'attachment_name' => $attachmentName,
            // A sua própria mensagem é marcada como não lida pelo destinatário
            'is_read' => false,
        ]);

        $this->addMessage([
            'id' => $msg->id,
            'sender_id' => $user->id,
            'message' => $this->message,
            'attachment_path' => $attachmentPath,
            'attachment_mime' => $attachmentMime,
            'attachment_name' => $attachmentName,
            'created_at' => $msg->created_at->format('H:i'),
            // Aqui, a sua cópia local deve ser considerada não lida até que o outro usuário a leia
            'is_read' => false,
        ]);

        broadcast(new MessageSent($msg))->toOthers();
        $this->message = '';
        $this->attachment = null;
        $this->stopTyping();
    }

    // --------------------
    // Edição/Exclusão de mensagens
    // --------------------
    public function startEditing($id)
    {
        $messageToEdit = collect($this->messages)->firstWhere('id', $id);
        if ($messageToEdit && $messageToEdit['sender_id'] === auth()->id()) {
            $this->editingMessageId = $id;
            $this->editedMessageContent = $messageToEdit['message'];
            $this->showEditInput = true;
            $this->selectedMessage = null;
        }
    }

    public function saveEditedMessage()
    {
        $this->validate(['editedMessageContent' => 'required|string|max:255']);
        $message = Message::find($this->editingMessageId);
        if ($message && $message->sender_id === auth()->id()) {
            $message->update(['message' => $this->editedMessageContent, 'is_edited' => true]);
            event(new MessageEdited($message));

            $messageArray = $message->toArray();
            $messageArray['is_edited'] = true;
            $this->updateMessageFromBroadcast($messageArray);
        }

        $this->editingMessageId = null;
        $this->editedMessageContent = '';
        $this->showEditInput = false;
    }

    public function updateMessageFromBroadcast($messageData)
    {
        $index = collect($this->messages)->search(fn($msg) => $msg['id'] === $messageData['id']);
        if ($index !== false) {
            $this->messages[$index]['message'] = $messageData['message'];
            $this->messages[$index]['is_edited'] = true;
        }
    }

    public function deleteSelectedMessage()
    {
        $message = Message::find($this->selectedMessage);
        if ($message && $message->sender_id === auth()->id()) {
            $id = $message->id;
            $message->delete();
            event(new MessageDeleted($id, $message->sender_id, $message->receiver_id));
            $this->removeMessageFromBroadcast(['id' => $id]);
        }

        $this->selectedMessage = null;
        $this->showDeleteModal = false;
    }

    public function removeMessageFromBroadcast($messageData)
    {
        $this->messages = collect($this->messages)
            ->filter(fn($msg) => $msg['id'] !== $messageData['id'])
            ->values()->toArray();
    }

    public function addMessageFromBroadcast($messageData)
    {
        if (
            $messageData['sender_id'] === $this->otherUser->id &&
            $messageData['receiver_id'] === auth()->id()
        ) {

            $this->addMessage([
                'id' => $messageData['id'],
                'sender_id' => $messageData['sender_id'],
                'message' => $messageData['message'],
                'attachment_path' => $messageData['attachment_path'],
                'attachment_mime' => $messageData['attachment_mime'],
                'attachment_name' => $messageData['attachment_name'],
                'created_at' => now()->format('H:i'),
                // A mensagem é lida imediatamente ao ser recebida
                'is_read' => true,
            ]);

            // Marca como lida no banco de dados e informa outros componentes
            Message::find($messageData['id'])->update(['is_read' => true]);
            $this->dispatch('messageRead')->to('unread-messages');
            $this->stopTyping();
        }
    }

    // --------------------
    // CORREÇÃO: Reatividade do is_read
    // --------------------
    public function handleMessageRead($event)
    {
        $index = collect($this->messages)->search(fn($msg) => $msg['id'] === $event['messageId']);
        if ($index !== false) {
            $this->messages[$index]['is_read'] = true;

            // 🚨 SOLUÇÃO: Reatribuir o array força o Livewire a re-renderizar a view
            // e atualizar o ícone de leitura imediatamente.
            $this->messages = $this->messages;
        }
    }

    public function addMessage($messageData)
    {
        $this->messages[] = $messageData;
    }

    // --------------------
    // Seleção, cópia e modais
    // --------------------
    public function selectMessage($id)
    {
        $this->selectedMessage = $id;
    }
    public function clearSelection()
    {
        $this->selectedMessage = null;
    }
    public function confirmDelete()
    {
        $this->showDeleteModal = true;
    }
    public function cancelDelete()
    {
        $this->showDeleteModal = false;
    }
    public function copyMessage($id)
    {
        $message = Message::find($id);
        if ($message) $this->dispatch('copy-message', message: $message->message);
        $this->clearSelection();
    }

    public function render()
    {
        return view('livewire.chat', ['receiver' => $this->otherUser]);
    }
}
