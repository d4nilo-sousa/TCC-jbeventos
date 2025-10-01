document.addEventListener('DOMContentLoaded', function () {
    const eventImageInput = document.getElementById('event_image');  
    const eventImagePreview = document.getElementById('event_image_preview');  
    let selectedCover = null;  

    const eventImagesInput = document.getElementById('event_images');  
    const eventImagesPreview = document.getElementById('event_images_preview');  
    let selectedFiles = [];  

    const existingImages = Array.from(document.querySelectorAll('#event_images_preview > div[data-filename]'))
        .map(div => div.dataset.filename.toLowerCase());

    function createFileItem(file, container, onRemove) {
        const fileDiv = document.createElement('div');
        fileDiv.style.display = "flex";
        fileDiv.style.alignItems = "center";
        fileDiv.style.gap = "10px"; // espaço entre input e botão

        const fileNameInput = document.createElement('input');
        fileNameInput.type = 'text';
        fileNameInput.value = file.name;
        fileNameInput.readOnly = true;
        fileNameInput.style.cursor = 'default';

        const removeBtn = document.createElement('button');
        removeBtn.type = 'button';
        removeBtn.textContent = 'Excluir';

        // estilo do botão azul
        removeBtn.style.backgroundColor = "#007BFF";
        removeBtn.style.color = "#fff";
        removeBtn.style.border = "none";
        removeBtn.style.padding = "5px 10px";
        removeBtn.style.borderRadius = "4px";
        removeBtn.style.cursor = "pointer";

        removeBtn.addEventListener('mouseenter', () => {
            removeBtn.style.backgroundColor = "#0056b3"; // hover mais escuro
        });
        removeBtn.addEventListener('mouseleave', () => {
            removeBtn.style.backgroundColor = "#007BFF";
        });

        removeBtn.addEventListener('click', () => {
            onRemove(file, fileDiv);
        });

        fileDiv.appendChild(fileNameInput);
        fileDiv.appendChild(removeBtn);
        container.appendChild(fileDiv);
    }

    eventImageInput.addEventListener('change', (e) => {
        const file = e.target.files[0];
        if (!file) return;

        selectedCover = file;
        eventImagePreview.innerHTML = '';
        createFileItem(file, eventImagePreview, () => {
            selectedCover = null;
            eventImagePreview.innerHTML = '';
            eventImageInput.value = '';
        });
    });

    eventImagesInput.addEventListener('change', (e) => {
        const files = Array.from(e.target.files);

        files.forEach(file => {
            const fileName = file.name.toLowerCase();

            const alreadySelected = selectedFiles.some(f => f.name.toLowerCase() === fileName);
            const alreadyExists = existingImages.includes(fileName);

            if (alreadySelected || alreadyExists) {
                alert(`A imagem "${file.name}" já foi adicionada.`);
                return;
            }

            selectedFiles.push(file);

            createFileItem(file, eventImagesPreview, (f, fileDiv) => {
                selectedFiles = selectedFiles.filter(item => item !== f);
                eventImagesPreview.removeChild(fileDiv);
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
