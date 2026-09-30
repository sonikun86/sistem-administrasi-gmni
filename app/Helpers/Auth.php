<?php
// Helpers/Auth.php
declare(strict_types=1);

namespace App\Helpers;

use App\Models\User;

class Auth
{
    public static function init(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            $sessionDir = __DIR__ . '/../../storage/sessions';
            if (!is_dir($sessionDir)) {
                @mkdir($sessionDir, 0777, true);
            }
            if (is_dir($sessionDir) && is_writable($sessionDir)) {
                session_save_path($sessionDir);
            }
            session_start();
        }
    }

    public static function check(): bool
    {
        self::init();
        return isset($_SESSION['user_id']);
    }

    public static function user(): ?array
    {
        self::init();
        if (!self::check()) {
            return null;
        }

        if (!isset($_SESSION['user_data'])) {
            $user = (new User())->find((int)$_SESSION['user_id']);
            if ($user) {
                $_SESSION['user_data'] = $user;
            } else {
                self::logout();
                return null;
            }
        }

        return $_SESSION['user_data'];
    }

    public static function id(): ?int
    {
        self::init();
        return $_SESSION['user_id'] ?? null;
    }

    public static function role(): ?string
    {
        $user = self::user();
        return $user['role'] ?? null;
    }

    public static function isKomisariatBesar(): bool
    {
        $role = self::role();
        return in_array($role, ['ketua_besar', 'sekretaris_besar', 'admin_besar'], true);
    }

    public static function isKetuaBesar(): bool
    {
        return self::role() === 'ketua_besar';
    }

    public static function isSekretarisBesar(): bool
    {
        return self::role() === 'sekretaris_besar';
    }

    public static function isDpk(): bool
    {
        return self::role() === 'dpk';
    }

    public static function dpkId(): ?int
    {
        $user = self::user();
        return isset($user['komisariat_id']) ? (int)$user['komisariat_id'] : null;
    }

    public static function login(array $user): void
    {
        self::init();
        if (!headers_sent()) {
            session_regenerate_id(true);
        }
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_data'] = $user;
    }

    public static function logout(): void
    {
        self::init();
        $_SESSION = [];
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }
        session_destroy();
    }

    public static function requireAuth(): void
    {
        if (!self::check()) {
            header('Location: ' . \App\Config\App::baseUrl('login'));
            exit;
        }
    }

    public static function requireKomisariatBesar(): void
    {
        self::requireAuth();
        if (!self::isKomisariatBesar()) {
            http_response_code(403);
            die("Akses ditolak: Hanya Pengurus Komisariat Besar GMNI UNISDA yang dapat mengakses halaman ini.");
        }
    }
}
