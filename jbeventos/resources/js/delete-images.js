window.deleteImage = function(id, btn, type) {
    let url;

    // Define a URL de exclusão
    if (type === 'icon') {
        // Rota: DELETE /courses/{id}/icon (Chama destroyCourseImage)
        url = `/courses/${id}/icon`; 
    } else if (type === 'banner') {
        // Rota: DELETE /courses/{id}/banner (Chama destroyCourseImage)
        url = `/courses/${id}/banner`; 
    } else if (type === 'event_cover') { // <-- NOVO TIPO ADICIONADO AQUI
        // Rota: DELETE /events/{event_id}/cover (Chama removeCoverImage)
        // O 'id' deve ser o ID do Evento
        url = `/events/${id}/cover`; 
    } else if (type === 'event') {
        // Rota: DELETE /event-images/{id} (Chama destroyEventImage - para GALERIA)
        // O 'id' deve ser o ID da EventImage
        url = `/event-images/${id}`; 
    } else {
        // Caso o tipo não seja válido
        alert('Tipo de imagem inválido.');
        return;
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