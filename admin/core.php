<?php
session_start();

$pdo = new PDO('mysql:host=localhost;dbname=convertor;charset=utf8mb4', 'root', '', [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
]);
function csrf_token() {
    if (!isset($_SESSION['csrf'])) {
        $_SESSION['csrf'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf'];
}
function csrf_check($token) {
    return isset($_SESSION['csrf']) && hash_equals($_SESSION['csrf'], $token);
}
function is_admin() {
    return isset($_SESSION['admin_id']);
}
function require_admin() {
    if (!is_admin()) {
        header("Location: login.php");
        exit;
    }
}
