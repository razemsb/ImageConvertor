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
<div class="min-h-screen bg-gradient-to-b from-white to-gray-50 py-14 px-4 font-sans text-gray-900">
    <div class="max-w-6xl mx-auto space-y-12">

        <section class="flex flex-col md:flex-row items-center md:items-start bg-white border border-gray-200 rounded-3xl shadow-[0_4px_24px_rgba(0,0,0,0.05)] p-10 gap-8">
            <img src="./assets/img/other/<?= htmlspecialchars($user['avatar'] ?? 'default-avatar.png') ?>"
                alt="Аватар"
                class="w-28 h-28 rounded-full border-4 border-white shadow-md object-cover transition-transform duration-300 hover:scale-105">

            <div class="flex-1 space-y-2 text-center md:text-left">
                <h1 class="text-3xl font-extrabold tracking-tight">
                    <?= htmlspecialchars($user['username']) ?>
                    <?php if ($auth->isAdmin()): ?>
                        <span class="ml-2 px-2 py-1 bg-red-100 text-red-700 rounded-md text-sm font-semibold">(Админ)</span>
                    <?php endif; ?>
                </h1>
                <p class="text-sm text-gray-500">Зарегистрирован: <?= date('d.m.Y', strtotime($user['created_at'])) ?></p>
                <div class="inline-flex items-center gap-2 px-3 py-1 bg-emerald-100 text-emerald-700 rounded-full text-sm font-medium">
                    <span class="w-2 h-2 bg-emerald-500 rounded-full"></span> Онлайн
                </div>
            </div>
        </section>

        <section class="grid grid-cols-1 sm:grid-cols-3 gap-6">
            <div class="bg-white border border-gray-200 rounded-2xl p-6 shadow-sm hover:shadow-md transition-all duration-200">
                <p class="text-sm text-gray-500">Всего конвертаций</p>
                <p class="text-2xl font-semibold mt-1" id="AllConversionCount"></p>
            </div>
            <div class="bg-white border border-gray-200 rounded-2xl p-6 shadow-sm hover:shadow-md transition-all duration-200">
                <p class="text-sm text-gray-500">Последний IP</p>
                <p class="text-lg font-medium mt-1"><?= htmlspecialchars($user['last_ip'] ?? $_SERVER['REMOTE_ADDR']) ?></p>
            </div>
            <div class="bg-white border border-gray-200 rounded-2xl p-6 shadow-sm hover:shadow-md transition-all duration-200">
                <p class="text-sm text-gray-500">Платформа</p>
                <p class="text-sm mt-1 line-clamp-2">
                    <?= $_SERVER['HTTP_USER_AGENT'] ?? 'Неизвестно' ?>
                </p>
            </div>
        </section>

        <section class="bg-white border border-gray-200 rounded-3xl p-10 shadow-sm">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-xl font-semibold flex items-center gap-2">
                    <i class="fas fa-clock-rotate-left text-indigo-500"></i> История конвертаций
                </h2>
                <button id="deleteHistory" class="text-sm text-red-500 font-medium hover:underline flex items-center gap-1">
                    <i class="fa-solid fa-trash-can"></i> Очистить всё
                </button>
            </div>

            <div id="history" class="space-y-4">
                <div id="noHistory" class="border border-dashed border-gray-300 p-6 rounded-xl text-gray-400 text-center text-sm bg-gray-50">
                    История пуста. Загрузи файл — и отслеживай магию тут 🔮
                </div>
            </div>
        </section>
    </div>
</div>
<?php
include "./assets/include/footer.php";
ob_end_flush();