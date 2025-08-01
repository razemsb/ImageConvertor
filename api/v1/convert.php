<?php
if (ob_get_length()) ob_clean();
header('Content-Type: application/json');
session_start();
require_once("../../logs/ConversionLogger.php");
require_once("../../config/DatabaseConnect.php");

ConversionLogger::logMessage('Запуск скрипта, проверка компонентов...', 'START', ['GD' => 'Проверка...']);

$clientIP = ConversionLogger::getClientIP();

$file = $_FILES['image'];

$user_id = null;

$user_agent = $_SERVER['HTTP_USER_AGENT'] ?? 'unknown';

$format = strtolower($_POST['format'] ?? 'webp');

$quality = intval($_POST['quality'] ?? 80);

ConversionLogger::logMessage('Получение параметров', 'INFO', [
    'original_filename' => $file['name'] ?? 'undefined',
    'tmp_name' => $file['tmp_name'] ?? 'undefined',
    'mime_type' => $file['type'] ?? 'undefined',
    'size' => $file['size'] ?? 0,
    'format' => $format,
    'quality' => $quality,
    'ip' => $clientIP
]);

if (isset($_SESSION['auth_user'])) {
    $user_id = $_SESSION['auth_user']['id'];
    ConversionLogger::logMessage('ID пользователя получен', 'INFO', ['ID' => $user_id]);
} else {
    ConversionLogger::logMessage('Пользователь не авторизован', 'INFO');
}


$maxSize = 5 * 1024 * 1024;

if ($file['size'] > $maxSize) {
    ConversionLogger::logMessage('Файл превышает максимальный размер', 'ERROR', [
        'size' => $file['size'],
        'limit' => $maxSize,
        'ip' => $clientIP
    ]);
    ConversionLogger::logError(
        $clientIP ?? $_SERVER['REMOTE_ADDR'],
        $user_id,
        $user_agent,
        $file['name'] ?? '',
        pathinfo($file['name'] ?? '', PATHINFO_EXTENSION) ?? '',
        $format,
        $quality,
        'MAX_FILE_SIZE_ERROR'
    ); 
    header('HTTP/1.1 400 Bad Request');
    die(json_encode(['error' => 'Файл слишком большой. Максимум 5 МБ']));
}

if (!extension_loaded('gd') || !function_exists('gd_info')) {
    ConversionLogger::logMessage('Ошибка: модуль GD неактивен', 'ERROR', ['GD Module ' => 'Недоступен', 'Включите его в php.ini и перезагрузите Apache сервер.']);
    ConversionLogger::logError(
        $clientIP,
        $user_id,
        $user_agent,
        $file['name'] ?? '',
        pathinfo($file['name'] ?? '', PATHINFO_EXTENSION) ?? '',
        $format ?? '',
        $quality ?? '',
          'GD Module Error!'
    ); 
    die(json_encode(['error' => 'GD не поддерживается на этом сервере']));
}

$upload_dir = "../../converted/";

if (!file_exists($upload_dir)) {
    if (!mkdir($upload_dir, 0777, true)) {
        ConversionLogger::logMessage('Не удалось создать директорию для хранения изображений', 'ERROR', [
            'directory' => $upload_dir,
            'ip' => $clientIP
        ]);
        header('HTTP/1.1 500 Internal Server Error');
        die(json_encode(['error' => 'Ошибка сервера при создании директории']));
    }
    ConversionLogger::logMessage('Создана новая директория для сохранения изображений', 'INFO', [
        'directory' => $upload_dir,
        'ip' => $clientIP
    ]);
}

ConversionLogger::logMessage('Запуск запроса на обработку изображения', 'INFO', ['request' => $_REQUEST, 'ip' => $clientIP]);

if (!isset($_FILES['image'])) {
    ConversionLogger::logMessage('Файл изображения не найден в запросе', 'ERROR', ['FILES' => $_FILES, 'ip' => $clientIP]);
    ConversionLogger::logError(
        $clientIP,
        $user_id,
        $user_agent,
        $file['name'] ?? '',
        pathinfo($file['name'] ?? '', PATHINFO_EXTENSION) ?? '',
        $format ?? '',
        $quality ?? '',
          'FILE ERROR EXSIST!'
    ); 
    header('HTTP/1.1 400 Bad Request');
    die(json_encode(['error' => 'Файл не загружен']));
}

