<?php
header('Content-Type: application/json');
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Проверка наличия файла
if (!isset($_FILES['image'])) {
    die(json_encode(['error' => 'Файл не загружен']));
}

$file = $_FILES['image'];
$format = $_POST['format'] ?? 'webp';
$quality = intval($_POST['quality'] ?? 80);

// Проверка ошибок загрузки
if ($file['error'] !== UPLOAD_ERR_OK) {
    die(json_encode(['error' => 'Ошибка загрузки файла']));
}

// Проверка типа файла
$allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
if (!in_array($file['type'], $allowed_types)) {
    die(json_encode(['error' => 'Неподдерживаемый формат файла']));
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
        break;
    case 'image/gif':
        $source_image = imagecreatefromgif($file['tmp_name']);
        break;
}

if (!$source_image) {
    die(json_encode(['error' => 'Не удалось загрузить изображение']));
}

// Генерация имени файла
$filename = uniqid() . '.' . $format;
$output_path = $upload_dir . $filename;

// Сохранение в выбранном формате
$success = false;
switch ($format) {
    case 'webp':
        $success = imagewebp($source_image, $output_path, $quality);
        break;
    case 'avif':
        // Для AVIF качество от 0 до 100
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
    echo json_encode([
        'success' => true,
        'file' => $filename,
        'path' => $output_path
    ]);
} else {
    echo json_encode(['error' => 'Ошибка при конвертации']);
}
?> 