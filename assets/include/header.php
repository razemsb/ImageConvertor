<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once "./config/DatabaseConnect.php";
require_once "./config/AuthConfig.php";
$pdo = DB::connect();
$auth = Auth::getInstance($pdo);
$isAuth = $auth->isLoggedIn();
$user = $auth->getUser();
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Enigma Convertor | <?= htmlspecialchars($page_title) ?></title>
    <link rel="icon" type="image/png" href="assets/img/favicon/favicon-96x96.png" sizes="96x96" />
    <link rel="icon" type="image/svg+xml" href="assets/img/favicon/favicon.svg" />
    <link rel="shortcut icon" href="assets/img/favicon/favicon.ico" />
    <link rel="apple-touch-icon" sizes="180x180" href="assets/img/favicon/apple-touch-icon.png" />
    <meta name="apple-mobile-web-app-title" content="MyWebSite" />
    <link rel="manifest" href="assets/img/favicon/site.webmanifest" />
    <link rel="stylesheet" href="assets/vendors/font-awesome/css/all.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/vendors/bootstrap/css/bootstrap.min.css">
    <script src="assets/vendors/bootstrap/js/bootstrap.min.js" defer></script>
    <script src="assets/vendors/tailwindcss/script.js"></script>
    <?php if (!empty($additional_style)): ?>
        <?php foreach ($additional_style as $style): ?>
            <link rel="stylesheet" href="<?= htmlspecialchars($style) ?>">
        <?php endforeach; ?>
    <?php endif; ?>
    <?php if (!empty($additional_scripts)): ?>
        <?php foreach ($additional_scripts as $script): ?>
            <script src="<?= htmlspecialchars($script) ?>" defer></script>
        <?php endforeach; ?>
    <?php endif; ?>
    <script>
        const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]');
        const tooltipList = [...tooltipTriggerList].map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl));

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
    </script>
