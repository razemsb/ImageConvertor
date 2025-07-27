<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once "./config/DatabaseConnect.php";
require_once "./config/AuthConfig.php";
require_once "./config/ErrorHandler.php";
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
    <script src="assets/vendors/tailwindcss/script.js"></script>
    <link rel="stylesheet" href="assets/vendors/font-awesome/css/all.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
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
                        <img src="./assets/img/other/<?= htmlspecialchars($user['avatar'] ?? 'default-avatar.png') ?>"
                            alt="Аватар" class="w-9 h-9 rounded-full object-cover shadow-md ring-2 ring-white">
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
                        <?php if ($auth->isAdmin()): ?>
                            <a href="admin/index.php"
                                class="flex items-center space-x-3 hover:bg-blue-50/50 rounded-lg px-3 py-2.5 transition-all text-gray-700 hover:text-blue-600">
                                <i class="fas fa-gears text-blue-500 w-5 text-center"></i>
                                <span class="text-sm font-medium">Админ-Панель</span>
                                <span class="ml-auto px-2 py-0.5 bg-blue-100 text-blue-800 text-xs rounded-full">Admin</span>
                            </a>
                        <?php endif; ?> 
                        <?php if (isset($page_title) && $page_title !== "Аккаунт"): ?>
                        <a href="./<?= htmlspecialchars($user['username']) ?>"
                            class="flex items-center space-x-3 hover:bg-blue-50/50 rounded-lg px-3 py-2.5 transition-all text-gray-700 hover:text-blue-600">
                            <i class="fas fa-user text-blue-500 w-5 text-center"></i>
                            <span class="text-sm font-medium">Профиль</span>
                        </a>
                        <?php endif; ?>
                        <?php if (isset($page_title) && $page_title !== "Главная"): ?>
                        <a href="./index.php"
                            class="flex items-center space-x-3 hover:bg-blue-50/50 rounded-lg px-3 py-2.5 transition-all text-gray-700 hover:text-blue-600">
                            <i class="fas fa-home text-blue-500 w-5 text-center"></i>
                            <span class="text-sm font-medium">Главная</span>
                        </a>
                        <?php endif; ?>
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