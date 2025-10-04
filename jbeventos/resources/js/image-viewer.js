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

    function showImage(index) {
        images.forEach((img, i) => {
            img.classList.toggle('hidden', i !== index);
            img.classList.toggle('active', i === index);
        });

        indicator.textContent = `${index + 1} / ${images.length}`;
        current = index;

        // Controle dos botões
        if (prevBtn) prevBtn.classList.toggle('hidden', current === 0);
        if (nextBtn) nextBtn.classList.toggle('hidden', current === images.length - 1);
    }

    if (prevBtn) {
        prevBtn.addEventListener('click', () => {
            if (current > 0) showImage(current - 1);
        });
    }

    if (nextBtn) {
        nextBtn.addEventListener('click', () => {
            if (current < images.length - 1) showImage(current + 1);
        });
    }

    if (zoomBtn) {
        zoomBtn.addEventListener('click', () => {
            zoomImg.src = images[current].src;
            zoomModal.classList.remove('hidden');
        });
    }

    if (closeZoom) {
        closeZoom.addEventListener('click', () => {
            zoomModal.classList.add('hidden');
            zoomImg.src = ''; // limpa imagem do modal ao fechar
        });
    }

    // Inicia exibindo a primeira
    showImage(current);
});
