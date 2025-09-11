document.addEventListener('DOMContentLoaded', function () {
    const images = document.querySelectorAll('.carousel-img');
    const indicator = document.getElementById('indicator');
    const prevBtn = document.getElementById('prevBtn');
    const nextBtn = document.getElementById('nextBtn');
    const zoomBtn = document.getElementById('zoomBtn');
    const zoomModal = document.getElementById('zoomModal');
    const zoomImg = document.getElementById('zoomImg');
    const closeZoom = document.getElementById('closeZoom');

    let current = 0;

    // Função para mostrar imagem
    function showImage(index) {
        images.forEach((img, i) => {
            img.classList.toggle('hidden', i !== index);
            img.classList.toggle('active', i === index);
        });
        indicator.textContent = `${index + 1} / ${images.length}`;
        current = index;
    }

    // Botões de navegação
    if (prevBtn) {
        prevBtn.addEventListener('click', () => {
            let newIndex = (current - 1 + images.length) % images.length;
            showImage(newIndex);
        });
    }

    if (nextBtn) {
        nextBtn.addEventListener('click', () => {
            let newIndex = (current + 1) % images.length;
            showImage(newIndex);
        });
    }

    // Abrir modal de zoom
    zoomBtn.addEventListener('click', () => {
        const activeImg = images[current];
        zoomImg.src = activeImg.src;
        zoomModal.classList.remove('hidden');
    });

    // Fechar modal
    closeZoom.addEventListener('click', () => {
        zoomModal.classList.add('hidden');
    });

    // Inicia primeira imagem
    showImage(current);
});