if ($file['error'] !== UPLOAD_ERR_OK) {
    $error_text = ConversionLogger::getUploadErrorText($file['error']);
    ConversionLogger::logMessage('Ошибка при загрузке файла', 'ERROR', [
        'error_code' => $file['error'],
        'error_text' => $error_text,
        'ip' => $clientIP
    ]);
    ConversionLogger::logError(
        $clientIP,
        $user_id,
        $user_agent,
        $file['name'] ?? '',
        pathinfo($file['name'] ?? '', PATHINFO_EXTENSION) ?? '',
        $format,
        $quality,
        'FILE ERROR UPLOAD!'
    ); 
    header('HTTP/1.1 400 Bad Request');
    die(json_encode(['error' => $error_text]));
}

$allowed_types = ['image/jpeg', 'image/png', 'image/gif'];

$finfo = new finfo(FILEINFO_MIME_TYPE);

$realMime = $finfo->file($file['tmp_name']);

if (!in_array($realMime, $allowed_types)) {
    ConversionLogger::logError(
        $clientIP,
        $user_id,
        $user_agent,
        $file['name'] ?? '',
        pathinfo($file['name'] ?? '', PATHINFO_EXTENSION) ?? '',
        $format,
        $quality,
        'MIME NOT SUPPORTED: ' . $realMime
    );
    ConversionLogger::logMessage('Неверный MIME-тип файла', 'ERROR', [
        'real_mime' => $realMime,
        'ip' => $clientIP
    ]);
    ConversionLogger::logError(
        $clientIP,
        $user_id,
        $user_agent,
        $file['name'] ?? '',
        pathinfo($file['name'] ?? '', PATHINFO_EXTENSION) ?? '',
        $format ?? '',
        $quality ?? '',
          'MIME TYPE ERROR'
    ); 
    header('HTTP/1.1 400 Bad Request');
    die(json_encode(['error' => 'Неподдерживаемый формат файла']));
}


if ($format === 'webp' && !function_exists('imagewebp')) {
    ConversionLogger::logMessage('Функция imagewebp отсутствует (WebP не поддерживается)', 'ERROR', ['ip' => $clientIP]);
    header('HTTP/1.1 400 Bad Request');
    die(json_encode(['error' => 'WebP не поддерживается на этом сервере']));
}

if ($format === 'avif' && !function_exists('imageavif')) {
    ConversionLogger::logMessage('Функция imageavif отсутствует (AVIF не поддерживается)', 'ERROR', ['ip' => $clientIP]);
    ConversionLogger::logError(
        $clientIP,
        $user_id,
        $user_agent,
        $file['name'] ?? '',
        pathinfo($file['name'] ?? '', PATHINFO_EXTENSION) ?? '',
        $format,
        $quality,
        $e->getMessage()
    ); 
    header('HTTP/1.1 400 Bad Request');
    die(json_encode(['error' => 'AVIF не поддерживается на этом сервере']));
}

$source_image = null;

