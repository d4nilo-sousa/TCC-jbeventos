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
    };

    // Garante seleção única entre os checkboxes de tipo
    typeCheckboxes.forEach(cb => {
        cb.addEventListener('change', function () {
            if (this.checked) {
                typeCheckboxes.forEach(other => { if (other !== this) other.checked = false; });
            }
            toggleCourseWrapper();
        });
    });

    toggleCourseWrapper(); // Executa no carregamento

    // Botão de reset limpa todos os filtros e mantém o menu aberto
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

    // Submissão: valida datas e só envia filtros preenchidos
    form.addEventListener('submit', function (e) {
        e.preventDefault();

        const startInput = form.querySelector('input[name="start_date"]');
        const endInput = form.querySelector('input[name="end_date"]');

        // Impede envio se a data final for menor que a inicial
        if (startInput.value && endInput.value && endInput.value < startInput.value) {
            alert('A data final não pode ser menor que a data inicial.');
            return;
        }

        // Monta query string apenas com campos preenchidos
        const params = new URLSearchParams();
        form.querySelectorAll('input, select').forEach(input => {
            if (input.type === 'checkbox' && input.checked) {
                params.append(input.name, input.value);
            } else if (input.value !== '' && input.type !== 'checkbox') {
                params.append(input.name, input.value);
            }
        });

        // Redireciona para a rota limpa ou com filtros aplicados
        const url = params.toString() ? `${form.action}?${params.toString()}` : form.action;
        window.location.href = url;
    });
});
