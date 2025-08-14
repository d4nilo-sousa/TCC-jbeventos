import Echo from 'laravel-echo';

import Pusher from 'pusher-js';
window.Pusher = Pusher;

window.Echo.private(`chat.${window.Laravel.userId}`) // Canal privado do usuário logado
    .listen('MessageSent', (e) => {
        // Dispara um evento do Livewire para atualizar o chat
        window.livewire.emit('messageReceived', {
            user: e.user.name,
            message: e.message
        });
    });