try {
    switch ($file['type']) {
        case 'image/jpeg':
            $source_image = imagecreatefromjpeg($file['tmp_name']);
            ConversionLogger::logMessage('Изображение JPEG успешно загружено', 'INFO', ['uploaded_image' =>  $file['name'],'ip' => $clientIP]);
            break;
        case 'image/png':
            $source_image = imagecreatefrompng($file['tmp_name']);
            if ($source_image) {
                imagealphablending($source_image, true);
                imagesavealpha($source_image, true);
                ConversionLogger::logMessage('PNG загружен с поддержкой прозрачности', 'INFO', ['ip' => $clientIP]);
            }
            break;
        case 'image/gif':
            $source_image = imagecreatefromgif($file['tmp_name']);
            if (!$source_image) {
                throw new Exception('Ошибка загрузки GIF изображения');
            }
            ConversionLogger::logMessage('GIF успешно загружен', 'INFO', ['ip' => $clientIP]);
            break;
    }

    if (!$source_image) {
        ConversionLogger::logError(
            $clientIP,
            $user_id,
            $user_agent,
            $file['name'] ?? '',
            pathinfo($file['name'] ?? '', PATHINFO_EXTENSION) ?? '',
            $format ?? '',
            $quality ?? '',
              'GD Module return Error!'
        ); 
        throw new Exception('Не удалось создать ресурс изображения (GD вернул null)');
    }

    $unique_name = ConversionLogger::generateUuidV4() . '-' . 'enigma-converted';

    $filename = $unique_name . '.' . $format;
    $output_path = $upload_dir . $filename;

    ConversionLogger::logMessage('Начало конвертации изображения', 'INFO', [
        'target_format' => $format,
        'output_file' => $filename,
        'full_path' => $output_path,
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
        case 'gif':
            if (!function_exists('imagegif')) {
                throw new Exception('Функция imagegif отсутствует');
            }

            if (!is_writable(dirname($output_path))) {
                throw new Exception("Папка не доступна для записи: " . dirname($output_path));
            }

            $fp = fopen($output_path, 'wb');
            if (!$fp) {
                throw new Exception("Не удалось открыть файл $output_path для записи GIF");
            }

            ob_start();
            $success = imagegif($source_image, $fp);
            fclose($fp);
            ob_end_clean();

            ConversionLogger::logMessage('Сохранение GIF', 'DEBUG', [
                'output_path' => $output_path,
                'success' => $success,
                'ip' => $clientIP
            ]);
            break;
        default:
            throw new Exception("Неподдерживаемый формат конвертации: $format");
    }

    if (!$success) {
        if (file_exists($output_path)) {
            unlink($output_path);
            ConversionLogger::logMessage('Удален битый или пустой файл после неудачной конвертации', 'WARNING', [
                'format' => $format,
                'path' => $output_path,
                'ip' => $clientIP
            ]);
        }

        ConversionLogger::logMessage('Ошибка при сохранении изображения', 'ERROR', [
            'format' => $format,
            'output_path' => $output_path,
            'ip' => $clientIP
        ]);
        throw new Exception("Ошибка при записи изображения в формате $format");
    }

    ConversionLogger::logMessage('Успешная конвертация изображения', 'SUCCESS', [
        'original_filename' => $file['name'],
        'converted_filename' => $filename,
        'converted_size' => filesize($output_path),
        'output_path' => realpath($output_path),
        'ip' => $clientIP
    ]);

    ConversionLogger::logSuccess(
        $clientIP,
        $user_id,
        $user_agent,
        $file['name'],
        $filename,
        pathinfo($file['name'], PATHINFO_EXTENSION),
        $format,
        $file['size'],
        filesize($output_path),
        $quality
    );

    echo json_encode([
        'success' => true,
        'filename' => $filename,
        'path' => $output_path,
        'url' => $output_path,
        'originalName' => $file['name'],
        'format' => $format,
        'quality' => $quality,
        'timestamp' => time()
    ]);

} catch (Exception $e) {
    if (ob_get_length()) {
        ob_clean();
    }
    ConversionLogger::logMessage('Фатальная ошибка при обработке изображения', 'ERROR', [
        'message' => $e->getMessage(),
        'trace' => $e->getTraceAsString(),
        'file' => $e->getFile(),
        'line' => $e->getLine(),
        'format' => $format,
        'quality' => $quality,
        'ip' => $clientIP
    ]);
    ConversionLogger::logError(
        $clientIP,
        $user_id,
        $user_agent,
        $file['name'] ?? '',
        pathinfo($file['name'] ?? '', PATHINFO_EXTENSION) ?? '',
        $format,
        $quality,
        $e->getMessage()
    ); 
    header('Content-Type: application/json');
    header('HTTP/1.1 500 Internal Server Error');
    echo json_encode(['error' => $e->getMessage()]);
    exit;
}
?>