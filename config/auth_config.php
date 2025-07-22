<?php
require_once("db.php");
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function loginUser($pdo, $username, $password) {
    $stmt = $pdo->prepare("SELECT id, username, password, avatar, is_admin, created_at, updated_at FROM users WHERE username = :username");
    $stmt->execute(['username' => $username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($user && password_verify($password, $user['password'])) {
        unset($user['password']);
        $_SESSION['user_auth'] = true;
        $_SESSION['user'] = $user;
        return true;
    }
    return "Неверный логин или пароль";
}

function registerUser($pdo, $username, $password) {
    $stmt = $pdo->prepare("SELECT id FROM users WHERE username = :username");
    $stmt->execute(['username' => $username]);
    if ($stmt->fetch()) {
        return "Имя пользователя занято";
    }

    $defaultAvatars = [
        'default-avatar-1.png',
        'default-avatar-2.png',
        'default-avatar-3.png',
        'default-avatar-4.png',
        'default-avatar-5.png',
        'default-avatar-6.png'
    ];
    
    $randomAvatar = $defaultAvatars[array_rand($defaultAvatars)];
    $passwordHash = password_hash($password, PASSWORD_DEFAULT);
    $stmt = $pdo->prepare("INSERT INTO users (username, password, avatar) VALUES (:username, :password_hash, :avatar)");
    $result = $stmt->execute([
        'username' => $username,
        'password_hash' => $passwordHash,
        'avatar' => $randomAvatar
    ]);
    
    return $result ? true : "Ошибка при регистрации";
}
function logoutUser() {
    $_SESSION = [];
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params["path"], $params["domain"],
            $params["secure"], $params["httponly"]
        );
    }
    session_destroy();
}
