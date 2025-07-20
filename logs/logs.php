<?php
/**
 * Универсальная функция логирования
 * @param string $message Основное сообщение
 * @param string $type Тип сообщения (по умолчанию INFO)
 * @param mixed $data Дополнительные данные (массив или строка)
 */
date_default_timezone_set('Europe/Moscow');
function logMessage($message, $type = 'INFO', $data = null) {
    $logFile = __DIR__ . '/converter.log';
    $timestamp = date('Y-m-d H:i:s');
    $ip = $_SERVER['REMOTE_ADDR'] ?? 'UNKNOWN';
    
    // Создаем структурированную запись
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
    
    // Добавляем детали операции
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
    
    // Добавляем разделитель между операциями
    $logEntry .= "=======================\n\n";
    
    file_put_contents($logFile, $logEntry, FILE_APPEND | LOCK_EX);
}
// function CrashReport($message, $type = 'ERROR') {
//     $logFile = __DIR__ . '/crash_report.log';
//     $timestamp = date('Y-m-d H:i:s');
//     $ip = $_SERVER['REMOTE_ADDR'] ?? 'UNKNOWN';
//     $logEntry = "[$timestamp][$type][IP:$ip] $message";
//     file_put_contents($logFile, $logEntry . PHP_EOL, FILE_APPEND | LOCK_EX);
// }