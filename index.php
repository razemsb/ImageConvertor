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
    <style>
        .drop-zone {
            background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
            border: 2px dashed #94a3b8;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            backdrop-filter: blur(8px);
        }

        .drop-zone:hover,
        .drop-zone.dragover {
            border-color: #2563eb;
            background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%);
            transform: translateY(-2px);
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
        }

        input[type="range"] {
            -webkit-appearance: none;
            height: 8px;
            background: #e2e8f0;
            border-radius: 4px;
            outline: none;
        }

        input[type="range"]::-webkit-slider-thumb {
            -webkit-appearance: none;
            width: 20px;
            height: 20px;
            background: #2563eb;
            border-radius: 50%;
            cursor: pointer;
            box-shadow: 0 4px 6px -1px rgba(37, 99, 235, 0.3);
            transition: all 0.2s ease;
        }

        input[type="range"]::-webkit-slider-thumb:hover {
            transform: scale(1.1);
        }

        .preview-item {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            background: white;
            border-radius: 12px;
            overflow: hidden;
        }

        .preview-item:hover {
            transform: translateY(-2px);
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        }

        .gradient-text {
            background: linear-gradient(135deg, #2563eb 0%, #4f46e5 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .glass-effect {
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .history-item {
            transition: all 0.3s ease;
        }

        .history-item:hover {
            transform: translateX(5px);
        }
    </style>
</head>
<div class="toolbar flex items-center sticky top-0 z-50 p-4 bg-white shadow-md mb-4" id="toolbar">
    <div class="flex items-center">
        <div class="w-10 h-10 rounded-lg flex items-center justify-center text-white text-xl font-bold mr-3">
            <img src="./assets/img/favicon/favicon-96x96.png" alt="EnigmaLogo" class="w-50 h-50" style="border-radius: 6px;" draggable="false">
        </div>
        <div class="text-xl font-bold text-gray-800">
            E-Convertor
        </div>
    </div>
    <div class="relative ms-auto" id="accountMenu">
    <?php if ($isAuth && $user): ?>
            <button id="accountButton"
                class="flex items-center space-x-3 focus:outline-none focus:ring-2 focus:ring-blue-500 rounded-full p-1 hover:bg-blue-50 transition">
                <img src="./assets/img/other/<?= htmlspecialchars($_SESSION['user']['avatar']) ?>" alt="Аватар"
                    class="w-10 h-10 rounded-full object-cover shadow-md">
                <span class="font-semibold text-gray-700"><?= htmlspecialchars($user['username'] ?? 'Пользователь') ?></span>
                <i class="fas fa-chevron-down text-gray-500 transition-transform duration-300" id="accountChevron"></i>
            </button>
            <div id="accountDropdown"
                class="absolute right-0 mt-2 w-56 bg-white rounded-xl shadow-lg ring-1 ring-black ring-opacity-5 opacity-0 pointer-events-none transform scale-95 origin-top-right transition-all duration-300">
                <div class="p-4 space-y-3">
                    <?php if(isset($_SESSION['user']['is_admin']) && $_SESSION['user']['is_admin'] === 'admin'): ?>
                    <a href="admin/index.php"
                        class="flex items-center space-x-3 hover:bg-red-50 rounded-lg px-3 py-2 text-red-600 transition">
                        <i class="fas fa-gears"></i>
                        <span>Админ-Панель</span>
                    </a>
                    <?php endif; ?>
                    <a href=""
                        class="flex items-center space-x-3 hover:bg-blue-50 rounded-lg px-3 py-2 transition">
                        <i class="fas fa-user text-blue-500"></i>
                        <span>Профиль</span>
                    </a>
                    <a href=""
                        class="flex items-center space-x-3 hover:bg-blue-50 rounded-lg px-3 py-2 transition">
                        <i class="fas fa-cog text-blue-500"></i>
                        <span>Настройки</span>
                    </a>
                    <button id="LogoutBtn" class="flex items-center space-x-3 hover:bg-red-50 rounded-lg px-3 py-2 text-red-600 transition">
                        <i class="fas fa-sign-out-alt"></i>
                        <span>Выйти</span>
                    </button>
                </div>
            </div>
    <?php else: ?>
        <a href="auth.php" class="px-5 py-2 bg-blue-600 text-white rounded-xl hover:bg-blue-700 transition font-semibold">
            Войти
        </a>
    <?php endif; ?>
    </div>
</div>

<body class="bg-gradient-to-br from-slate-50 via-blue-50 to-indigo-50 min-h-screen">
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
                                <label for="format" class="block text-lg font-medium text-gray-800 mb-4">
                                    <i class="fas fa-file-image mr-3 text-blue-500"></i>
                                    Формат выходного файла
                                </label>
                                <select id="format" name="format"
                                    class="w-full rounded-xl border-gray-200 shadow-sm focus:border-blue-500 focus:ring-blue-500 bg-white text-lg">
                                    <option value="webp">WebP</option>
                                    <option value="avif">AVIF</option>
                                    <option value="jpeg">JPEG</option>
                                    <option value="png">PNG</option>
                                </select>
                            </div>
                            <div class="glass-effect p-8 rounded-2xl">
                                <label for="quality" class="block text-lg font-medium text-gray-800 mb-4">
                                    <i class="fas fa-sliders-h mr-3 text-blue-500"></i>
                                    Качество (0-100%)
                                </label>
                                <input type="range" id="quality" name="quality" class="w-full" min="0" max="100"
                                    value="80" step="5">
                                <div class="text-center text-lg font-medium text-gray-800 quality-value mt-4">80%</div>
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
    <footer class="bg-white text-white mt-20">
        <div class="container mx-auto px-4 py-12">
            <div class="max-w-6xl mx-auto">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-12">
                    <div class="glass-effect rounded-2xl p-6">
                        <h3 class="text-xl font-bold mb-4 gradient-text">
                            <i class="fas fa-info-circle mr-2"></i> О проекте
                        </h3>
                        <p class="text-gray-700">
                            Enigma Image Converter - мощный инструмент для конвертации изображений между различными
                            форматами с сохранением качества.
                        </p>
                    </div>

                    <div class="glass-effect rounded-2xl p-6">
                        <h3 class="text-xl font-bold mb-4 gradient-text">
                            <i class="fas fa-book mr-2"></i> Документация
                        </h3>
                        <ul class="space-y-2">
                            <li>
                                <a href="#" class="text-gray-700 hover:text-blue-600 transition-colors">
                                    <i class="fas fa-file-alt mr-2"></i> Руководство пользователя
                                </a>
                            </li>
                            <li>
                                <a href="#" class="text-gray-700 hover:text-blue-600 transition-colors">
                                    <i class="fas fa-code mr-2"></i> API разработчика
                                </a>
                            </li>
                            <li>
                                <a href="#" class="text-gray-700 hover:text-blue-600 transition-colors">
                                    <i class="fas fa-question-circle mr-2"></i> Частые вопросы
                                </a>
                            </li>
                        </ul>
                    </div>

                    <div class="glass-effect rounded-2xl p-6">
                        <h3 class="text-xl font-bold mb-4 gradient-text">
                            <i class="fas fa-share-alt mr-2"></i> Контакты
                        </h3>
                        <div class="flex space-x-4">
                            <a href="https://github.com/razemsb"
                                class="text-2xl text-gray-700 hover:text-blue-500 transition-colors">
                                <i class="fab fa-github"></i>
                            </a>
                            <a href="https://t.me/razemsb"
                                class="text-2xl text-gray-700 hover:text-blue-400 transition-colors">
                                <i class="fab fa-telegram"></i>
                            </a>
                        </div>
                    </div>
                </div>

                <div class="border-t border-white/20 mt-12 pt-8 text-center">
                    <div class="flex flex-col md:flex-row justify-between items-center">
                        <p class="text-gray-900">
                            © <?= htmlspecialchars(date('Y')) ?> Enigma ImageConverter. Все права защищены.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </footer>
    <script src="assets/js/script.js"></script>
</body>

</html>