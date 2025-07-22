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

    let toolbar = document.querySelector('.toolbar');
    if (!toolbar) {
        toolbar = document.createElement('div');
        toolbar.className = 'toolbar flex items-center justify-between sticky p-4 bg-white shadow-md mb-4';
        document.body.insertBefore(toolbar, document.body.firstChild);
    }

    const logoContainer = document.createElement('div');
    logoContainer.className = 'flex items-center';

    const logo = document.createElement('div');
    logo.className = 'w-10 h-10 rounded-lg flex items-center justify-center text-white text-xl font-bold mr-3';
    
    // Создаем элемент img
    const img = document.createElement('img');
    img.src = './assets/img/favicon/favicon-96x96.png'; 
    img.alt = 'Enigma'; 
    img.className = 'w-50 h-50'; 
    img.style.borderRadius = '6px';
    img.draggable = false;
    
    logo.appendChild(img);
    logoContainer.appendChild(logo);

    const siteName = document.createElement('div');
    siteName.className = 'text-xl font-bold text-gray-800';
    siteName.textContent = 'E-Convertor';
    logoContainer.appendChild(siteName);

    const buttonsContainer = document.createElement('div');
    buttonsContainer.className = 'flex items-center';

    toolbar.appendChild(logoContainer);
    toolbar.appendChild(buttonsContainer);

    // const logsModal = document.createElement('div');
    // logsModal.id = 'logsModal';
    // logsModal.className = 'fixed inset-0 z-50 hidden';
    // logsModal.innerHTML = `
    //     <div class="absolute inset-0 bg-black bg-opacity-50 backdrop-blur-sm"></div>
    //     <div class="relative h-screen w-full max-w-5xl mx-auto flex items-center justify-center p-4">
    //         <div class="bg-white rounded-xl shadow-xl w-full max-h-[85vh] flex flex-col overflow-hidden">
    //             <div class="p-4 border-b border-gray-200 flex justify-between items-center bg-gray-50">
    //                 <h3 class="text-xl font-bold text-gray-800">Логи конвертаций</h3>
    //                 <button id="closeLogsModal" class="text-gray-500 hover:text-gray-700">
    //                     <i class="fas fa-times text-xl"></i>
    //                 </button>
    //             </div>
    //             <div class="p-3 border-b border-gray-200 flex flex-wrap gap-2 bg-gray-50">
    //                 <select id="logLevelFilter" class="bg-white border border-gray-300 rounded-md px-3 py-1 text-sm">
    //                     <option value="all">Все уровни</option>
    //                     <option value="INFO">Инфо</option>
    //                     <option value="WARNING">Предупреждения</option>
    //                     <option value="ERROR">Ошибки</option>
    //                     <option value="SUCCESS">Успех</option>
    //                 </select>
    //                 <input type="text" id="logSearch" placeholder="Поиск по логам..." 
    //                        class="bg-white border border-gray-300 rounded-md px-3 py-1 text-sm flex-grow min-w-[200px]">
    //                 <button id="refreshLogs" class="bg-blue-500 hover:bg-blue-600 text-white px-3 py-1 rounded-md text-sm whitespace-nowrap">
    //                     <i class="fas fa-sync-alt mr-1"></i>Обновить
    //                 </button>
    //             </div>
    //             <div class="overflow-y-auto flex-grow bg-white">
    //                 <table class="w-full text-sm">
    //                     <thead class="sticky top-0 bg-gray-100">
    //                         <tr>
    //                             <th class="p-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-36">Время</th>
    //                             <th class="p-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-28">Уровень</th>
    //                             <th class="p-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Сообщение</th>
    //                             <th class="p-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-24">IP</th>
    //                         </tr>
    //                     </thead>
    //                     <tbody id="logsContent" class="divide-y divide-gray-200"></tbody>
    //                 </table>
    //             </div>
    //             <div class="p-2 border-t border-gray-200 text-xs text-gray-500 bg-gray-50">
    //                 Всего записей: <span id="logsCount" class="font-medium">0</span>
    //             </div>
    //         </div>
    //     </div>
    // `;
    // document.body.appendChild(logsModal);

    // const logsButton = document.createElement('button');
    // logsButton.id = 'showLogsButton';
    // logsButton.className = `
    //     log-button 
    //     px-4 py-3 
    //     rounded-xl 
    //     ml-4 
    //     flex items-center 
    //     bg-gradient-to-r from-blue-500 to-blue-600
    //     text-white 
    //     font-medium
    //     shadow-lg
    //     hover:shadow-xl
    //     hover:from-blue-600 hover:to-blue-700
    //     transition-all
    //     duration-300
    //     transform
    //     active:translate-y-0
    //     active:scale-95
    //     group
    // `;
    // logsButton.innerHTML = `
    //     <i class="fas fa-scroll mr-3 text-lg group-hover:rotate-6 transition-transform duration-300"></i>
    //     <span class="text-sm tracking-wide">История конвертаций</span>
    //     <span class="ml-2 bg-white/20 px-2 py-1 rounded-full text-xs">
    //         <span id="logsCounter">0</span>
    //     </span>
    // `;

    // const testButton = document.createElement('a');
    // testButton.id = 'openTests';
    // testButton.href = 'test/index.html';
    // testButton.className = `
    //     px-4 py-3 
    //     rounded-xl 
    //     ml-4 
    //     flex items-center 
    //     bg-gradient-to-r from-blue-500 to-blue-600
    //     text-white 
    //     font-medium
    //     shadow-lg
    //     hover:shadow-xl
    //     hover:from-blue-600 hover:to-blue-700
    //     transition-all
    //     duration-300
    //     transform
    //     active:translate-y-0
    //     active:scale-95
    //     group
    // `;

    // testButton.innerHTML = `
    //     <i class="fa-solid fa-code mr-3 text-lg group-hover:rotate-6 transition-transform duration-300"></i>
    //     <span class="text-sm tracking-wide">Тесты</span>
    // `;

    //buttonsContainer.appendChild(logsButton);
    // buttonsContainer.appendChild(testButton);

    // const newLogsIndicator = document.createElement('div');
    // newLogsIndicator.className = `
    //     absolute 
    //     -top-1 -right-1 
    //     w-3 h-3 
    //     bg-red-500 
    //     rounded-full 
    //     animate-pulse
    //     hidden
    // `;
    // logsButton.appendChild(newLogsIndicator);

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
            const response = await fetch('./api/v1/convert.php', { method: 'POST', body: formData });
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

    function parseLogs(logText) {
        const logEntries = [];
        const operations = logText.split('=======================\n\n').filter(op => op.trim() !== '');

        for (const operation of operations) {
            try {
                const lines = operation.split('\n').filter(line => line.trim() !== '');
                if (lines.length < 4) continue;

                const time = lines[1].replace('Время начала: ', '').trim();
                const ip = lines[2].replace('IP адрес: ', '').trim();
                const type = lines[3].replace('Тип операции: ', '').trim();
                const message = lines[4].replace('Сообщение: ', '').trim();

                const details = {};
                let inDetails = false;
                let currentKey = null;

                // Парсим детали операции
                for (let i = 5; i < lines.length; i++) {
                    const line = lines[i].trim();
                    if (line === 'Детали операции:') {
                        inDetails = true;
                        continue;
                    }

                    if (inDetails) {
                        if (line.startsWith('    ') && !line.startsWith('        ')) {
                            const [key, value] = line.replace('    ', '').split(': ');
                            if (key && value) {
                                currentKey = key;
                                try {
                                    details[key] = JSON.parse(value);
                                } catch {
                                    details[key] = value;
                                }
                            }
                        } else if (line.startsWith('        ') && currentKey) {
                            const [subKey, subValue] = line.replace('        ', '').split(': ');
                            if (subKey && subValue) {
                                if (!details[currentKey]) {
                                    details[currentKey] = {};
                                }
                                try {
                                    details[currentKey][subKey] = JSON.parse(subValue);
                                } catch {
                                    details[currentKey][subKey] = subValue;
                                }
                            }
                        }
                    }
                }

                logEntries.push({
                    time: time,
                    type:
                        type.includes('ERROR') ? 'ERROR' :
                            type.includes('WARNING') ? 'WARNING' :
                                type.includes('THE-END') ? 'THE-END' :
                                    type.includes('START') ? 'START' :
                                        type.includes('SUCCESS') ? 'SUCCESS' : 'INFO',
                    rawType: type,
                    ip: ip,
                    message: message,
                    data: details
                });
            } catch (e) {
                console.error('Ошибка при разборе записи лога:', e);
                continue;
            }
        }

        return logEntries.reverse();
    }

    function displayLogs(logs) {
        const levelFilter = document.getElementById('logLevelFilter').value;
        const searchText = document.getElementById('logSearch').value.toLowerCase();

        const filteredLogs = logs.filter(log => {
            const typeMatch = levelFilter === 'all' || levelFilter === log.type;
            const textMatch = log.message.toLowerCase().includes(searchText) ||
                JSON.stringify(log.data).toLowerCase().includes(searchText) ||
                log.ip.toLowerCase().includes(searchText);

            return typeMatch && textMatch;
        });

        document.getElementById('logsCount').textContent = filteredLogs.length;
        const logsContent = document.getElementById('logsContent');
        logsContent.innerHTML = '';

        if (filteredLogs.length === 0) {
            logsContent.innerHTML = `
                <tr>
                    <td colspan="4" class="p-4 text-center text-gray-500">
                        Нет записей, соответствующих критериям поиска
                    </td>
                </tr>
            `;
            return;
        }

        filteredLogs.forEach(log => {
            const row = document.createElement('tr');
            row.className = 'hover:bg-gray-50';

            let typeClass = 'bg-blue-100 text-blue-800';
            let icon = 'fa-info-circle text-blue-500';

            if (log.type === 'ERROR') {
                typeClass = 'bg-red-100 text-red-800';
                icon = 'fa-exclamation-circle text-red-500';
            } else if (log.type === 'SUCCESS' || log.type === 'START' || log.type === 'THE-END') {
                typeClass = 'bg-green-100 text-green-800';
                icon = 'fa-check-circle text-green-500';
            } else if (log.type === 'WARNING') {
                typeClass = 'bg-yellow-100 text-yellow-800';
                icon = 'fa-solid fa-triangle-exclamation text-yellow-500';
            }

            const detailsHtml = Object.entries(log.data)
                .map(([key, value]) => {
                    if (typeof value === 'object' && value !== null) {
                        return `
                            <div class="text-xs text-gray-600 mb-2">
                                <div class="font-medium mb-1">${key}:</div>
                                ${Object.entries(value)
                                .map(([subKey, subValue]) => `
                                        <div class="ml-2">
                                            <span class="font-medium">${subKey}:</span> 
                                            ${typeof subValue === 'object' ?
                                        `<pre class="mt-1 max-h-20 overflow-auto">${JSON.stringify(subValue, null, 2)}</pre>` :
                                        subValue}
                                        </div>
                                    `).join('')}
                            </div>
                        `;
                    }
                    return `
                        <div class="text-xs text-gray-600">
                            <span class="font-medium">${key}:</span> ${value}
                        </div>
                    `;
                }).join('');

            row.innerHTML = `
                <td class="p-3 text-xs whitespace-nowrap">${log.time}</td>
                <td class="p-3 whitespace-nowrap">
                    <span class="${typeClass} px-2 py-1 rounded-full text-xs inline-flex items-center">
                        <i class="fas ${icon} mr-1"></i>
                        ${log.rawType}
                    </span>
                </td>
                <td class="p-3">
                    <div class="text-sm font-medium mb-2">${log.message}</div>
                    ${detailsHtml}
                </td>
                <td class="p-3 text-xs text-gray-500">${log.ip}</td>
            `;

            logsContent.appendChild(row);
        });
    }

    async function loadLogs() {
        try {
            const response = await fetch('logs/converter.log', {
                cache: 'no-store',
                headers: {
                    'Content-Type': 'text/plain; charset=utf-8'
                }
            });

            if (!response.ok) throw new Error(`Ошибка загрузки: ${response.status}`);

            const logText = await response.text();
            currentLogs = parseLogs(logText);
            displayLogs(currentLogs);

        } catch (error) {
            document.getElementById('logsContent').innerHTML = `
                <tr>
                    <td colspan="4" class="p-4 text-red-500 text-center">
                        ${error.message}
                    </td>
                </tr>
            `;
            document.getElementById('logsCount').textContent = '0';
        }
    }

    let currentLogs = [];

    // document.getElementById('showLogsButton').addEventListener('click', () => {
    //     logsModal.classList.remove('hidden');
    //     document.body.classList.add('overflow-hidden');
    //     loadLogs();
    // });

    // document.getElementById('closeLogsModal').addEventListener('click', () => {
    //     logsModal.classList.add('hidden');
    //     document.body.classList.remove('overflow-hidden');
    // });

    // logsModal.addEventListener('click', (e) => {
    //     if (e.target === logsModal) {
    //         logsModal.classList.add('hidden');
    //         document.body.classList.remove('overflow-hidden');
    //     }
    // });

    // document.getElementById('logLevelFilter').addEventListener('change', () => displayLogs(currentLogs));
    // document.getElementById('logSearch').addEventListener('input', () => displayLogs(currentLogs));
    // document.getElementById('refreshLogs').addEventListener('click', loadLogs);

    // qualitySlider.addEventListener('input', (e) => {
    //     qualityValue.textContent = `${e.target.value}%`;
    // });

    // dropZone.addEventListener('mouseenter', () => dropZone.style.transform = 'translateY(-2px)');
    // dropZone.addEventListener('mouseleave', () => dropZone.style.transform = 'translateY(0)');

    displayHistory();
});