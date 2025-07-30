document.addEventListener('DOMContentLoaded', function () {
    const LogoutBtn = document.getElementById('LogoutBtn');
    const historyContainer = document.getElementById('history');
    const btn = document.getElementById('accountButton');
    const dropdown = document.getElementById('accountDropdown');
    const deleteBtn = document.getElementById('deleteHistory');

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

    updateConversionCount(conversionHistory.length);

    displayHistory();

    function updateConversionCount(count) {
        const counterElement = document.getElementById('AllConversionCount');
        if (counterElement) {
            counterElement.textContent = `${count}`;
        }
    }


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
                        <img src="./converted/${item.convertedName}" class="object-cover w-full h-full">
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
});