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

    // Lógica de Alternância de Visualização baseada no localStorage
    const currentView = localStorage.getItem('event_view') || 'list';
    if (currentView === 'calendar') {
        showCalendarView(false); // apenas exibe
    } else {
        showListView(false); // Apenas exibe a lista
    }

    // Adiciona Listeners
    viewListBtn?.addEventListener('click', () => showListView(true));
    viewCalendarBtn?.addEventListener('click', () => showCalendarView(true));

    // Lógica para alternar o campo 'Curso' no filtro
    document.querySelectorAll('input[name="event_type"]').forEach(input => {
        input.addEventListener('change', toggleCourseSelect);
    });

    // Lógica para limpar filtros
    document.getElementById('resetFiltres')?.addEventListener('click', resetFilters);

    // Lógica para abrir/fechar o menu de filtros
    document.getElementById('filterBtn')?.addEventListener('click', toggleFilterMenu);
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

        // Faz o calendário caber na tela sem linhas vazias
        fixedWeekCount: false, 
        
        // Impede que os dias de outros meses sejam exibidos no DayGridMonth
        showNonCurrentDates: false, 

        // Configuração do Header
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,timeGridWeek,timeGridDay'
        },

        // ------------------------------------
        // CORREÇÃO: Enviar parâmetros de filtro da URL para o feed do calendário
        // ------------------------------------
        events: {
            url: '/events/calendar-feed',
            method: 'GET',
            // Função para injetar dinamicamente os parâmetros da URL (filtros)
            extraParams: function() {
                const urlParams = new URLSearchParams(window.location.search);
                const params = {};
                for (const [key, value] of urlParams.entries()) {
                    // Ignora parâmetros que o FullCalendar envia automaticamente
                    if (key !== 'start' && key !== 'end' && key !== '_') {
                        params[key] = value;
                    }
                }
                return params;
            },
            failure: function() {
                console.error('Erro ao carregar os eventos do calendário.');
            }
        }, 

        // ------------------------------------
        // Interatividade: Clicar em um DIA (dateClick)
        // ------------------------------------
        dateClick: function(info) {
            // Apenas reage ao clique se não for um dia de outro mês
            if (info.dayEl.classList.contains('fc-day-other') && calendarInstance.getOption('initialView') === 'dayGridMonth') {
                return;
            }
            // info.dateStr é a data clicada (ex: '2025-11-03')
            showDayEventsModal(info.dateStr);
        },

        // ------------------------------------
        // Personalização da renderização
        // ------------------------------------
        eventDidMount: function(info) {
            // Adiciona um tooltip simples ou informação extra ao passar o mouse
            info.el.setAttribute('title', info.event.title + ' | Local: ' + (info.event.extendedProps.location || 'Não Informado'));
        },
        
        // Adiciona um listener para quando a navegação do calendário for alterada (mês/ano)
        datesSet: function(info) {
            // Recarrega os eventos com os novos parâmetros de start/end 
            // (Isso é mais redundante, mas ajuda a forçar a atualização após uma navegação)
            calendarInstance.refetchEvents();
        }
    });

    // Renderiza o calendário uma vez, mas ele só ficará visível se a view 'calendar' for ativada.
    calendarInstance.render();
}

/**
 * Alterna para a visualização de LISTA.
 * @param {boolean} updateStorage Se deve atualizar o localStorage.
 */
function showListView(updateStorage = true) {
    listView?.classList.remove('hidden');
    paginationLinks?.classList.remove('hidden');
    calendarView?.classList.add('hidden');
    // Apenas mostra a mensagem de "sem eventos" se a lista estiver vazia 
    document.getElementById('no-events-message')?.classList.remove('hidden'); 

    // Atualiza a seleção visual dos botões
    viewListBtn?.classList.add('bg-red-600', 'text-white');
    viewListBtn?.classList.remove('text-gray-700', 'hover:bg-gray-50');
    viewCalendarBtn?.classList.remove('bg-red-600', 'text-white');
    viewCalendarBtn?.classList.add('text-gray-700', 'hover:bg-gray-50');

    if (updateStorage) {
        localStorage.setItem('event_view', 'list');
    }
}

/**
 * Alterna para a visualização de CALENDÁRIO.
 * @param {boolean} updateStorage Se deve atualizar o localStorage.
 */
