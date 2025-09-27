document.addEventListener('DOMContentLoaded', function () {
    const filterBtn = document.getElementById('filterBtn');
    const filterMenu = document.getElementById('filterMenu');
    const courseSelectWrapper = document.getElementById('courseSelectWrapper');
    const resetButton = document.getElementById('resetFiltres');
    const form = filterMenu.querySelector('form');

    // Alterna visibilidade do menu de filtros
    filterBtn.addEventListener('click', function (e) {
        e.preventDefault();
        filterMenu.style.display = filterMenu.style.display === 'block' ? 'none' : 'block';
    });

    // Fecha o menu se clicar fora (exceto no botão de reset)
    document.addEventListener('click', function (e) {
        if (!filterMenu.contains(e.target) && e.target !== filterBtn && e.target !== resetButton) {
            filterMenu.style.display = 'none';
        }
    });

    // Mostra/esconde o campo de curso dependendo do tipo selecionado
    const typeCheckboxes = document.querySelectorAll('input[name="event_type"]');
    const toggleCourseWrapper = () => {
        const courseChecked = Array.from(typeCheckboxes).some(cb => cb.value === 'course' && cb.checked);
        courseSelectWrapper.style.display = courseChecked ? 'block' : 'none';

        if (!courseChecked) {
            const courseSelect = form.querySelector('select[name="course"]');
            if (courseSelect) {
                courseSelect.selectedIndex = 0; // Reseta o curso selecionado
            }
        }
    };

    typeCheckboxes.forEach(cb => {
        cb.addEventListener('change', function () {
            if (this.checked) {
                typeCheckboxes.forEach(other => { if (other !== this) other.checked = false; });
            }
            toggleCourseWrapper();
        });
    });

    toggleCourseWrapper();

    if (resetButton && form) {
        resetButton.addEventListener('click', function (e) {
            e.preventDefault();
            form.querySelectorAll('input[type="checkbox"]').forEach(cb => cb.checked = false);
            form.querySelectorAll('input[type="date"]').forEach(input => input.value = '');
            form.querySelectorAll('select').forEach(select => select.selectedIndex = 0);
            toggleCourseWrapper();
            filterMenu.style.display = 'block';
        });
    }

    form.addEventListener('submit', function (e) {
        e.preventDefault();

        const startInput = form.querySelector('input[name="start_date"]');
        const endInput = form.querySelector('input[name="end_date"]');

        if (startInput.value && endInput.value && endInput.value < startInput.value) {
            alert('A data final não pode ser menor que a data inicial.');
            return;
        }

        const currentParams = new URLSearchParams(window.location.search);

        // Limpa os parâmetros que vamos atualizar para evitar duplicidade
        ['status', 'event_type', 'likes_order', 'schedule_order', 'start_date', 'end_date'].forEach(name => {
            currentParams.delete(name);
        });
        // Também limpa os arrays (course_id[], category_id[])
        [...currentParams.keys()]
            .filter(key => key.endsWith('[]'))
            .forEach(key => currentParams.delete(key));

        form.querySelectorAll('input, select').forEach(input => {
            if (input.type === 'checkbox' && input.checked) {
                // Para os que só aceitam 1 valor (status, event_type, likes_order, schedule_order)
                if (['status', 'event_type', 'likes_order', 'schedule_order'].includes(input.name)) {
                    currentParams.set(input.name, input.value);
                }
                // Para os que aceitam múltiplos (course_id[], category_id[])
                else if (input.name.endsWith('[]')) {
                    currentParams.append(input.name, input.value);
                }
            } else if (input.type !== 'checkbox' && input.value !== '') {
                currentParams.set(input.name, input.value);
            }
        });

        // Remove course_id[] se 'course' não estiver marcado em event_type
        const courseChecked = Array.from(typeCheckboxes).some(cb => cb.value === 'course' && cb.checked);
        if (!courseChecked) {
            currentParams.delete('course_id[]');
        }

        const queryString = currentParams.toString();
        const url = queryString ? `${window.location.pathname}?${queryString}` : window.location.pathname;

        window.location.href = url;
    });
});
