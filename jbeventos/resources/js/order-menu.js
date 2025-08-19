document.addEventListener('DOMContentLoaded', function () {
    const orderBtn = document.getElementById('orderBtn');
    const orderMenu = document.getElementById('orderMenu');

    // Alterna visibilidade do menu de ordenação
    orderBtn.addEventListener('click', function (e) {
        e.preventDefault();
        const isHidden = orderMenu.style.display !== 'block';
        orderMenu.style.display = isHidden ? 'block' : 'none';
    });

    // Fecha o menu se clicar fora (ignora clique no botão)
    document.addEventListener('click', function (e) {
        if (!orderMenu.contains(e.target) && e.target !== orderBtn) {
            orderMenu.style.display = 'none';
        }
    });

    // Garante seleção única nos checkboxes de ordenação
    const likesOrderCheckboxes = document.querySelectorAll('input[name="likes_order"]');
    likesOrderCheckboxes.forEach(cb => {
        cb.addEventListener('change', function () {
            if (this.checked) {
                likesOrderCheckboxes.forEach(other => {
                    if (other !== this) other.checked = false;
                });
            }
        });
    });

    // Intercepta submissão do form dentro do menu
    const form = orderMenu.querySelector('form');
    if (form) {
        form.addEventListener('submit', function (e) {
            const formData = new FormData(form);
            const params = new URLSearchParams(formData);

            // Se não houver parâmetros, redireciona para a rota "limpa"
            if ([...params].length === 0) {
                e.preventDefault();
                window.location.href = form.action;
            }
        });
    }
});
