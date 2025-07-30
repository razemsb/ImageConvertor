<?php
function showErrorPage(int $errorCode, string $errorMessage = '', string $customDescription = '') {
    http_response_code($errorCode);
    
    $errorTitles = [
        400 => 'Неверный запрос',
        403 => 'Доступ запрещён',
        404 => 'Страница не найдена',
        500 => 'Ошибка сервера'
    ];
    
    $defaultDescriptions = [
        400 => 'Сервер не может обработать ваш запрос',
        403 => 'У вас нет прав для доступа к этой странице',
        404 => 'Запрошенная страница не существует или была перемещена',
        500 => 'На сервере произошла внутренняя ошибка'
    ];
    
    $title = $errorTitles[$errorCode] ?? 'Ошибка';
    $description = $customDescription ?: ($defaultDescriptions[$errorCode] ?? 'Произошла ошибка');
    
    include './../templates/error_page.php';
    exit;
}

function redirectToErrorPage(int $errorCode, string $message = '') {
    header("Location: /error.php?code=$errorCode&message=".urlencode($message));
    exit;
}