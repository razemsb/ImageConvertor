<?php
ob_start();
require_once "./config/DatabaseConnect.php";
require_once "./config/AuthConfig.php";

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

function normalizeUserAgent(string $userAgent): string
{
    $userAgent = strtolower($userAgent);


    if (str_contains($userAgent, 'yabrowser')) {
        return 'Yandex';
    }

    if (str_contains($userAgent, 'edg/')) {
        return 'Edge';
    }

    if (str_contains($userAgent, 'opr/') || str_contains($userAgent, 'opera')) {
        return 'Opera';
    }

    if (str_contains($userAgent, 'firefox') && !str_contains($userAgent, 'seamonkey')) {
        return 'Firefox';
    }


    if (str_contains($userAgent, 'chrome') && !str_contains($userAgent, 'edg/') && !str_contains($userAgent, 'opr/')) {
        return 'Chrome';
    }

    if (str_contains($userAgent, 'safari') && !str_contains($userAgent, 'chrome')) {
        return 'Safari';
    }

    return 'Другое';
}

function getNormalizedUserAgents(PDO $pdo): array
{
    $stmt = $pdo->query("SELECT user_agent FROM conversions WHERE user_agent IS NOT NULL AND user_agent != ''");

    $agents = [];

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $normalized = normalizeUserAgent($row['user_agent']);
        $agents[$normalized] = ($agents[$normalized] ?? 0) + 1;
    }

    arsort($agents);

    return $agents;
}

function getBrowserInfo(string $ua): array
{
    $ua = strtolower($ua);

    return match (true) {
        str_contains($ua, 'yabrowser') => ['Яндекс Браузер', 'fa-yandex-international', '#ffcc00'],
        str_contains($ua, 'opr') || str_contains($ua, 'opera gx') => ['Opera GX', 'fa-opera', 'oklch(57.7% 0.245 27.325)'],
        str_contains($ua, 'opera') => ['Opera', 'fa-opera', '#d03af7'],
        str_contains($ua, 'vivaldi') => ['Vivaldi', 'fa-v', '#ef3939'],
        str_contains($ua, 'brave') => ['Brave', 'fa-leaf', '#fb542b'],
        str_contains($ua, 'edg') => ['Edge', 'fa-edge', '#2a7cec'],
        str_contains($ua, 'samsungbrowser') => ['Samsung Internet', 'fa-mobile', '#1428a0'],
        str_contains($ua, 'ucbrowser') => ['UC Browser', 'fa-paw', '#ff7e00'],
        str_contains($ua, 'tor') => ['Tor Browser', 'fa-mask', '#7e4798'],
        str_contains($ua, 'chrome') && !str_contains($ua, 'edg') && !str_contains($ua, 'opr') && !str_contains($ua, 'yabrowser') && !str_contains($ua, 'vivaldi') && !str_contains($ua, 'brave') => ['Chrome', 'fa-chrome', '#f2af1c'],
        str_contains($ua, 'firefox') => ['Firefox', 'fa-firefox-browser', '#f25a29'],
        str_contains($ua, 'safari') && !str_contains($ua, 'chrome') => ['Safari', 'fa-safari', '#4ab0f7'],
        str_contains($ua, 'msie') || str_contains($ua, 'trident') => ['Internet Explorer', 'fa-internet-explorer', '#157dc3'],
        default => ['Неизвестно', 'fa-question-circle', '#999'],
    };
}

[$browserName, $iconClass, $color] = getBrowserInfo($_SERVER['HTTP_USER_AGENT'] ?? '');

