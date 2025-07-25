document.addEventListener('DOMContentLoaded', function () {
    const LogoutBtn = document.getElementById('LogoutBtn');
    const historyContainer = document.getElementById('history'); 
    const btn = document.getElementById('accountButton');
    const dropdown = document.getElementById('accountDropdown');

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
});