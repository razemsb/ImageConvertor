<?php
require_once "../config/DatabaseConnect.php";
require_once 'AdminCore.php';

$pdo = DB::connect();

$adminCore = AdminCore::init($pdo);
$adminCore->requireAdmin();

$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
$perPage = isset($_GET['perPage']) && in_array($_GET['perPage'], [25, 50, 100]) ? (int)$_GET['perPage'] : 25;

$stats = $adminCore->getConversionStats();
$logData = $adminCore->getConversionLogs($page, $perPage);
$logs = $logData['logs'];
$totalLogs = $logData['total'];
$totalPages = $logData['total_pages'];

$csrfToken = $adminCore->generateCsrfToken();
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
    <script src="../assets/vendors/font-awesome/js/all.min.js" crossorigin="anonymous"></script>
    <script src="../assets/vendors/chartjs/chart.js"></script>
    <style>
        ::-webkit-scrollbar {
            width: 8px;
        }

        ::-webkit-scrollbar-track {
            background: #1f2937;
        }

        ::-webkit-scrollbar-thumb {
            background: #4b5563;
            border-radius: 4px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: #6b7280;
        }

        .error-details {
            display: none;
            position: absolute;
            background: #1f2937;
            border: 1px solid #374151;
            padding: 10px;
            border-radius: 5px;
            z-index: 100;
            max-width: 400px;
            word-wrap: break-word;
        }
    </style>
