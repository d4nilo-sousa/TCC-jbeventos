import Echo from 'laravel-echo';

import Pusher from 'pusher-js';
window.Pusher = Pusher;

// Adiciona o join para o canal de presença global de usuários online
window.Echo.join('online-users'); 

window.Echo.private(`user.${window.Laravel.userId}`)
    .listen('.MessageSent', (e) => {
        // CORREÇÃO: Disparar apenas o evento 'messageReceived' para atualizar o contador
        window.Livewire.dispatch('messageReceived');
    });
