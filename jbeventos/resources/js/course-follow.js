document.addEventListener('DOMContentLoaded', function () {
    // Função para seguir um curso
    function toggleFollow(courseId, action) {
        fetch(`/courses/${action}/${courseId}`, {
            method: action === 'follow' ? 'POST' : 'DELETE', // POST para seguir, DELETE para deixar de seguir
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const button = document.querySelector(`#${action === 'follow' ? 'followButton' : 'unfollowButton'}`);
                // Altere o texto e o estilo do botão
                if (action === 'follow') {
                    button.textContent = '✔ Seguindo';
                    button.classList.replace('bg-blue-600', 'bg-gray-200');
                    button.classList.replace('hover:bg-blue-700', 'hover:bg-gray-300');
                    button.id = 'unfollowButton'; // Troca o id para o de "Deixar de Seguir"
                    button.dataset.action = 'unfollow'; // Atualiza o dado
                } else {
                    button.textContent = '+ Seguir';
                    button.classList.replace('bg-gray-200', 'bg-blue-600');
                    button.classList.replace('hover:bg-gray-300', 'hover:bg-blue-700');
                    button.id = 'followButton'; // Troca o id para o de "Seguir"
                    button.dataset.action = 'follow'; // Atualiza o dado
                }
            }
        })
        .catch(error => {
            console.error('Erro ao realizar a ação:', error);
        });
    }

    // Adicionando o evento de clique nos botões de seguir e deixar de seguir
    document.addEventListener('click', function (event) {
        if (event.target && event.target.id === 'followButton') {
            toggleFollow(event.target.dataset.courseId, 'follow');
        } else if (event.target && event.target.id === 'unfollowButton') {
            toggleFollow(event.target.dataset.courseId, 'unfollow');
        }
    });
});
