document.addEventListener('DOMContentLoaded', function () {
    const filterBtn = document.getElementById('filterBtn');
    const filterMenu = document.getElementById('filterMenu');

    filterBtn.addEventListener('click', function (e) {
        e.preventDefault();
        if (filterMenu.style.display === 'none' || filterMenu.style.display === '') {
            filterMenu.style.display = 'block';
        } else {
            filterMenu.style.display = 'none';
        }
    });

    // Fecha o menu se clicar fora dele
    document.addEventListener('click', function (e) {
        if (!filterMenu.contains(e.target) && e.target !== filterBtn) {
            filterMenu.style.display = 'none';
        }
    });
});
