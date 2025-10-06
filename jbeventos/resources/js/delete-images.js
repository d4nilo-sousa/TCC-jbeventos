// Garante que a função é global
window.deleteImage = function(imageId, btn, type = 'event') {
    let url;

    if (type === 'icon') {
        url = `/course-icons/${imageId}`;
    } else if (type === 'banner') {
        url = `/course-banners/${imageId}`;
    } else {
        url = `/event-images/${imageId}`;
    }

    fetch(url, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Accept': 'application/json'
        }
    })
    .then(response => {
        if (response.ok) {
            // Se a exclusão no servidor for bem-sucedida, remove o elemento da tela.
            btn.closest('div[data-id]').remove();
        } else {
            alert('Erro ao excluir a imagem.');
        }
    })
    .catch(() => alert('Erro ao excluir a imagem.'));
}