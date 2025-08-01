document.addEventListener('DOMContentLoaded', function () {
    const dropZone = document.getElementById('dropZone');
    const fileInput = document.getElementById('fileInput');
    const preview = document.getElementById('preview');
    const qualitySlider = document.getElementById('quality');
    const qualityValue = document.querySelector('.quality-value');
    const progressBar = document.getElementById('progressBar');
    const progressBarInner = progressBar.querySelector('div');
    const historyContainer = document.getElementById('history');
    const btn = document.getElementById('accountButton');
    const dropdown = document.getElementById('accountDropdown');
    const chevron = document.getElementById('accountChevron');
    const LogoutBtn = document.getElementById('LogoutBtn');
    const deleteHistory = document.getElementById('deleteHistory');
    const deleteBtn = document.getElementById('deleteHistory');

    const scrollToTopBtn = document.getElementById('scrollToTopBtn');
    const scrollThreshold = 200;

    window.addEventListener('scroll', function () {
        if (window.pageYOffset > scrollThreshold) {
            scrollToTopBtn.classList.add('visible');
        } else {
            scrollToTopBtn.classList.remove('visible');
        }
    });

    scrollToTopBtn.addEventListener('click', function () {
        window.scrollTo({
            top: 0,
            behavior: 'smooth'
        });
    });


    const now = new Date();
    const hours = now.getHours().toString().padStart(2, '0');
    const minutes = now.getMinutes().toString().padStart(2, '0');
    const currentTime = `${hours}:${minutes}`;

    function showPopUp(type = 'info', message = 'Сообщение', time = '') {
        const containerId = 'popup-container';
        let container = document.getElementById(containerId);
        if (!container) {
            container = document.createElement('div');
            container.id = containerId;
            container.className = 'popup-container';
            document.body.appendChild(container);
        }

        const icons = {
            success: '<i class="fa-solid fa-thumbs-up"></i>',
            error: '<i class="fa-solid fa-circle-exclamation"></i>',
            info: '<i class="fa-solid fa-circle-info"></i>'
        };

        const popup = document.createElement('div');
        popup.className = `popup ${type}`;
        popup.innerHTML = `
            <div class="popup-icon">${icons[type] || 'ℹ️'}</div>
            <div class="popup-content">
                <div class="popup-message">${message}</div>
                <div class="popup-time">${time}</div>
            </div>
        `;

        container.appendChild(popup);

        setTimeout(() => {
            popup.classList.add('hide');
            popup.addEventListener('transitionend', () => popup.remove());
        }, 2500);
    }

    function getSelectedFormat() {
        const selectedFormat = document.querySelector('input[name="format"]:checked');
        return selectedFormat ? selectedFormat.value : 'webp';
    }

    function clearPreview() {
        const preview = document.getElementById('preview');
        if (preview) {

            const items = preview.querySelectorAll('.preview-item');
            items.forEach(item => {
                item.style.transition = 'opacity 0.3s ease, transform 0.3s ease';
                item.style.opacity = '0';
                item.style.transform = 'translateY(20px)';
                setTimeout(() => item.remove(), 300);
            });
        }
    }

    if (LogoutBtn) {
        LogoutBtn.addEventListener('click', function () {
            fetch('./config/logout.php', {
                method: 'POST',
                credentials: 'include'
            })
                .then(res => {
                    if (res.ok) {
                        window.location.reload();
                    } else {
                        alert('Ошибка при выходе из аккаунта');
                    }
                })
                .catch(() => alert('Ошибка сети'));
        });
    }

    if (btn && dropdown) {
        btn.addEventListener('click', () => {
            const isOpen = dropdown.classList.contains('opacity-100');

            if (isOpen) {
                dropdown.classList.remove('opacity-100', 'pointer-events-auto', 'scale-100');
                dropdown.classList.add('opacity-0', 'pointer-events-none', 'scale-95');
                chevron.style.transform = 'rotate(0deg)';
                document.body.classList.remove('overflow-hidden');
            } else {
                dropdown.classList.add('opacity-100', 'pointer-events-auto', 'scale-100');
                dropdown.classList.remove('opacity-0', 'pointer-events-none', 'scale-95');
                chevron.style.transform = 'rotate(180deg)';
                document.body.classList.add('overflow-hidden');
            }
        });

        document.addEventListener('click', (e) => {
            if (!btn.contains(e.target) && !dropdown.contains(e.target)) {
                dropdown.classList.remove('opacity-100', 'pointer-events-auto', 'scale-100');
                dropdown.classList.add('opacity-0', 'pointer-events-none', 'scale-95');
                chevron.style.transform = 'rotate(0deg)';
                document.body.classList.remove('overflow-hidden');
            }
        });
    }

    window.addEventListener('scroll', function() {
        const toolbar = document.getElementById('toolbar');
        if (window.scrollY > 10) {
            toolbar.classList.add('shadow-md', 'bg-white/100');
            toolbar.classList.remove('bg-white/95');
        } else {
            toolbar.classList.remove('shadow-md', 'bg-white/100');
            toolbar.classList.add('bg-white/95');
        }
    });

    const accountButton = document.getElementById('accountButton');
    const accountDropdown = document.getElementById('accountDropdown');
    const accountChevron = document.getElementById('accountChevron');

    if (accountButton && accountDropdown) {
        accountButton.addEventListener('click', function() {
            const isOpen = accountDropdown.classList.contains('opacity-0');
            
            accountDropdown.classList.toggle('opacity-0', !isOpen);
            accountDropdown.classList.toggle('scale-95', !isOpen);
            accountDropdown.classList.toggle('pointer-events-none', !isOpen);
            accountChevron.classList.toggle('transform', isOpen);
            accountChevron.classList.toggle('rotate-180', isOpen);
        });
    }


    let conversionHistory = [];

    try {
        const storedHistory = localStorage.getItem('conversionHistory');
        if (storedHistory) {
            if (storedHistory.trim().startsWith('[') || storedHistory.trim().startsWith('{')) {
                conversionHistory = JSON.parse(storedHistory);
                if (!Array.isArray(conversionHistory)) {
                    console.warn('История не является массивом, сбрасываем');
                    conversionHistory = [];
                    localStorage.removeItem('conversionHistory');
                }
            } else {
                console.warn('Некорректный формат истории, сбрасываем');
                localStorage.removeItem('conversionHistory');
            }
        }
    } catch (e) {
        console.error('Ошибка загрузки истории:', e);
        localStorage.removeItem('conversionHistory');
        conversionHistory = [];
    }

    displayHistory();

    function showDeleteConfirmationModal() {

        const modal = document.createElement('div');
        modal.id = 'confirmDeleteModal';
        modal.className = 'fixed inset-0 bg-black bg-opacity-30 backdrop-blur-sm flex items-center justify-center z-50 p-4 transition-opacity duration-300 opacity-0';
        modal.innerHTML = `
            <div class="bg-white rounded-xl shadow-2xl max-w-md w-full border border-gray-200 transform transition-all duration-300 scale-95">
                <div class="p-6">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="p-3 rounded-full bg-red-100 text-red-500">
                            <i class="fas fa-exclamation-circle text-xl"></i>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-800">Подтвердите действие</h3>
                    </div>
                    <p class="text-gray-600 mb-6">Вы собираетесь удалить всю историю конвертаций. Это действие невозможно отменить.</p>
                    
                    <div class="flex justify-end gap-3">
                        <button id="cancelDelete" class="px-4 py-2 rounded-lg bg-gray-100 text-gray-700 hover:bg-gray-200 transition-colors duration-200 ease-in-out">
                            <i class="fas fa-times mr-2"></i> Отменить
                        </button>
                        <button id="confirmDelete" class="px-4 py-2 rounded-lg bg-red-500 text-white hover:bg-red-600 transition-colors duration-200 ease-in-out transform hover:scale-[1.02]">
                            <i class="fas fa-trash-alt mr-2"></i> Удалить
                        </button>
                    </div>
                </div>
            </div>
        `;

        document.body.appendChild(modal);
        document.body.style.overflow = 'hidden';


        setTimeout(() => {
            modal.classList.remove('opacity-0');
            modal.querySelector('div').classList.remove('scale-95');
        }, 10);


        document.getElementById('cancelDelete').addEventListener('click', () => {
            modal.classList.add('opacity-0');
            modal.querySelector('div').classList.add('scale-95');
            setTimeout(() => {
                document.body.removeChild(modal);
                document.body.style.overflow = '';
            }, 300);
        });

        document.getElementById('confirmDelete').addEventListener('click', async () => {

            modal.classList.add('opacity-0');
            modal.querySelector('div').classList.add('scale-95');
            await new Promise(resolve => setTimeout(resolve, 300));
            localStorage.removeItem('conversionHistory');
            conversionHistory = [];
            document.body.removeChild(modal);
            document.body.style.overflow = '';
            updateUIAfterDeletion();

            showPopUp('info', 'Ваша история конвертаций успешно удалена!', currentTime);
        });

        function updateUIAfterDeletion() {
            const deleteHistory = document.getElementById('deleteHistory');
            if (deleteHistory) deleteHistory.style.display = 'none';
            const historyList = document.getElementById('history');
            if (historyList) historyList.innerHTML = `
            <div class="text-center text-gray-500 py-8">
                    <i class="fas fa-history text-4xl mb-4"></i>
                    <p>История конвертаций пуста</p>
            </div>
            `;
            clearPreview();
            updateHistoryDependentElements();
        }

    }

    if (deleteHistory) {
        deleteHistory.addEventListener('click', function (e) {
            e.preventDefault();
            showDeleteConfirmationModal();
        });


        deleteHistory.classList.add(
            'transition-colors',
            'duration-200',
            'ease-in-out',
            'transform',
            'hover:scale-[1.02]'
        );
    }

    function displayHistory() {
        historyContainer.innerHTML = '';
        const maxVisible = 5;
        let isExpanded = false;

        const existingBtn = document.getElementById('toggleHistoryBtn');
        if (existingBtn) existingBtn.remove();

        if (conversionHistory.length === 0) {
            deleteBtn.style.display = "none";
            historyContainer.innerHTML = `
                <div class="text-center text-gray-500 py-8">
                    <i class="fas fa-history text-4xl mb-4"></i>
                    <p>История конвертаций пуста</p>
                </div>
            `;
            return;
        }

        deleteBtn.style.display = "block";

        conversionHistory.forEach((item, index) => {
            const historyItem = document.createElement('div');
            historyItem.className = 'history-item glass-effect p-4 rounded-xl flex items-center justify-between transition-all duration-300';
            if (index >= maxVisible) historyItem.classList.add('hidden');
            historyItem.dataset.index = index;

            const date = new Date(item.timestamp * 1000);
            const formattedDate = date.toLocaleString('ru-RU', {
                day: '2-digit', month: '2-digit', year: 'numeric', hour: '2-digit', minute: '2-digit'
            });

            historyItem.innerHTML = `
                <div class="flex items-center space-x-4">
                    <div class="w-12 h-12 bg-gray-100 rounded-lg flex items-center justify-center overflow-hidden">
                        <img src="./converted/${item.convertedName}" class="object-cover w-full h-full" loading="lazy">
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

        if (conversionHistory.length > maxVisible) {
            const toggleBtn = document.createElement('button');
            toggleBtn.id = 'toggleHistoryBtn';
            toggleBtn.textContent = 'Показать ещё';
            toggleBtn.className = `
                px-5 py-2 mt-6 mx-auto block
                bg-white/30 backdrop-blur-md
                text-sm font-semibold text-blue-700 
                hover:bg-blue-50 hover:text-blue-800
                border border-blue-300 rounded-xl 
                shadow-sm transition-all duration-200
            `;


            toggleBtn.addEventListener('click', () => {
                isExpanded = !isExpanded;
                document.querySelectorAll('.history-item').forEach(item => {
                    const idx = parseInt(item.dataset.index);
                    if (idx >= maxVisible) {
                        item.classList.toggle('hidden', !isExpanded);
                    }
                });
                toggleBtn.textContent = isExpanded ? 'Скрыть' : 'Показать ещё';
            });

            historyContainer.appendChild(toggleBtn);
        }
    }


    window.deleteHistoryItem = function (index) {
        conversionHistory.splice(index, 1);
        localStorage.setItem('conversionHistory', JSON.stringify(conversionHistory));
        displayHistory();
    };

    ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
        dropZone.addEventListener(eventName, preventDefaults, false);
    });

    function preventDefaults(e) {
        e.preventDefault();
        e.stopPropagation();
    }

    function highlight() { dropZone.classList.add('dragover'); }
    function unhighlight() { dropZone.classList.remove('dragover'); }

    ['dragenter', 'dragover'].forEach(eventName => dropZone.addEventListener(eventName, highlight, false));
    ['dragleave', 'drop'].forEach(eventName => dropZone.addEventListener(eventName, unhighlight, false));

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
            } else {
                showPopUp('ERROR', 'Файл не является изображением!', currentTime);
            }
        });
    }

    function previewAndConvertFile(file) {
        const reader = new FileReader();
        reader.readAsDataURL(file);
        reader.onloadend = function () {
            const previewItem = document.createElement('div');
            previewItem.className = 'preview-item p-4 bg-white rounded-xl shadow-sm hover:shadow-md transition-all duration-300';
            previewItem.innerHTML = `
                <div class="relative group">
                    <img src="${reader.result}" class="w-full h-48 object-cover rounded-lg" alt="${file.name}">
                    <div class="absolute inset-0 bg-black/10 opacity-0 group-hover:opacity-100 transition-opacity duration-300 rounded-lg flex items-center justify-center">
                        <div class="status-indicator bg-white/90 backdrop-blur-sm px-4 py-2 rounded-full shadow-lg flex items-center">
                            <div class="loading-spinner mr-2">
                                <div class="spinner-circle"></div>
                            </div>
                            <span class="text-sm font-medium text-gray-700">Обработка изображения...</span>
                        </div>
                    </div>
                </div>
                <div class="mt-3 flex justify-between items-start">
                    <div class="truncate">
                        <p class="text-sm font-medium text-gray-800 truncate" title="${file.name}">${file.name}</p>
                        <p class="text-xs text-gray-500">${(file.size / 1024).toFixed(1)} KB</p>
                    </div>
                    <div class="file-type-badge px-2 py-1 bg-gray-100 text-gray-600 rounded-md text-xs">
                        ${file.type.split('/')[1]?.toUpperCase() || 'IMG'}
                    </div>
                </div>
            `;

            preview.appendChild(previewItem);
            previewItem.style.opacity = '0';
            previewItem.style.transform = 'translateY(20px)';
            setTimeout(() => {
                previewItem.style.opacity = '1';
                previewItem.style.transform = 'translateY(0)';
            }, 100);

            convertFile(file, previewItem);
        };
    }

    async function convertFile(file, previewItem) {
        const formData = new FormData();
        formData.append('image', file);
        formData.append('format', getSelectedFormat());
        formData.append('quality', qualitySlider.value);

        progressBar.classList.remove('hidden');
        progressBarInner.style.width = '0%';
        progressBarInner.style.transition = 'width 0.3s ease';

        const loadingInterval = setInterval(() => {
            progressBarInner.style.width = `${Math.min(parseInt(progressBarInner.style.width) || 0 + 5, 90)}%`;
        }, 300);

        try {
            const response = await fetch('./api/v1/convert.php', {
                method: 'POST',
                body: formData,
                credentials: 'include'
            });

            if (!response.ok) {
                throw response;
            }

            clearInterval(loadingInterval);
            progressBarInner.style.width = '100%';

            const rawResponse = await response.text();
            //console.log('Raw response:', rawResponse);

            try {
                data = JSON.parse(rawResponse);
            } catch (e) {
                console.error('Failed to parse:', rawResponse);
                throw e;
            }
            //const data = await response.json();
            //console.log('Response: сука ', data);

            if (!data.filename) {
                throw new Error('Сервер не вернул имя файла');
            }

            data.format = data.format || formatSelect.value;
            data.quality = data.quality || qualitySlider.value;
            data.timestamp = data.timestamp || Math.floor(Date.now() / 1000);

            const downloadPath = `./converted/${data.filename}`;

            conversionHistory.unshift({
                originalName: data.originalName,
                convertedName: data.filename,
                format: data.format,
                quality: data.quality,
                timestamp: data.timestamp,
                path: downloadPath
            });

            showPopUp('success', 'Изображение успешно конвертированно!', currentTime);

            if (conversionHistory.length > 50) conversionHistory = conversionHistory.slice(0, 50);
            localStorage.setItem('conversionHistory', JSON.stringify(conversionHistory));

            const statusIndicator = previewItem.querySelector('.status-indicator');
            statusIndicator.innerHTML = `
                <div class="flex items-center space-x-2 animate-pop-in">
                    <div class="w-5 h-5 bg-green-100 rounded-full flex items-center justify-center flex-shrink-0">
                        <i class="fas fa-check text-green-500 text-xs"></i>
                    </div>
                    <span class="text-sm font-medium text-gray-700">Готово!</span>
                    <a href="${downloadPath}" target="_blank" 
                       class="ml-2 w-6 h-6 bg-blue-500 hover:bg-blue-600 rounded-full flex items-center justify-center text-white text-xs transition-colors"
                       title="Скачать">
                        <i class="fas fa-download"></i>
                    </a>
                </div>
            `;
            statusIndicator.classList.remove('bg-white/90');
            statusIndicator.classList.add('bg-green-50', 'border', 'border-green-100');

            const fileInfo = previewItem.querySelector('.file-type-badge');
            fileInfo.innerHTML = `
                <span class="text-green-600 font-medium">${data.format.toUpperCase()}</span>
                <span class="text-gray-400 mx-1">•</span>
                <span>${data.quality}%</span>
            `;
            fileInfo.classList.remove('bg-gray-100', 'text-gray-600');
            fileInfo.classList.add('bg-green-50', 'text-green-700');

            previewItem.querySelector('img').style.transform = 'scale(0.97)';
            setTimeout(() => {
                previewItem.querySelector('img').style.transform = 'scale(1)';
                previewItem.querySelector('img').style.transition = 'transform 0.3s ease';
            }, 200);

            displayHistory();

        } catch (error) {
            clearInterval(loadingInterval);
            progressBarInner.style.width = '0%';

            let errorMsg = 'Произошла ошибка на сервере, попробуйте позже.';

            if (error instanceof Response && error.status === 400) {
                const errText = await error.text();
                try {
                    const errData = JSON.parse(errText);
                    if (errData?.error?.includes('слишком большой')) {
                        errorMsg = errData.error;
                    }
                } catch { }
            }

            showPopUp('error', errorMsg, `${hours}:${minutes}`);

            const statusIndicator = previewItem.querySelector('.status-indicator');
            statusIndicator.innerHTML = `
                <div class="flex items-center space-x-2 animate-pop-in">
                    <div class="w-5 h-5 bg-red-100 rounded-full flex items-center justify-center flex-shrink-0">
                        <i class="fas fa-times text-red-500 text-xs"></i>
                    </div>
                    <span class="text-sm font-medium text-gray-700">Ошибка конвертации</span>
                    <button onclick="retryConversion(this)" 
                            class="ml-2 w-6 h-6 bg-gray-100 hover:bg-gray-200 rounded-full flex items-center justify-center text-gray-700 text-xs transition-colors"
                            title="Повторить">
                        <i class="fas fa-redo"></i>
                    </button>
                </div>
            `;
            statusIndicator.classList.remove('bg-white/90');
            statusIndicator.classList.add('bg-red-50', 'border', 'border-red-100');

            const fileInfo = previewItem.querySelector('.file-type-badge');
            fileInfo.innerHTML = `<span class="text-red-600">Ошибка</span>`;
            fileInfo.classList.remove('bg-gray-100', 'text-gray-600');
            fileInfo.classList.add('bg-red-50', 'text-red-700');

            previewItem.style.animation = 'shake 0.5s';
            setTimeout(() => previewItem.style.animation = '', 500);
        } finally {
            setTimeout(() => progressBar.classList.add('hidden'), 500);
        }
    }

    qualitySlider.addEventListener('input', (e) => {
        qualityValue.textContent = `${e.target.value}%`;
    });

    displayHistory();
}); 