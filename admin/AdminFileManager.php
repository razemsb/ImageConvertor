<?php

require_once __DIR__ . '/../config/DatabaseConnect.php';

class AdminFileManager
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = DB::connect();
    }

    public function getAllFiles(): array
    {
        $stmt = $this->pdo->query("
            SELECT * FROM conversions
            ORDER BY created_at DESC
        ");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getOldFiles(int $hours = 3): array
    {
        $timeLimit = date('Y-m-d H:i:s', time() - ($hours * 3600));
        $stmt = $this->pdo->prepare("
        SELECT * FROM conversions
        WHERE created_at < :timeLimit
        ORDER BY created_at DESC
    ");
        $stmt->execute(['timeLimit' => $timeLimit]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getUsernameById(int $userId): string
    {
        $stmt = $this->pdo->prepare("SELECT username FROM users WHERE id = :id LIMIT 1");
        $stmt->execute(['id' => $userId]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        return $result ? htmlspecialchars($result['username']) : 'Неизвестный';
    }


    public function restoreFileById(int $id): bool
    {
        $stmt = $this->pdo->prepare("UPDATE conversions SET `record-status` = 'active' WHERE id = :id");
        return $stmt->execute(['id' => $id]);
    }

    public function deleteFileById(int $id): bool
    {
        $stmt = $this->pdo->prepare("UPDATE conversions SET `record-status` = 'deleted' WHERE id = :id");
        return $stmt->execute(['id' => $id]);
    }

    public function getFileById(int $id): ?array
    {
        $stmt = $this->pdo->prepare("SELECT * FROM conversions WHERE id = :id");
        $stmt->execute(['id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    public static function formatFileSize(int $bytes): string
    {
        if ($bytes < 1024)
            return $bytes . ' B';
        if ($bytes < 1048576)
            return round($bytes / 1024, 2) . ' KB';
        if ($bytes < 1073741824)
            return round($bytes / 1048576, 2) . ' MB';
        return round($bytes / 1073741824, 2) . ' GB';
    }
}
