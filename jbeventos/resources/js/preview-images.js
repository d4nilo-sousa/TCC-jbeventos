document.addEventListener('DOMContentLoaded', function () {
    // ==== EVENTOS ====
    const eventImageInput = document.getElementById('event_image');
    const eventImagePreview = document.getElementById('event_image_preview');
    let selectedCover = null;

    const eventImagesInput = document.getElementById('event_images');
    const eventImagesPreview = document.getElementById('event_images_preview');
    let selectedFiles = [];

    // ==== CURSOS ====
    const courseIconInput = document.getElementById('course_icon');
    // CORRIGIDO: O ID correto é 'course_icon_preview' (sem 's')
    const courseIconPreview = document.getElementById('course_icon_preview'); 

    const courseBannerInput = document.getElementById('course_banner');
    // CORRIGIDO: O ID correto é 'course_banner_preview' (sem 's')
    const courseBannerPreview = document.getElementById('course_banner_preview'); 

    // ==== FUNÇÃO GENÉRICA PARA CRIAR ITENS ====
    function createFileItem(file, container, onRemove) {
        const fileDiv = document.createElement('div');
        fileDiv.style.display = "flex";
        fileDiv.style.alignItems = "center";
        fileDiv.style.gap = "10px";

        const fileNameInput = document.createElement('input');
        fileNameInput.type = 'text';
        fileNameInput.value = file.name;
        fileNameInput.readOnly = true;
        fileNameInput.style.cursor = 'default';

        const removeBtn = document.createElement('button');
        removeBtn.type = 'button';
        removeBtn.textContent = 'Excluir';
        removeBtn.style.backgroundColor = "#007BFF";
        removeBtn.style.color = "#fff";
        removeBtn.style.border = "none";
        removeBtn.style.padding = "5px 10px";
        removeBtn.style.borderRadius = "4px";
        removeBtn.style.cursor = "pointer";

        removeBtn.addEventListener('mouseenter', () => removeBtn.style.backgroundColor = "#0056b3");
        removeBtn.addEventListener('mouseleave', () => removeBtn.style.backgroundColor = "#007BFF");

        removeBtn.addEventListener('click', () => onRemove(file, fileDiv));

        fileDiv.appendChild(fileNameInput);
        fileDiv.appendChild(removeBtn);

        container.appendChild(fileDiv);
    }

    // ==== EVENTOS: CAPA ====
    if (eventImageInput) {
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
    }

    // ==== EVENTOS: IMAGENS MÚLTIPLAS (GALERIA) ====
    if (eventImagesInput) {
        eventImagesInput.addEventListener('change', (e) => {
            const files = Array.from(e.target.files);

            files.forEach(file => {
                const fileName = file.name.toLowerCase();

                // Verifica duplicatas
                const alreadySelected = selectedFiles.some(f => f.name.toLowerCase() === fileName);
                const alreadyExists = Array.from(eventImagesPreview.querySelectorAll('div[data-filename]'))
                    .some(div => div.dataset.filename.toLowerCase() === fileName);

                if (alreadySelected || alreadyExists) {
                    alert(`A imagem "${file.name}" já foi adicionada.`);
                    return;
                }

                selectedFiles.push(file);

                createFileItem(file, eventImagesPreview, (f, fileDiv) => {
                    selectedFiles = selectedFiles.filter(item => item !== f);
                    fileDiv.remove();
                    updateInputFiles();
                });
            });

            updateInputFiles();
        });
    }

    function updateInputFiles() {
        const dataTransfer = new DataTransfer();
        selectedFiles.forEach(file => dataTransfer.items.add(file));
        eventImagesInput.files = dataTransfer.files;
    }

    // ==== FUNÇÃO PARA EXCLUIR IMAGENS EXISTENTES (EVENTOS) ====
    window.deleteExistingImage = function(id, button) {
        const container = button.closest('div[data-id]');
        if (!container) return;

        container.remove();

        // Cria um hidden para avisar o backend que a imagem deve ser removida
        const form = document.querySelector('form');
        const removedInput = document.createElement('input');
        removedInput.type = 'hidden';
        removedInput.name = 'remove_event_images[]';
        removedInput.value = id;
        form.appendChild(removedInput);
    };

    // ==== CURSOS: ÍCONE ====
    if (courseIconInput) {
        courseIconInput.addEventListener('change', (e) => {
            const file = e.target.files[0];
            if (!file) return;

            // 1. Remove o preview anterior (existente ou novo)
            courseIconPreview.innerHTML = '';
            
            // 2. Garante que o input hidden 'remove_course_icon' é resetado
            document.querySelector('input[name="remove_course_icon"]').value = 0;

            // 3. Cria o preview do novo arquivo
            createFileItem(file, courseIconPreview, () => {
                courseIconPreview.innerHTML = '';
                courseIconInput.value = '';
            });
        });
    }

    // ==== CURSOS: BANNER ====
    if (courseBannerInput) {
        courseBannerInput.addEventListener('change', (e) => {
            const file = e.target.files[0];
            if (!file) return;

            // 1. Remove o preview anterior (existente ou novo)
            courseBannerPreview.innerHTML = '';
            
            // 2. Garante que o input hidden 'remove_course_banner' é resetado
            document.querySelector('input[name="remove_course_banner"]').value = 0;

            // 3. Cria o preview do novo arquivo
            createFileItem(file, courseBannerPreview, () => {
                courseBannerPreview.innerHTML = '';
                courseBannerInput.value = '';
            });
        });
    }
});