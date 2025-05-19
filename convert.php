<?php
header('Content-Type: application/json');
error_reporting(E_ALL);
ini_set('display_errors', 1);
require_once("logs/logs.php");

function getClientIP() {
    if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
        return $_SERVER['HTTP_CLIENT_IP'];
    } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        return $_SERVER['HTTP_X_FORWARDED_FOR'];
    } else {
        return $_SERVER['REMOTE_ADDR'];
    }
}

$clientIP = getClientIP();
logMessage('Запуск обработки изображения', 'INFO', ['request' => $_REQUEST, 'ip' => $clientIP]);

if (!isset($_FILES['image'])) {
    logMessage('Отсутствует файл изображения', 'ERROR', ['files' => $_FILES, 'ip' => $clientIP]);
    header('HTTP/1.1 400 Bad Request');
    die(json_encode(['error' => 'Файл не загружен']));
}

$file = $_FILES['image'];
$format = strtolower($_POST['format'] ?? 'webp');
$quality = intval($_POST['quality'] ?? 80);

logMessage('Параметры конвертации', 'INFO', [
    'filename' => $file['name'],
    'size' => $file['size'],
    'format' => $format,
    'quality' => $quality,
    'ip' => $clientIP
]);

if ($file['error'] !== UPLOAD_ERR_OK) {
    logMessage('Ошибка загрузки файла', 'ERROR', [
        'error_code' => $file['error'],
        'error_text' => getUploadErrorText($file['error']),
        'ip' => $clientIP
    ]);
    header('HTTP/1.1 400 Bad Request');
    die(json_encode(['error' => 'Ошибка загрузки файла']));
}

$allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
if (!in_array($file['type'], $allowed_types)) {
    logMessage('Неподдерживаемый тип файла', 'ERROR', [
        'current_type' => $file['type'],
        'allowed_types' => $allowed_types,
        'ip' => $clientIP
    ]);
    header('HTTP/1.1 400 Bad Request');
    die(json_encode(['error' => 'Неподдерживаемый формат файла']));
}

if ($format === 'webp' && !function_exists('imagewebp')) {
    logMessage('WebP не поддерживается сервером', 'ERROR', ['ip' => $clientIP]);
    header('HTTP/1.1 400 Bad Request');
    die(json_encode(['error' => 'WebP не поддерживается на этом сервере']));
}

if ($format === 'avif' && !function_exists('imageavif')) {
    logMessage('AVIF не поддерживается сервером', 'ERROR', ['ip' => $clientIP]);
    header('HTTP/1.1 400 Bad Request');
    die(json_encode(['error' => 'AVIF не поддерживается на этом сервере']));
}

$upload_dir = 'converted/';
if (!file_exists($upload_dir)) {
    if (!mkdir($upload_dir, 0777, true)) {
        logMessage('Не удалось создать директорию', 'ERROR', [
            'directory' => $upload_dir,
            'ip' => $clientIP
        ]);
        header('HTTP/1.1 500 Internal Server Error');
        die(json_encode(['error' => 'Ошибка сервера']));
    }
    logMessage('Директория создана', 'INFO', [
        'path' => realpath($upload_dir),
        'ip' => $clientIP
    ]);
}

$source_image = null;
try {
    switch ($file['type']) {
        case 'image/jpeg':
            $source_image = imagecreatefromjpeg($file['tmp_name']);
            logMessage('JPEG изображение загружено', 'INFO', ['ip' => $clientIP]);
            break;
        case 'image/png':
            $source_image = imagecreatefrompng($file['tmp_name']);
            if ($source_image) {
                imagealphablending($source_image, true);
                imagesavealpha($source_image, true);
                logMessage('PNG изображение загружено с сохранением прозрачности', 'INFO', ['ip' => $clientIP]);
            }
            break;
        case 'image/gif':
            $source_image = imagecreatefromgif($file['tmp_name']);
            if (!$source_image) {
                throw new Exception('Не удалось загрузить GIF изображение');
            }
            logMessage('GIF изображение загружено', 'INFO', ['ip' => $clientIP]);
            break;
    }

    if (!$source_image) {
        throw new Exception('Не удалось загрузить изображение в GD');
    }

    $filename = uniqid() . '.' . $format;
    $output_path = $upload_dir . $filename;

    logMessage('Начало конвертации', 'INFO', [
        'target_format' => $format,
        'output_path' => $output_path,
        'ip' => $clientIP
    ]);

    $success = false;
    switch ($format) {
        case 'webp':
            $success = imagewebp($source_image, $output_path, $quality);
            break;
        case 'avif':
            $success = imageavif($source_image, $output_path, $quality);
            break;
        case 'jpeg':
            $success = imagejpeg($source_image, $output_path, $quality);
            break;
        case 'png':
            $png_quality = round(9 - ($quality / 100 * 9));
            $success = imagepng($source_image, $output_path, $png_quality);
            break;
        default:
            throw new Exception('Неподдерживаемый формат конвертации');
    }

    if (!$success) {
        throw new Exception('Ошибка при конвертации изображения');
    }

    logMessage('Изображение успешно сконвертировано', 'SUCCESS', [
        'original' => $file['name'],
        'result' => $filename,
        'format' => $format,
        'size' => filesize($output_path),
        'ip' => $clientIP
    ]);

    echo json_encode([
        'success' => true,
        'filename' => $filename,
        'path' => $output_path,
        'originalName' => $file['name'],
        'format' => $format,
        'quality' => $quality,
        'timestamp' => time()
    ]);

} catch (Exception $e) {
    logMessage('Ошибка обработки изображения: ' . $e->getMessage(), 'ERROR', [
        'format' => $format,
        'quality' => $quality,
        'ip' => $clientIP
    ]);
    header('HTTP/1.1 500 Internal Server Error');
    echo json_encode(['error' => $e->getMessage()]);
} finally {
    if ($source_image) {
        imagedestroy($source_image);
    }
}

function getUploadErrorText($error_code) {
    $errors = [
        UPLOAD_ERR_INI_SIZE => 'Превышен максимальный размер файла',
        UPLOAD_ERR_FORM_SIZE => 'Превышен MAX_FILE_SIZE в форме',
        UPLOAD_ERR_PARTIAL => 'Файл загружен частично',
        UPLOAD_ERR_NO_FILE => 'Файл не был загружен',
        UPLOAD_ERR_NO_TMP_DIR => 'Отсутствует временная папка',
        UPLOAD_ERR_CANT_WRITE => 'Не удалось записать файл на диск',
        UPLOAD_ERR_EXTENSION => 'Загрузка остановлена расширением'
    ];
    return $errors[$error_code] ?? 'Неизвестная ошибка';
}
?>