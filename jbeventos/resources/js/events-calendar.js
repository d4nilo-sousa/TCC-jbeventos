// Variável global para armazenar a instância do FullCalendar
let calendarInstance = null;

// Elementos do DOM 
const viewListBtn = document.getElementById('view-list-btn');
const viewCalendarBtn = document.getElementById('view-calendar-btn');
const listView = document.getElementById('list-view');
const calendarView = document.getElementById('calendar-view');
const paginationLinks = document.getElementById('pagination-links');
const modal = document.getElementById('dayDetailsModal');
const modalDate = document.getElementById('modal-date');
const modalEventsList = document.getElementById('modal-events-list');

// ------------------------------------
// Lógica do FullCalendar e Interatividade
// ------------------------------------

/**
 * Função principal de inicialização da página de eventos.
 */
function initializeEventsPage() {
    // Inicializa o calendário
    initializeCalendar();

    // Lógica de Alternância de Visualização
    const currentView = localStorage.getItem('event_view') || 'list';
    if (currentView === 'calendar') {
        showCalendarView();
    } else {
        showListView();
    }

    // Adiciona Listeners APENAS se os botões existirem
    viewListBtn?.addEventListener('click', showListView);
    viewCalendarBtn?.addEventListener('click', showCalendarView);
}


/**
 * Função para inicializar o FullCalendar
 */
function initializeCalendar() {
    const calendarEl = document.getElementById('full-calendar');

    // Verifica se a div do calendário existe antes de inicializar
    if (!calendarEl) {
        return;
    }

    calendarInstance = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        locale: 'pt-br',
        height: 'auto', // Ajusta a altura automaticamente

        // Configuração do Header
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,timeGridWeek,timeGridDay'
        },

        // Fonte de dados dos eventos: Puxa do endpoint JSON
        events: '/events/calendar-feed', 

        // ------------------------------------
        // Interatividade: Clicar em um DIA (dateClick)
        // ------------------------------------
        dateClick: function(info) {
            // info.dateStr é a data clicada (ex: '2025-11-03')
            showDayEventsModal(info.dateStr);
        },

        // ------------------------------------
        // Personalização da renderização
        // ------------------------------------
        eventDidMount: function(info) {
            // Adiciona um tooltip simples ou informação extra ao passar o mouse
            info.el.setAttribute('title', info.event.title + ' | Local: ' + info.event.extendedProps.location);
        },
    });

    // Renderiza (mas a div do calendário ainda está escondida)
    calendarInstance.render();
}

/**
 * Alterna para a visualização de LISTA.
 */
function showListView() {
    listView?.classList.remove('hidden');
    paginationLinks?.classList.remove('hidden');
    calendarView?.classList.add('hidden');
    document.getElementById('no-events-message')?.classList.remove('hidden');

    // Atualiza a seleção visual dos botões
    viewListBtn?.classList.add('bg-red-600', 'text-white');
    viewListBtn?.classList.remove('text-gray-700', 'hover:bg-gray-50');
    viewCalendarBtn?.classList.remove('bg-red-600', 'text-white');
    viewCalendarBtn?.classList.add('text-gray-700', 'hover:bg-gray-50');

    localStorage.setItem('event_view', 'list');
}

/**
 * Alterna para a visualização de CALENDÁRIO.
 */
function showCalendarView() {
    calendarView?.classList.remove('hidden');
    listView?.classList.add('hidden');
    paginationLinks?.classList.add('hidden');
    document.getElementById('no-events-message')?.classList.add('hidden');

    // Garante que o calendário renderize corretamente
    if (calendarInstance) {
        calendarInstance.render();
    }

    // Atualiza a seleção visual dos botões
    viewCalendarBtn?.classList.add('bg-red-600', 'text-white');
    viewCalendarBtn?.classList.remove('text-gray-700', 'hover:bg-gray-50');
    viewListBtn?.classList.remove('bg-red-600', 'text-white');
    viewListBtn?.classList.add('text-gray-700', 'hover:bg-gray-50');

    localStorage.setItem('event_view', 'calendar');
}

/**
 * Busca os eventos para um dia específico e exibe no modal.
 */
function showDayEventsModal(dateStr) {
    if (!modal) return; // Sai se o modal não existir

    // 1. Usa a função global para abrir o modal (se estiver disponível)
    if (window.openModal) {
        window.openModal('dayDetailsModal');
    } else {
        modal.classList.remove('hidden');
    }

    // Formato para display (ex: 27 de Outubro de 2025)
    const displayDate = new Date(dateStr + 'T00:00:00').toLocaleDateString('pt-BR', {
        weekday: 'long',
        year: 'numeric',
        month: 'long',
        day: 'numeric'
    });

    // Filtra os eventos do calendário pela data clicada
    const events = calendarInstance.getEvents();

    const eventsOnDay = events.filter(event => {
        if (!event.start) return false;

        // Compara a data de início (YYYY-MM-DD)
        const eventStartDay = event.start.toISOString().substring(0, 10);
        return eventStartDay === dateStr;
    });

    modalDate.textContent = displayDate;
    modalEventsList.innerHTML = '';

    if (eventsOnDay.length > 0) {
        eventsOnDay.forEach(event => {
            // Formata a hora de início
            const startTime = event.start.toLocaleTimeString('pt-BR', {
                hour: '2-digit',
                minute: '2-digit',
                hour12: false
            });

            // Determina se a hora deve ser exibida
            const timeDisplay = event.allDay ? ' (Dia Inteiro)' : ` (${startTime}h)`;

            const eventHtml = `
                <div class="p-3 border-b border-gray-100 last:border-b-0">
                    <h4 class="text-lg font-semibold text-red-600">
                        <a href="${event.extendedProps.url}" class="hover:underline">${event.title}</a>
                    </h4>
                    <p class="text-sm text-gray-500 mt-1">
                        ${timeDisplay} |
                        <strong>Local:</strong> ${event.extendedProps.location}<br>
                        <strong>Coordenador:</strong> ${event.extendedProps.coordinator}
                    </p>
                </div>
            `;
            modalEventsList.innerHTML += eventHtml;
        });
    } else {
        modalEventsList.innerHTML = `
            <div class="text-center p-4 text-gray-500 border rounded-md bg-gray-50">
                Nenhum evento agendado para este dia.
            </div>
        `;
    }
}


// Exporta a função principal e a instância do calendário para uso no app.js 
export { initializeEventsPage, calendarInstance };