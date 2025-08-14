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
    <meta name="description" content="Бесплатный онлайн-конвертер изображений в WebP, AVIF, JPEG и PNG">
    <?php
    if (!empty($meta_tags)):
        echo "\n";
        foreach ($meta_tags as $tag):
            $clean_tag = trim(preg_replace('/\s+/', ' ', $tag));
            echo "    {$clean_tag}\n";
        endforeach;
    endif;
    ?>
    <link rel="icon" type="image/png" href="assets/img/favicon/favicon-96x96.png" sizes="96x96" />
    <link rel="icon" type="image/svg+xml" href="assets/img/favicon/favicon.svg" />
    <link rel="shortcut icon" href="assets/img/favicon/favicon.ico" />
    <link rel="apple-touch-icon" sizes="180x180" href="assets/img/favicon/apple-touch-icon.png" />
    <meta name="apple-mobile-web-app-title" content="MyWebSite" />
    <link rel="manifest" href="assets/img/favicon/site.webmanifest" />
    <link rel="stylesheet" href="assets/vendors/font-awesome/css/all.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/vendors/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.12/cropper.min.css">
    <script src="assets/vendors/bootstrap/js/bootstrap.min.js" defer></script>
    <script src="assets/vendors/tailwindcss/script.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.12/cropper.min.js"></script>
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

        window.addEventListener('scroll', function () {
            const toolbar = document.getElementById('toolbar');
            if (window.scrollY > 10) {
                toolbar.classList.add('shadow-md', 'bg-white/100');
                toolbar.classList.remove('bg-white/95');
            } else {
                toolbar.classList.remove('shadow-md', 'bg-white/100');
                toolbar.classList.add('bg-white/95');
            }
        });
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
                <div class="relative ml-4 md:ml-6">
                    <?php if ($isAuth && $user): ?>
                        <button id="userMenuButton" class="flex items-center gap-3 group focus:outline-none">
                            <div class="relative">
                                <div
                                    class="w-10 h-10 rounded-full overflow-hidden border border-white/20 shadow-[0_4px_20px_rgba(0,0,0,0.15)] group-hover:border-blue-400/60 transition-all duration-300 group-hover:scale-105">
                                    <img src="./assets/img/other/<?= htmlspecialchars($user['avatar'] ?? 'default-avatar.png') ?>"
                                        alt="Аватар" class="w-full h-full object-cover">
                                </div>
                                <div
                                    class="absolute bottom-0 right-0 w-3 h-3 bg-emerald-400 rounded-full border-2 border-white shadow-md animate-pulse">
                                </div>
                            </div>

                            <span
                                class="hidden sm:inline font-semibold text-gray-800 group-hover:text-blue-600 transition-colors duration-200">
                                <?= htmlspecialchars($user['username'] ?? 'Пользователь') ?>
                            </span>

                            <div
                                class="text-gray-400 transition-transform duration-200 group-hover:text-blue-500 group-[.menu-open]:rotate-180">
                                <i class="fas fa-chevron-down text-sm"></i>
                            </div>
                        </button>

                        <div id="userDropdown"
                            class="fixed inset-0 z-40 pointer-events-none opacity-0 transition-opacity duration-200 md:absolute md:inset-auto md:right-0 md:top-full md:mt-2 md:w-72">
                            <div class="fixed inset-0 bg-black/40 backdrop-blur-sm md:hidden" id="dropdownOverlay"></div>
                            <div
                                class="absolute right-0 top-16 md:top-auto w-full max-w-xs md:w-72 bg-white backdrop-blur-lg rounded-2xl shadow-[0_8px_30px_rgba(0,0,0,0.12)] overflow-hidden border border-white/20 transform md:scale-95 origin-top-right transition-transform duration-200 max-h-[calc(90vh-3rem)] md:max-h-[70vh] overflow-y-auto">
                                <div
                                    class="h-20 bg-gradient-to-br from-indigo-500 via-purple-500 to-pink-500 relative backdrop-blur-sm">
                                    <div class="absolute -bottom-8 left-4">
                                        <div
                                            class="w-16 h-16 rounded-full border-4 border-white bg-white overflow-hidden shadow-xl">
                                            <img src="./assets/img/other/<?= htmlspecialchars($user['avatar'] ?? 'default-avatar.png') ?>"
                                                alt="Аватар" class="w-full h-full object-cover">
                                        </div>
                                    </div>
                                </div>

                                <div class="pt-10 px-3 pb-3 bg-white">
                                    <div class="ml-20">
                                        <h3 class="font-bold text-gray-900 truncate text-lg">
                                            <?= htmlspecialchars($user['username']) ?>
                                        </h3>
                                        <p class="text-xs text-gray-500 truncate">
                                            <?= htmlspecialchars($user['email'] ?? '') ?>
                                        </p>
                                        <?php if ($auth->isAdmin()): ?>
                                            <span
                                                class="inline-block mt-1 px-2 py-0.5 text-xs font-bold text-white bg-gradient-to-r from-red-500 to-pink-500 rounded-full shadow-sm">
                                                Администратор
                                            </span>
                                        <?php endif; ?>
                                    </div>
                                </div>

                                <div class="px-2 pb-2 space-y-1">
                                    <?php if ($auth->isAdmin()): ?>
                                        <a href="admin/index.php?access=ok"
                                            class="flex items-center gap-3 px-3 py-2 text-sm rounded-lg hover:bg-blue-50/80 transition-all group">
                                            <div
                                                class="w-8 h-8 rounded-lg bg-blue-100 flex items-center justify-center text-blue-600 group-hover:bg-blue-600 group-hover:text-white transition-colors shadow-sm">
                                                <i class="fas fa-shield-alt"></i>
                                            </div>
                                            <span class="font-medium">Админ-панель</span>
                                            <span
                                                class="ml-auto px-2 py-0.5 text-xs font-bold text-blue-600 bg-blue-100 rounded-full">PRO</span>
                                        </a>
                                    <?php endif; ?>

                                    <!-- <a href="./profile/<?= htmlspecialchars($user['username']) ?>"
                                        class="flex items-center gap-3 px-3 py-2 text-sm rounded-lg hover:bg-indigo-50/80 transition-all group">
                                        <div
                                            class="w-8 h-8 rounded-lg bg-indigo-100 flex items-center justify-center text-indigo-600 group-hover:bg-indigo-600 group-hover:text-white transition-colors shadow-sm">
                                            <i class="fas fa-user"></i>
                                        </div>
                                        <span class="font-medium">Мой профиль</span>
                                    </a> -->

                                    <?php if (isset($page_title) && $page_title !== 'Главная'): ?>
                                        <a href="./index.php"
                                            class="flex items-center gap-3 px-3 py-2 text-sm rounded-lg hover:bg-green-50/80 transition-all group">
                                            <div
                                                class="w-8 h-8 rounded-lg bg-green-100 flex items-center justify-center text-green-600 group-hover:bg-green-600 group-hover:text-white transition-colors shadow-sm">
                                                <i class="fas fa-home"></i>
                                            </div>
                                            <span class="font-medium">Главная</span>
                                        </a>
                                    <?php endif; ?>
                                </div>

                                <div class="px-2 py-2 border-t border-gray-100/50">
                                    <button id="LogoutBtn"
                                        class="w-full flex items-center gap-3 px-3 py-2 text-sm rounded-lg hover:bg-red-50/80 transition-all group text-red-600">
                                        <div
                                            class="w-8 h-8 rounded-lg bg-red-100 flex items-center justify-center text-red-600 group-hover:bg-red-600 group-hover:text-white transition-colors shadow-sm">
                                            <i class="fas fa-sign-out-alt"></i>
                                        </div>
                                        <span class="font-medium">Выйти</span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    <?php else: ?>
                        <div class="flex items-center gap-3">
                            <a href="auth.php"
                                class="px-4 py-2 bg-gradient-to-r from-blue-600 to-indigo-600 text-white text-sm font-medium rounded-lg hover:shadow-[0_4px_20px_rgba(37,99,235,0.4)] transition-all hover:-translate-y-0.5 focus:outline-none focus:ring-2 focus:ring-blue-500/30">
                                <i class="fas fa-sign-in-alt mr-2"></i>Вход
                            </a>
                            <a href="auth.php?action=register"
                                class="px-4 py-2 border border-blue-600 text-blue-600 text-sm font-medium rounded-lg hover:bg-blue-50/50 transition-all focus:outline-none focus:ring-2 focus:ring-blue-500/30">
                                <i class="fas fa-user-plus mr-2"></i>Регистрация
                            </a>
                        </div>
                    <?php endif; ?>
                </div>

            </div>
        </div>
    </div>