function showCalendarView(updateStorage = true) {
    calendarView?.classList.remove('hidden');
    listView?.classList.add('hidden');
    paginationLinks?.classList.add('hidden');
    document.getElementById('no-events-message')?.classList.add('hidden'); // O calendário lida com eventos vazios de outra forma

    // Garante que o calendário seja redimensionado corretamente ao ser exibido.
    if (calendarInstance) {
        calendarInstance.updateSize(); 
        // Força o recarregamento ao mudar para a visualização do calendário
        calendarInstance.refetchEvents(); 
    }

    // Atualiza a seleção visual dos botões
    viewCalendarBtn?.classList.add('bg-red-600', 'text-white');
    viewCalendarBtn?.classList.remove('text-gray-700', 'hover:bg-gray-50');
    viewListBtn?.classList.remove('bg-red-600', 'text-white');
    viewListBtn?.classList.add('text-gray-700', 'hover:bg-gray-50');

    if (updateStorage) {
        localStorage.setItem('event_view', 'calendar');
    }
}

/**
 * Busca os eventos para um dia específico e exibe no modal.
 * MELHORIA: Melhor manipulação de data e hora.
 */
function showDayEventsModal(dateStr) {
    if (!modal) return; 

    // Abre o modal
    if (window.openModal) {
        window.openModal('dayDetailsModal');
    } else {
        modal.classList.remove('hidden');
    }

    // Formato para display (ex: Domingo, 27 de Outubro de 2025)
    const dateObj = new Date(dateStr + 'T00:00:00'); // Cria a data no fuso zero para evitar problemas de fuso
    const displayDate = dateObj.toLocaleDateString('pt-BR', {
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
    }).sort((a, b) => {
        // Ordena por horário: eventos "dia inteiro" primeiro, depois por hora de início
        if (a.allDay && !b.allDay) return -1;
        if (!a.allDay && b.allDay) return 1;
        return a.start.getTime() - b.start.getTime();
    });

    modalDate.textContent = displayDate;
    modalEventsList.innerHTML = '';

    if (eventsOnDay.length > 0) {
        eventsOnDay.forEach(event => {
            // Formata a hora de início
            let timeDisplay;
            if (event.allDay) {
                timeDisplay = 'Dia Inteiro';
            } else {
                const startTime = event.start.toLocaleTimeString('pt-BR', {
                    hour: '2-digit',
                    minute: '2-digit',
                    hour12: false
                });
                timeDisplay = `${startTime}h`;
            }

            const eventHtml = `
    <div class="p-4 border-b border-gray-100 last:border-b-0 transition-colors duration-200 hover:bg-red-50/50 rounded-lg">

        <!-- Container completo com borda -->
        <div class="flex items-start space-x-4 border border-red-300 bg-red-50/20 rounded-xl p-3">

            <!-- Ícone -->
            <div class="pt-0.5 shrink-0">
                <div class="p-2 bg-red-100/80 rounded-full border border-red-300">
                    <i class="ph-fill ph-calendar-blank text-lg text-red-600 group-hover:text-red-700 transition-colors"></i>
                </div>
            </div>

            <!-- Conteúdo do Evento -->
            <div class="flex-1 min-w-0">

                <!-- Título -->
                <h4 class="text-base font-extrabold text-gray-800 truncate mb-1">
                    <span class="text-gray-800 group-hover:text-red-600 transition-colors">${event.title}</span>
                </h4>

                <!-- Detalhes -->
                <div class="text-sm text-gray-600 space-y-0.5">
                    
                    <!-- Data e Hora -->
                    <p class="font-bold text-red-600 flex items-center">
                        <i class="ph-fill ph-clock text-base mr-2"></i>
                        ${timeDisplay}
                    </p>

                    <!-- Local -->
                    <p class="truncate">
                        <span class="font-semibold text-gray-700">Local:</span>
                        <span class="text-gray-500">${event.extendedProps.location ?? 'Não Informado'}</span>
                    </p>
                    
                    <!-- Coordenador -->
                    <p class="truncate">
                        <span class="font-semibold text-gray-700">Coord.:</span> 
                        <span class="text-gray-500">${event.extendedProps.coordinator ?? 'Não Informado'}</span>
                    </p>
                </div>
            </div>

        </div>
    </div>
`;

            modalEventsList.innerHTML += eventHtml;
        });
    } else {
        modalEventsList.innerHTML = `
            <div class="text-center p-4 text-gray-500 border rounded-md bg-gray-50">
                🎉 Nenhum evento agendado para este dia.
            </div>
        `;
    }
}

// Exporta a função principal para uso no app.js 
export { initializeEventsPage, calendarInstance };