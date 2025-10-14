window.deleteImage = function(id, btn, type) {
    let url;

    // Define a URL de exclusão
    if (type === 'icon') {
        url = `/course-icons/${id}`; // Para excluir a imagem de ícone do curso
    } else if (type === 'banner') {
        url = `/course-banners/${id}`; // Para excluir a imagem de banner do curso
    } else if (type === 'event') {
        url = `/event-images/${id}`; // Para excluir a imagem de evento
    } else {
        // Caso o tipo não seja válido
        alert('Tipo de imagem inválido.');
        return;
    }

    // 1. Remove o elemento da tela imediatamente antes do fetch (para dar feedback rápido)
    const previewDiv = btn.closest('div[data-filename]');
    if (previewDiv) {
        previewDiv.remove(); // Remove o preview da imagem da tela
    } else {
        // Fallback para caso o botão esteja em outra estrutura
        btn.closest('div[data-id]')?.remove();
    }

    // 2. Envia a requisição DELETE para o servidor
    fetch(url, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Accept': 'application/json'
        }
    })
    .then(response => {
        if (!response.ok) {
            alert('Erro ao excluir a imagem no servidor. Recarregue a página.');
        }
    })
    .catch(() => alert('Erro de conexão ao excluir a imagem.'));
};
