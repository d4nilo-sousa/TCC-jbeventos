import './bootstrap';
import './datetime-validation';
import './event-reactions';
import './event-realtime';
import './filter-menu';
import './password-generator';
import './password-validator';
import './echo';
import 'alpinejs';
import './image-viewer';
import './preview-images';
import './search-highlight';
import './modal-actions';
import './tabs-navigation';
import './delete-images';


import { initializeEventsPage } from './events_calendar'; 

window.Alpine = Alpine;
Alpine.start();

// -----------------------------------------------------
// Inicialização do FullCalendar / Eventos
// -----------------------------------------------------

document.addEventListener('DOMContentLoaded', function() {
    // Verifica se os elementos da página de eventos existem antes de inicializar
    const isEventsPage = document.getElementById('list-view');

    if (isEventsPage) {
        initializeEventsPage();
    }

});