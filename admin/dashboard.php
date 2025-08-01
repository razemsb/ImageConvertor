<?php
require_once "../config/DatabaseConnect.php";
require_once 'AdminCore.php';
require_once 'AdminFileManager.php';

$pdo = DB::connect();

$adminCore = AdminCore::init($pdo);
$adminCore->requireAdmin();

$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int) $_GET['page'] : 1;
$perPage = isset($_GET['perPage']) && in_array($_GET['perPage'], [50, 100, 250, 500]) ? (int) $_GET['perPage'] : 100;
$tab = $_GET['tab'] ?? 'stats';

$csrfToken = $adminCore->generateCsrfToken();

$stats = $adminCore->getConversionStats();
$logData = $adminCore->getConversionLogs($page, $perPage);
$logs = $logData['logs'];
$totalLogs = $logData['total'];
$totalPages = $logData['total_pages'];
$allUsers = $adminCore->getAllUsersWithDetails();
$agents = $adminCore->getNormalizedUserAgents($pdo);
$fileManager = new AdminFileManager();
$oldFiles = $fileManager->getOldFiles();
?>
<!DOCTYPE html>
<html lang="ru" class="dark">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Админ-панель | Конвертер</title>
    <link rel="icon" type="image/png" href="../assets/img/favicon/favicon-96x96.png" sizes="96x96" />
    <link rel="icon" type="image/svg+xml" href="../assets/img/favicon/favicon.svg" />
    <link rel="shortcut icon" href="../assets/img/favicon/favicon.ico" />
    <link rel="apple-touch-icon" sizes="180x180" href="../assets/img/favicon/apple-touch-icon.png" />
    <meta name="apple-mobile-web-app-title" content="MyWebSite" />
    <link rel="manifest" href="../assets/img/favicon/site.webmanifest" />
    <link rel="stylesheet" href="../assets/css/style.css">
    <script src="../assets/vendors/tailwindcss/script.js"></script>
    <link rel="stylesheet" href="../assets/vendors/font-awesome/css/all.min.js">
    <script src="../assets/vendors/chartjs/chart.js"></script>
    <script src="../assets/js/AdminCore.js" defer></script>
    <script src="../assets/vendors/font-awesome/js/all.min.js" crossorigin="anonymous"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            document.querySelectorAll('.file-action-btn').forEach(btn => {
                btn.addEventListener('click', async () => {
                    const fileId = btn.dataset.id;
                    const action = btn.dataset.action;

                    try {
                        const res = await fetch('AjaxRequest.php', {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json' },
                            body: JSON.stringify({ action, id: fileId })
                        });

                        const data = await res.json();
                        console.log('response:', data);
                        console.log('debug message: ', data.message);

                        if (data.status === 'success') {
                            const parent = btn.closest('td');
                            parent.innerHTML = `
                    <button class="file-action-btn ${data.newClass} text-white px-3 py-1 rounded" 
                            data-id="${fileId}" data-action="${data.newAction}">
                        ${data.newLabel}
                    </button>`;

                            parent.querySelector('.file-action-btn').addEventListener('click', async (e) => {
                                e.preventDefault();
                                e.stopPropagation();
                                parent.querySelector('.file-action-btn').click();
                            });
                        } else {
                            alert('Ошибка при выполнении действия');
                        }
                    } catch (err) {
                        console.error('Fetch error:', err);
                    }
                });
            });


            const tabs = document.querySelectorAll('[data-tab]');
            const sections = document.querySelectorAll('[data-tab-content]');

            tabs.forEach(tab => {
                tab.addEventListener('click', () => {
                    const target = tab.getAttribute('data-tab');

                    sections.forEach(sec => {
                        sec.classList.toggle('hidden', sec.getAttribute('data-tab-content') !== target);
                    });

                    tabs.forEach(t => t.classList.remove('border-blue-500'));
                    tab.classList.add('border-blue-500');

                    const url = new URL(window.location);
                    url.searchParams.set('tab', target);
                    history.pushState({}, '', url);
                });
            });

            const header = document.getElementById('adminHeader');
            let lastScrollY = window.scrollY;
            let ticking = false;

            function onScroll() {
                const currentScroll = window.scrollY;

                if (currentScroll > lastScrollY && currentScroll > 100) {
                    header.style.transform = 'translateY(-100%)';
                } else {
                    header.style.transform = 'translateY(0)';
                }
                lastScrollY = currentScroll;
                ticking = false;
            }

            window.addEventListener('scroll', () => {
                if (!ticking) {
                    window.requestAnimationFrame(onScroll);
                    ticking = true;
                }
            });

            const buttons = document.querySelectorAll('.tab-btn');

            buttons.forEach(button => {
                button.addEventListener('click', () => {
                    buttons.forEach(btn => btn.classList.remove('active'));
                    button.classList.add('active');
                });
            });

            const defaultTab = document.querySelector('[data-tab="stats"]');
            if (defaultTab) defaultTab.classList.add('active');
        });

    </script>
