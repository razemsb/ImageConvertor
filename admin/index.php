<?php
require 'core.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_check($_POST['csrf'] ?? '')) {
        exit('CSRF FAIL');
    }

    $login = $_POST['login'] ?? '';
    $password = $_POST['password'] ?? '';

    $stmt = $pdo->prepare("SELECT * FROM admins WHERE login = ?");
    $stmt->execute([$login]);
    $admin = $stmt->fetch();

    if ($admin && password_verify($password, $admin['password_hash'])) {
        $_SESSION['admin_id'] = $admin['id'];
        $_SESSION['admin_loggin'] = true;
        header("Location: dashboard.php");
        exit;
    } else {
        $error = "Неверные данные";
    }
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Enigma | Вход</title>
  <link rel="icon" type="image/png" href=../assets/img/favicon/favicon-96x96.png" sizes="96x96" />
    <link rel="icon" type="image/svg+xml" href="../assets/img/favicon/favicon.svg" />
    <link rel="shortcut icon" href="../assets/img/favicon/favicon.ico" />
    <link rel="apple-touch-icon" sizes="180x180" href="../assets/img/favicon/apple-touch-icon.png" />
    <meta name="apple-mobile-web-app-title" content="MyWebSite" />
    <link rel="manifest" href="../assets/img/favicon/site.webmanifest" />
    <script src="../assets/vendors/tailwindcss/script.js"></script>
    <link rel="stylesheet" href="../assets/vendors/font-awesome/css/all.min.css">
</head>
<body class="min-h-screen flex items-center justify-center bg-gray-100">
  <form method="POST" class="bg-white p-6 rounded-xl shadow-md w-full max-w-sm">
    <h1 class="text-xl font-bold mb-4">Вход в админку</h1>
    <?php if (!empty($error)): ?>
      <div class="text-red-500 mb-2"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>
    <input name="login" placeholder="Логин" class="w-full mb-3 p-2 border rounded" required>
    <input type="password" name="password" placeholder="Пароль" class="w-full mb-3 p-2 border rounded" required>
    <input type="hidden" name="csrf" value="<?= csrf_token() ?>">
    <button class="w-full bg-blue-600 text-white py-2 rounded hover:bg-blue-700">Войти</button>
  </form>
</body>
</html>
