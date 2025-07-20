<?php
header('Content-Type: application/json');

$folderPath ='converted/';

// Проверяем существование папки
if (!file_exists($folderPath)) {
    echo json_encode(['success' => false, 'error' => 'Папка converted не существует']);
    exit;
}

// Проверяем, является ли путь папкой
if (!is_dir($folderPath)) {
    echo json_encode(['success' => false, 'error' => 'Указанный путь не является папкой']);
    exit;
}

try {
    $deletedFiles = 0;
    $errors = [];
    
    // Открываем папку
    $dir = new DirectoryIterator($folderPath);
    
    foreach ($dir as $fileInfo) {
        // Пропускаем текущую и родительскую директории, а также саму папку
        if ($fileInfo->isDot()) continue;
        
        // Полный путь к файлу/папке
        $filePath = $fileInfo->getPathname();
        
        try {
            // Если это файл - удаляем
            if ($fileInfo->isFile()) {
                if (unlink($filePath)) {
                    $deletedFiles++;
                } else {
                    $errors[] = "Не удалось удалить файл: " . $fileInfo->getFilename();
                }
            } 
            // Если это папка - удаляем рекурсивно
            elseif ($fileInfo->isDir()) {
                $dirToDelete = new RecursiveDirectoryIterator($filePath, RecursiveDirectoryIterator::SKIP_DOTS);
                $files = new RecursiveIteratorIterator($dirToDelete, RecursiveIteratorIterator::CHILD_FIRST);
                
                foreach ($files as $file) {
                    if ($file->isDir()) {
                        rmdir($file->getRealPath());
                    } else {
                        unlink($file->getRealPath());
                    }
                }
                rmdir($filePath);
                $deletedFiles++;
            }
        } catch (Exception $e) {
            $errors[] = "Ошибка при удалении " . $fileInfo->getFilename() . ": " . $e->getMessage();
        }
    }
    
    if (empty($errors)) {
        echo json_encode([
            'success' => true,
            'message' => "Успешно удалено $deletedFiles файлов/папок",
            'deleted' => $deletedFiles
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'error' => 'Произошли ошибки при удалении',
            'deleted' => $deletedFiles,
            'errors' => $errors
        ]);
    }
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'error' => 'Ошибка: ' . $e->getMessage()
    ]);
}
?>