</head>

<body class="bg-gray-900 text-gray-100 min-h-screen">
    <header id="adminHeader"
        class="fixed top-0 left-0 right-0 z-50 bg-gray-800 shadow-lg transition-transform duration-300">
        <div class="container mx-auto px-4 py-4 flex justify-between items-center">
            <h1 class="text-2xl font-bold text-blue-400 flex items-center gap-2">
                <img src="../assets/img/favicon/favicon-96x96.png" class="w-10 h-10" style="border-radius: 6px;"
                    alt="Logotype">
                <span class="hidden sm:inline">Enigma Панель управления</span>
            </h1>
            <div class="flex items-center space-x-4">
                <span class="text-sm text-gray-400 hidden md:inline-flex items-center">
                    <i class="fas fa-user-shield mr-1"></i>
                    <?= htmlspecialchars($_SESSION['admin_login'] ?? 'Администратор') ?>
                </span>
                <a href="logout.php"
                    class="bg-gray-700 hover:bg-gray-600 px-3 py-2 rounded-lg text-sm text-gray-200 transition flex items-center gap-1">
                    <i class="fas fa-sign-out-alt"></i>
                    <span class="hidden sm:inline">Выход</span>
                </a>
            </div>
        </div>
    </header>
    <div class="h-[65px]"></div>

    <main class="container mx-auto px-4 py-6">
        <nav id="tabsNav" class="flex space-x-2 mb-6 border-b border-gray-700">
            <button data-tab="stats"
                class="tab-btn px-4 py-2 font-semibold rounded-t-md transition-all duration-300 text-gray-400 hover:text-blue-300 hover:bg-gray-700">
                <i class="fa-solid fa-chart-simple mr-1"></i> Статистика
            </button>
            <button data-tab="users"
                class="tab-btn px-4 py-2 font-semibold rounded-t-md transition-all duration-300 text-gray-400 hover:text-blue-300 hover:bg-gray-700">
                <i class="fa-solid fa-users mr-1"></i> Пользователи
            </button>
            <button data-tab="old-files"
                class="tab-btn px-4 py-2 font-semibold rounded-t-md transition-all duration-300 text-gray-400 hover:text-blue-300 hover:bg-gray-700">
                <i class="fa-solid fa-folder-minus mr-1"></i> Старые файлы
            </button>
        </nav>

        <section data-tab-content="old-files" class="<?= $tab !== 'old-files' ? 'hidden' : '' ?>">
            <h2 class="text-xl font-semibold mb-4 text-blue-400 border-b border-gray-700 pb-2">
                <i class="fas fa-file mr-2"></i>Старые файлы
            </h2>
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm text-gray-300 bg-gray-800 rounded-lg overflow-hidden">
                    <thead class="bg-gray-700 text-gray-200 text-left">
                        <tr>
                            <th class="px-4 py-2">ID</th>
                            <th class="px-4 py-2">Файл</th>
                            <th class="px-4 py-2">Пользователь</th>
                            <th class="px-4 py-2">User-Agent</th>
                            <th class="px-4 py-2">Дата</th>
                            <th class="px-4 py-2">Статус</th>
                            <th class="px-4 py-2">Действие</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($oldFiles as $file): ?>
                            <?php
                            $browser = $adminCore->getBrowserInfo($file['user_agent']);
                            $isDeleted = $file['record-status'] === 'deleted';
                            ?>
                            <tr class="border-b border-gray-700 hover:bg-gray-700 transition">
                                <td class="px-4 py-2"><?= $file['id'] ?></td>
                                <td class="px-4 py-2"><?= htmlspecialchars($file['original_name']) ?></td>
                                <td class="px-4 py-2"><?= $fileManager->getUsernameById((int) $file['user_id']) ?></td>
                                <td class="px-4 py-2">
                                    <i class="fa-brands <?= $browser[1] ?> mr-1" style="color: <?= $browser[2] ?>"></i>
                                    <span class="text-xs"><?= $browser[0] ?></span>
                                </td>
                                <td class="px-4 py-2"><?= date('d.m.Y H:i', strtotime($file['created_at'])) ?></td>
                                <td class="px-4 py-2">
                                    <?php if ($isDeleted): ?>
                                        <span class="text-red-400">Удалён</span>
                                    <?php else: ?>
                                        <span class="text-green-400">Активен</span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-4 py-2">
                                    <?php if ($file['record-status'] === 'active'): ?>
                                        <button data-id="<?= $file['id'] ?>" data-action="delete"
                                            class="file-action-btn px-3 py-1 text-sm bg-red-600 hover:bg-red-700 text-white rounded">
                                            Удалить
                                        </button>
                                    <?php else: ?>
                                        <button data-id="<?= $file['id'] ?>" data-action="restore"
                                            class="file-action-btn px-3 py-1 text-sm bg-green-600 hover:bg-green-700 text-white rounded">
                                            Восстановить
                                        </button>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach ?>
                    </tbody>
                </table>
            </div>
        </section>


        <section data-tab-content="users" class="<?= $tab !== 'users' ? 'hidden' : '' ?>">
            <h2 class="text-xl font-semibold mb-4 text-blue-400 border-b border-gray-700 pb-2">
                <i class="fas fa-users mr-2"></i>Статистика пользователей
            </h2>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
                <div class="bg-gray-800 rounded-lg p-4 shadow-lg border-l-4 border-blue-500">
                    <div class="flex justify-between items-start">
                        <div>
                            <p class="text-gray-400 text-sm">Всего пользователей</p>
                            <p class="text-xl font-bold"><?= $stats['total_users'] ?? 'UNKNOW' ?></p>
                        </div>
                        <div class="bg-blue-500/20 p-3 rounded-full">
                            <i class="fas fa-exchange-alt text-blue-400"></i>
                        </div>
                    </div>
                </div>

                <div class="bg-gray-800 rounded-lg p-4 shadow-lg border-l-4 border-green-500">
                    <div class="flex justify-between items-start">
                        <div>
                            <p class="text-gray-400 text-sm">Сегодня</p>
                            <p class="text-xl font-bold"><?= $stats['today_users'] ?? 'UNKNOW' ?></p>
                        </div>
                        <div class="bg-green-500/20 p-3 rounded-full">
                            <i class="fas fa-calendar-day text-green-400"></i>
                        </div>
                    </div>
                </div>

                <div class="bg-gray-800 rounded-lg p-4 shadow-lg border-l-4 border-purple-500">
                    <div class="flex justify-between items-start">
                        <div>
                            <p class="text-gray-400 text-sm">Активных пользователей</p>
                            <p class="text-xl font-bold"><?= count($stats['active_users']) ?></p>
                        </div>
                        <div class="bg-purple-500/20 p-3 rounded-full">
                            <i class="fas fa-users text-purple-400"></i>
                        </div>
                    </div>
                </div>
            </div>
            <h2 class="text-xl font-semibold mb-4 text-blue-400 border-b border-gray-700 pb-2">
                <i class="fas fa-users mr-2"></i>Все пользователи
            </h2>
            <div class="bg-gray-800 p-4 rounded-lg shadow-lg overflow-auto">
                <table class="min-w-full text-sm text-left text-gray-300">
                    <thead class="bg-gray-900">
                        <tr>
                            <th class="p-3 font-medium">ID</th>
                            <th class="p-3 font-medium">Имя</th>
                            <th class="p-3 font-medium">Email</th>
                            <th class="p-3 font-medium">Последний IP</th>
                            <th class="p-3 font-medium">Браузер</th>
                            <th class="p-3 font-medium">Роль</th>
                            <th class="p-3 font-medium">Конвертаций</th>
                            <th class="p-3 font-medium">Аватар</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-700">
                        <?php foreach ($allUsers as $user): ?>
                            <tr class="hover:bg-gray-700/50 transition">
                                <td class="p-3 font-mono text-gray-400">#<?= $user['id'] ?></td>
                                <td class="p-3 font-medium text-white"><?= htmlspecialchars($user['username']) ?></td>
                                <td class="p-3 text-gray-400"><?= htmlspecialchars($user['email'] ?? 'UNKNOW') ?></td>
                                <td class="p-3 font-mono text-gray-400">
                                    <?= htmlspecialchars($user['last_ip'] ?? 'UNKNOW') ?>
                                </td>
                                <td>
                                    <?php
                                    $user['user_agent'] = $adminCore->getLastAgentByUserId((int) $user['id']);
                                    [$browserName, $iconClass, $color] = AdminCore::getBrowserInfo($user['user_agent'] ?? '');
                                    ?>
                                    <span class="inline-flex items-center gap-1 px-2 py-1 rounded bg-gray-700 text-xs"
                                        style="color: <?= $color ?>;">
                                        <i class="fab <?= $iconClass ?>"></i> <?= htmlspecialchars($browserName) ?>
                                    </span>
                                </td>
                                <td class="p-3 capitalize text-sm <?=
                                    $user['role'] === 'admin' ? 'text-purple-400' :
                                    ($user['role'] === 'moderator' ? 'text-blue-400' : 'text-gray-400')
                                    ?>">
                                    <?= htmlspecialchars($user['role']) ?>
                                </td>
                                <td class="p-3 font-semibold text-green-400">
                                    <?= (int) $user['conversions_count'] ?>
                                </td>
                                <td class="p-3">
                                    <img src="../assets/img/other/<?= htmlspecialchars($user['avatar']) ?>" alt="avatar"
                                        class="w-8 h-8 rounded-full object-cover">
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </section>

        <section data-tab-content="stats" class="<?= $tab !== 'stats' ? 'hidden' : '' ?>">
            <section class="mb-8">
                <h2 class="text-xl font-semibold mb-4 text-blue-400 border-b border-gray-700 pb-2">
                    <i class="fas fa-chart-bar mr-2"></i>Статистика использования
                </h2>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
                    <div class="bg-gray-800 rounded-lg p-4 shadow-lg border-l-4 border-blue-500">
                        <div class="flex justify-between items-start">
                            <div>
                                <p class="text-gray-400 text-sm">Всего конвертаций</p>
                                <p class="text-2xl font-bold"><?= $stats['total_conversions'] ?></p>
                            </div>
                            <div class="bg-blue-500/20 p-3 rounded-full">
                                <i class="fas fa-exchange-alt text-blue-400"></i>
                            </div>
                        </div>
                    </div>

                    <div class="bg-gray-800 rounded-lg p-4 shadow-lg border-l-4 border-green-500">
                        <div class="flex justify-between items-start">
                            <div>
                                <p class="text-gray-400 text-sm">Сегодня</p>
                                <p class="text-2xl font-bold"><?= $stats['today_conversions'] ?></p>
                            </div>
                            <div class="bg-green-500/20 p-3 rounded-full">
                                <i class="fas fa-calendar-day text-green-400"></i>
                            </div>
                        </div>
                    </div>

                    <div class="bg-gray-800 rounded-lg p-4 shadow-lg border-l-4 border-red-500">
                        <div class="flex justify-between items-start">
                            <div>
                                <p class="text-gray-400 text-sm">Ошибок</p>
                                <p class="text-2xl font-bold"><?= $stats['error_count'] ?></p>
                            </div>
                            <div class="bg-red-500/20 p-3 rounded-full">
                                <i class="fas fa-exclamation-triangle text-red-400"></i>
                            </div>
                        </div>
                    </div>

                    <div class="bg-gray-800 rounded-lg p-4 shadow-lg border-l-4 border-purple-500">
                        <div class="flex justify-between items-start">
                            <div>
                                <p class="text-gray-400 text-sm">Активных пользователей</p>
                                <p class="text-2xl font-bold"><?= count($stats['active_users']) ?></p>
                            </div>
                            <div class="bg-purple-500/20 p-3 rounded-full">
                                <i class="fas fa-users text-purple-400"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <div class="bg-gray-800 rounded-lg p-4 shadow-lg flex flex-col">
                        <h3 class="font-medium mb-3 text-gray-300">
                            <i class="fas fa-file-alt mr-2"></i>Популярные форматы
                        </h3>
                        <div class="flex-1 min-h-[250px] flex items-center justify-center">
                            <?php if (empty($stats['popular_formats'])): ?>
                                <p class="text-gray-400 text-center py-10">Нет данных о популярных форматах</p>
                            <?php else: ?>
                                <canvas id="formatChart" class="max-h-full max-w-full"
                                    data-labels='<?= json_encode(array_column($stats['popular_formats'], 'format')) ?>'
                                    data-counts='<?= json_encode(array_column($stats['popular_formats'], 'count')) ?>'>
                                </canvas>
                            <?php endif; ?>
                        </div>
                    </div>



                    <div class="bg-gray-800 rounded-lg p-3 shadow-lg">
                        <h3 class="font-medium mb-3 text-gray-300 text-sm">
                            <i class="fas fa-user-clock mr-2"></i>Самые активные пользователи
                        </h3>

                        <?php if (!empty($stats['active_users'])): ?>
                            <div class="space-y-2">
                                <?php foreach ($stats['active_users'] as $user): ?>
                                    <div
                                        class="bg-gray-700/50 rounded p-3 flex items-center gap-3 shadow hover:shadow-md transition-shadow w-full">
                                        <div class="w-8 h-8 rounded-full overflow-hidden bg-blue-500/20 flex-shrink-0">
                                            <img src="../assets/img/other/<?= htmlspecialchars($user['avatar']) ?>"
                                                alt="<?= htmlspecialchars($user['username']) ?>"
                                                class="w-full h-full object-cover">
                                        </div>

                                        <div class="flex-1 min-w-0">
                                            <div class="flex items-center gap-1.5 flex-wrap">
                                                <span class="text-sm font-medium text-gray-100 truncate max-w-[120px]">
                                                    <?= htmlspecialchars($user['username']) ?>
                                                </span>

                                                <?php if ($user['last_ip']): ?>
                                                    <span class="text-xs text-gray-400">
                                                        (<?= htmlspecialchars($user['last_ip']) ?>)
                                                    </span>
                                                <?php endif; ?>

                                                <?= AdminCore::formatUserAgentBadge($adminCore->getLastAgentByUserId((int) $user['id']) ?? '') ?>
                                            </div>

                                            <div class="flex justify-between items-center text-xs text-gray-400 mt-0.5">
                                                <div class="truncate">
                                                    <?= $user['conversions_count'] ?> конвертаций |
                                                    <span class="capitalize <?=
                                                        $user['role'] === 'admin' ? 'text-purple-400' :
                                                        ($user['role'] === 'moderator' ? 'text-blue-400' : 'text-gray-400')
                                                        ?>">
                                                        <?= htmlspecialchars($user['role']) ?>
                                                    </span>
                                                </div>
                                                <span class="bg-gray-600 px-1.5 py-0.5 rounded text-xs">
                                                    <?= $user['conversions_count'] ?>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <p class="text-gray-400 text-center py-4 text-sm">Нет данных о активных пользователях</p>
                        <?php endif; ?>
                    </div>


                    <div class="bg-gray-800 rounded-lg p-4 shadow-lg flex flex-col">
                        <h3 class="font-medium mb-3 text-gray-300">
                            <i class="fas fa-browser mr-2"></i>Популярные браузеры
                        </h3>
                        <div class="flex-1 min-h-[250px] flex items-center justify-center">
                            <?php if (empty($agents)): ?>
                                <p class="text-gray-400 text-center py-10">Нет данных о популярных браузерах</p>
                            <?php else: ?>
                                <canvas id="userAgentChart" class="max-h-full max-w-full"
                                    data-labels='<?= json_encode(array_keys($agents)) ?>'
                                    data-counts='<?= json_encode(array_values($agents)) ?>'>
                                </canvas>
                            <?php endif; ?>
                        </div>
                    </div>

                </div>
            </section>

            <section id="history">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-xl font-semibold text-blue-400 border-b border-gray-700 pb-2">
                        <i class="fas fa-clipboard-list mr-2"></i>История конвертаций
                    </h2>
                    <?php if (!empty($logs)): ?>
                        <div class="flex flex-wrap gap-3 items-center">
                            <label for="perPageSelect" class="text-sm text-gray-400 hidden sm:inline">Показывать:</label>
                            <select id="perPageSelect"
                                class="bg-gray-800 text-gray-100 px-4 py-2 rounded-lg border border-gray-600 hover:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500 transition duration-200 text-sm">
                                <option value="50" <?= $perPage == 50 ? 'selected' : '' ?>>50 записей</option>
                                <option value="100" <?= $perPage == 100 ? 'selected' : '' ?>>100 записей</option>
                                <option value="250" <?= $perPage == 250 ? 'selected' : '' ?>>250 записей</option>
                                <option value="500" <?= $perPage == 500 ? 'selected' : '' ?>>500 записей</option>
                            </select>

                            <button onclick="window.location.reload()"
                                class="flex items-center gap-2 bg-blue-600 hover:bg-blue-500 text-white px-4 py-2 rounded-lg text-sm font-medium shadow-sm transition duration-200">
                                <i class="fas fa-sync-alt animate-spin-on-hover"></i>
                                <span class="hidden sm:inline">Обновить</span>
                            </button>
                        </div>
                    <?php endif; ?>

                </div>

                <div class="bg-gray-800 rounded-lg shadow-lg overflow-hidden">
                    <div class="overflow-x-auto">
                        <?php if (!empty($logs)): ?>
                            <table class="w-full text-sm">
                                <thead class="bg-gray-900">
                                    <tr class="text-left">
                                        <th class="p-3 font-semibold text-gray-300">Дата и время</th>
                                        <th class="p-3 font-semibold text-gray-300">IP адрес</th>
                                        <th class="p-3 font-semibold text-gray-300">Пользователь</th>
                                        <th class="p-3 font-semibold text-gray-300">Статус</th>
                                        <th class="p-3 font-semibold text-gray-300">Исходный файл</th>
                                        <th class="p-3 font-semibold text-gray-300">Форматы</th>
                                        <th class="p-3 font-semibold text-gray-300">Размеры</th>
                                        <th class="p-3 font-semibold text-gray-300">Браузер</th>
                                        <th class="p-3 font-semibold text-gray-300">Детали</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-700">
                                    <?php foreach ($logs as $log): ?>
                                        <tr class="hover:bg-gray-700/50 transition-colors">
                                            <td class="p-3 text-gray-400 whitespace-nowrap">
                                                <?= date('d.m.Y H:i:s', strtotime($log['created_at'])) ?>
                                            </td>
                                            <td class="p-3 font-mono text-gray-400">
                                                <?= htmlspecialchars($log['ip']) ?>
                                            </td>
                                            <td class="p-3 text-gray-400">
                                                <?php if (!empty($log['user_info']) && !empty($log['user_info']['username'])): ?>
                                                    <?= htmlspecialchars($log['user_info']['username'], ENT_QUOTES, 'UTF-8') ?>
                                                    <span class="text-gray-500 font-mono">(ID: <?= (int) $log['user_id'] ?>)</span>
                                                <?php elseif (!empty($log['user_id'])): ?>
                                                    <span class="text-gray-500 font-mono">User #<?= (int) $log['user_id'] ?></span>
                                                <?php else: ?>
                                                    <span class="text-gray-500 italic">Unknown</span>
                                                <?php endif; ?>
                                            </td>
                                            <td class="p-3">
                                                <span
                                                    class="px-2 py-1 rounded-full text-xs font-medium 
                                            <?= $log['status'] === 'success' ? 'bg-green-500/20 text-green-400' : 'bg-red-500/20 text-red-400' ?>">
                                                    <?= $log['status'] === 'success' ? 'Успех' : 'Ошибка' ?>
                                                </span>
                                            </td>
                                            <td
                                                class="p-3 text-gray-400 max-w-[100px] md:max-w-[200px] lg:max-w-[300px] overflow-hidden text-ellipsis whitespace-nowrap">
                                                <?= htmlspecialchars($log['original_name']) ?>
                                            </td>

                                            <td class="p-3">
                                                <?= htmlspecialchars(strtoupper($log['original_format'])) ?>
                                                →
                                                <?= $log['new_format'] ? htmlspecialchars(strtoupper($log['new_format'])) : '-' ?>
                                            </td>
                                            <td class="p-3">
                                                <?= $log['original_size'] ? AdminCore::formatFileSize($log['original_size']) : 'NULL' ?>
                                                →
                                                <?= $log['new_size'] ? AdminCore::formatFileSize($log['new_size']) : 'NULL' ?>
                                            </td>
                                            <td class="p-3 text-gray-300 text-sm">
                                                <?php
                                                [$browserName, $iconClass, $color] = AdminCore::getBrowserInfo($log['user_agent'] ?? '');
                                                ?>
                                                <span
                                                    class="inline-flex items-center gap-1 px-2 py-1 rounded bg-gray-700 text-xs"
                                                    style="color: <?= $color ?>;">
                                                    <i class="fab <?= $iconClass ?>"></i> <?= htmlspecialchars($browserName) ?>
                                                </span>
                                            </td>
                                            <td class="p-3">
                                                <?php if ($log['status'] === 'error'): ?>
                                                    <span
                                                        class="error-message text-gray-400 font-mono"><?= htmlspecialchars($log['error_message'] ?? 'NOTHING') ?></span>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        <?php else: ?>
                            <tr>
                                <p class="text-gray-400 text-center py-10">Нет данных о последних конвертаций.</p>
                            </tr>
                        <?php endif; ?>
                    </div>

                    <div class="p-3 text-sm text-gray-400 bg-gray-900 border-t border-gray-700">
                        <div class="flex justify-between items-center">
                            <?php if (!empty($logs)): ?>
                                <span>Всего записей: <span class="font-bold text-white"><?= $totalLogs ?></span></span>
                                <div class="flex items-center space-x-2">
                                    <span>Страница <?= $page ?> из <?= $totalPages ?></span>
                                    <button id="prevPage" data-current-page="<?= $page ?>" <?= $page <= 1 ? 'disabled' : '' ?>>
                                        <i class="fas fa-chevron-left"></i> Предыдущая
                                    </button>

                                    <button id="nextPage" data-current-page="<?= $page ?>"
                                        data-total-pages="<?= $totalPages ?>" <?= $page >= $totalPages ? 'disabled' : '' ?>>
                                        Следующая <i class="fas fa-chevron-right"></i>
                                    </button>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </section>
    </main>
    <button id="scrollToTopBtn"
        class="fixed bottom-8 right-8 w-12 h-12 bg-indigo-600 hover:bg-indigo-700 text-white rounded-full shadow-lg transition-all duration-300 opacity-0 invisible flex items-center justify-center">
        <i class="fas fa-arrow-up text-xl"></i>
    </button>
</body>

</html>