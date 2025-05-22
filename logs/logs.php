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
    
    $logEntry = "[$timestamp][$type][IP:$ip] $message";
    
    if ($data !== null) {
        if (is_array($data)) {
            $logEntry .= ' | ' . json_encode($data, JSON_UNESCAPED_UNICODE);
        } else {
            $logEntry .= ' | ' . $data;
        }
    }
    
    file_put_contents($logFile, $logEntry . PHP_EOL, FILE_APPEND | LOCK_EX);
}
// function CrashReport($message, $type = 'ERROR') {
//     $logFile = __DIR__ . '/crash_report.log';
//     $timestamp = date('Y-m-d H:i:s');
//     $ip = $_SERVER['REMOTE_ADDR'] ?? 'UNKNOWN';
//     $logEntry = "[$timestamp][$type][IP:$ip] $message";
//     file_put_contents($logFile, $logEntry . PHP_EOL, FILE_APPEND | LOCK_EX);
// }