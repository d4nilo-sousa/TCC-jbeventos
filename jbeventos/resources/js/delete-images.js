// Garante que a função é global
window.deleteImage = function(courseId, btn, type) { // Mudança no parâmetro de 'imageId' para 'courseId'
    let url;

    // Define a URL de exclusão
    if (type === 'icon') {
        url = `/course-icons/${courseId}`;
    } else if (type === 'banner') {
        url = `/course-banners/${courseId}`;
    } else {
        // Para imagens de evento, a URL é diferente e o ID deve ser o da imagem de evento.
        // Se a sua intenção é usar esta função SÓ para cursos, você pode remover o 'else'.
        // Se for para evento, certifique-se que você está passando o ID CORRETO da EventImage
        url = `/event-images/${courseId}`; // Assumindo que courseId aqui é EventImage ID para tipo 'event'
    }

    // 1. Remove o elemento da tela IMEDIATAMENTE antes do fetch (para dar feedback rápido)
    const previewDiv = btn.closest('div[data-filename]');
    if (!previewDiv) {
        // Adicionando um fallback caso o botão esteja em uma estrutura diferente para eventos
        btn.closest('div[data-id]')?.remove();
    } else {
        previewDiv.remove();
    }
    
    // 2. Garante que o input hidden de 'remove' é resetado se o usuário tivesse mudado de ideia
    // Embora a remoção AJAX seja imediata, é bom garantir que o formulário está limpo.
    if (type === 'icon') {
        document.querySelector('input[name=remove_course_icon]').value = 0;
    } else if (type === 'banner') {
        document.querySelector('input[name=remove_course_banner]').value = 0;
    }


    fetch(url, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Accept': 'application/json'
        }
    })
    .then(response => {
        if (!response.ok) {
            // Se a exclusão no servidor falhar, avisa o usuário.
            // O ideal seria re-adicionar o preview para não ter sumido do front, mas falhado no back.
            alert('Erro ao excluir a imagem no servidor. Recarregue a página.');
        } else {
            // Limpa o input file caso o usuário tente atualizar sem selecionar uma nova imagem
            const inputId = type === 'icon' ? 'course_icon' : 'course_banner';
            document.getElementById(inputId).value = '';
        }
    })
    .catch(() => alert('Erro de conexão ao excluir a imagem.'));
};