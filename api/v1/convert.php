<?php
header('Content-Type: application/json');
require_once("../../logs/logs.php");
require_once("../../logs/db_logger.php");
require_once("../../config/db.php");

logMessage('Запуск скрипта, проверка компонентов...', 'START', ['GD' => 'Проверка...']);

if (!extension_loaded('gd') || !function_exists('gd_info')) {
    logMessage('Ошибка: модуль GD неактивен', 'ERROR', ['GD Module ' => 'Недоступен', 'Включите его в php.ini и перезагрузите Apache сервер.']);
    die(json_encode(['error' => 'GD не поддерживается на этом сервере']));
}

$upload_dir = "../../converted/";
if (!file_exists($upload_dir)) {
    if (!mkdir($upload_dir, 0777, true)) {
        logMessage('Не удалось создать директорию для хранения изображений', 'ERROR', [
            'directory' => $upload_dir,
            'ip' => $clientIP
        ]);
        header('HTTP/1.1 500 Internal Server Error');
        die(json_encode(['error' => 'Ошибка сервера при создании директории']));
    }
    logMessage('Создана новая директория для сохранения изображений', 'INFO', [
        'directory' => $upload_dir,
        'ip' => $clientIP
    ]);
}

function getClientIP()
{
    if (php_sapi_name() === 'cli') {
        return 'CLI';
    }

    if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
        return $_SERVER['HTTP_CLIENT_IP'];
    } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        return $_SERVER['HTTP_X_FORWARDED_FOR'];
    } elseif (!empty($_SERVER['REMOTE_ADDR'])) {
        logMessage('IP не определен через прокси, взят REMOTE_ADDR', 'WARNING', ['Default IP' => $_SERVER['REMOTE_ADDR']]);
        return $_SERVER['REMOTE_ADDR'];
    } else {
        logMessage('IP не определен через прокси и REMOTE_ADDR', 'WARNING', ['Default IP' => 'UNKNOWN']);
        return 'UNKNOWN';
    }
}


$clientIP = getClientIP();
logMessage('Запуск запроса на обработку изображения', 'INFO', ['request' => $_REQUEST, 'ip' => $clientIP]);

if (!isset($_FILES['image'])) {
    logMessage('Файл изображения не найден в запросе', 'ERROR', ['FILES' => $_FILES, 'ip' => $clientIP]);
    header('HTTP/1.1 400 Bad Request');
    die(json_encode(['error' => 'Файл не загружен']));
}

$file = $_FILES['image'];
$format = strtolower($_POST['format'] ?? 'webp');
$quality = intval($_POST['quality'] ?? 80);

logMessage('Получение параметров', 'INFO', [
    'original_filename' => $file['name'] ?? 'undefined',
    'tmp_name' => $file['tmp_name'] ?? 'undefined',
    'mime_type' => $file['type'] ?? 'undefined',
    'size' => $file['size'] ?? 0,
    'format' => $format,
    'quality' => $quality,
    'ip' => $clientIP
]);

if ($file['error'] !== UPLOAD_ERR_OK) {
    $error_text = getUploadErrorText($file['error']);
    logMessage('Ошибка при загрузке файла', 'ERROR', [
        'error_code' => $file['error'],
        'error_text' => $error_text,
        'ip' => $clientIP
    ]);
    ConversionLogger::logError(
        $clientIP,
        $file['name'] ?? '',
        pathinfo($file['name'] ?? '', PATHINFO_EXTENSION) ?? '',
        $format,
        $quality,
        $e->getMessage()
    );
    header('HTTP/1.1 400 Bad Request');
    die(json_encode(['error' => $error_text]));
}

$allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
if (!in_array($file['type'], $allowed_types)) {
    ConversionLogger::logError(
        $clientIP,
        $file['name'] ?? '',
        pathinfo($file['name'] ?? '', PATHINFO_EXTENSION) ?? '',
        $format,
        $quality,
        $e->getMessage()
    );
    logMessage('Тип файла не поддерживается', 'ERROR', [
        'uploaded_type' => $file['type'],
        'allowed_types' => $allowed_types,
        'ip' => $clientIP
    ]);
    header('HTTP/1.1 400 Bad Request');
    die(json_encode(['error' => 'Неподдерживаемый формат файла']));
}

