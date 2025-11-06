import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

// ----------------------------
// Inicializa o Pusher no navegador
// ----------------------------
window.Pusher = Pusher;

window.Echo = new Echo({
    broadcaster: 'pusher',                           // tipo de broadcaster
    key: import.meta.env.VITE_PUSHER_APP_KEY,        // chave do Pusher (vem do .env)
    cluster: import.meta.env.VITE_PUSHER_APP_CLUSTER,// cluster/região
    forceTLS: true,                                  // usa HTTPS
    encrypted: true,                                 // garante criptografia
});

// ----------------------------
// Se inscreve no canal "events"
// ----------------------------
const channel = window.Echo.channel('events');

// ----------------------------
// Evento deletado (event.deleted) - LÓGICA CONSOLIDADA E CORRIGIDA
// ----------------------------
channel.listen('.event.deleted', (e) => {
    
    // 1. LÓGICA PARA PÁGINA DE INDEX/LISTA: Remover o card (Isto estava no bloco de fora)
    // Procura o card correspondente pelo ID
    const eventCard = document.querySelector(`#event-card-${e.eventId}`);
    if (eventCard) {
        eventCard.remove(); // remove do DOM se existir
    }

    // 2. LÓGICA PARA PÁGINA DE SHOW/DETALHE: Verificar e redirecionar (Isto estava no bloco 'if' que causava conflito)
    if (window.location.pathname.startsWith('/events/')) {
        // Tenta pegar o ID da URL, tratando a string de forma segura
        const urlSegments = window.location.pathname.split('/').filter(Boolean);
        const eventIdFromUrl = urlSegments.length > 1 && urlSegments[0] === 'events' ? urlSegments[1] : null;

        // só age se o evento deletado for o mesmo da página atual
        if (eventIdFromUrl && e.eventId == eventIdFromUrl) {
            
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
        }
    }
});