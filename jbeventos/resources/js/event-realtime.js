import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

// Inicializa Pusher
window.Pusher = Pusher;

window.Echo = new Echo({
    broadcaster: 'pusher',
    key: import.meta.env.VITE_PUSHER_APP_KEY,
    cluster: import.meta.env.VITE_PUSHER_APP_CLUSTER,
    forceTLS: true,
    encrypted: true,
});

const channel = window.Echo.channel('events');

// ----------------------------
// Evento criado
// ----------------------------
channel.listen('.event.created', (e) => {
    const eventsContainer = document.querySelector('.grid');
    if (!eventsContainer) return;

    // Evita duplicata
    if (document.querySelector(`#event-card-${e.event.id}`)) return;

    fetch(`/events/card/${e.event.id}`)
        .then(res => res.text())
        .then(html => {
            const tempDiv = document.createElement('div');
            tempDiv.innerHTML = html;

            // Adiciona ID a todos os filhos do HTML renderizado
            Array.from(tempDiv.children).forEach(child => {
                child.id = `event-card-${e.event.id}`;
                eventsContainer.prepend(child); // adiciona no topo
            });
        })
        .catch(err => console.error('Erro ao carregar card do evento:', err));
});