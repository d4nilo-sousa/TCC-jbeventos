document.addEventListener('DOMContentLoaded', function () {
    const images = document.querySelectorAll('.carousel-img'); // todas as imagens do carrossel
    const indicator = document.getElementById('indicator'); // indicador da posição atual
    const prevBtn = document.getElementById('prevBtn'); // botão anterior
    const nextBtn = document.getElementById('nextBtn'); // botão próximo
    const zoomBtn = document.getElementById('zoomBtn'); // botão para abrir zoom
    const zoomModal = document.getElementById('zoomModal'); // modal do zoom
    const zoomImg = document.getElementById('zoomImg'); // imagem dentro do modal
    const closeZoom = document.getElementById('closeZoom'); // botão para fechar o modal

    let current = 0; // índice da imagem atual

    // Atualiza a exibição das imagens e o indicador
    function showImage(index) {
        images.forEach((img, i) => {
            img.classList.toggle('hidden', i !== index); // mostra só a imagem atual
            img.classList.toggle('active', i === index);
        });
        indicator.textContent = `${index + 1} / ${images.length}`; // atualiza contador
        current = index;
    }

    // Navegação para a imagem anterior, com loop
    if (prevBtn) {
        prevBtn.addEventListener('click', () => {
            let newIndex = (current - 1 + images.length) % images.length;
            showImage(newIndex);
        });
    }

    // Navegação para a próxima imagem, com loop
    if (nextBtn) {
        nextBtn.addEventListener('click', () => {
            let newIndex = (current + 1) % images.length;
            showImage(newIndex);
        });
    }

    // Abre modal de zoom mostrando a imagem atual
    zoomBtn.addEventListener('click', () => {
        const activeImg = images[current];
        zoomImg.src = activeImg.src;
        zoomModal.classList.remove('hidden');
    });

    // Fecha o modal de zoom
    closeZoom.addEventListener('click', () => {
        zoomModal.classList.add('hidden');
    });

    // Exibe a primeira imagem ao carregar
    showImage(current);
});
