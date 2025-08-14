<?php
class ConversionLogger
{
    private const COOKIE_NAME = 'conv_sid';
    private const SESSION_KEY = 'anon_sid';

    public static function logSuccess($ip, $user_id, $user_agent, $originalName, $newName, $originalFormat, $newFormat, $originalSize, $newSize, $quality)
    {
        $db = DB::connect();
        $stmt = $db->prepare("
            INSERT INTO conversions (
                ip, user_id, user_agent, original_name, new_name, original_format, 
                new_format, original_size, new_size, quality, status
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'success')
        ");
        $stmt->execute([$ip, $user_id, $user_agent, $originalName, $newName, $originalFormat, $newFormat, $originalSize, $newSize, $quality]);
    }

    public static function logError($ip, $user_id, $user_agent, $originalName, $originalFormat, $newFormat, $quality, $errorMessage)
    {
        $db = DB::connect();
        $stmt = $db->prepare("
            INSERT INTO conversions (
                ip, user_id, user_agent, original_name, original_format, 
                new_format, quality, status, error_message
            ) VALUES (?, ?, ?, ?, ?, ?, ?, 'error', ?)
        ");
        $stmt->execute([$ip, $user_id, $user_agent, $originalName, $originalFormat, $newFormat, $quality, $errorMessage]);
    }

    public static function getClientIP()
    {
        if (php_sapi_name() === 'cli') {
            return 'CLI';
        }

        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            return $_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            return $_SERVER['HTTP_X_FORWARDED_FOR'];
        } elseif (!empty($_SERVER['REMOTE_ADDR'])) {
            ConversionLogger::logMessage('IP не определен через прокси, взят REMOTE_ADDR', 'WARNING', ['Default IP' => $_SERVER['REMOTE_ADDR']]);
            return $_SERVER['REMOTE_ADDR'];
        } else {
            ConversionLogger::logMessage('IP не определен через прокси и REMOTE_ADDR', 'WARNING', ['Default IP' => 'UNKNOWN']);
            return 'UNKNOWN';
        }
    }

    public static function getUploadErrorText($error_code){
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

    // public static function logSuccess(
    //     string $originalName,
    //     string $newName,
    //     string $originalFormat,
    //     string $newFormat,
    //     int $originalSize,
    //     int $newSize,
    //     int $quality,
    //     ?int $userId = null
    // ): int {
    //     $db = DB::connect();

    //     $stmt = $db->prepare("
    //         INSERT INTO conversions (
    //             user_id,
    //             session_id,
    //             ip_address,
    //             original_name,
    //             new_name,
    //             original_format,
    //             new_format,
    //             original_size,
    //             new_size,
    //             quality,
    //             status,
    //             created_at
    //         ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'success', NOW())
    //     ");

    //     $sessionId = $userId ? null : self::getCurrentSessionId();

    //     $stmt->execute([
    //         $userId,
    //         $sessionId,
    //         self::getClientIp(),
    //         $originalName,
    //         $newName,
    //         $originalFormat,
    //         $newFormat,
    //         $originalSize,
    //         $newSize,
    //         $quality
    //     ]);

    //     return $db->lastInsertId();
    // }

    // public static function logError(
    //     string $originalName,
    //     string $originalFormat,
    //     string $newFormat,
    //     int $quality,
    //     string $errorMessage,
    //     ?int $userId = null
    // ): void {
    //     $db = DB::connect();

    //     $stmt = $db->prepare("
    //         INSERT INTO conversions (
    //             user_id,
    //             session_id,
    //             ip_address,
    //             original_name,
    //             original_format,
    //             new_format,
    //             quality,
    //             status,
    //             error_message,
    //             created_at
    //         ) VALUES (?, ?, ?, ?, ?, ?, ?, 'error', ?, NOW())
    //     ");

    //     $sessionId = $userId ? null : self::getCurrentSessionId();

    //     $stmt->execute([
    //         $userId,
    //         $sessionId,
    //         self::getClientIp(),
    //         $originalName,
    //         $originalFormat,
    //         $newFormat,
    //         $quality,
    //         $errorMessage
    //     ]);
    // }

    public static function getHistory(?int $userId = null, int $limit = 50): array
    {
        $db = DB::connect();

        if ($userId) {
            $stmt = $db->prepare("
                SELECT * FROM conversions 
                WHERE user_id = ?
                ORDER BY created_at DESC 
                LIMIT ?
            ");
            $stmt->execute([$userId, $limit]);
        } else {
            $stmt = $db->prepare("
                SELECT * FROM conversions 
                WHERE session_id = ? AND user_id IS NULL
                ORDER BY created_at DESC 
                LIMIT ?
            ");
            $stmt->execute([self::getCurrentSessionId(), $limit]);
        }

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function mergeAnonymousHistory(int $userId): int
    {
        $db = DB::connect();

        $stmt = $db->prepare("
            UPDATE conversions 
            SET user_id = ?, 
                session_id = NULL
            WHERE session_id = ?
              AND user_id IS NULL
        ");

        $stmt->execute([$userId, self::getCurrentSessionId()]);

        self::clearSession();

        return $stmt->rowCount();
    }

    public static function getCurrentSessionId(): string
    {
        if (isset($_SESSION['user']['id'])) {
            return null;
        }


        if (!empty($_COOKIE[self::COOKIE_NAME])) {
            return $_COOKIE[self::COOKIE_NAME];
        }


        return self::createNewSession();
    }

    private static function createNewSession(): string
    {
        $newSessionId = bin2hex(random_bytes(16));


        setcookie(
            self::COOKIE_NAME,
            $newSessionId,
            [
                'expires' => time() + 60 * 60 * 24 * 30,
                'path' => '/',
                'secure' => true,
                'httponly' => true,
                'samesite' => 'Lax'
            ]
        );

        return $newSessionId;
    }

    public static function clearSession(): void
    {
        if (isset($_COOKIE[self::COOKIE_NAME])) {
            setcookie(self::COOKIE_NAME, '', time() - 3600, '/');
        }
    }

    public static function logMessage($message, $type = 'INFO', $data = null)
    {
        $logFile = __DIR__ . '/Conversion.log';
        $timestamp = date('Y-m-d H:i:s');
        $ip = $_SERVER['REMOTE_ADDR'] ?? 'UNKNOWN';
        $logEntry = sprintf(
            "=== Операция конвертации ===\n" .
            "Время начала: %s\n" .
            "IP адрес: %s\n" .
            "Тип операции: %s\n" .
            "Сообщение: %s\n",
            $timestamp,
            $ip,
            $type,
            $message
        );
        if ($data !== null) {
            $logEntry .= "Детали операции:\n";
            if (is_array($data)) {
                foreach ($data as $key => $value) {
                    if (is_array($value)) {
                        $logEntry .= sprintf("    %s:\n", $key);
                        foreach ($value as $subKey => $subValue) {
                            $logEntry .= sprintf("        %s: %s\n", $subKey, is_array($subValue) ? json_encode($subValue, JSON_UNESCAPED_UNICODE) : $subValue);
                        }
                    } else {
                        $logEntry .= sprintf("    %s: %s\n", $key, $value);
                    }
                }
            } else {
                $logEntry .= sprintf("    %s\n", $data);
            }
        }
        $logEntry .= "=======================\n\n";

        file_put_contents($logFile, $logEntry, FILE_APPEND | LOCK_EX);
    }
    public static function generateUuidV4() {
        return sprintf('%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
            mt_rand(0, 0xffff), mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0x0fff) | 0x4000,
            mt_rand(0, 0x3fff) | 0x8000,
            mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
        );
    }
}