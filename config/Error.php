<?php
require_once './config/ErrorHandler.php';

$errorCode = isset($_GET['code']) ? (int)$_GET['code'] : 404;
$errorMessage = $_GET['message'] ?? '';

$allowedCodes = [400, 403, 404, 500];

if (!in_array($errorCode, $allowedCodes)) {
    $errorCode = 404;
}

showErrorPage($errorCode, $errorMessage);