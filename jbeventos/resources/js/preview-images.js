document.addEventListener('DOMContentLoaded', function () {
    // --- Seleção de capa ---
    const eventImageInput = document.getElementById('event_image');
    const eventImagePreview = document.getElementById('event_image_preview');
    let selectedCover = null;

    // --- Seleção de imagens extras ---
    const eventImagesInput = document.getElementById('event_images');
    const eventImagesPreview = document.getElementById('event_images_preview');
    let selectedFiles = [];

    // Função genérica para criar preview
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

            // Botão de remover
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

    // --- Capa ---
    eventImageInput.addEventListener('change', (e) => {
        const file = e.target.files[0];
        if (!file) return;

        selectedCover = file;
        eventImagePreview.innerHTML = '';

        createImagePreview(file, eventImagePreview, () => {
            selectedCover = null;
            eventImagePreview.innerHTML = '';
            eventImageInput.value = '';
        });
    });

    // --- Imagens extras ---
    eventImagesInput.addEventListener('change', (e) => {
        const files = Array.from(e.target.files);

        files.forEach(file => {
            selectedFiles.push(file);

            createImagePreview(file, eventImagesPreview, (f, container) => {
                selectedFiles = selectedFiles.filter(item => item !== f);
                eventImagesPreview.removeChild(container);
                updateInputFiles();
            });
        });

        updateInputFiles();
    });

    function updateInputFiles() {
        const dataTransfer = new DataTransfer();
        selectedFiles.forEach(file => dataTransfer.items.add(file));
        eventImagesInput.files = dataTransfer.files;
    }
});
