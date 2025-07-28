<?php
require_once '../config/DatabaseConnect.php';
require_once 'AdminCore.php';


if (getenv('ENVIRONMENT') === 'development') {
  ini_set('display_errors', 1);
  error_reporting(E_ALL);
}


if (session_status() === PHP_SESSION_NONE) {
  session_start([
    'cookie_lifetime' => 86400,
    'cookie_secure' => isset($_SERVER['HTTPS']),
    'cookie_httponly' => true,
    'cookie_samesite' => 'Strict'
  ]);
}

try {
  $pdo = DB::connect();
  $adminCore = AdminCore::init($pdo);


  if ($adminCore->isAdmin()) {
    header("Location: dashboard.php");
    exit;
  }
} catch (PDOException $e) {
  error_log("Database connection error: " . $e->getMessage());
  die("Системная ошибка. Пожалуйста, попробуйте позже или обратитесь к администратору.");
}

$error = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

  if (!$adminCore->validateCsrfToken($_POST['csrf_token'] ?? '')) {
    $error = "Ошибка безопасности сессии. Пожалуйста, обновите страницу.";
  } else {
    $login = trim($_POST['login'] ?? '');
    $password = $_POST['password'] ?? '';


    if (empty($login) || empty($password)) {
      $error = "Все поля обязательны для заполнения";
    } else {
      try {

        $stmt = $pdo->prepare("
                    SELECT id, login, password_hash 
                    FROM admins 
                    WHERE login = :login 
                    LIMIT 1
                ");
        $stmt->bindParam(':login', $login, PDO::PARAM_STR);
        $stmt->execute();

        $admin = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($admin) {

          if (password_verify($password, $admin['password_hash'])) {

            $_SESSION['admin_id'] = $admin['id'];
            $_SESSION['admin_login'] = $admin['login'];


            session_regenerate_id(true);

            header("Location: dashboard.php");
            exit;
          } else {

            $error = "Неверные учетные данные";
            error_log("Failed login attempt for admin: " . $login);
          }
        } else {

          $error = "Неверные учетные данные";
        }
      } catch (PDOException $e) {
        error_log("Database error during login: " . $e->getMessage());
        $error = "Временная системная ошибка. Пожалуйста, попробуйте позже.";
      }
    }
  }
}


$csrfToken = $adminCore->generateCsrfToken();
?>
<!DOCTYPE html>
<html lang="ru" class="h-full bg-gray-900">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Вход в админ-панель | Конвертер</title>
  <link rel="icon" type="image/png" href="../assets/img/favicon/favicon-96x96.png" sizes="96x96" />
  <link rel="icon" type="image/svg+xml" href="../assets/img/favicon/favicon.svg" />
  <link rel="shortcut icon" href="../assets/img/favicon/favicon.ico" />
  <link rel="apple-touch-icon" sizes="180x180" href="../assets/img/favicon/apple-touch-icon.png" />
  <meta name="apple-mobile-web-app-title" content="MyWebSite" />
  <link rel="manifest" href="../assets/img/favicon/site.webmanifest" />
  <script src="../assets/vendors/tailwindcss/script.js"></script>
  <link rel="stylesheet" href="../assets/vendors/font-awesome/css/all.min.css">
  <link rel="stylesheet" href="../assets/css/AuthStyles.css">
</head>

<body class="h-full flex items-center justify-center bg-gradient-to-br from-gray-800 to-gray-900 p-4">
  <div class="login-box rounded-xl overflow-hidden w-full max-w-md">
    <a id="historyBack" class="back-btn" title="Назад">
      <i class="fas fa-arrow-left"></i>
    </a>
    <div class="p-8">
      <div class="text-center mb-8">
        <img src="../assets/img/favicon/favicon-96x96.png" alt="Logo" class="w-16 h-16 mx-auto mb-4 rounded-lg">
        <h1 class="text-2xl font-bold text-blue-400">Административная панель</h1>
        <p class="text-gray-400 mt-2">Введите данные для входа</p>
      </div>

      <?php if ($error): ?>
        <div class="mb-4 p-3 bg-red-500/20 text-red-400 rounded-lg flex items-center animate-fade-in">
          <i class="fas fa-exclamation-circle mr-2"></i>
          <span><?= htmlspecialchars($error) ?></span>
        </div>
      <?php endif; ?>

      <form method="POST" class="space-y-4">
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">

        <div>
          <label for="login" class="block text-sm font-medium text-gray-300 mb-2">Логин</label>
          <div class="relative">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
              <i class="fas fa-user text-gray-500"></i>
            </div>
            <input id="login" name="login" type="text" required
              class="input-field pl-10 w-full border border-gray-600 rounded-lg py-3 px-4 text-white placeholder-gray-400 focus:outline-none"
              placeholder="Введите логин" autocomplete="username"
              value="<?= isset($_POST['login']) ? htmlspecialchars($_POST['login']) : '' ?>">
          </div>
        </div>

        <div>
          <label for="password" class="block text-sm font-medium text-gray-300 mb-2">Пароль</label>
          <div class="relative">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
              <i class="fas fa-lock text-gray-500"></i>
            </div>
            <input id="password" name="password" type="password" required
              class="input-field pl-10 w-full border border-gray-600 rounded-lg py-3 px-4 text-white placeholder-gray-400 focus:outline-none"
              placeholder="Введите пароль" autocomplete="current-password">
          </div>
        </div>

        <div class="pt-4">
          <button type="submit"
            class="btn-primary w-full text-white font-medium py-3 px-4 rounded-lg flex items-center justify-center">
            <i class="fas fa-sign-in-alt mr-2"></i>
            Войти
          </button>
        </div>
      </form>

      <div class="mt-8 text-center text-xs text-gray-500">
        <p>&copy; <?= date('Y') ?> Конвертер. Все права защищены.</p>
      </div>
    </div>
  </div>

  <script>

    document.addEventListener('DOMContentLoaded', () => {
      const BackBtn = document.getElementById('historyBack');
      const form = document.querySelector('.login-box');
      form.style.opacity = '0';
      form.style.transform = 'translateY(20px)';
      form.style.transition = 'opacity 0.6s ease, transform 0.6s ease';

      BackBtn.addEventListener('click', function() {
        window.history.back();
      });

      setTimeout(() => {
        form.style.opacity = '1';
        form.style.transform = 'translateY(0)';
      }, 100);


      document.getElementById('login')?.focus();
    });
  </script>
</body>

</html>