</head>
<body class="bg-gray-900 text-gray-100 min-h-screen">
    <!-- Шапка -->
    <header class="bg-gray-800 shadow-lg">
        <div class="container mx-auto px-4 py-4 flex justify-between items-center">
            <h1 class="text-2xl font-bold text-blue-400">
                <img src="../assets/img/favicon/favicon-96x96.png" class="logotype" alt="Logotype">
            </h1>
            <div class="flex items-center space-x-4">
                <span class="text-sm text-gray-400 hidden md:inline">
                    <i class="fas fa-user-shield mr-1"></i>
                    <?= htmlspecialchars($_SESSION['admin_username'] ?? 'Администратор') ?>
                </span>
                <a href="logout.php" class="bg-gray-700 hover:bg-gray-600 px-3 py-2 rounded-lg transition-colors">
                    <i class="fas fa-sign-out-alt mr-1"></i> Выход
                </a>
            </div>
        </div>
    </header>

    <main class="container mx-auto px-4 py-6">
        <!-- Статистика -->
        <section class="mb-8">
            <h2 class="text-xl font-semibold mb-4 text-blue-400 border-b border-gray-700 pb-2">
                <i class="fas fa-chart-bar mr-2"></i>Статистика использования
            </h2>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
                <!-- Карточка общих конвертаций -->
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

                <!-- Карточка конвертаций за сегодня -->
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

                <!-- Карточка ошибок -->
                <div class="bg-gray-800 rounded-lg p-4 shadow-lg border-l-4 border-red-500">
                    <div class="flex justify-between items-start">
                        <div>
                            <p class="text-gray-400 text-sm">Ошибок</p>
                            <p class="text-2xl font-bold"><?= $stats['error_rate'] ?>%</p>
                        </div>
                        <div class="bg-red-500/20 p-3 rounded-full">
                            <i class="fas fa-exclamation-triangle text-red-400"></i>
                        </div>
                    </div>
                </div>

                <!-- Карточка активных пользователей -->
                <div class="bg-gray-800 rounded-lg p-4 shadow-lg border-l-4 border-purple-500">
                    <div class="flex justify-between items-start">
                        <div>
                            <p class="text-gray-400 text-sm">Активных IP</p>
                            <p class="text-2xl font-bold"><?= count($stats['active_users']) ?></p>
                        </div>
                        <div class="bg-purple-500/20 p-3 rounded-full">
                            <i class="fas fa-users text-purple-400"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <div class="bg-gray-800 rounded-lg p-4 shadow-lg">
                    <h3 class="font-medium mb-3 text-gray-300">
                        <i class="fas fa-file-alt mr-2"></i>Популярные форматы
                    </h3>
                    <div class="h-64">
                        <?php if (empty($stats['popular_formats'])): ?>
                            <p class="text-gray-400 text-center py-10">Нет данных о популярных форматах</p>
                        <?php else: ?>
                            <canvas id="formatChart"></canvas>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Список активных пользователей -->
                <div class="bg-gray-800 rounded-lg p-4 shadow-lg">
                    <h3 class="font-medium mb-3 text-gray-300">
                        <i class="fas fa-user-clock mr-2"></i>Самые активные пользователи
                    </h3>
                    <div class="space-y-3">
                        <?php foreach ($stats['active_users'] as $user): ?>
                            <div class="flex justify-between items-center bg-gray-700/50 p-3 rounded-lg">
                                <div class="flex items-center">
                                    <!-- Фиксированный размер аватарки с выравниванием -->
                                    <div
                                        class="bg-blue-500/20 rounded-full mr-3 flex items-center justify-center w-10 h-10">
                                        <img src="../assets/img/other/<?= htmlspecialchars($user['avatar']) ?>"
                                            alt="<?= htmlspecialchars($user['username']) ?>" class="w-full h-full"
                                            style="object-fit: cover !important; border-radius: 50%;">
                                    </div>
                                    <div>
                                        <div class="font-medium">
                                            <?= htmlspecialchars($user['username']) ?>
                                            <?php if ($user['last_ip']): ?>
                                                <span class="text-xs font-normal text-gray-400 ml-2">
                                                    (<?= htmlspecialchars($user['last_ip']) ?>)
                                                </span>
                                            <?php endif; ?>
                                        </div>
                                        <div class="text-xs text-gray-400">
                                            <?= $user['conversions_count'] ?> конвертаций |
                                            <span class="capitalize <?=
                                                $user['role'] === 'admin' ? 'text-purple-400' :
                                                ($user['role'] === 'moderator' ? 'text-blue-400' : 'text-gray-400')
                                                ?>">
                                                <?= htmlspecialchars($user['role']) ?>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                <span class="bg-gray-600 px-2 py-1 rounded text-sm">
                                    <?= $user['conversions_count'] ?>
                                </span>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </section>

        <!-- Логи конвертаций -->
        <section id="history">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-xl font-semibold text-blue-400 border-b border-gray-700 pb-2">
                    <i class="fas fa-clipboard-list mr-2"></i>История конвертаций
                </h2>
                <div class="flex space-x-3">
                    <select id="perPageSelect"
                        class="bg-gray-700 text-gray-100 px-3 py-2 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="25" <?= $perPage == 25 ? 'selected' : '' ?>>25 записей</option>
                        <option value="50" <?= $perPage == 50 ? 'selected' : '' ?>>50 записей</option>
                        <option value="100" <?= $perPage == 100 ? 'selected' : '' ?>>100 записей</option>
                    </select>
                    <button onclick="window.location.reload()"
                        class="bg-gray-700 hover:bg-gray-600 px-4 py-2 rounded-lg transition-colors">
                        <i class="fas fa-sync-alt mr-2"></i>Обновить
                    </button>
                </div>
            </div>

            <div class="bg-gray-800 rounded-lg shadow-lg overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-gray-900">
                            <tr class="text-left">
                                <th class="p-3 font-semibold text-gray-300">Дата и время</th>
                                <th class="p-3 font-semibold text-gray-300">IP адрес</th>
                                <th class="p-3 font-semibold text-gray-300">Статус</th>
                                <th class="p-3 font-semibold text-gray-300">Исходный файл</th>
                                <th class="p-3 font-semibold text-gray-300">Форматы</th>
                                <th class="p-3 font-semibold text-gray-300">Размеры</th>
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
                                    <td class="p-3">
                                        <span
                                            class="px-2 py-1 rounded-full text-xs font-medium 
                                            <?= $log['status'] === 'success' ? 'bg-green-500/20 text-green-400' : 'bg-red-500/20 text-red-400' ?>">
                                            <?= $log['status'] === 'success' ? 'Успех' : 'Ошибка' ?>
                                        </span>
                                    </td>
                                    <td class="p-3 text-gray-400">
                                        <?= htmlspecialchars($log['original_name']) ?>
                                    </td>
                                    <td class="p-3">
                                        <?= htmlspecialchars(strtoupper($log['original_format'])) ?>
                                        →
                                        <?= $log['new_format'] ? htmlspecialchars(strtoupper($log['new_format'])) : '-' ?>
                                    </td>
                                    <td class="p-3">
                                        <?= $log['original_size'] ? AdminCore::formatFileSize($log['original_size']) : '-' ?>
                                        →
                                        <?= $log['new_size'] ? AdminCore::formatFileSize($log['new_size']) : '-' ?>
                                    </td>
                                    <td class="p-3">
                                        <?php if ($log['status'] === 'error' && !empty($log['error_message'])): ?>
                                            <button onclick="showErrorDetails('error-details-<?= $log['id'] ?>')"
                                                class="text-gray-400 hover:text-blue-400 p-1">
                                                <i class="fas fa-info-circle"></i>
                                            </button>
                                            <div id="error-details-<?= $log['id'] ?>" class="error-details">
                                                <?= htmlspecialchars($log['error_message']) ?>
                                            </div>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <div class="p-3 text-sm text-gray-400 bg-gray-900 border-t border-gray-700">
                    <div class="flex justify-between items-center">
                        <span>Всего записей: <span class="font-bold text-white"><?= $totalLogs ?></span></span>
                        <div class="flex items-center space-x-2">
                            <span>Страница <?= $page ?> из <?= $totalPages ?></span>
                            <button id="prevPage" data-current-page="<?= $page ?>" <?= $page <= 1 ? 'disabled' : '' ?>>
                                <i class="fas fa-chevron-left"></i> Предыдущая
                            </button>

                            <button id="nextPage" data-current-page="<?= $page ?>" data-total-pages="<?= $totalPages ?>"
                                <?= $page >= $totalPages ? 'disabled' : '' ?>>
                                Следующая <i class="fas fa-chevron-right"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>
    <button id="scrollToTopBtn"
        class="fixed bottom-8 right-8 w-12 h-12 bg-indigo-600 hover:bg-indigo-700 text-white rounded-full shadow-lg transition-all duration-300 opacity-0 invisible flex items-center justify-center">
        <i class="fas fa-arrow-up text-xl"></i>
    </button>
    <script>
        const scrollToTopBtn = document.getElementById('scrollToTopBtn');
        const scrollThreshold = 200;

        window.addEventListener('scroll', function () {
            if (window.pageYOffset > scrollThreshold) {
                scrollToTopBtn.classList.add('visible');
            } else {
                scrollToTopBtn.classList.remove('visible');
            }
        });
        scrollToTopBtn.addEventListener('click', function () {
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        });

        function updateUrlParams(params) {
            const url = new URL(window.location.href);


            Object.keys(params).forEach(key => {
                if (params[key] !== null && params[key] !== undefined) {
                    url.searchParams.set(key, params[key]);
                } else {
                    url.searchParams.delete(key);
                }
            });


            window.location.href = url.toString();
        }


        const prevPageBtn = document.getElementById('prevPage');
        if (prevPageBtn) {
            prevPageBtn.addEventListener('click', function (e) {
                if (this.disabled) return;

                const currentPage = parseInt(this.dataset.currentPage) || 1;
                updateUrlParams({ page: currentPage - 1 });
            });
        }


        const nextPageBtn = document.getElementById('nextPage');
        if (nextPageBtn) {
            nextPageBtn.addEventListener('click', function (e) {
                if (this.disabled) return;

                const currentPage = parseInt(this.dataset.currentPage) || 1;
                updateUrlParams({ page: currentPage + 1 });
            });
        }


        const perPageSelect = document.getElementById('perPageSelect');
        if (perPageSelect) {
            perPageSelect.addEventListener('change', function () {
                updateUrlParams({
                    perPage: this.value,
                    page: 1
                });
            });
        }


        function setupErrorDetails() {
            document.querySelectorAll('[data-error-details]').forEach(btn => {
                btn.addEventListener('click', function () {
                    const detailsId = this.getAttribute('data-error-details');
                    const details = document.getElementById(detailsId);

                    if (!details) return;


                    document.querySelectorAll('.error-details').forEach(el => {
                        if (el.id !== detailsId) el.style.display = 'none';
                    });


                    details.style.display = details.style.display === 'block' ? 'none' : 'block';


                    if (details.style.display === 'block') {
                        const rect = this.getBoundingClientRect();
                        details.style.top = `${rect.bottom + window.scrollY}px`;
                        details.style.left = `${rect.left + window.scrollX}px`;
                    }
                });
            });


            document.addEventListener('click', function (e) {
                if (!e.target.closest('[data-error-details]') && !e.target.closest('.error-details')) {
                    document.querySelectorAll('.error-details').forEach(el => {
                        el.style.display = 'none';
                    });
                }
            });
        }

        setupErrorDetails();

        function showErrorDetails(id) {

            document.querySelectorAll('.error-details').forEach(el => {
                el.style.display = 'none';
            });


            const details = document.getElementById(id);
            if (details) {
                details.style.display = 'block';


                const btn = details.previousElementSibling;
                const rect = btn.getBoundingClientRect();
                details.style.top = `${rect.bottom + window.scrollY}px`;
                details.style.left = `${rect.left + window.scrollX}px`;


                setTimeout(() => {
                    const clickHandler = (e) => {
                        if (!details.contains(e.target)) {
                            details.style.display = 'none';
                            document.removeEventListener('click', clickHandler);
                        }
                    };
                    document.addEventListener('click', clickHandler);
                }, 10);
            }
        }

        document.addEventListener('DOMContentLoaded', function () {
            const canvas = document.getElementById('formatChart');
            if (!canvas) {
                console.error('Canvas element for formatChart not found');
                return;
            }

            const ctx = canvas.getContext('2d');
            if (!ctx) {
                console.error('Failed to get canvas context');
                return;
            }


            const formatLabels = <?php echo json_encode(array_column($stats['popular_formats'], 'format')); ?>;
            const formatCounts = <?php echo json_encode(array_column($stats['popular_formats'], 'count')); ?>;


            if (!formatLabels || !formatCounts || formatLabels.length === 0 || formatCounts.length === 0) {
                canvas.style.display = 'none';
                const noDataMsg = document.createElement('p');
                noDataMsg.textContent = 'Нет данных о популярных форматах';
                noDataMsg.className = 'text-gray-400 text-center py-10';
                canvas.parentNode.appendChild(noDataMsg);
                return;
            }


            new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: formatLabels,
                    datasets: [{
                        data: formatCounts,
                        backgroundColor: [
                            'rgba(59, 130, 246, 0.7)',
                            'rgba(16, 185, 129, 0.7)',
                            'rgba(245, 158, 11, 0.7)',
                            'rgba(139, 92, 246, 0.7)'
                        ],
                        borderColor: [
                            'rgba(59, 130, 246, 1)',
                            'rgba(16, 185, 129, 1)',
                            'rgba(245, 158, 11, 1)',
                            'rgba(139, 92, 246, 1)'
                        ],
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'right',
                            labels: {
                                color: '#d1d5db',
                                font: {
                                    family: "'Inter', sans-serif"
                                }
                            }
                        },
                        tooltip: {
                            callbacks: {
                                label: function (context) {
                                    const label = context.label || '';
                                    const value = context.raw || 0;
                                    const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                    const percentage = total ? Math.round((value / total) * 100) : 0;
                                    return `${label}: ${value} (${percentage}%)`;
                                }
                            }
                        }
                    },
                    cutout: '65%'
                }
            });
        });
    </script>
</body>

</html>