if ($format === 'webp' && !function_exists('imagewebp')) {
    logMessage('Функция imagewebp отсутствует (WebP не поддерживается)', 'ERROR', ['ip' => $clientIP]);
    ConversionLogger::logError(
        $clientIP,
        $file['name'] ?? '',
        pathinfo($file['name'] ?? '', PATHINFO_EXTENSION) ?? '',
        $format,
        $quality,
        $e->getMessage()
    );
    header('HTTP/1.1 400 Bad Request');
    die(json_encode(['error' => 'WebP не поддерживается на этом сервере']));
}

if ($format === 'avif' && !function_exists('imageavif')) {
    logMessage('Функция imageavif отсутствует (AVIF не поддерживается)', 'ERROR', ['ip' => $clientIP]);
    ConversionLogger::logError(
        $clientIP,
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
            logMessage('Изображение JPEG успешно загружено', 'INFO', ['uploaded_image' =>  $file['name'],'ip' => $clientIP]);
            break;
        case 'image/png':
            $source_image = imagecreatefrompng($file['tmp_name']);
            if ($source_image) {
                imagealphablending($source_image, true);
                imagesavealpha($source_image, true);
                logMessage('PNG загружен с поддержкой прозрачности', 'INFO', ['ip' => $clientIP]);
            }
            break;
        case 'image/gif':
            $source_image = imagecreatefromgif($file['tmp_name']);
            if (!$source_image) {
                throw new Exception('Ошибка загрузки GIF изображения');
            }
            logMessage('GIF успешно загружен', 'INFO', ['ip' => $clientIP]);
            break;
    }

    if (!$source_image) {
        throw new Exception('Не удалось создать ресурс изображения (GD вернул null)');
    }

    $filename = uniqid() . '.' . $format;
    $output_path = $upload_dir . $filename;

    logMessage('Начало конвертации изображения', 'INFO', [
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

            logMessage('Сохранение GIF', 'DEBUG', [
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
            logMessage('Удален битый или пустой файл после неудачной конвертации', 'WARNING', [
                'format' => $format,
                'path' => $output_path,
                'ip' => $clientIP
            ]);
        }

        logMessage('Ошибка при сохранении изображения', 'ERROR', [
            'format' => $format,
            'output_path' => $output_path,
            'ip' => $clientIP
        ]);
        throw new Exception("Ошибка при записи изображения в формате $format");
    }


    logMessage('Успешная конвертация изображения', 'SUCCESS', [
        'original_filename' => $file['name'],
        'converted_filename' => $filename,
        'converted_size' => filesize($output_path),
        'output_path' => realpath($output_path),
        'ip' => $clientIP
    ]);
    ConversionLogger::logSuccess(
        $clientIP,
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
    logMessage('Фатальная ошибка при обработке изображения', 'ERROR', [
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


function getUploadErrorText($error_code)
{
    $errors = [
        UPLOAD_ERR_INI_SIZE => 'Превышен максимальный размер файла в php.ini',
        UPLOAD_ERR_FORM_SIZE => 'Превышен MAX_FILE_SIZE в HTML-форме',
        UPLOAD_ERR_PARTIAL => 'Файл загружен только частично',
        UPLOAD_ERR_NO_FILE => 'Файл не был загружен',
        UPLOAD_ERR_NO_TMP_DIR => 'Отсутствует временная папка на сервере',
        UPLOAD_ERR_CANT_WRITE => 'Ошибка записи файла на диск',
        UPLOAD_ERR_EXTENSION => 'Загрузка прервана расширением PHP'
    ];
    return $errors[$error_code] ?? 'Неизвестная ошибка загрузки';
}
?>