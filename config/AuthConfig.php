<?php
require_once("DatabaseConnect.php");

class Auth
{
    private static $instance = null;
    private $pdo;
    private $sessionKey = 'auth_user';
    private $cookieName = 'remember_me';
    private $cookieExpire = 2592000;
    private $defaultAvatars = [
        'default-avatar-1.png',
        'default-avatar-2.png',
        'default-avatar-3.png',
        'default-avatar-4.png',
        'default-avatar-5.png',
        'default-avatar-6.png'
    ];

    private function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
        $this->startSession();
    }


    public static function getInstance(PDO $pdo = null): self
    {
        if (self::$instance === null) {
            if ($pdo === null) {
                throw new RuntimeException('PDO instance is required for first initialization');
            }
            self::$instance = new self($pdo);
        }
        return self::$instance;
    }


    private function startSession(): void
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


    public function isLoggedIn(): bool
    {
        return isset($_SESSION[$this->sessionKey]) && !empty($_SESSION[$this->sessionKey]['id']);
    }


    public function login(string $username, string $password): bool
    {
        $stmt = $this->pdo->prepare("SELECT id, username, password, avatar, role, created_at, updated_at FROM users WHERE username = :username");
        $stmt->execute(['username' => $username]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password'])) {
            unset($user['password']);
            $_SESSION[$this->sessionKey] = $user;
            return true;
        }

        return false;
    }


    public function register(string $username, string $password, string $email): bool
    {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return false;
        }

        $stmt = $this->pdo->prepare("SELECT id FROM users WHERE username = :username");
        $stmt->execute(['username' => $username]);

        if ($stmt->fetch()) {
            return false;
        }

        $stmt = $this->pdo->prepare("SELECT id FROM users WHERE email = :email");
        $stmt->execute(['email' => $email]);

        if ($stmt->fetch()) {
            return false;
        }

        $randomAvatar = $this->defaultAvatars[array_rand($this->defaultAvatars)];
        $passwordHash = password_hash($password, PASSWORD_DEFAULT);

        $stmt = $this->pdo->prepare("
        INSERT INTO users 
        (username, password, email, avatar, role, created_at, updated_at) 
        VALUES 
        (:username, :password_hash, :email, :avatar, 'user', NOW(), NOW())
        ");

        $result = $stmt->execute([
            'username' => $username,
            'password_hash' => $passwordHash,
            'email' => $email,
            'avatar' => $randomAvatar
        ]);

        if ($result) {
            $userId = $this->pdo->lastInsertId();

            $stmt = $this->pdo->prepare("
            SELECT id, username, email, avatar, role, created_at, updated_at 
            FROM users 
            WHERE id = :id
        ");
            $stmt->execute(['id' => $userId]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user) {
                $_SESSION[$this->sessionKey] = $user;
                return true;
            }
        }

        return false;
    }


    public function logout(): void
    {

        unset($_SESSION[$this->sessionKey]);


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


    public function getUser(): ?array
    {
        return $_SESSION[$this->sessionKey] ?? null;
    }


    public function generateCsrfToken(): string
    {
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }


    public function verifyCsrfToken(string $token): bool
    {
        return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
    }


    public function isAdmin(): bool
    {
        if (!$this->isLoggedIn()) {
            return false;
        }

        $user = $this->getUser();
        return isset($user['role']) && $user['role'] === 'admin';
    }
}