</head>
<body class="bg-gradient-to-br from-slate-50 via-blue-50 to-indigo-50 min-h-screen">
    <div class="toolbar-container sticky top-0 z-50 bg-white/95 backdrop-blur-md shadow-sm border-b border-gray-100/50 transition-shadow duration-300"
        id="toolbar">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                <div class="flex-shrink-0 flex items-center">
                    <a href="./" class="flex items-center group">
                        <img src="./assets/img/favicon/favicon-96x96.png" alt="EnigmaLogo"
                            class="h-9 w-9 rounded-lg transition-all duration-300 group-hover:rotate-12 group-hover:shadow-md"
                            draggable="false">
                        <span
                            class="ml-3 text-xl font-bold bg-gradient-to-r from-blue-600 to-indigo-600 bg-clip-text text-transparent">
                            E-Convertor
                        </span>
                    </a>
                </div>
                <div class="hidden md:flex items-center space-x-6 ml-10">
                    <a href="./"
                        class="text-sm font-medium text-gray-600 hover:text-blue-500 transition-colors duration-200">
                        Главная
                    </a>
                    <a href=""
                        class="text-sm font-medium text-gray-600 hover:text-blue-500 transition-colors duration-200">
                        Возможности
                    </a>
                </div>
                <div class="ml-4 flex items-center md:ml-6">
                    <?php if ($isAuth && $user): ?>
                        <div class="relative" id="accountMenu">
                            <button id="accountButton"
                                class="flex items-center space-x-2 focus:outline-none focus:ring-2 focus:ring-blue-500/30 rounded-full p-1 transition-all duration-200">
                                <div class="relative">
                                    <img src="./assets/img/other/<?= htmlspecialchars($user['avatar'] ?? 'default-avatar.png') ?>"
                                        alt="Аватар"
                                        class="w-8 h-8 rounded-full object-cover shadow-md ring-2 ring-white/80 transition-all duration-300 hover:ring-blue-300">
                                    <span class="absolute bottom-0 right-0 w-2.5 h-2.5 bg-green-500 rounded-full border-2 border-white animate-pulse"></span>
                                </div>
                                <span
                                    class="hidden sm:inline-block font-medium text-gray-700 hover:text-blue-600 transition-colors">
                                    <?= htmlspecialchars($user['username'] ?? 'Пользователь') ?>
                                </span>
                                <svg id="accountChevron" class="w-4 h-4 text-gray-400 transition-transform duration-200"
                                    fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 9l-7 7-7-7" />
                                </svg>
                            </button>
                            <div id="accountDropdown"
                                class="absolute right-0 top-14 w-80 bg-white border border-neutral-200 shadow-xl rounded-3xl ring-1 ring-black/5 backdrop-blur-2xl transition-all duration-200 origin-top-right scale-95 opacity-0 pointer-events-none z-50">
                                <div class="p-5 border-b border-neutral-100">
                                    <div class="flex items-center gap-4">
                                        <img src="./assets/img/other/<?= htmlspecialchars($user['avatar'] ?? 'default-avatar.png') ?>"
                                            alt="Avatar"
                                            class="w-12 h-12 rounded-full object-cover border border-neutral-200 shadow-sm">
                                        <div class="flex-1 min-w-0">
                                            <p class="text-sm font-semibold text-neutral-900 truncate">
                                                <?= htmlspecialchars($user['username']) ?>
                                                <?php if ($auth->isAdmin()): ?>
                                                <p class="text-xs text-red-500 font-bold truncate">Администратор</p>
                                            <?php endif; ?>
                                            </p>
                                        </div>
                                    </div>
                                </div>
                                <div class="py-2 text-sm text-neutral-800">
                                    <?php if ($auth->isAdmin()): ?>
                                        <a href="admin/index.php?access=ok"
                                            class="flex items-center gap-3 px-5 py-3 hover:bg-neutral-50 transition rounded-2xl group">
                                            <i class="fas fa-gears text-blue-500 w-5 text-center transition-transform"></i>
                                            <span class="flex-1">Админ-Панель</span>
                                            <span
                                                class="bg-blue-100 text-blue-600 text-xs font-semibold px-2 py-0.5 rounded-full">Admin</span>
                                        </a>
                                    <?php endif; ?>

                                    <?php if (isset($page_title) && $page_title !== "Аккаунт"): ?>
                                        <a href="./<?= htmlspecialchars($user['username']) ?>"
                                            class="flex items-center gap-3 px-5 py-3 hover:bg-neutral-50 transition rounded-2xl group">
                                            <i class="fas fa-user text-blue-500 w-5 text-center transition-transform"></i>
                                            <span>Профиль</span>
                                        </a>
                                    <?php endif; ?>

                                    <?php if (isset($page_title) && $page_title !== "Главная"): ?>
                                        <a href="./index.php"
                                            class="flex items-center gap-3 px-5 py-3 hover:bg-neutral-50 transition rounded-2xl group">
                                            <i
                                                class="fas fa-home text-blue-500 w-5 text-center group-hover:scale-110 transition-transform"></i>
                                            <span>Главная</span>
                                        </a>
                                    <?php endif; ?>
                                </div>
                                <div class="py-2 border-t border-neutral-100">
                                    <button id="LogoutBtn"
                                        class="w-full text-left flex items-center gap-3 px-5 py-3 text-red-600 hover:bg-red-50 transition rounded-2xl group">
                                        <i class="fas fa-sign-out-alt w-5 text-center transition-transform"></i>
                                        <span>Выйти</span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    <?php else: ?>
                        <div class="flex space-x-3">
                            <a href="auth.php"
                                class="px-4 py-2 bg-gradient-to-r from-blue-600 to-indigo-600 text-white text-sm font-medium rounded-lg hover:shadow-md transition-all duration-300 transform hover:-translate-y-0.5">
                                Вход
                            </a>
                            <a href="auth.php?action=register"
                                class="px-4 py-2 border border-blue-600 text-blue-600 text-sm font-medium rounded-lg hover:bg-blue-50 transition-colors">
                                Регистрация
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>