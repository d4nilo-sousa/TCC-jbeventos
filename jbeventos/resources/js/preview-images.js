document.addEventListener('DOMContentLoaded', function () {
    // --- Seleção de capa ---
    const eventImageInput = document.getElementById('event_image');  // input da capa
    const eventImagePreview = document.getElementById('event_image_preview');  // preview da capa
    let selectedCover = null;  // arquivo da capa selecionada

    // --- Seleção de imagens extras ---
    const eventImagesInput = document.getElementById('event_images');  // input das imagens extras
    const eventImagesPreview = document.getElementById('event_images_preview');  // preview das imagens extras
    let selectedFiles = [];  // arquivos extras selecionados

    // Pega nomes das imagens já existentes no blade para evitar duplicatas
    const existingImages = Array.from(document.querySelectorAll('#event_images_preview > div[data-filename]'))
        .map(div => div.dataset.filename.toLowerCase());

    // Função para criar preview de imagem com botão de remover
    function createImagePreview(file, previewContainer, onRemove) {
        const reader = new FileReader();
        reader.onload = function (event) {
            const imgContainer = document.createElement('div');
            imgContainer.classList.add(
                'relative', 'rounded', 'overflow-hidden',
                'flex', 'items-center', 'justify-center', 'bg-gray-100',
                'w-full', 'max-w-full', 'aspect-[2/1]'
            );

            const img = document.createElement('img');
            img.src = event.target.result;
            img.classList.add('object-contain', 'w-full', 'h-full');

            // Botão para remover a imagem do preview e da seleção
            const removeBtn = document.createElement('button');
            removeBtn.type = 'button';
            removeBtn.innerHTML = '×';
            removeBtn.classList.add(
                'absolute', 'z-20',
                'bg-red-600', 'text-white', 'rounded-full', 'w-6', 'h-6',
                'flex', 'items-center', 'justify-center', 'hover:bg-red-700'
            );

            removeBtn.addEventListener('click', () => {
                onRemove(file, imgContainer);
            });

            imgContainer.appendChild(img);
            imgContainer.appendChild(removeBtn);
            previewContainer.appendChild(imgContainer);
        };
        reader.readAsDataURL(file);
    }

    // --- Quando seleciona capa ---
    eventImageInput.addEventListener('change', (e) => {
        const file = e.target.files[0];
        if (!file) return;

        selectedCover = file;
        eventImagePreview.innerHTML = '';  // limpa preview anterior

        createImagePreview(file, eventImagePreview, () => {
            selectedCover = null;
            eventImagePreview.innerHTML = '';
            eventImageInput.value = '';  // reseta input
        });
    });

    // --- Quando seleciona imagens extras ---
    eventImagesInput.addEventListener('change', (e) => {
        const files = Array.from(e.target.files);

        files.forEach(file => {
            const fileName = file.name.toLowerCase();

            // Evita arquivos duplicados (selecionados ou existentes)
            const alreadySelected = selectedFiles.some(f => f.name.toLowerCase() === fileName);
            const alreadyExists = existingImages.includes(fileName);

            if (alreadySelected || alreadyExists) {
                alert(`A imagem "${file.name}" já foi adicionada.`);
                return;
            }

            selectedFiles.push(file);

            createImagePreview(file, eventImagesPreview, (f, container) => {
                selectedFiles = selectedFiles.filter(item => item !== f);
                eventImagesPreview.removeChild(container);
                updateInputFiles();
            });
        });

        updateInputFiles();  // atualiza input com arquivos selecionados
    });

    // Atualiza o input 'eventImagesInput' com os arquivos selecionados (para enviar no form)
    function updateInputFiles() {
        const dataTransfer = new DataTransfer();
        selectedFiles.forEach(file => dataTransfer.items.add(file));
        eventImagesInput.files = dataTransfer.files;
    }
});
