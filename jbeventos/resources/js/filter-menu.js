document.addEventListener('DOMContentLoaded', function () {
    const filterBtn = document.getElementById('filterBtn');
    const filterMenu = document.getElementById('filterMenu');
    const eventTypeSelect = document.querySelector('select[name="event_type"]');
    const courseSelectWrapper = document.getElementById('courseSelectWrapper');
    const resetButton = document.getElementById('resetFiltres');
    const form = filterMenu.querySelector('form');

    // Alterna visibilidade do menu de filtros
    filterBtn.addEventListener('click', function (e) {
        e.preventDefault();
        filterMenu.style.display = filterMenu.style.display === 'block' ? 'none' : 'block';
    });

    // Fecha o menu se clicar fora dele
    document.addEventListener('click', function (e) {
        if (!filterMenu.contains(e.target) && e.target !== filterBtn) {
            filterMenu.style.display = 'none';
        }
    });

    // Mostra/esconde o select de cursos dependendo do tipo de evento
    const toggleCourseSelect = () => {
        if (eventTypeSelect.value === 'course') {
            courseSelectWrapper.style.display = 'block';
        } else {
            courseSelectWrapper.style.display = 'none';
        }
    };

    if (eventTypeSelect && courseSelectWrapper) {
        toggleCourseSelect();
        eventTypeSelect.addEventListener('change', toggleCourseSelect);
    }

    // Reseta os filtros para padrão sem fechar o menu
    if (resetButton && form) {
        resetButton.addEventListener('click', function (e) {
            e.preventDefault(); // evita o envio imediato do formulário

            // Reseta todos os selects para o valor padrão (primeira opção)
            form.querySelectorAll('select').forEach(select => select.selectedIndex = 0);

            // Atualiza a exibição do select de cursos
            toggleCourseSelect();

            // Mantém o menu aberto
            filterMenu.style.display = 'block';
        });
    }
});
