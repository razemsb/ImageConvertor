<?php
require_once 'config/csrf.php';
require_once 'config/auth_config.php';
$pdo = DB::connect();
$errorsLogin = [];
$errorsRegister = [];
$activeTab = 'login'; 

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verifyCsrfToken($_POST['csrf_token'] ?? '')) {
        if (isset($_POST['action']) && $_POST['action'] === 'register') {
            $errorsRegister[] = "Ошибка безопасности";
            $activeTab = 'register';
        } else {
            $errorsLogin[] = "Ошибка безопасности";
            $activeTab = 'login';
        }
    } else {
        if (isset($_POST['action']) && $_POST['action'] === 'login') {
            // обработка входа
            $username = trim($_POST['username'] ?? '');
            $password = $_POST['password'] ?? '';
            if (!$username || !$password) {
                $errorsLogin[] = "Заполните все поля";
            } else {
                $result = loginUser($pdo, $username, $password);
                if ($result === true) {
                    header("Location: index.php");
                    exit;
                } else {
                    $errorsLogin[] = $result;
                }
            }
        } elseif (isset($_POST['action']) && $_POST['action'] === 'register') {
            $username = trim($_POST['username'] ?? '');
            $password = $_POST['password'] ?? '';
            $passwordConfirm = $_POST['password_confirm'] ?? '';
            if (!$username || !$password || !$passwordConfirm) {
                $errorsRegister[] = "Заполните все поля";
            } elseif ($password !== $passwordConfirm) {
                $errorsRegister[] = "Пароли не совпадают";
            } else {
                $result = registerUser($pdo, $username, $password);
                if ($result === true) {
                    $activeTab = 'login';
                } else {
                    $errorsRegister[] = $result;
                    $activeTab = 'register';
                }
            }
        }
    }
}

$csrfToken = generateCsrfToken();
?>
<!DOCTYPE html>
<html lang="ru" class="h-full bg-gray-100">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Вход и регистрация — Enigma</title>
    <link rel="icon" type="image/png" href="assets/img/favicon/favicon-96x96.png" sizes="96x96" />
    <link rel="icon" type="image/svg+xml" href="assets/img/favicon/favicon.svg" />
    <link rel="shortcut icon" href="assets/img/favicon/favicon.ico" />
    <link rel="apple-touch-icon" sizes="180x180" href="assets/img/favicon/apple-touch-icon.png" />
    <meta name="apple-mobile-web-app-title" content="MyWebSite" />
    <link rel="manifest" href="assets/img/favicon/site.webmanifest" />
    <script src="assets/vendors/tailwindcss/script.js"></script>
    <link rel="stylesheet" href="assets/vendors/font-awesome/css/all.min.css">
    <script src="assets/js/auth.js" defer></script>
    <style>
        .form-container>form {
            transition: opacity 0.3s ease, transform 0.3s ease;
        }

        .hidden-form {
            opacity: 0;
            transform: translateX(50px);
            position: absolute;
            pointer-events: none;
            user-select: none;
        }

        .visible-form {
            opacity: 1;
            transform: translateX(0);
            position: relative;
            pointer-events: auto;
            user-select: auto;
        }
    </style>
</head>

