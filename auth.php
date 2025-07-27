<?php
require_once "./config/DatabaseConnect.php";
require_once './config/AuthConfig.php';
$pdo = DB::connect();
$auth = Auth::getInstance($pdo);

$errorsLogin = [];
$errorsRegister = [];
$activeTab = 'login';

if (isset($_GET['action'])) {
    $activeTab = $_GET['action'] === 'register' ? 'register' : 'login';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!$auth->verifyCsrfToken($_POST['csrf_token'] ?? '')) {
        $errors = ["Ошибка безопасности. Пожалуйста, обновите страницу."];
        if (isset($_POST['action']) && $_POST['action'] === 'register') {
            $errorsRegister = $errors;
            $activeTab = 'register';
        } else {
            $errorsLogin = $errors;
            $activeTab = 'login';
        }
    } else {
        if (isset($_POST['action']) && $_POST['action'] === 'login') {
            $username = trim($_POST['username'] ?? '');
            $password = $_POST['password'] ?? '';

            if (empty($username) || empty($password)) {
                $errorsLogin[] = "Все поля обязательны для заполнения";
            } else {
                if ($auth->login($username, $password)) {
                    header("Location: index.php");
                    exit;
                } else {
                    $errorsLogin[] = "Неверный логин или пароль";
                }
            }
        } elseif (isset($_POST['action']) && $_POST['action'] === 'register') {
            $username = trim($_POST['username'] ?? '');
            $password = $_POST['password'] ?? '';
            $passwordConfirm = $_POST['password_confirm'] ?? '';

            if (empty($username) || empty($password) || empty($passwordConfirm)) {
                $errorsRegister[] = "Все поля обязательны для заполнения";
            } elseif (strlen($username) < 4) {
                $errorsRegister[] = "Имя пользователя должно быть не короче 4 символов";
            } elseif ($password !== $passwordConfirm) {
                $errorsRegister[] = "Пароли не совпадают";
            } elseif (strlen($password) < 8) {
                $errorsRegister[] = "Пароль должен содержать минимум 8 символов";
            } else {
                if ($auth->register($username, $password)) {
                    header("Location: index.php");
                    exit;
                } else {
                    $errorsRegister[] = "Имя пользователя уже занято";
                }
            }
            $activeTab = 'register';
        }
    }
}

