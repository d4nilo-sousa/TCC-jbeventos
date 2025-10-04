function deleteImage(imageId, btn) {
    fetch(`/event-images/${imageId}`, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Accept': 'application/json'
        }
    })
        .then(response => {
            if (response.ok) {
                // Remove o container da imagem da tela
                btn.closest('div[data-id]').remove();
            } else {
                alert('Erro ao excluir a imagem.');
            }
        })
        .catch(() => alert('Erro ao excluir a imagem.'));
}