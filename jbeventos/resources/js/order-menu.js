document.addEventListener('DOMContentLoaded', function () {
    const filterMenu = document.getElementById('filterMenu');
    const form = filterMenu.querySelector('form');
    const resetBtn = document.getElementById('resetFiltres');

    // Função para permitir desmarcar radios clicando neles novamente
    function enableRadioUncheck(name) {
        const radios = form.querySelectorAll(`input[name="${name}"]`);
        radios.forEach(radio => {
            radio.dataset.wasChecked = radio.checked ? 'true' : 'false'; // inicializa

            radio.addEventListener('click', function () {
                if (this.checked && this.dataset.wasChecked === 'true') {
                    this.checked = false;
                    this.dataset.wasChecked = 'false';
                } else {
                    radios.forEach(r => r.dataset.wasChecked = 'false');
                    this.dataset.wasChecked = 'true';
                }
            });
        });
    }

    enableRadioUncheck('schedule_order');
    enableRadioUncheck('likes_order');

    form.addEventListener('submit', function (e) {
        e.preventDefault();

        const formData = new FormData(form);
        const currentParams = new URLSearchParams(window.location.search);

        // Parâmetros que o filtro controla
        const filterParams = [
            'search', 'event_type', 'course_id[]', 'category_id[]',
            'start_date', 'end_date', 'schedule_order', 'likes_order'
        ];

        // Remove os parâmetros antigos relacionados ao filtro
        filterParams.forEach(param => {
            if (param.endsWith('[]')) {
                const base = param.slice(0, -2);
                currentParams.forEach((value, key) => {
                    if (key === base || key.startsWith(base + '[]')) {
                        currentParams.delete(key);
                    }
                });
            } else {
                currentParams.delete(param);
            }
        });

        // Busca explicitamente os valores dos radios de ordenação:
        const scheduleOrder = form.querySelector('input[name="schedule_order"]:checked');
        const likesOrder = form.querySelector('input[name="likes_order"]:checked');

        // Adiciona os outros parâmetros normalmente
        for (const [key, value] of formData.entries()) {
            // Ignora os parâmetros de ordenação que serão tratados separadamente
            if (key === 'schedule_order' || key === 'likes_order') continue;

            if (key.endsWith('[]')) {
                currentParams.append(key.slice(0, -2), value);
            } else {
                currentParams.set(key, value);
            }
        }

        // Adiciona os parâmetros de ordenação apenas se houver valor selecionado
        if (scheduleOrder) {
            currentParams.set('schedule_order', scheduleOrder.value);
        }
        if (likesOrder) {
            currentParams.set('likes_order', likesOrder.value);
        }

        // Remove parâmetros com valores vazios (exemplo: start_date=)
        for (const [key, value] of [...currentParams]) {
            if (value === '') {
                currentParams.delete(key);
            }
        }

        const queryString = currentParams.toString();
        const url = queryString ? `${window.location.pathname}?${queryString}` : window.location.pathname;
        window.location.href = url;
    });

    resetBtn.addEventListener('click', function () {
        // Desmarca os radios de ordenação sem recarregar a página
        const scheduleRadios = form.querySelectorAll('input[name="schedule_order"]');
        scheduleRadios.forEach(radio => {
            radio.checked = false;
            radio.dataset.wasChecked = 'false';
        });

        const likesRadios = form.querySelectorAll('input[name="likes_order"]');
        likesRadios.forEach(radio => {
            radio.checked = false;
            radio.dataset.wasChecked = 'false';
        });
    });
});