$page_title = "Аккаунт";
include "./assets/include/header.php";
?>
<div class="min-h-screen bg-gradient-to-b from-white to-gray-50 py-14 px-4 font-sans text-gray-900">
    <div class="max-w-6xl mx-auto space-y-12">

        <section
            class="flex flex-col md:flex-row items-center md:items-start bg-white border border-gray-200 rounded-3xl shadow-[0_4px_24px_rgba(0,0,0,0.05)] p-10 gap-8">
            <img src="./assets/img/other/<?= htmlspecialchars($user['avatar'] ?? 'default-avatar.png') ?>" alt="Аватар"
                class="w-28 h-28 rounded-full border-4 border-white shadow-md object-cover transition-transform duration-300 hover:scale-105">

            <div class="flex-1 space-y-2 text-center md:text-left">
                <h1 class="text-3xl font-extrabold tracking-tight">
                    <?= htmlspecialchars($user['username']) ?>
                    <?php if ($auth->isAdmin()): ?>
                        <span
                            class="ml-2 px-2 py-1 bg-red-100 text-red-700 rounded-md text-sm font-semibold">(Администратор)</span>
                    <?php endif; ?>
                </h1>
                <p class="text-sm text-gray-500">Зарегистрирован: <?= date('d.m.Y', strtotime($user['created_at'])) ?>
                </p>
                <div
                    class="inline-flex items-center gap-2 px-3 py-1 bg-emerald-100 text-emerald-700 rounded-full text-sm font-medium">
                    <span class="w-2 h-2 bg-emerald-500 rounded-full"></span> Онлайн
                </div>
            </div>
        </section>

        <section class="grid grid-cols-1 sm:grid-cols-3 gap-5">
            <div class="bg-white border border-gray-200 rounded-xl p-5 shadow-md hover:shadow-lg transition-all">
                <p class="text-sm text-gray-600 mb-3 font-medium">Всего конвертаций</p>
                <div class="flex justify-center">
                    <span
                        class="inline-flex items-center justify-center min-w-[160px] px-4 py-3 rounded-lg bg-gray-50 text-gray-900 font-semibold text-base border border-gray-300 shadow-sm">
                        <span id="AllConversionCount" class="text-indigo-600">0</span>
                    </span>
                </div>
            </div>

            <div class="bg-white border border-gray-200 rounded-xl p-5 shadow-md hover:shadow-lg transition-all">
                <p class="text-sm text-gray-600 mb-3 font-medium">Последний IP</p>
                <div class="flex justify-center">
                    <span
                        class="inline-flex items-center justify-center min-w-[160px] px-4 py-3 rounded-lg bg-gray-50 text-gray-900 font-semibold text-base border border-gray-300 shadow-sm truncate">
                        <?= htmlspecialchars($user['last_ip'] ?? $_SERVER['REMOTE_ADDR']) ?>
                    </span>
                </div>
            </div>

            <div class="bg-white border border-gray-200 rounded-xl p-5 shadow-md hover:shadow-lg transition-all">
                <p class="text-sm text-gray-600 mb-3 font-medium">Платформа</p>
                <div class="flex justify-center">
                    <span
                        class="inline-flex items-center justify-center min-w-[160px] px-4 py-3 rounded-lg bg-gray-50 text-gray-900 font-semibold text-base border border-gray-300 shadow-sm truncate"
                        style="color: <?= $color ?>;">
                        <i class="fab <?= $iconClass ?> mr-2 text-lg"></i>
                        <span class="truncate"><?= htmlspecialchars($browserName) ?></span>
                    </span>
                </div>
            </div>
        </section>

        <section class="bg-white border border-gray-200 rounded-3xl p-10 shadow-sm">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-xl font-semibold flex items-center gap-2">
                    <i class="fas fa-clock-rotate-left text-indigo-500"></i> История конвертаций
                </h2>
                <button id="deleteHistory"
                    class="text-sm text-red-500 font-medium hover:underline flex items-center gap-1">
                    <i class="fa-solid fa-trash-can"></i> Очистить всё
                </button>
            </div>

            <div id="history" class="space-y-4">
                <div id="noHistory"
                    class="border border-dashed border-gray-300 p-6 rounded-xl text-gray-400 text-center text-sm bg-gray-50">
                    История пуста. Загрузи файл — и отслеживай магию тут 🔮
                </div>
            </div>
        </section>
    </div>
</div>
<?php
include "./assets/include/footer.php";
ob_end_flush();