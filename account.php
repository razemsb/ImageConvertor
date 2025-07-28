<?php

ob_start();

require_once "./config/DatabaseConnect.php";
require_once "./config/AuthConfig.php";
require_once "./config/ErrorHandler.php";

$pdo = DB::connect();
$auth = Auth::getInstance($pdo);
$additional_scripts = ['./assets/js/account.js'];


if (!$auth->isLoggedIn()) {
    ob_end_clean();
    header("Location: ./auth.php");
    exit();
}


$user = $auth->getUser();
if (!$user) {
    $auth->logout();
    ob_end_clean();
    header("Location: ./auth.php");
    exit();
}


$currentScript = basename($_SERVER['SCRIPT_NAME']);
if ($currentScript === 'account.php' && !empty($user['username'])) {

    $requestUri = $_SERVER['REQUEST_URI'] ?? '';
    if (!str_contains($requestUri, $user['username'])) {
        ob_end_clean();
        header("Location: ./{$user['username']}");
        exit();
    }
}

$page_title = "Аккаунт";
include "./assets/include/header.php";
?>
<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
        <div class="bg-white/90 backdrop-blur-lg rounded-3xl shadow-xl overflow-hidden border border-white/20">
            <div class="bg-gradient-to-r from-blue-600 via-indigo-600 to-violet-600 px-8 py-6">
                <div class="flex items-center justify-between">
                    <h1 class="text-2xl font-bold text-white flex items-center">
                        <i class="fas fa-user-circle mr-3" style="font-size: 2rem;"></i>
                        Мой профиль
                    </h1>
                </div>
            </div>
            <div class="p-6">
                <div class="user-card rounded-xl shadow-sm p-6 mb-8">
                    <div
                        class="flex flex-col md:flex-row items-center md:items-start space-y-6 md:space-y-0 md:space-x-6">
                        <div class="relative">
                            <img src="./assets/img/other/<?= htmlspecialchars($user['avatar'] ?? 'default-avatar.png') ?>"
                                alt="Аватар"
                                class="w-24 h-24 rounded-full object-cover border-4 border-white shadow-lg">
                        </div>
                        <div class="flex-1">
                            <div class="space-y-4">
                                <div>
                                    <h2 class="text-2xl font-bold text-gray-800">
                                        <?= htmlspecialchars($user['username']) ?>
                                        <?php if ($auth->isAdmin()): ?>
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
                                            <span class="inline-block w-2 h-2 rounded-full bg-green-500 mr-2"></span>
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
                    </div>

                    <div id="history" class="space-y-4"></div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php
include "./assets/include/footer.php";
ob_end_flush();