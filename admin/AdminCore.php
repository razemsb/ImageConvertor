<?php

class AdminCore
{
    private static $instance = null;
    private $pdo;

    private function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
        $this->initSession();
    }

    public static function init(PDO $pdo): self
    {
        if (self::$instance === null) {
            self::$instance = new self($pdo);
        }
        return self::$instance;
    }

    private function initSession(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start([
                'cookie_lifetime' => 86400,
                'cookie_secure' => isset($_SERVER['HTTPS']),
                'cookie_httponly' => true,
                'cookie_samesite' => 'Strict'
            ]);
        }
    }


    public function isAdmin(): bool
    {
        return isset($_SESSION['admin_id']);
    }

    public function requireAdmin(): void
    {
        if (!$this->isAdmin()) {
            header("Location: ./index.php");
            exit;
        }
    }


    public function generateCsrfToken(): string
    {
        if (!isset($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }

    public function validateCsrfToken(string $token): bool
    {
        return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
    }


    public function getConversionStats(): array
    {
        $stats = [
            'total_conversions' => 0,
            'today_conversions' => 0,
            'error_rate' => 0,
            'popular_formats' => [],
            'active_users' => []
        ];

        try {
            $stmt = $this->pdo->query("SELECT COUNT(*) as count FROM conversions");
            $stats['total_conversions'] = $stmt->fetchColumn();

            $stmt = $this->pdo->query("SELECT COUNT(*) as count FROM conversions WHERE DATE(created_at) = CURDATE()");
            $stats['today_conversions'] = $stmt->fetchColumn();

            $stmt = $this->pdo->query("SELECT COUNT(*) FROM conversions WHERE status = 'error'");
            $stats['error_count'] = (int) $stmt->fetchColumn();

            $stmt = $this->pdo->query("
            SELECT new_format as format, COUNT(*) as count 
            FROM conversions 
            WHERE status = 'success' AND new_format IS NOT NULL
            GROUP BY new_format 
            ORDER BY count DESC 
            LIMIT 4
        ");
            $stats['popular_formats'] = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $stmt = $this->pdo->query("
                SELECT 
                    u.id,
                    u.username,
                    u.role,
                    u.avatar,
                    COUNT(c.id) as conversions_count,
                    (
                        SELECT ip 
                        FROM conversions 
                        WHERE user_id = u.id 
                        ORDER BY created_at DESC 
                        LIMIT 1
                    ) as last_ip
                FROM users u
                LEFT JOIN conversions c 
                    ON c.user_id = u.id 
                    AND c.created_at >= DATE_SUB(NOW(), INTERVAL 1 MONTH)
                GROUP BY u.id
                HAVING conversions_count > 0
                ORDER BY conversions_count DESC
                LIMIT 5
            ");

            $stats['active_users'] = $stmt->fetchAll(PDO::FETCH_ASSOC);



        } catch (PDOException $e) {
            error_log("Error getting stats: " . $e->getMessage());
        }

        return $stats;
    }

    public function getUserInfo(int $userId): ?array
    {
        try {
            $stmt = $this->pdo->prepare("
            SELECT 
                id, username, role, avatar
            FROM users
            WHERE id = :user_id
        ");
            $stmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
            $stmt->execute();

            return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
        } catch (PDOException $e) {
            error_log("Error getting user info: " . $e->getMessage());
            return null;
        }
    }

    public function getConversionLogs(int $page = 1, int $perPage = 25): array
    {
        $logs = [];
        $total = 0;

        try {
            $stmt = $this->pdo->query("SELECT COUNT(*) as count FROM conversions");
            $total = $stmt->fetchColumn();

            $offset = ($page - 1) * $perPage;
            $stmt = $this->pdo->prepare("
                SELECT 
                    id, ip, user_id, original_name, new_name, 
                    original_format, new_format, 
                    original_size, new_size, quality, 
                    status, error_message, created_at
                FROM conversions
                ORDER BY created_at DESC
                LIMIT :limit OFFSET :offset
            ");
            $stmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
            $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
            $stmt->execute();

            $logs = $stmt->fetchAll(PDO::FETCH_ASSOC);

            foreach ($logs as &$log) {
                if (!empty($log['user_id'])) {
                    $log['user_info'] = $this->getUserInfo($log['user_id']);
                } else {
                    $log['user_info'] = null;
                }
            }

        } catch (PDOException $e) {
            error_log("Error getting logs: " . $e->getMessage());
        }

        return [
            'logs' => $logs,
            'total' => $total,
            'total_pages' => ceil($total / $perPage),
            'current_page' => $page
        ];
    }

    public function getAllUsersWithDetails(): array
    {
        $stmt = $this->pdo->prepare("
        SELECT 
            u.id,
            u.username,
            u.email,
            u.avatar,
            u.role,
            u.created_at,
            u.updated_at,
            MAX(l.ip) AS last_ip,
            COUNT(l.id) AS conversions_count
        FROM users u
        LEFT JOIN conversions l ON l.user_id = u.id
        GROUP BY u.id
        ORDER BY conversions_count DESC, u.username ASC
    ");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }



    public static function formatFileSize(int $bytes): string
    {
        if ($bytes == 0) {
            return '0 Bytes';
        }

        $units = ['Bytes', 'KB', 'MB', 'GB'];
        $i = floor(log($bytes, 1024));
        return round($bytes / pow(1024, $i), 2) . ' ' . $units[$i];
    }

    public function logout(): void
    {
        $_SESSION = [];

        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params["path"],
                $params["domain"],
                $params["secure"],
                $params["httponly"]
            );
        }

        session_destroy();
    }
}