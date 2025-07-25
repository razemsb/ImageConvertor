<?php
session_start();
$isAuth = isset($_SESSION['user_auth']) && $_SESSION['user_auth'] === true;
$user = $isAuth && !empty($_SESSION['user']) ? $_SESSION['user'] : null;
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Enigma | Конвертер изображений</title>
    <link rel="icon" type="image/png" href="assets/img/favicon/favicon-96x96.png" sizes="96x96" />
    <link rel="icon" type="image/svg+xml" href="assets/img/favicon/favicon.svg" />
    <link rel="shortcut icon" href="assets/img/favicon/favicon.ico" />
    <link rel="apple-touch-icon" sizes="180x180" href="assets/img/favicon/apple-touch-icon.png" />
    <meta name="apple-mobile-web-app-title" content="MyWebSite" />
    <link rel="manifest" href="assets/img/favicon/site.webmanifest" />
    <script src="assets/vendors/tailwindcss/script.js"></script>
    <link rel="stylesheet" href="assets/vendors/font-awesome/css/all.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
    </style>
</head>
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
                        <i class="fas fa-image mr-4"></i>
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
                        </h2>
                        <div id="history" class="space-y-4"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <footer class="bg-gradient-to-br from-gray-50 to-blue-50 border-t border-gray-200 mt-20">
        <div class="container mx-auto px-4 py-12">
            <div class="max-w-7xl mx-auto">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                    <div class="space-y-5">
                        <div class="flex items-center">
                            <img src="./assets/img/favicon/favicon-96x96.png" alt="Logo"
                                class="h-10 w-10 mr-3 rounded-md">
                            <span
                                class="text-2xl font-bold bg-gradient-to-r from-blue-600 to-indigo-600 bg-clip-text text-transparent">E-Convertor</span>
                        </div>
                        <p class="text-gray-600 text-sm leading-relaxed">
                            Профессиональный инструмент для конвертации изображений между различными форматами с
                            сохранением качества.
                        </p>
                        <div class="flex space-x-4">
                            <a href="https://github.com/razemsb"
                                class="text-gray-500 hover:text-blue-600 transition-colors" aria-label="GitHub">
                                <i class="fab fa-github text-xl"></i>
                            </a>
                            <a href="https://t.me/razemsb" class="text-gray-500 hover:text-blue-400 transition-colors"
                                aria-label="Telegram">
                                <i class="fab fa-telegram text-xl"></i>
                            </a>
                        </div>
                    </div>

                    <div>
                        <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                            <i class="fas fa-compass mr-2 text-blue-500"></i> Навигация
                        </h3>
                        <ul class="space-y-3">
                            <li>
                                <a href="#"
                                    class="text-gray-600 hover:text-blue-600 transition-colors flex items-center">
                                    <i class="fas fa-chevron-right text-xs text-blue-400 mr-2"></i>
                                    Главная
                                </a>
                            </li>
                            <li>
                                <a href="#"
                                    class="text-gray-600 hover:text-blue-600 transition-colors flex items-center">
                                    <i class="fas fa-chevron-right text-xs text-blue-400 mr-2"></i>
                                    Конвертер
                                </a>
                            </li>
                            <li>
                                <a href="#"
                                    class="text-gray-600 hover:text-blue-600 transition-colors flex items-center">
                                    <i class="fas fa-chevron-right text-xs text-blue-400 mr-2"></i>
                                    История
                                </a>
                            </li>
                            <li>
                                <a href="#"
                                    class="text-gray-600 hover:text-blue-600 transition-colors flex items-center">
                                    <i class="fas fa-chevron-right text-xs text-blue-400 mr-2"></i>
                                    Настройки
                                </a>
                            </li>
                        </ul>
                    </div>

                    <div>
                        <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                            <i class="fas fa-book-open mr-2 text-blue-500"></i> Документация
                        </h3>
                        <ul class="space-y-3">
                            <li>
                                <a href="#"
                                    class="text-gray-600 hover:text-blue-600 transition-colors flex items-center">
                                    <i class="fas fa-file-alt text-sm text-blue-400 mr-2"></i>
                                    Руководство
                                </a>
                            </li>
                            <li>
                                <a href="#"
                                    class="text-gray-600 hover:text-blue-600 transition-colors flex items-center">
                                    <i class="fas fa-code text-sm text-blue-400 mr-2"></i>
                                    API
                                </a>
                            </li>
                            <li>
                                <a href="#"
                                    class="text-gray-600 hover:text-blue-600 transition-colors flex items-center">
                                    <i class="fas fa-question-circle text-sm text-blue-400 mr-2"></i>
                                    FAQ
                                </a>
                            </li>
                            <li>
                                <a href="#"
                                    class="text-gray-600 hover:text-blue-600 transition-colors flex items-center">
                                    <i class="fas fa-bug text-sm text-blue-400 mr-2"></i>
                                    Сообщить об ошибке
                                </a>
                            </li>
                        </ul>
                    </div>

                    <div>
                        <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                            <i class="fas fa-envelope mr-2 text-blue-500"></i> Контакты
                        </h3>
                        <ul class="space-y-3">
                            <li class="flex items-center">
                                <i class="fas fa-envelope text-blue-400 mr-2"></i>
                                <a href="mailto:support@enigma-convertor.ru"
                                    class="text-gray-600 hover:text-blue-600 transition-colors">
                                    support@enigma-convertor.ru
                                </a>
                            </li>
                            <li class="flex items-center">
                                <i class="fas fa-clock text-blue-400 mr-2"></i>
                                <span class="text-gray-600">Поддержка 24/7</span>
                            </li>
                        </ul>

                    </div>
                </div>

                <div class="border-t border-gray-200 mt-12 pt-8">
                    <div class="flex flex-col md:flex-row justify-between items-center">
                        <p class="text-gray-500 text-sm">
                            © <?= htmlspecialchars(date('Y')) ?> Enigma ImageConverter. Все права защищены.
                        </p>
                        <div class="flex space-x-6 mt-4 md:mt-0">
                            <a href="#" class="text-gray-500 hover:text-gray-700 text-sm transition-colors">Политика
                                конфиденциальности</a>
                            <a href="#" class="text-gray-500 hover:text-gray-700 text-sm transition-colors">Условия
                                использования</a>
                            <a href="#" class="text-gray-500 hover:text-gray-700 text-sm transition-colors">Cookie</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </footer>
    <script src="assets/js/script.js"></script>
</body>
</html>