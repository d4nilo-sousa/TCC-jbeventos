window.deleteImage = function(id, btn, type) {
    let url;

    // Define a URL de exclusão
    if (type === 'icon') {
        // Rota CORRIGIDA: DELETE /courses/{id}/image/icon (Chama destroyCourseImage)
        url = `/courses/${id}/image/icon`; // <-- MUDANÇA AQUI
    } else if (type === 'banner') {
        // Rota CORRIGIDA: DELETE /courses/{id}/image/banner (Chama destroyCourseImage)
        url = `/courses/${id}/image/banner`; // <-- MUDANÇA AQUI
    } else if (type === 'event_cover') { 
        // Rota: DELETE /events/{event_id}/cover
        url = `/events/${id}/cover`; 
    } else if (type === 'event') {
        // Rota: DELETE /event-images/{id}
        url = `/event-images/${id}`; 
    } else {
        // Caso o tipo não seja válido
    alert('Tipo de imagem inválido.');
    return;// Caso o tipo não seja válido
    }

    // 1. Remove o elemento da tela imediatamente antes do fetch (para dar feedback rápido)
    const previewDiv = btn.closest('div[data-id]'); // Altera para buscar por 'data-id'
    if (previewDiv) {
        previewDiv.remove(); // Remove o preview da imagem da tela
    } else {
        // Fallback para caso o botão esteja em outra estrutura
        btn.closest('div[data-filename]')?.remove();
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