$csrfToken = $auth->generateCsrfToken();
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
    <style>
        :root {
            --primary: #6366f1;
            --primary-hover: #4f46e5;
            --secondary: #ec4899;
            --secondary-hover: #db2777;
        }

        .form-container {
            position: relative;
            min-height: 400px;
            transition: all 0.3s ease;
        }

        .auth-form {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            opacity: 0;
            transform: translateX(20px);
            transition: all 0.4s cubic-bezier(0.68, -0.55, 0.27, 1.55);
            pointer-events: none;
            visibility: hidden;
        }

        .auth-form.active {
            opacity: 1;
            transform: translateX(0);
            pointer-events: auto;
            visibility: visible;
            position: relative;
        }

        .tab-btn {
            position: relative;
            transition: all 0.3s ease;
            color: #6b7280;
            font-weight: 500;
        }

        .tab-btn::after {
            content: '';
            position: absolute;
            bottom: -4px;
            left: 0;
            width: 0;
            height: 3px;
            background-color: var(--primary);
            transition: width 0.3s ease;
            border-radius: 3px;
        }

        .tab-btn.active {
            color: var(--primary);
            font-weight: 600;
        }

        .tab-btn.active::after {
            width: 100%;
        }

        .input-field {
            transition: all 0.3s ease;
            border: 1px solid #e5e7eb;
        }

        .input-field:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.2);
        }

        .password-strength {
            height: 4px;
            background: #e5e7eb;
            border-radius: 2px;
            margin-top: 8px;
            overflow: hidden;
        }

        .password-strength-bar {
            height: 100%;
            width: 0;
            transition: all 0.3s ease;
        }

        .password-hint {
            font-size: 0.75rem;
            color: #6b7280;
            margin-top: 4px;
        }

        .password-requirement {
            display: flex;
            align-items: center;
            font-size: 0.75rem;
            color: #6b7280;
            margin-top: 2px;
        }

        .password-requirement i {
            margin-right: 4px;
            font-size: 0.6rem;
        }

        .requirement-met {
            color: #10b981;
        }

        .requirement-not-met {
            color: #6b7280;
        }

        .btn-primary {
            background: var(--primary);
            transition: all 0.3s ease;
        }

        .btn-primary:hover {
            background: var(--primary-hover);
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(99, 102, 241, 0.2);
        }

        .btn-secondary {
            background: var(--secondary);
            transition: all 0.3s ease;
        }

        .btn-secondary:hover {
            background: var(--secondary-hover);
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(236, 72, 153, 0.2);
        }

        .error-message {
            animation: fadeIn 0.3s ease;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(-5px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @media (max-width: 768px) {
            .auth-layout {
                flex-direction: column;
            }

            .auth-form-section,
            .auth-hero {
                width: 100% !important;
            }

            .auth-hero {
                padding: 2rem;
                text-align: center;
            }
        }
    </style>
</head>

<body class="h-full flex auth-layout">
    <div
        class="w-full md:w-2/5 bg-white flex flex-col justify-center px-8 md:px-12 py-12 md:py-16 shadow-lg relative overflow-hidden min-h-screen auth-form-section">
        <a href="index.html" class="absolute top-5 left-5 text-gray-600 hover:text-gray-900 transition"
            aria-label="Назад">
            <i class="fa-solid fa-arrow-left text-2xl"></i>
        </a>

        <div class="mb-8 text-center">
            <h1 class="text-3xl md:text-4xl font-extrabold text-gray-800 mb-2">Добро пожаловать в Enigma</h1>
            <p class="text-gray-500">Вход или регистрация для продолжения</p>
        </div>

        <div class="flex justify-center mb-8 md:mb-10 space-x-4 md:space-x-6">
            <button id="tabLoginBtn"
                class="tab-btn px-4 py-2 text-base md:text-lg <?= $activeTab === 'login' ? 'active' : '' ?>"
                data-target="login">Вход</button>
            <button id="tabRegisterBtn"
                class="tab-btn px-4 py-2 text-base md:text-lg <?= $activeTab === 'register' ? 'active' : '' ?>"
                data-target="register">Регистрация</button>
        </div>

        <div class="form-container">
            <form id="loginForm" method="POST" class="auth-form space-y-6 <?= $activeTab === 'login' ? 'active' : '' ?>"
                novalidate>
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">
                <input type="hidden" name="action" value="login">

                <?php if ($errorsLogin): ?>
                    <div class="mb-4 bg-red-50 border-l-4 border-red-500 text-red-700 p-4 rounded-lg error-message">
                        <ul class="list-disc list-inside space-y-1">
                            <?php foreach ($errorsLogin as $err): ?>
                                <li><?= htmlspecialchars($err) ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>

                <div class="space-y-6">
                    <div>
                        <label for="loginUsername" class="block mb-2 font-medium text-gray-700">Имя пользователя</label>
                        <input id="loginUsername" name="username" type="text" required
                            class="w-full px-4 py-2.5 rounded-lg input-field focus:outline-none focus:ring-0"
                            placeholder="Введите ваш логин">
                    </div>

                    <div>
                        <label for="loginPassword" class="block mb-2 font-medium text-gray-700">Пароль</label>
                        <div class="relative">
                            <input id="loginPassword" name="password" type="password" required
                                class="w-full px-4 py-2.5 rounded-lg input-field focus:outline-none focus:ring-0 pr-10"
                                placeholder="Введите ваш пароль">
                            <button type="button"
                                class="togglePassword absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-indigo-600"
                                aria-label="Показать пароль">
                                <i class="fa-solid fa-eye"></i>
                            </button>
                        </div>
                    </div>

                    <button type="submit" class="w-full btn-primary text-white font-semibold py-3 rounded-lg">
                        Войти
                    </button>
                </div>
            </form>

            <form id="registerForm" method="POST"
                class="auth-form space-y-6 <?= $activeTab === 'register' ? 'active' : '' ?>" novalidate>
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">
                <input type="hidden" name="action" value="register">

                <?php if ($errorsRegister): ?>
                    <div class="mb-4 bg-red-50 border-l-4 border-red-500 text-red-700 p-4 rounded-lg error-message">
                        <ul class="list-disc list-inside space-y-1">
                            <?php foreach ($errorsRegister as $err): ?>
                                <li><?= htmlspecialchars($err) ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>

                <div class="space-y-6">
                    <div>
                        <label for="registerUsername" class="block mb-2 font-medium text-gray-700">Имя
                            пользователя</label>
                        <input id="registerUsername" name="username" type="text" required minlength="4" maxlength="30"
                            class="w-full px-4 py-2.5 rounded-lg input-field focus:outline-none focus:ring-0"
                            placeholder="Придумайте логин (от 4 символов)">
                    </div>

                    <div>
                        <label for="registerPassword" class="block mb-2 font-medium text-gray-700">Пароль</label>
                        <div class="relative">
                            <input id="registerPassword" name="password" type="password" required minlength="8"
                                class="w-full px-4 py-2.5 rounded-lg input-field focus:outline-none focus:ring-0 pr-10 password-input"
                                placeholder="Придумайте пароль (от 8 символов)">
                            <button type="button"
                                class="togglePassword absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-pink-600"
                                aria-label="Показать пароль">
                                <i class="fa-solid fa-eye"></i>
                            </button>
                        </div>
                        <div class="password-strength mt-2">
                            <div class="password-strength-bar"></div>
                        </div>
                        <div class="password-requirements mt-3 space-y-1">
                            <div class="password-requirement" data-requirement="length">
                                <i class="fas fa-circle"></i>
                                <span>Минимум 8 символов</span>
                            </div>
                            <div class="password-requirement" data-requirement="uppercase">
                                <i class="fas fa-circle"></i>
                                <span>Хотя бы одна заглавная буква</span>
                            </div>
                            <div class="password-requirement" data-requirement="number">
                                <i class="fas fa-circle"></i>
                                <span>Хотя бы одна цифра</span>
                            </div>
                            <div class="password-requirement" data-requirement="special">
                                <i class="fas fa-circle"></i>
                                <span>Хотя бы один спецсимвол</span>
                            </div>
                        </div>
                    </div>

                    <div>
                        <label for="registerPasswordConfirm" class="block mb-2 font-medium text-gray-700">Подтвердите
                            пароль</label>
                        <div class="relative">
                            <input id="registerPasswordConfirm" name="password_confirm" type="password" required
                                minlength="8"
                                class="w-full px-4 py-2.5 rounded-lg input-field focus:outline-none focus:ring-0 pr-10"
                                placeholder="Повторите пароль">
                            <button type="button"
                                class="togglePassword absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-pink-600"
                                aria-label="Показать пароль">
                                <i class="fa-solid fa-eye"></i>
                            </button>
                        </div>
                        <div class="password-match mt-2 text-sm hidden">
                            <i class="fas fa-check-circle text-green-500 mr-1"></i>
                            <span class="text-green-600">Пароли совпадают</span>
                        </div>
                    </div>

                    <button type="submit" id="registerSubmit" disabled
                        class="w-full btn-secondary text-white font-semibold py-3 rounded-lg opacity-70 cursor-not-allowed">
                        Зарегистрироваться
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div
        class="w-full md:w-3/5 bg-gradient-to-tr from-indigo-700 via-purple-800 to-pink-700 text-white p-8 md:p-20 flex flex-col justify-center items-center min-h-screen auth-hero">
        <div class="w-full">
            <img src="./assets/img/other/auth_image.avif" alt="Enigma illustration"
                class="rounded-lg shadow-xl mb-8 w-full h-auto object-cover max-h-[600px] md:max-h-[600px]">
            <h2 class="text-3xl md:text-4xl font-extrabold mb-4 text-center">Добро пожаловать в Enigma</h2>
            <p class="text-base md:text-lg text-center text-indigo-100">
                Удобный и безопасный конвертер изображений с поддержкой разных форматов и качеств.
                Присоединяйтесь к нам и управляйте своими изображениями быстро и легко!
            </p>
        </div>
    </div>

    <script src="./assets/js/auth.js"></script>
</body>
</html>