const dropzoneCover = document.getElementById('dropzone-cover');
const eventImageInput = document.getElementById('event_image');

dropzoneCover.addEventListener('dragover', event => {
    event.preventDefault();
    dropzoneCover.classList.add('border-blue-500', 'bg-gray-50');
});

dropzoneCover.addEventListener('dragleave', () => {
    dropzoneCover.classList.remove('border-blue-500', 'bg-gray-50');
});

dropzoneCover.addEventListener('drop', event => {
    event.preventDefault();
    dropzoneCover.classList.remove('border-blue-500', 'bg-gray-50');
    eventImageInput.files = event.dataTransfer.files; // Apenas seta arquivos
});

dropzoneCover.addEventListener('click', () => {
    eventImageInput.click();
});

// Galeria de imagens
const dropzoneGallery = document.getElementById('dropzone-gallery');
const eventImagesInput = document.getElementById('event_images');

dropzoneGallery.addEventListener('dragover', event => {
    event.preventDefault();
    dropzoneGallery.classList.add('border-blue-500', 'bg-gray-50');
});

dropzoneGallery.addEventListener('dragleave', () => {
    dropzoneGallery.classList.remove('border-blue-500', 'bg-gray-50');
});

dropzoneGallery.addEventListener('drop', event => {
    event.preventDefault();
    dropzoneGallery.classList.remove('border-blue-500', 'bg-gray-50');
    eventImagesInput.files = event.dataTransfer.files; // Apenas seta arquivos
});

dropzoneGallery.addEventListener('click', () => {
    eventImagesInput.click();
});