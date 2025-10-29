<?php

use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\Facades\Auth;

// Canal privado para notificações específicas de usuário (como notificação da navbar)
Broadcast::channel('user.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

// Canal de Presença do Chat (para mensagens, digitação, etc.)
Broadcast::channel('chat.{userId1}.{userId2}', function ($user, $userId1, $userId2) {
    if ((int) $user->id === (int) $userId1 || (int) $user->id === (int) $userId2) {
        // Retorna os dados necessários para o rastreamento no chat
        return ['id' => $user->id, 'name' => $user->name, 'user_icon' => $user->user_icon];
    }
});

// NOVO: Canal de Presença GLOBAL para rastrear o status online de qualquer usuário logado no sistema.
// Este é o canal que o Livewire 'Chat.php' usa agora para determinar o 'isOnline'.
Broadcast::channel('online-users', function ($user) {
    if ($user) {
        // Retorna o mínimo necessário para rastrear a presença global
        return ['id' => $user->id];
    }
});

// Canal para o modelo de usuário (Geralmente usado para eventos privados direcionados ao modelo User)
Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});
