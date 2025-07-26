<?php
$additional_scripts = [
    './assets/js/script.js'
];
$additional_style = [];
include "./assets/include/header.php";
?>

<body class="bg-gradient-to-br from-slate-50 via-blue-50 to-indigo-50 min-h-screen">
    <div class="toolbar flex items-center sticky top-0 z-50 p-3 bg-white/90 backdrop-blur-md shadow-sm border-b border-gray-100 mb-4"
        id="toolbar">
        <div class="flex items-center">
            <div class="w-10 h-10 rounded-lg flex items-center justify-center text-white text-xl font-bold mr-3">
                <img src="./assets/img/favicon/favicon-96x96.png" style="border-radius: 6px;" alt="EnigmaLogo"
                    class="w-10 h-10" draggable="false">
            </div>
            <div class="text-xl font-bold bg-gradient-to-r from-blue-600 to-indigo-600 bg-clip-text text-transparent">
                E-Convertor
            </div>
        </div>
        <div class="relative ms-auto" id="accountMenu">
            <?php if ($isAuth && $user): ?>
                <button id="accountButton"
                    class="flex items-center space-x-3 focus:outline-none focus:ring-2 focus:ring-blue-500/30 rounded-full p-1 pr-3 hover:bg-gray-50/80 transition-all duration-200 group">
                    <div class="relative">
                        <img src="./assets/img/other/<?= htmlspecialchars($_SESSION['user']['avatar']) ?>" alt="Аватар"
                            class="w-9 h-9 rounded-full object-cover shadow-md ring-2 ring-white">
                        <span
                            class="absolute bottom-0 right-0 w-3 h-3 bg-green-500 rounded-full border-2 border-white"></span>
                    </div>
                    <span class="font-medium text-gray-700 group-hover:text-blue-600 transition-colors">
                        <?= htmlspecialchars($user['username'] ?? 'Пользователь') ?>
                    </span>
                    <i class="fas fa-chevron-down text-gray-400 group-hover:text-blue-500 transition-all duration-200 transform group-hover:translate-y-0.5"
                        id="accountChevron"></i>
                </button>
                <div id="accountDropdown"
                    class="absolute right-0 mt-2 w-56 bg-white/95 backdrop-blur-lg rounded-xl shadow-xl ring-1 ring-gray-200/50 opacity-0 pointer-events-none transform scale-95 origin-top-right transition-all duration-200">
                    <div class="p-2 space-y-1">
                        <?php if (isset($_SESSION['user']['is_admin']) && $_SESSION['user']['is_admin'] === 'admin'): ?>
                            <a href="admin/index.php"
                                class="flex items-center space-x-3 hover:bg-blue-50/50 rounded-lg px-3 py-2.5 transition-all text-gray-700 hover:text-blue-600">
                                <i class="fas fa-gears text-blue-500 w-5 text-center"></i>
                                <span class="text-sm font-medium">Админ-Панель</span>
                                <span class="ml-auto px-2 py-0.5 bg-blue-100 text-blue-800 text-xs rounded-full">Admin</span>
                            </a>
                        <?php endif; ?>
                        <a href="./<?= htmlspecialchars($user['username']) ?>"
                            class="flex items-center space-x-3 hover:bg-blue-50/50 rounded-lg px-3 py-2.5 transition-all text-gray-700 hover:text-blue-600">
                            <i class="fas fa-user text-blue-500 w-5 text-center"></i>
                            <span class="text-sm font-medium">Профиль</span>
                        </a>
                        <div class="border-t border-gray-100 my-1"></div>
                        <button id="LogoutBtn"
                            class="w-full flex items-center space-x-3 hover:bg-red-50/50 rounded-lg px-3 py-2.5 transition-all text-red-600 hover:text-red-700">
                            <i class="fas fa-sign-out-alt w-5 text-center"></i>
                            <span class="text-sm font-medium">Выйти</span>
                        </button>
                    </div>
                </div>
            <?php else: ?>
                <div class="flex items-center space-x-3">
                    <a href="auth.php?action=register"
                        class="px-4 py-1.5 text-sm font-medium text-blue-600 hover:text-blue-700 transition-colors">
                        Регистрация
                    </a>
                    <a href="auth.php"
                        class="px-5 py-2 bg-gradient-to-r from-blue-500 to-blue-600 text-white rounded-xl hover:from-blue-600 hover:to-blue-700 transition-all shadow-sm hover:shadow-md font-medium flex items-center">
                        <i class="fas fa-sign-in-alt mr-2"></i>
                        Войти
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </div>
    <div class="container mx-auto px-4 py-12">
        <div class="max-w-4xl mx-auto">
            <div class="glass-effect rounded-3xl shadow-2xl overflow-hidden">
                <div class="bg-gradient-to-r from-blue-600 via-indigo-600 to-violet-600 px-8 py-8">
                    <h1 class="text-4xl font-bold text-white flex items-center justify-center">
                        <i class="fas fa-image mr-4" style="font-size: 3rem;"></i>
                        Конвертер изображений
                    </h1>
                </div>
                <div class="p-8">
                    <div id="dropZone" class="drop-zone rounded-2xl p-12 text-center cursor-pointer">
                        <input type="file" id="fileInput" multiple accept="image/*" class="hidden">
                        <div class="space-y-6">
                            <i class="fas fa-cloud-upload-alt text-7xl text-blue-500"></i>
                            <h4 class="text-2xl font-semibold text-gray-800">Перетащите изображения сюда</h4>
                            <p class="text-base text-gray-600">или нажмите для выбора файлов</p>
                            <p class="text-sm text-gray-500">Поддерживаются форматы: JPG, PNG, GIF</p>
                        </div>
                    </div>
                    <div class="mt-12 space-y-8">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                            <div class="glass-effect p-8 rounded-2xl">
                                <label class="block text-lg font-medium text-gray-800 mb-4">
                                    <i class="fas fa-file-image mr-3 text-blue-500"></i>
                                    Формат выходного файла
                                </label>
                                <div class="grid grid-cols-2 gap-4" id="formatSelector">
                                    <label class="format-option">
                                        <input type="radio" name="format" value="webp" checked class="hidden peer">
                                        <div
                                            class="flex flex-col items-center justify-center p-4 border-2 border-gray-200 rounded-xl cursor-pointer transition-all duration-200 peer-checked:border-blue-500 peer-checked:bg-blue-50 hover:border-blue-300">
                                            <i class="fas fa-file-code text-3xl text-blue-500 mb-2"></i>
                                            <span class="font-medium">WebP</span>
                                        </div>
                                    </label>
                                    <label class="format-option">
                                        <input type="radio" name="format" value="avif" class="hidden peer">
                                        <div
                                            class="flex flex-col items-center justify-center p-4 border-2 border-gray-200 rounded-xl cursor-pointer transition-all duration-200 peer-checked:border-blue-500 peer-checked:bg-blue-50 hover:border-blue-300">
                                            <i class="fas fa-file-image text-3xl text-purple-500 mb-2"></i>
                                            <span class="font-medium">AVIF</span>
                                        </div>
                                    </label>
                                    <label class="format-option">
                                        <input type="radio" name="format" value="jpeg" class="hidden peer">
                                        <div
                                            class="flex flex-col items-center justify-center p-4 border-2 border-gray-200 rounded-xl cursor-pointer transition-all duration-200 peer-checked:border-blue-500 peer-checked:bg-blue-50 hover:border-blue-300">
                                            <i class="fas fa-file-image text-3xl text-red-500 mb-2"></i>
                                            <span class="font-medium">JPEG</span>
                                        </div>
                                    </label>
                                    <label class="format-option">
                                        <input type="radio" name="format" value="png" class="hidden peer">
                                        <div
                                            class="flex flex-col items-center justify-center p-4 border-2 border-gray-200 rounded-xl cursor-pointer transition-all duration-200 peer-checked:border-blue-500 peer-checked:bg-blue-50 hover:border-blue-300">
                                            <i class="fas fa-file-image text-3xl text-blue-400 mb-2"></i>
                                            <span class="font-medium">PNG</span>
                                        </div>
                                    </label>
                                </div>
                            </div>
                            <div class="glass-effect p-8 rounded-2xl">
                                <label class="block text-lg font-medium text-gray-800 mb-6">
                                    <i class="fas fa-sliders-h mr-3 text-blue-500"></i>
                                    Качество изображения
                                </label>
                                <div class="relative">
                                    <input type="range" id="quality" name="quality"
                                        class="quality-slider w-full h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer"
                                        min="0" max="100" value="80" step="5">
                                    <div class="flex justify-between px-1 mt-3">
                                        <span class="text-sm text-gray-500">0%</span>
                                        <span class="text-sm text-gray-500">25%</span>
                                        <span class="text-sm text-gray-500">50%</span>
                                        <span class="text-sm text-gray-500">75%</span>
                                        <span class="text-sm text-gray-500">100%</span>
                                    </div>
                                </div>
                                <div class="mt-6 flex items-center justify-center">
                                    <div class="text-3xl font-bold text-blue-600 quality-value mr-3">80%</div>
                                    <div class="w-12 h-12 rounded-full bg-blue-100 flex items-center justify-center">
                                        <i class="fas fa-image text-blue-500 text-xl"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div id="preview" class="mt-12 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6"></div>
                    <div id="progressBar" class="mt-8 hidden">
                        <div class="w-full bg-gray-200 rounded-full h-3">
                            <div class="bg-gradient-to-r from-blue-500 to-indigo-500 h-3 rounded-full transition-all duration-300"
                                style="width: 0%"></div>
                        </div>
                    </div>
                    <div class="mt-16">
                        <h2 class="text-2xl font-bold text-gray-800 mb-6 flex items-center">
                            <i class="fas fa-history mr-3 text-blue-500"></i>
                            История конвертаций
                            <button id="deleteHistory"
                                class="ms-auto text-sm font-bold text-red-500 mb6 flex items-center">
                                <i class="fa-solid fa-trash-can me-1"></i>
                                Удалить всю историю
                            </button>
                        </h2>
                        <div id="history" class="space-y-4"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div id="popup-container" class="fixed bottom-4 right-4 space-y-2 z-50"></div>
    <?php include "./assets/include/footer.php"; ?>