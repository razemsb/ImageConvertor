<?php
$additional_scripts = [
    './assets/js/account.js'
];
include "./assets/include/header.php";
$requested_username = basename($_SERVER['REQUEST_URI']);

if ($_SERVER['SCRIPT_NAME'] === '/account.php') {
    if (isset($_SESSION['user_auth']) && $_SESSION['user_auth'] === true) {
        header("Location: /{$_SESSION['user']['username']}");
        exit();
    } else {
        header("Location: /auth.php");
        exit();
    }
}

if (!isset($_SESSION['user_auth']) || $_SESSION['user_auth'] !== true) {
    header('Location: ./index.php');
    exit();
}

$user = $_SESSION['user'];
?>

<body class="bg-gradient-to-br from-slate-50 via-blue-50 to-indigo-50 min-h-screen">
    <div
        class="toolbar flex items-center sticky top-0 z-50 p-3 bg-white/90 backdrop-blur-md shadow-sm border-b border-gray-100 mb-4">
        <div class="flex items-center">
            <div
                class="w-10 h-10 rounded-lg flex items-center justify-center bg-gradient-to-br from-blue-500 to-indigo-600 text-white text-xl font-bold mr-3">
                <img src="./assets/img/favicon/favicon-96x96.png" alt="EnigmaLogo" class="w-8 h-8" draggable="false">
            </div>
            <div class="text-xl font-bold bg-gradient-to-r from-blue-600 to-indigo-600 bg-clip-text text-transparent">
                E-Convertor
            </div>
        </div>
        <div class="relative ms-auto">
            <button id="accountButton"
                class="flex items-center space-x-3 focus:outline-none focus:ring-2 focus:ring-blue-500/30 rounded-full p-1 pr-3 hover:bg-gray-50/80 transition-all duration-200 group">
                <div class="relative">
                    <img src="./assets/img/other/<?= htmlspecialchars($user['avatar']) ?>" alt="Аватар"
                        class="w-9 h-9 rounded-full object-cover shadow-md ring-2 ring-white">
                    <span
                        class="absolute bottom-0 right-0 w-3 h-3 bg-green-500 rounded-full border-2 border-white"></span>
                </div>
                <span class="font-medium text-gray-700 group-hover:text-blue-600 transition-colors">
                    <?= htmlspecialchars($user['username']) ?>
                </span>
                <i class="fas fa-chevron-down text-gray-400 group-hover:text-blue-500 transition-all duration-200 transform group-hover:translate-y-0.5"
                    id="accountChevron"></i>
            </button>
            <div id="accountDropdown"
                class="absolute right-0 mt-2 w-56 bg-white/95 backdrop-blur-lg rounded-xl shadow-xl ring-1 ring-gray-200/50 opacity-0 pointer-events-none transform scale-95 origin-top-right transition-all duration-200">
                <div class="p-2 space-y-1">
                    <?php if ($user['is_admin'] !== 'user'): ?>
                        <a href="./admin/"
                            class="flex items-center space-x-3 hover:bg-blue-50/50 rounded-lg px-3 py-2.5 transition-all text-gray-700 hover:text-blue-600">
                            <i class="fas fa-gears text-blue-500 w-5 text-center"></i>
                            <span class="text-sm font-medium">Админ-Панель</span>
                            <span class="ml-auto px-2 py-0.5 bg-blue-100 text-blue-800 text-xs rounded-full">
                                <?= $user['is_admin'] === 'admin' ? 'Admin' : 'Moder' ?>
                            </span>
                        </a>
                    <?php endif; ?>
                    <a href="./"
                        class="flex items-center space-x-3 hover:bg-blue-50/50 rounded-lg px-3 py-2.5 transition-all text-gray-700 hover:text-blue-600">
                        <i class="fas fa-user text-blue-500 w-5 text-center"></i>
                        <span class="text-sm font-medium">На главную</span>
                    </a>
                    <div class="border-t border-gray-100 my-1"></div>
                    <button id="LogoutBtn"
                        class="w-full flex items-center space-x-3 hover:bg-red-50/50 rounded-lg px-3 py-2.5 transition-all text-red-600 hover:text-red-700">
                        <i class="fas fa-sign-out-alt w-5 text-center"></i>
                        <span class="text-sm font-medium">Выйти</span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="container mx-auto px-4 py-8">
        <div class="max-w-4xl mx-auto">
            <div class="bg-white/90 backdrop-blur-lg rounded-3xl shadow-xl overflow-hidden border border-white/20">
                <div class="bg-gradient-to-r from-blue-600 via-indigo-600 to-violet-600 px-8 py-6">
                    <div class="flex items-center justify-between">
                        <h1 class="text-2xl font-bold text-white flex items-center">
                            <i class="fas fa-user-circle mr-3"></i>
                            Мой профиль
                        </h1>
                    </div>
                </div>
                <div class="p-6">
                    <div class="user-card rounded-xl shadow-sm p-6 mb-8">
                        <div
                            class="flex flex-col md:flex-row items-center md:items-start space-y-6 md:space-y-0 md:space-x-6">
                            <div class="relative">
                                <img src="./assets/img/other/<?= htmlspecialchars($user['avatar']) ?>" alt="Аватар"
                                    class="w-24 h-24 rounded-full object-cover border-4 border-white shadow-lg">
                            </div>
                            <div class="flex-1">
                                <div class="space-y-4">
                                    <div>
                                        <h2 class="text-2xl font-bold text-gray-800">
                                            <?= htmlspecialchars($user['username']) ?>
                                            <?php if ($_SESSION['user']['is_admin'] !== 'user'): ?>
                                                <span class="text-xl font-bold text-red-600 ml-2">(Администратор)</span>
                                            <?php endif; ?>
                                        </h2>
                                        <p class="text-gray-500 text-sm">Зарегистрирован:
                                            <?= date('d.m.Y', strtotime($user['created_at'])) ?>
                                        </p>
                                    </div>
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        <div class="bg-gray-50 p-3 rounded-lg">
                                            <p class="text-xs text-gray-500 mb-1">Статус</p>
                                            <p class="font-medium text-gray-700">
                                                <span
                                                    class="inline-block w-2 h-2 rounded-full bg-green-500 mr-2"></span>
                                                Активен
                                            </p>
                                        </div>
                                        <div class="bg-gray-50 p-3 rounded-lg">
                                            <p class="text-xs text-gray-500 mb-1">Последняя активность</p>
                                            <p class="font-medium text-gray-700">
                                                <?= date('H:i', strtotime($user['updated_at'])) ?>
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="user-card rounded-xl shadow-sm p-6">
                        <div class="flex items-center justify-between mb-6">
                            <h2 class="text-xl font-bold text-gray-800 flex items-center">
                                <i class="fas fa-history text-blue-500 mr-3"></i>
                                История конвертаций
                            </h2>
                            <!-- <div class="flex items-center space-x-3">
                                <button class="text-sm text-gray-500 hover:text-blue-600 flex items-center">
                                    <i class="fas fa-filter mr-1"></i> Фильтр
                                </button>
                                <button class="text-sm text-gray-500 hover:text-red-500 flex items-center">
                                    <i class="fas fa-trash-alt mr-1"></i> Очистить
                                </button>
                            </div> -->
                        </div>

                        <div id="history" class="space-y-4"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php include "./assets/include/footer.php";