document.addEventListener('DOMContentLoaded', function () {
    const dropZone = document.getElementById('dropZone');
    const fileInput = document.getElementById('fileInput');
    const preview = document.getElementById('preview');
    const formatSelect = document.getElementById('format');
    const qualitySlider = document.getElementById('quality');
    const qualityValue = document.querySelector('.quality-value');
    const progressBar = document.getElementById('progressBar');
    const progressBarInner = progressBar.querySelector('div');
    const historyContainer = document.getElementById('history');
    const btn = document.getElementById('accountButton');
    const dropdown = document.getElementById('accountDropdown');
    const chevron = document.getElementById('accountChevron');
    const LogoutBtn = document.getElementById('LogoutBtn');


    let toolbar = document.querySelector('.toolbar');

    if (!toolbar) {
        toolbar = document.createElement('div');
        toolbar.className = 'toolbar flex items-center justify-between sticky p-4 bg-white shadow-md mb-4';
        document.body.insertBefore(toolbar, document.body.firstChild);
    }

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
    })

    if (btn && dropdown) {
        btn.addEventListener('click', () => {
            const isOpen = dropdown.classList.contains('opacity-100');
            if (isOpen) {
                dropdown.classList.remove('opacity-100', 'pointer-events-auto', 'scale-100');
                dropdown.classList.add('opacity-0', 'pointer-events-none', 'scale-95');
                chevron.style.transform = 'rotate(0deg)';
            } else {
                dropdown.classList.add('opacity-100', 'pointer-events-auto', 'scale-100');
                dropdown.classList.remove('opacity-0', 'pointer-events-none', 'scale-95');
                chevron.style.transform = 'rotate(180deg)';
            }
        });

        document.addEventListener('click', (e) => {
            if (!btn.contains(e.target) && !dropdown.contains(e.target)) {
                dropdown.classList.remove('opacity-100', 'pointer-events-auto', 'scale-100');
                dropdown.classList.add('opacity-0', 'pointer-events-none', 'scale-95');
                chevron.style.transform = 'rotate(0deg)';
            }
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
                day: '2-digit', month: '2-digit', year: 'numeric', hour: '2-digit', minute: '2-digit'
            });

            historyItem.innerHTML = `
                <div class="flex items-center space-x-4">
                    <div class="w-12 h-12 bg-gray-100 rounded-lg flex items-center justify-center">
                        <img src="./converted/${item.convertedName}" width="100%" height="100%">
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
                console.warn(`Файл ${file.name} не является изображением и будет пропущен`);
            }
        });
    }

    function previewAndConvertFile(file) {
        const reader = new FileReader();
        reader.readAsDataURL(file);
        reader.onloadend = function () {
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
                    <p class="text-sm font-medium text-gray-800 truncate">${file.name}</p>
                    <p class="text-xs text-gray-500">${(file.size / 1024).toFixed(1)} KB</p>
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
        formData.append('format', formatSelect.value);
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
            clearInterval(loadingInterval);
            progressBarInner.style.width = '100%';

            const data = await response.json();

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

            if (conversionHistory.length > 50) conversionHistory = conversionHistory.slice(0, 50);
            localStorage.setItem('conversionHistory', JSON.stringify(conversionHistory));

            const statusIndicator = previewItem.querySelector('.status-indicator');
            statusIndicator.innerHTML = `
                <div class="flex items-center space-x-2">
                    <i class="fas fa-check-circle text-green-500"></i>
                    <span>Конвертировано</span>
                    <a href="${downloadPath}" target="_blank" class="text-blue-500 hover:text-blue-600">
                        <i class="fas fa-download"></i>
                    </a>
                </div>
            `;
            statusIndicator.classList.add('bg-green-500');

            previewItem.style.transform = 'scale(0.95)';
            setTimeout(() => previewItem.style.transform = 'scale(1)', 200);
            displayHistory();

        } catch (error) {
            clearInterval(loadingInterval);
            progressBarInner.style.width = '0%';

            const statusIndicator = previewItem.querySelector('.status-indicator');
            console.log(error);
            statusIndicator.innerHTML = `
                <div class="flex items-center space-x-2">
                    <i class="fas fa-exclamation-circle text-red-500"></i>
                    <span>Ошибка конвертации</span>
                </div>
            `;
            statusIndicator.classList.add('bg-red-50', 'text-red-600');

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