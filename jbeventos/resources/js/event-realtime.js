import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

// ----------------------------
// Inicializa o Pusher no navegador
// ----------------------------
window.Pusher = Pusher;

window.Echo = new Echo({
    broadcaster: 'pusher',                           // tipo de broadcaster
    key: import.meta.env.VITE_PUSHER_APP_KEY,        // chave do Pusher (vem do .env)
    cluster: import.meta.env.VITE_PUSHER_APP_CLUSTER,// cluster/região
    forceTLS: true,                                  // usa HTTPS
    encrypted: true,                                 // garante criptografia
});

// ----------------------------
// Se inscreve no canal "events"
// ----------------------------
const channel = window.Echo.channel('events');

// ----------------------------
// Evento criado (event.created)
// ----------------------------
channel.listen('.event.created', (e) => {
    const eventsContainer = document.querySelector('.grid');
    if (!eventsContainer) return;

    // Evita adicionar duplicata (se já existe card com esse ID)
    if (document.querySelector(`#event-card-${e.event.id}`)) return;

    // Busca no backend o HTML do card pronto
    fetch(`/events/card/${e.event.id}`)
        .then(res => res.text())
        .then(html => {
            const tempDiv = document.createElement('div');
            tempDiv.innerHTML = html;

            // Dá um ID único pro card, baseado no event.id
            Array.from(tempDiv.children).forEach(child => {
                child.id = `event-card-${e.event.id}`;
                eventsContainer.prepend(child); // insere o card no topo da grade
            });
        })
        .catch(err => console.error('Erro ao carregar card do evento:', err));
});

// ----------------------------
// Evento deletado (event.deleted)
// ----------------------------
channel.listen('.event.deleted', (e) => {
    // Procura o card correspondente pelo ID
    const eventCard = document.querySelector(`#event-card-${e.eventId}`);
    if (eventCard) {
        eventCard.remove(); // remove do DOM se existir
    }
});

// ----------------------------
// Caso o usuário esteja na página
// de um evento específico (/events/{id})
// ----------------------------
if (window.location.pathname.startsWith('/events/')) {
    const eventId = window.location.pathname.split('/')[2];

    window.Echo.channel('events')
        .listen('.event.deleted', (e) => {
            // só age se o evento deletado for o mesmo da página atual
            if (e.eventId != eventId) return;

            // Cria um aviso visual (toast) na tela
            const toast = document.createElement('div');
            toast.textContent = 'Este evento foi excluído. Redirecionando...';
            toast.style.position = 'fixed';
            toast.style.bottom = '20px';
            toast.style.right = '20px';
            toast.style.background = '#f56565';
            toast.style.color = 'white';
            toast.style.padding = '15px 20px';
            toast.style.borderRadius = '8px';
            toast.style.boxShadow = '0 4px 6px rgba(0,0,0,0.2)';
            toast.style.zIndex = 1000;
            document.body.appendChild(toast);

            // Redireciona após 3 segundos para a lista de eventos
            setTimeout(() => {
                window.location.href = '/events';
            }, 3000);
        });
}
