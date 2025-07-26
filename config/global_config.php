<?php
ini_set("php_error_log", '../logs/php_error.log');

class DB {
    private static $connection = null;

    public static function connect() {
        if (self::$connection === null) {
            self::$connection = new PDO(
                'mysql:host=localhost;dbname=convertor;charset=utf8mb4',
                'root',
                '', 
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
                ]
            );
        }
        return self::$connection;
    }
}