<?php
header('Content-Type: application/json');
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Проверка наличия файла
if (!isset($_FILES['image'])) {
    header('HTTP/1.1 400 Bad Request');
    die('Файл не загружен');
}

$file = $_FILES['image'];
$format = $_POST['format'] ?? 'webp';
$quality = intval($_POST['quality'] ?? 80);

// Проверка ошибок загрузки
if ($file['error'] !== UPLOAD_ERR_OK) {
    header('HTTP/1.1 400 Bad Request');
    die('Ошибка загрузки файла');
}

// Проверка типа файла
$allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
if (!in_array($file['type'], $allowed_types)) {
    header('HTTP/1.1 400 Bad Request');
    die('Неподдерживаемый формат файла');
}

// Проверка поддержки форматов
if ($format === 'webp' && !function_exists('imagewebp')) {
    header('HTTP/1.1 400 Bad Request');
    die('WebP не поддерживается на этом сервере');
}

if ($format === 'avif' && !function_exists('imageavif')) {
    header('HTTP/1.1 400 Bad Request');
    die('AVIF не поддерживается на этом сервере');
}

// Создание директории для сохранения
$upload_dir = 'converted/';
if (!file_exists($upload_dir)) {
    mkdir($upload_dir, 0777, true);
}

// Загрузка изображения
$source_image = null;
switch ($file['type']) {
    case 'image/jpeg':
        $source_image = imagecreatefromjpeg($file['tmp_name']);
        break;
    case 'image/png':
        $source_image = imagecreatefrompng($file['tmp_name']);
        if ($source_image) {
            // Сохраняем прозрачность для PNG
            imagealphablending($source_image, true);
            imagesavealpha($source_image, true);
        }
        break;
    case 'image/gif':
        $source_image = imagecreatefromgif($file['tmp_name']);
        break;
}

if (!$source_image) {
    header('HTTP/1.1 500 Internal Server Error');
    die('Не удалось загрузить изображение');
}

// Генерация имени файла
$filename = uniqid() . '.' . $format;
$output_path = $upload_dir . $filename;

// Конвертация и сохранение
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
        // Для PNG качество от 0 до 9
        $png_quality = round(9 - ($quality / 100 * 9));
        $success = imagepng($source_image, $output_path, $png_quality);
        break;
}

// Освобождение памяти
imagedestroy($source_image);

if ($success) {
    // Возвращаем информацию о сохраненном файле
    echo json_encode([
        'success' => true,
        'filename' => $filename,
        'path' => $output_path,
        'originalName' => $file['name'],
        'format' => $format,
        'quality' => $quality,
        'timestamp' => time()
    ]);
} else {
    header('HTTP/1.1 500 Internal Server Error');
    die('Ошибка при конвертации');
}
?> 