<body class="h-full flex">
    <div class="w-2/5 bg-white flex flex-col justify-center px-12 py-16 shadow-lg relative overflow-hidden min-h-screen">
        <a href="index.html" class="absolute top-5 left-5 text-gray-600 hover:text-gray-900 transition" aria-label="Назад">
            <i class="fa-solid fa-arrow-left text-2xl"></i>
        </a>

        <div class="mb-8 text-center">
            <h1 class="text-4xl font-extrabold text-gray-800 mb-2">Добро пожаловать в Enigma</h1>
            <p class="text-gray-500">Вход или регистрация для продолжения</p>
        </div>

        <div class="flex justify-center mb-10 space-x-6">
            <button id="tabLoginBtn" class="tab-btn px-6 py-2 rounded-t-lg font-semibold text-lg border-b-4" data-target="login">Вход</button>
            <button id="tabRegisterBtn" class="tab-btn px-6 py-2 rounded-t-lg font-semibold text-lg border-b-4" data-target="register">Регистрация</button>
        </div>

        <div class="form-container relative min-h-[300px]">

            <form id="loginForm" method="POST" class="space-y-6 visible-form" novalidate>
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">
                <input type="hidden" name="action" value="login">

                <?php if ($errorsLogin): ?>
                    <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                        <ul class="list-disc list-inside">
                            <?php foreach ($errorsLogin as $err): ?>
                                <li><?= htmlspecialchars($err) ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>

                <div>
                    <label for="loginUsername" class="block mb-1 font-semibold text-gray-700">Имя пользователя</label>
                    <input id="loginUsername" name="username" type="text" required class="w-full px-4 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-indigo-500" />
                </div>

                <div class="relative">
                    <label for="loginPassword" class="block mb-1 font-semibold text-gray-700">Пароль</label>
                    <input id="loginPassword" name="password" type="password" required
                        class="w-full px-4 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-indigo-500" autocomplete="false" />
                    <button type="button"
                        class="togglePassword absolute right-3 top-9 text-gray-400 hover:text-indigo-600"
                        aria-label="Показать пароль">
                        <i class="fa-solid fa-eye"></i>
                    </button>
                </div>

                <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-2 rounded transition">Войти</button>
            </form>

            <form id="registerForm" method="POST" class="space-y-6 hidden-form" novalidate>
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">
                <input type="hidden" name="action" value="register">

                <?php if ($errorsRegister): ?>
                    <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                        <ul class="list-disc list-inside">
                            <?php foreach ($errorsRegister as $err): ?>
                                <li><?= htmlspecialchars($err) ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>

                <div>
                    <label for="registerUsername" class="block mb-1 font-semibold text-gray-700">Имя пользователя</label>
                    <input id="registerUsername" name="username" type="text" required minlength="3" maxlength="50" class="w-full px-4 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-pink-500" />
                </div>

                <div class="relative">
                    <label for="registerPassword" class="block mb-1 font-semibold text-gray-700">Пароль</label>
                    <input id="registerPassword" name="password" type="password" required minlength="6" class="w-full px-4 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-pink-500" />
                    <button type="button" class="togglePassword absolute right-3 top-9 text-gray-400 hover:text-pink-600" aria-label="Показать пароль">
                        <i class="fa-solid fa-eye"></i>
                    </button>
                </div>

                <div class="relative">
                    <label for="registerPasswordConfirm" class="block mb-1 font-semibold text-gray-700">Повторите пароль</label>
                    <input id="registerPasswordConfirm" name="password_confirm" type="password" required minlength="6" class="w-full px-4 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-pink-500" />
                    <button type="button" class="togglePassword absolute right-3 top-9 text-gray-400 hover:text-pink-600" aria-label="Показать пароль">
                        <i class="fa-solid fa-eye"></i>
                    </button>
                </div>

                <button type="submit" class="w-full bg-pink-600 hover:bg-pink-700 text-white font-semibold py-2 rounded transition">Зарегистрироваться</button>
            </form>
        </div>
    </div>
    <div class="w-3/5 bg-gradient-to-tr from-indigo-700 via-purple-800 to-pink-700 text-white p-20 flex flex-col justify-center items-center min-h-screen">
        <img src="https://images.unsplash.com/photo-1506744038136-46273834b3fb?auto=format&fit=crop&w=800&q=80" alt="Enigma illustration" class="rounded-lg shadow-lg mb-10 max-w-full h-auto">
        <h2 class="text-4xl font-extrabold mb-4">Добро пожаловать в Enigma</h2>
        <p class="max-w-lg text-lg text-center">Удобный и безопасный конвертер изображений с поддержкой разных форматов и качеств. Присоединяйтесь к нам и управляйте своими изображениями быстро и легко!</p>
    </div>
</body>
</html>