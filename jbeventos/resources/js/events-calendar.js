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
                <div class="p-3 border-b border-gray-100 last:border-b-0 flex items-start space-x-3">
                    <div class="pt-1">
                        <i class="ph-fill ph-calendar-blank text-xl text-red-500"></i>
                    </div>
                    <div>
                        <h4 class="text-lg font-semibold text-gray-800">
                            <a href="${event.extendedProps.url || '#'}" class="hover:text-red-600 transition-colors">${event.title}</a>
                        </h4>
                        <p class="text-sm text-gray-500 mt-0.5">
                            <span class="font-medium text-red-600">${timeDisplay}</span> |
                            <strong>Local:</strong> ${event.extendedProps.location || 'Não Informado'}<br>
                            <strong>Coordenador:</strong> ${event.extendedProps.coordinator || 'Não Informado'}
                        </p>
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

// MELHORIA: Funções de Filtro
function toggleCourseSelect(event) {
    const courseSelectWrapper = document.getElementById('courseSelectWrapper');
    const checkedCheckbox = event.target;

    if (checkedCheckbox.value === 'course' && checkedCheckbox.checked) {
        courseSelectWrapper?.classList.remove('hidden');
    } else if (checkedCheckbox.value === 'general' && checkedCheckbox.checked) {
        // Se 'Geral' for marcado, esconde o seletor de curso
        courseSelectWrapper?.classList.add('hidden');
        // Opcional: desmarcar todos os cursos ao mudar para 'Geral'
        document.querySelectorAll('#courseSelectWrapper input[type="checkbox"]').forEach(checkbox => {
            checkbox.checked = false;
        });
    }
}

function toggleFilterMenu() {
    document.getElementById('filterMenu')?.classList.toggle('hidden');
}

/**
 * Limpa os filtros e submete o formulário, retornando à visualização de lista.
 */
function resetFilters() {
    const filterForm = document.getElementById('filterMenu').querySelector('form');
    // Limpa os campos visíveis
    filterForm.querySelectorAll('input:not([type="hidden"]), select').forEach(input => {
        if (input.type === 'checkbox' || input.type === 'radio') {
            input.checked = false;
        } else {
            input.value = '';
        }
    });

    // Se houver um input 'event_type' com valor 'general', marque-o como padrão após o reset
    const generalRadio = filterForm.querySelector('input[name="event_type"][value="general"]');
    if (generalRadio) {
        generalRadio.checked = true;
    }
    
    // Garantir que a view de lista seja exibida após o reset (limpa o localStorage)
    showListView(true); 

    // Submete o formulário com os campos limpos.
    filterForm.submit();
}

// Exporta a função principal para uso no app.js 
export { initializeEventsPage, calendarInstance };