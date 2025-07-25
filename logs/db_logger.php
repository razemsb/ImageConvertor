<?php
class ConversionLogger {
    public static function logSuccess($ip, $user_id, $originalName, $newName, $originalFormat, $newFormat, $originalSize, $newSize, $quality) {
        $db = DB::connect();
        $stmt = $db->prepare("
            INSERT INTO conversions (
                ip, user_id, original_name, new_name, original_format, 
                new_format, original_size, new_size, quality, status
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'success')
        ");
        $stmt->execute([$ip, $user_id, $originalName, $newName, $originalFormat, $newFormat, $originalSize, $newSize, $quality]);
    }

    public static function logError($ip, $user_id, $originalName, $originalFormat, $newFormat, $quality, $errorMessage) {
        $db = DB::connect();
        $stmt = $db->prepare("
            INSERT INTO conversions (
                ip, user_id, original_name, original_format, 
                new_format, quality, status, error_message
            ) VALUES (?, ?, ?, ?, ?, ?, 'error', ?)
        ");
        $stmt->execute([$ip, $user_id, $originalName, $originalFormat, $newFormat, $quality, $errorMessage]);
    }
}