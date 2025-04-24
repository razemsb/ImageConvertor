document.addEventListener('DOMContentLoaded', () => {
    const dropZone = document.getElementById('dropZone');
    const fileInput = document.getElementById('fileInput');
    const preview = document.getElementById('preview');
    const progressBar = document.getElementById('progressBar');
    const progressBarInner = progressBar.querySelector('.progress-bar');

    // Обработка клика по зоне загрузки
    dropZone.addEventListener('click', () => {
        fileInput.click();
    });

    // Обработка выбора файлов
    fileInput.addEventListener('change', handleFiles);

    // Обработка drag & drop
    dropZone.addEventListener('dragover', (e) => {
        e.preventDefault();
        dropZone.classList.add('dragover');
    });

    dropZone.addEventListener('dragleave', () => {
        dropZone.classList.remove('dragover');
    });

    dropZone.addEventListener('drop', (e) => {
        e.preventDefault();
        dropZone.classList.remove('dragover');
        handleFiles({ target: { files: e.dataTransfer.files } });
    });

    function handleFiles(e) {
        const files = e.target.files;
        if (files.length === 0) return;

        progressBar.classList.remove('d-none');
        let processed = 0;

        Array.from(files).forEach((file, index) => {
            if (!file.type.startsWith('image/')) {
                showError(`Файл ${file.name} не является изображением`);
                return;
            }

            const formData = new FormData();
            formData.append('image', file);

            const previewItem = createPreviewItem(file);
            preview.appendChild(previewItem);

            fetch('convert.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    updatePreviewStatus(previewItem, 'success', 'Конвертировано');
                    processed++;
                    updateProgress(processed, files.length);
                } else {
                    updatePreviewStatus(previewItem, 'error', data.error);
                }
            })
            .catch(error => {
                updatePreviewStatus(previewItem, 'error', 'Ошибка при конвертации');
                console.error('Error:', error);
            });
        });
    }

    function createPreviewItem(file) {
        const div = document.createElement('div');
        div.className = 'preview-item';
        div.innerHTML = `
            <img src="${URL.createObjectURL(file)}" alt="${file.name}">
            <div class="preview-info">
                <h5>${file.name}</h5>
                <p class="text-muted">${formatFileSize(file.size)}</p>
            </div>
            <div class="preview-status">
                <span class="badge bg-secondary">Ожидание</span>
            </div>
        `;
        return div;
    }

    function updatePreviewStatus(previewItem, status, message) {
        const statusBadge = previewItem.querySelector('.preview-status .badge');
        statusBadge.className = `badge bg-${status === 'success' ? 'success' : 'danger'}`;
        statusBadge.textContent = message;
    }

    function updateProgress(processed, total) {
        const percentage = (processed / total) * 100;
        progressBarInner.style.width = `${percentage}%`;
        progressBarInner.setAttribute('aria-valuenow', percentage);

        if (processed === total) {
            setTimeout(() => {
                progressBar.classList.add('d-none');
                progressBarInner.style.width = '0%';
            }, 1000);
        }
    }

    function showError(message) {
        const alert = document.createElement('div');
        alert.className = 'alert alert-danger mt-3';
        alert.textContent = message;
        preview.appendChild(alert);
        setTimeout(() => alert.remove(), 5000);
    }

    function formatFileSize(bytes) {
        if (bytes === 0) return '0 Bytes';
        const k = 1024;
        const sizes = ['Bytes', 'KB', 'MB', 'GB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
    }
}); 