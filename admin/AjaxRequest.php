<?php
require_once 'AdminFileManager.php';

header('Content-Type: application/json');
$fileManager = new AdminFileManager();
$response = [
    'status' => 'error',
    'message' => 'Неизвестная ошибка'
];

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Метод запроса должен быть POST');
    }

    $data = json_decode(file_get_contents('php://input'), true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        throw new Exception('Неверный JSON формат данных');
    }

    if (!isset($data['action'], $data['id'])) {
        throw new Exception('Отсутствуют обязательные поля action или id');
    }

    $id = (int) $data['id'];
    if ($id <= 0) {
        throw new Exception('Некорректный ID файла');
    }

    if (!isset($fileManager) || !is_object($fileManager)) {
        throw new Exception('Файловый менеджер не инициализирован');
    }

    if ($data['action'] === 'delete_file') {
        if ($fileManager->deleteFileById($id)) {
            $response = [
                'status' => 'success',
                'newAction' => 'restore_file',
                'newLabel' => 'Восстановить',
                'newClass' => 'bg-green-500',
                'message' => ''
            ];
        } else {
            throw new Exception('Не удалось удалить файл. Возможно, файл не существует или нет прав доступа');
        }
    } elseif ($data['action'] === 'restore_file') {
        if ($fileManager->restoreFileById($id)) {
            $response = [
                'status' => 'success',
                'newAction' => 'delete_file',
                'newLabel' => 'Удалить',
                'newClass' => 'bg-red-500',
                'message' => ''
            ];
        } else {
            throw new Exception('Не удалось восстановить файл. Возможно, файл уже восстановлен или нет прав доступа');
        }
    } else {
        throw new Exception('Неизвестное действие: ' . $data['action']);
    }

} catch (Exception $e) {
    $response['message'] = $e->getMessage();
}

echo json_encode($response);
exit;