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
    const courseIconPreview = document.getElementById('course_icon_preview');

    const courseBannerInput = document.getElementById('course_banner');
    const courseBannerPreview = document.getElementById('course_banner_preview');

    // ==== FUNÇÃO GENÉRICA PARA CRIAR ITENS (NOVO UPLOAD) - NENHUMA MUDANÇA ====
    function createFileItem(file, container, onRemove) {
        const fileDiv = document.createElement('div');
        fileDiv.style.display = "flex";
        fileDiv.style.alignItems = "flex-start"; 
        fileDiv.style.gap = "8px";
        fileDiv.style.marginBottom = "6px";

        const fileNameInput = document.createElement('input');
        fileNameInput.type = 'text';
        fileNameInput.value = file.name;
        fileNameInput.readOnly = true;
        fileNameInput.style.cursor = 'default';
        fileNameInput.style.width = "150px";
        fileNameInput.style.fontSize = "13px";
        fileNameInput.style.padding = "3px 5px";
        fileNameInput.style.border = "1px solid #ccc";
        fileNameInput.style.borderRadius = "4px";

        const removeBtn = document.createElement('button');
        removeBtn.type = 'button';
        removeBtn.textContent = 'Excluir';
        removeBtn.style.backgroundColor = "#dc2626"; 
        removeBtn.style.color = "#fff";
        removeBtn.style.border = "none";
        removeBtn.style.padding = "6px 14px"; 
        removeBtn.style.fontSize = "13px";
        removeBtn.style.fontWeight = "bold"; 
        removeBtn.style.borderRadius = "4px";
        removeBtn.style.cursor = "pointer";
        removeBtn.style.transition = "background-color 0.2s ease";

        removeBtn.addEventListener('mouseenter', () => removeBtn.style.backgroundColor = "#b91c1c");
        removeBtn.addEventListener('mouseleave', () => removeBtn.style.backgroundColor = "#dc2626");

        removeBtn.addEventListener('click', () => onRemove(file, fileDiv));

        fileDiv.appendChild(fileNameInput);
        fileDiv.appendChild(removeBtn);

        container.style.display = "flex";
        container.style.flexWrap = "wrap";
        container.style.gap = "16px";
        container.appendChild(fileDiv);
    }
    
    // ==== FUNÇÃO AUXILIAR PARA CRIAR ITENS EXISTENTES (COM ID) - NENHUMA MUDANÇA NECESSÁRIA AQUI ====
    function createExistingFileItem(id, filename, container, type) {
        const fileDiv = document.createElement('div');
        fileDiv.setAttribute('data-id', id); // Mantém o ID no DOM para exclusão
        fileDiv.style.display = "flex";
        fileDiv.style.alignItems = "flex-start";
        fileDiv.style.gap = "8px";
        fileDiv.style.marginBottom = "6px";

        const fileNameInput = document.createElement('input');
        fileNameInput.type = 'text';
        fileNameInput.value = `[Existente] ${filename}`; 
        fileNameInput.readOnly = true;
        fileNameInput.style.cursor = 'default';
        fileNameInput.style.width = "150px";
        fileNameInput.style.fontSize = "13px";
        fileNameInput.style.padding = "3px 5px";
        fileNameInput.style.border = "1px solid #ccc";
        fileNameInput.style.borderRadius = "4px";
        fileNameInput.style.backgroundColor = "#f0fdf4"; 

        const removeBtn = document.createElement('button');
        removeBtn.type = 'button';
        removeBtn.textContent = 'Excluir';
        removeBtn.style.backgroundColor = "#dc2626"; 
        removeBtn.style.color = "#fff";
        removeBtn.style.border = "none";
        removeBtn.style.padding = "6px 14px"; 
        removeBtn.style.fontSize = "13px";
        removeBtn.style.fontWeight = "bold"; 
        removeBtn.style.borderRadius = "4px";
        removeBtn.style.cursor = "pointer";
        removeBtn.style.transition = "background-color 0.2s ease";

        removeBtn.addEventListener('mouseenter', () => removeBtn.style.backgroundColor = "#b91c1c");
        removeBtn.addEventListener('mouseleave', () => removeBtn.style.backgroundColor = "#dc2626");

        // ⭐ CHAMA A FUNÇÃO AJAX DE EXCLUSÃO (window.deleteImage)
        removeBtn.addEventListener('click', () => window.deleteImage(id, removeBtn, type)); 

        fileDiv.appendChild(fileNameInput);
        fileDiv.appendChild(removeBtn);

        container.style.display = "flex";
        container.style.flexWrap = "wrap";
        container.style.gap = "16px";
        container.appendChild(fileDiv);
    }

    // ==== EVENTOS: CAPA (NOVO UPLOAD) ====
    // ... (Mantido sem alteração) ...
    if (eventImageInput) {
        eventImageInput.addEventListener('change', (e) => {
            const file = e.target.files[0];
            if (!file) return;
            
            // Remove o preview existente (que agora é criado via JS)
            eventImagePreview.innerHTML = '';

            selectedCover = file;
            createFileItem(file, eventImagePreview, (f, fileDiv) => {
                selectedCover = null;
                eventImagePreview.innerHTML = '';
                eventImageInput.value = '';
            });
        });
    }

    // ==== EVENTOS: IMAGENS MÚLTIPLAS (GALERIA - NOVO UPLOAD) ====
    // ... (Mantido sem alteração) ...
    if (eventImagesInput) {
        eventImagesInput.addEventListener('change', (e) => {
            const files = Array.from(e.target.files);

            files.forEach(file => {
                const fileName = file.name.toLowerCase();

                // Verifica duplicatas em novos uploads
                const alreadySelected = selectedFiles.some(f => f.name.toLowerCase() === fileName);
                
                // Verifica duplicatas em arquivos já existentes no DOM (que não foram removidos)
                const existingItems = Array.from(eventImagesPreview.querySelectorAll('input[value]'));
                const alreadyExists = existingItems.some(input => input.value.toLowerCase().includes(fileName));

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

    // ==== CURSOS: ÍCONE (NOVO UPLOAD) ====
    // ... (Mantido sem alteração) ...
    if (courseIconInput) {
        courseIconInput.addEventListener('change', (e) => {
            const file = e.target.files[0];
            if (!file) return;

            // Remove o preview anterior (existente ou novo)
            courseIconPreview.innerHTML = '';
            
            // Cria o preview do novo arquivo
            createFileItem(file, courseIconPreview, () => {
                courseIconPreview.innerHTML = '';
                courseIconInput.value = '';
            });
        });
    }

    // ==== CURSOS: BANNER (NOVO UPLOAD) ====
    // ... (Mantido sem alteração) ...
    if (courseBannerInput) {
        courseBannerInput.addEventListener('change', (e) => {
            const file = e.target.files[0];
            if (!file) return;

            // Remove o preview anterior (existente ou novo)
            courseBannerPreview.innerHTML = '';

            // Cria o preview do novo arquivo
            createFileItem(file, courseBannerPreview, () => {
                courseBannerPreview.innerHTML = '';
                courseBannerInput.value = '';
            });
        });
    }
    
    // -------------------------------------------------------------------
    // ⭐ CÓDIGO PARA RENDERIZAR IMAGENS EXISTENTES (ON LOAD) - ATUALIZADO ⭐
    // -------------------------------------------------------------------
    
    function initializeExistingImages() {
        // 1. Renderizar Capa Existente (type: 'event_cover' - ATUALIZADO)
        const existingCoverContainer = document.getElementById('existing-event_image-preview');
        if (existingCoverContainer && eventImagePreview) {
            const id = existingCoverContainer.dataset.fileId; // Deve ser o ID do Evento!
            const filename = existingCoverContainer.dataset.filename;
            
            if (id && filename) {
                // AGORA PASSA 'event_cover' e o ID do Evento
                createExistingFileItem(id, filename, eventImagePreview, 'event_cover');
            }
            existingCoverContainer.remove(); 
        }
        
        // 2. Renderizar Imagens da Galeria Existente (type: 'event')
        const existingGalleryContainer = document.getElementById('existing-event_images-gallery');
        if (existingGalleryContainer && eventImagesPreview) {
            const existingItems = Array.from(existingGalleryContainer.querySelectorAll('div[data-id][data-filename]'));
            
            existingItems.forEach(item => {
                const id = item.dataset.id; // ID da EventImage
                const filename = item.dataset.filename;
                
                if (id && filename) {
                    // Mantém 'event' para a galeria (EventImage)
                    createExistingFileItem(id, filename, eventImagesPreview, 'event');
                }
            });
            
            existingGalleryContainer.remove(); 
        }

        // 3. Renderizar Ícone do Curso Existente (type: 'icon')
        const existingIconContainer = document.getElementById('existing-course_icon-preview');
        if (existingIconContainer && courseIconPreview) {
            const id = existingIconContainer.dataset.fileId; // ID do Curso!
            const filename = existingIconContainer.dataset.filename;
            
            if (id && filename) {
                createExistingFileItem(id, filename, courseIconPreview, 'icon');
            }
            existingIconContainer.remove(); 
        }

        // 4. Renderizar Banner do Curso Existente (type: 'banner')
        const existingBannerContainer = document.getElementById('existing-course_banner-preview');
        if (existingBannerContainer && courseBannerPreview) {
            const id = existingBannerContainer.dataset.fileId; // ID do Curso!
            const filename = existingBannerContainer.dataset.filename;
            
            if (id && filename) {
                createExistingFileItem(id, filename, courseBannerPreview, 'banner');
            }
            existingBannerContainer.remove(); 
        }
    }
    
    initializeExistingImages();
    // -------------------------------------------------------------------
});