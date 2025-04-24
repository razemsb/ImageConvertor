<?php
header('Content-Type: application/json');
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Создаем директории, если они не существуют
$originalDir = 'original/';
$convertedDir = 'converted/';

if (!file_exists($originalDir)) {
    mkdir($originalDir, 0777, true);
}
if (!file_exists($convertedDir)) {
    mkdir($convertedDir, 0777, true);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_FILES['image'])) {
        $file = $_FILES['image'];
        $fileName = $file['name'];
        $fileTmpName = $file['tmp_name'];
        $fileError = $file['error'];

        if ($fileError === 0) {
            // Генерируем случайное имя файла
            $randomName = uniqid() . '_' . time();
            $originalPath = $originalDir . $randomName . '_' . $fileName;
            $convertedPath = $convertedDir . $randomName . '.webp';

            // Сохраняем оригинальное изображение
            if (!move_uploaded_file($fileTmpName, $originalPath)) {
                echo json_encode([
                    'success' => false,
                    'error' => 'Ошибка при сохранении оригинального файла: ' . error_get_last()['message']
                ]);
                exit;
            }

            // Конвертируем в WebP
            $image = null;
            $extension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

            try {
                switch ($extension) {
                    case 'jpeg':
                    case 'jpg':
                        $image = imagecreatefromjpeg($originalPath);
                        break;
                    case 'png':
                        $image = imagecreatefrompng($originalPath);
                        break;
                    case 'gif':
                        $image = imagecreatefromgif($originalPath);
                        break;
                    default:
                        throw new Exception('Неподдерживаемый формат файла: ' . $extension);
                }

                if (!$image) {
                    throw new Exception('Не удалось создать изображение из файла');
                }

                // Получаем размеры изображения
                $width = imagesx($image);
                $height = imagesy($image);

                // Если изображение слишком большое, уменьшаем его
                $maxDimension = 2000;
                if ($width > $maxDimension || $height > $maxDimension) {
                    $ratio = $width / $height;
                    if ($ratio > 1) {
                        $newWidth = $maxDimension;
                        $newHeight = $maxDimension / $ratio;
                    } else {
                        $newHeight = $maxDimension;
                        $newWidth = $maxDimension * $ratio;
                    }
                    
                    $resizedImage = imagecreatetruecolor($newWidth, $newHeight);
                    
                    // Сохраняем прозрачность для PNG
                    if ($extension === 'png') {
                        imagealphablending($resizedImage, false);
                        imagesavealpha($resizedImage, true);
                    }
                    
                    imagecopyresampled($resizedImage, $image, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);
                    imagedestroy($image);
                    $image = $resizedImage;
                }

                // Оптимальные настройки для WebP
                $quality = 80; // Баланс между качеством и размером
                $lossless = false; // Используем сжатие с потерями для меньшего размера

                // Сохраняем в WebP с оптимизированными настройками
                if (!imagewebp($image, $convertedPath, $quality)) {
                    throw new Exception('Ошибка при сохранении WebP: ' . error_get_last()['message']);
                }

                imagedestroy($image);

                // Получаем размеры файлов для сравнения
                $originalSize = filesize($originalPath);
                $convertedSize = filesize($convertedPath);
                $compressionRatio = round(($originalSize - $convertedSize) / $originalSize * 100, 2);

                echo json_encode([
                    'success' => true,
                    'original' => $originalPath,
                    'converted' => $convertedPath,
                    'originalSize' => $originalSize,
                    'convertedSize' => $convertedSize,
                    'compressionRatio' => $compressionRatio
                ]);
            } catch (Exception $e) {
                if ($image) {
                    imagedestroy($image);
                }
                echo json_encode([
                    'success' => false,
                    'error' => $e->getMessage()
                ]);
            }
        } else {
            echo json_encode([
                'success' => false,
                'error' => 'Ошибка при загрузке файла: ' . $fileError
            ]);
        }
    } else {
        echo json_encode([
            'success' => false,
            'error' => 'Файл не был загружен'
        ]);
    }
} else {
    echo json_encode([
        'success' => false,
        'error' => 'Неверный метод запроса'
    ]);
} 