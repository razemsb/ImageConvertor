document.addEventListener('DOMContentLoaded', () => {
    const dropZone = document.getElementById('dropZone');
    const fileInput = document.getElementById('fileInput');
    const preview = document.getElementById('preview');
    const progressBar = document.getElementById('progressBar');
    const progressBarInner = progressBar.querySelector('div > div');
    const qualitySlider = document.getElementById('quality');
    const qualityValue = document.querySelector('.quality-value');

    // Обновление значения качества
    qualitySlider.addEventListener('input', function() {
        qualityValue.textContent = this.value + '%';
    });

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

        progressBar.classList.remove('hidden');
        let processed = 0;

        Array.from(files).forEach((file, index) => {
            if (!file.type.startsWith('image/')) {
                showError(`Файл ${file.name} не является изображением`);
                return;
            }

            const formData = new FormData();
            formData.append('image', file);
            formData.append('format', document.getElementById('format').value);
            formData.append('quality', document.getElementById('quality').value);

            const previewItem = createPreviewItem(file);
            preview.appendChild(previewItem);

            fetch('convert.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    updatePreviewStatus(previewItem, 'success', 'Конвертировано', data.path);
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
        div.className = 'preview-item flex items-center p-4 bg-white rounded-xl shadow-sm border border-gray-200';
        div.innerHTML = `
            <div class="flex-shrink-0 w-20 h-20">
                <img src="${URL.createObjectURL(file)}" alt="${file.name}" class="w-full h-full object-cover rounded-lg">
            </div>
            <div class="ml-4 flex-grow">
                <h5 class="text-sm font-medium text-gray-900">${file.name}</h5>
                <p class="text-sm text-gray-500">${formatFileSize(file.size)}</p>
            </div>
            <div class="ml-4 flex items-center space-x-3">
                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                    <i class="fas fa-clock mr-1"></i>
                    Ожидание
                </span>
            </div>
        `;
        return div;
    }

    function updatePreviewStatus(previewItem, status, message, filePath) {
        const statusContainer = previewItem.querySelector('.ml-4.flex.items-center');
        const icon = status === 'success' ? 'check-circle' : 'exclamation-circle';
        const color = status === 'success' ? 'green' : 'red';
        
        let statusHTML = `
            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-${color}-100 text-${color}-800">
                <i class="fas fa-${icon} mr-1"></i>${message}
            </span>
        `;

        if (status === 'success' && filePath) {
            statusHTML += `
                <button onclick="window.open('${filePath}', '_blank')" class="inline-flex items-center px-3 py-1 rounded-lg text-xs font-medium bg-blue-100 text-blue-800 hover:bg-blue-200 transition-colors duration-200">
                    <i class="fas fa-folder-open mr-1"></i>
                    Открыть
                </button>
            `;
        }

        statusContainer.innerHTML = statusHTML;
    }

    function updateProgress(processed, total) {
        const percentage = (processed / total) * 100;
        progressBarInner.style.width = `${percentage}%`;

        if (processed === total) {
            setTimeout(() => {
                progressBar.classList.add('hidden');
                progressBarInner.style.width = '0%';
            }, 1000);
        }
    }

    function showError(message) {
        const alert = document.createElement('div');
        alert.className = 'p-4 mb-4 text-sm text-red-700 bg-red-100 rounded-lg flex items-center';
        alert.innerHTML = `
            <i class="fas fa-exclamation-circle mr-2"></i>
            ${message}
        `;
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