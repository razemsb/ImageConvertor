document.addEventListener('DOMContentLoaded', function() {
    const dropZone = document.getElementById('dropZone');
    const fileInput = document.getElementById('fileInput');
    const preview = document.getElementById('preview');
    const formatSelect = document.getElementById('format');
    const qualitySlider = document.getElementById('quality');
    const qualityValue = document.querySelector('.quality-value');
    const progressBar = document.getElementById('progressBar');
    const progressBarInner = progressBar.querySelector('div');
    const historyContainer = document.getElementById('history');

    // Загрузка истории конвертаций
    let conversionHistory = JSON.parse(localStorage.getItem('conversionHistory') || '[]');

    // Отображение истории при загрузке страницы
    displayHistory();

    // Функция отображения истории
    function displayHistory() {
        historyContainer.innerHTML = '';
        
        if (conversionHistory.length === 0) {
            historyContainer.innerHTML = `
                <div class="text-center text-gray-500 py-8">
                    <i class="fas fa-history text-4xl mb-4"></i>
                    <p>История конвертаций пуста</p>
                </div>
            `;
            return;
        }

        conversionHistory.forEach((item, index) => {
            const historyItem = document.createElement('div');
            historyItem.className = 'history-item glass-effect p-4 rounded-xl flex items-center justify-between';
            
            const date = new Date(item.timestamp * 1000);
            const formattedDate = date.toLocaleString('ru-RU', {
                day: '2-digit',
                month: '2-digit',
                year: 'numeric',
                hour: '2-digit',
                minute: '2-digit'
            });

            historyItem.innerHTML = `
                <div class="flex items-center space-x-4">
                    <div class="w-12 h-12 bg-gray-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-file-image text-blue-500 text-xl"></i>
                    </div>
                    <div>
                        <h3 class="font-medium text-gray-800">${item.originalName}</h3>
                        <p class="text-sm text-gray-500">
                            Конвертировано в ${item.format.toUpperCase()} (качество: ${item.quality}%)
                        </p>
                        <p class="text-xs text-gray-400">${formattedDate}</p>
                    </div>
                </div>
                <div class="flex items-center space-x-2">
                    <a href="${item.path}" target="_blank" class="text-blue-500 hover:text-blue-600 p-2 rounded-lg hover:bg-blue-50">
                        <i class="fas fa-download"></i>
                    </a>
                    <button onclick="deleteHistoryItem(${index})" class="text-red-500 hover:text-red-600 p-2 rounded-lg hover:bg-red-50">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            `;

            historyContainer.appendChild(historyItem);
        });
    }

    // Функция удаления элемента из истории
    window.deleteHistoryItem = function(index) {
        conversionHistory.splice(index, 1);
        localStorage.setItem('conversionHistory', JSON.stringify(conversionHistory));
        displayHistory();
    };

    // Анимация при наведении на зону загрузки
    dropZone.addEventListener('mouseenter', () => {
        dropZone.style.transform = 'translateY(-2px)';
    });

    dropZone.addEventListener('mouseleave', () => {
        dropZone.style.transform = 'translateY(0)';
    });

    // Обработка перетаскивания файлов
    ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
        dropZone.addEventListener(eventName, preventDefaults, false);
    });

    function preventDefaults(e) {
        e.preventDefault();
        e.stopPropagation();
    }

    ['dragenter', 'dragover'].forEach(eventName => {
        dropZone.addEventListener(eventName, highlight, false);
    });

    ['dragleave', 'drop'].forEach(eventName => {
        dropZone.addEventListener(eventName, unhighlight, false);
    });

    function highlight(e) {
        dropZone.classList.add('dragover');
    }

    function unhighlight(e) {
        dropZone.classList.remove('dragover');
    }

    // Обработка выбора файлов
    dropZone.addEventListener('click', () => fileInput.click());
    fileInput.addEventListener('change', handleFiles);
    dropZone.addEventListener('drop', handleDrop);

    function handleDrop(e) {
        const dt = e.dataTransfer;
        const files = dt.files;
        handleFiles({ target: { files } });
    }

    function handleFiles(e) {
        const files = [...e.target.files];
        files.forEach(file => {
            if (file.type.startsWith('image/')) {
                previewAndConvertFile(file);
            }
        });
    }

    // Предпросмотр и конвертация файлов
    function previewAndConvertFile(file) {
        const reader = new FileReader();
        reader.readAsDataURL(file);
        reader.onloadend = function() {
            const previewItem = document.createElement('div');
            previewItem.className = 'preview-item p-4';
            previewItem.innerHTML = `
                <div class="relative">
                    <img src="${reader.result}" class="w-full h-48 object-cover rounded-lg" alt="${file.name}">
                    <div class="absolute inset-0 bg-black bg-opacity-0 hover:bg-opacity-20 transition-all duration-300 rounded-lg flex items-center justify-center">
                        <div class="status-indicator text-white px-4 py-2 rounded-lg">
                            <i class="fas fa-spinner fa-spin mr-2"></i>
                            Конвертация...
                        </div>
                    </div>
                </div>
                <div class="mt-3">
                    <p class="text-sm font-medium text-gray-800">${file.name}</p>
                    <p class="text-xs text-gray-500">${(file.size / 1024).toFixed(1)} KB</p>
                </div>
            `;

            preview.appendChild(previewItem);
            
            // Анимация появления
            previewItem.style.opacity = '0';
            previewItem.style.transform = 'translateY(20px)';
            setTimeout(() => {
                previewItem.style.opacity = '1';
                previewItem.style.transform = 'translateY(0)';
            }, 100);

            // Сразу начинаем конвертацию
            convertFile(file, previewItem);
        };
    }

    // Конвертация файла
    async function convertFile(file, previewItem) {
        const formData = new FormData();
        formData.append('image', file);
        formData.append('format', formatSelect.value);
        formData.append('quality', qualitySlider.value);

        progressBar.classList.remove('hidden');
        progressBarInner.style.width = '0%';

        try {
            const response = await fetch('convert.php', {
                method: 'POST',
                body: formData
            });

            if (response.ok) {
                const data = await response.json();
                
                // Добавляем в историю
                conversionHistory.unshift({
                    originalName: data.originalName,
                    convertedName: data.filename,
                    format: data.format,
                    quality: data.quality,
                    timestamp: data.timestamp,
                    path: data.path
                });
                
                // Ограничиваем историю последними 50 конвертациями
                if (conversionHistory.length > 50) {
                    conversionHistory = conversionHistory.slice(0, 50);
                }
                
                // Сохраняем историю
                localStorage.setItem('conversionHistory', JSON.stringify(conversionHistory));
                
                // Обновляем статус в превью
                const statusIndicator = previewItem.querySelector('.status-indicator');
                statusIndicator.innerHTML = `
                    <div class="flex items-center space-x-2">
                        <i class="fas fa-check-circle text-green-500"></i>
                        <span>Конвертировано</span>
                        <a href="${data.path}" target="_blank" class="text-blue-500 hover:text-blue-600">
                            <i class="fas fa-download"></i>
                        </a>
                    </div>
                `;
                statusIndicator.classList.add('bg-green-500');

                // Анимация успешной конвертации
                previewItem.style.transform = 'scale(0.95)';
                setTimeout(() => {
                    previewItem.style.transform = 'scale(1)';
                }, 200);

                // Обновляем отображение истории
                displayHistory();
            } else {
                const errorText = await response.text();
                throw new Error(errorText || 'Ошибка конвертации');
            }
        } catch (error) {
            console.error('Ошибка:', error);
            // Обновляем статус ошибки
            const statusIndicator = previewItem.querySelector('.status-indicator');
            statusIndicator.innerHTML = `
                <i class="fas fa-exclamation-circle mr-2"></i>
                ${error.message}
            `;
            statusIndicator.classList.add('bg-red-500');
            
            // Анимация ошибки
            previewItem.style.transform = 'translateX(-10px)';
            setTimeout(() => {
                previewItem.style.transform = 'translateX(0)';
            }, 200);
        } finally {
            progressBar.classList.add('hidden');
        }
    }

    // Обновление значения качества
    qualitySlider.addEventListener('input', (e) => {
        qualityValue.textContent = `${e.target.value}%`;
    });
}); 