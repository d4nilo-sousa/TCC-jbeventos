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

// ----------------------------
// Evento deletado
// ----------------------------
channel.listen('.event.deleted', (e) => {
    const eventCard = document.querySelector(`#event-card-${e.eventId}`);
    if (eventCard) {
        eventCard.remove(); // remove o card do DOM
    }
});

// ----------------------------
// Evento atualizado
// ----------------------------
channel.listen('.event.updated', (e) => {
    const existingCard = document.querySelector(`#event-card-${e.event.id}`);
    if (!existingCard) {
        // Opcional: logar ou ignorar silenciosamente
        return;
    }

    fetch(`/events/card/${e.event.id}`)
        .then(res => res.text())
        .then(html => {
            const tempDiv = document.createElement('div');
            tempDiv.innerHTML = html;

            Array.from(tempDiv.children).forEach(child => {
                child.id = `event-card-${e.event.id}`;
                existingCard.replaceWith(child);
            });
        })
        .catch(err => console.error('Erro ao atualizar card do evento:', err));

    // Atualiza detalhes na página show, se estiver nela
    if (window.location.pathname === `/events/${e.event.id}`) {
        const detailsContainer = document.getElementById('event-details');
        if (detailsContainer) {
            fetch(`/events/partial/${e.event.id}`)
                .then(res => res.text())
                .then(html => {
                    detailsContainer.innerHTML = html;
                })
                .catch(err => console.error('Erro ao atualizar detalhes do evento:', err));
        }
    }
});