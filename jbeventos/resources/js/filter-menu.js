document.addEventListener('DOMContentLoaded', function () {
    const filterBtn = document.getElementById('filterBtn');
    const filterMenu = document.getElementById('filterMenu');
    const courseSelectWrapper = document.getElementById('courseSelectWrapper');
    const resetButton = document.getElementById('resetFiltres');
    const form = filterMenu.querySelector('form');

    // DROPDOWN
    filterBtn.addEventListener('click', () => {
        filterMenu.style.display = filterMenu.style.display === 'block' ? 'none' : 'block';
    });

    document.addEventListener('click', e => {
        if (!filterMenu.contains(e.target) && e.target !== filterBtn && e.target !== resetButton) {
            filterMenu.style.display = 'none';
        }
    });

    // TOGGLE CURSOS
    const typeCheckboxes = form.querySelectorAll('input[name="event_type"]');
    const toggleCourseWrapper = () => {
        const courseChecked = Array.from(typeCheckboxes).some(cb => cb.value === 'course' && cb.checked);
        courseSelectWrapper.style.display = courseChecked ? 'block' : 'none';

        if (!courseChecked) {
            courseSelectWrapper.querySelectorAll('input[type="checkbox"]').forEach(cb => cb.checked = false);
        }
    };

    typeCheckboxes.forEach(cb => {
        cb.addEventListener('change', () => {
            if (cb.checked) typeCheckboxes.forEach(other => { if (other !== cb) other.checked = false; });
            toggleCourseWrapper();
        });
    });

    toggleCourseWrapper();

    // RESET
    resetButton.addEventListener('click', e => {
        e.preventDefault();

        // Desmarca TODOS os checkboxes e radios
        form.querySelectorAll('input[type="checkbox"], input[type="radio"]').forEach(input => {
            input.checked = false;
        });

        // Limpa campos de data
        form.querySelectorAll('input[type="date"]').forEach(input => input.value = '');

        // Atualiza visibilidade do wrapper de cursos
        toggleCourseWrapper();

        // Mantém menu aberto
        filterMenu.style.display = 'block';
    });

    // SUBMIT
    form.addEventListener('submit', e => {
        e.preventDefault();

        const start = form.querySelector('input[name="start_date"]');
        const end = form.querySelector('input[name="end_date"]');
        if (start.value && end.value && end.value < start.value) {
            alert('A data final não pode ser menor que a data inicial.');
            return;
        }

        const params = new URLSearchParams();

        // Tipo de evento
        const selectedType = form.querySelector('input[name="event_type"]:checked');
        if (selectedType) params.set('event_type', selectedType.value);

        // Cursos (apenas se tipo = course)
        if (selectedType && selectedType.value === 'course') {
            const selectedCourses = Array.from(courseSelectWrapper.querySelectorAll('input[name="course_id[]"]:checked'));
            selectedCourses.forEach(cb => params.append('course_id[]', cb.value));
        }

        // Categorias
        const selectedCategories = Array.from(form.querySelectorAll('input[name="category_id[]"]:checked'));
        selectedCategories.forEach(cb => params.append('category_id[]', cb.value));

        // Datas
        if (start.value) params.set('start_date', start.value);
        if (end.value) params.set('end_date', end.value);

        // Ordenações
        const scheduleOrder = form.querySelector('input[name="schedule_order"]:checked');
        if (scheduleOrder) params.set('schedule_order', scheduleOrder.value);

        const likesOrder = form.querySelector('input[name="likes_order"]:checked');
        if (likesOrder) params.set('likes_order', likesOrder.value);

        // Redireciona
        const queryString = params.toString();
        window.location.href = queryString ? `${window.location.pathname}?${queryString}` : window.location.pathname;
    });
});
