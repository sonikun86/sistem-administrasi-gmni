<?php
// Helpers/Csrf.php
declare(strict_types=1);

namespace App\Helpers;

class Csrf
{
    public static function token(): string
    {
        Auth::init();
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }

    public static function field(): string
    {
        $token = self::token();
        return '<input type="hidden" name="_csrf" value="' . htmlspecialchars($token, ENT_QUOTES, 'UTF-8') . '">';
    }

    public static function validate(?string $token = null): bool
    {
        Auth::init();
        $token = $token ?? $_POST['_csrf'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? null;
        if (!$token || empty($_SESSION['csrf_token'])) {
            return false;
        }
        return hash_equals($_SESSION['csrf_token'], (string)$token);
    }

    public static function verify(): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!self::validate()) {
                http_response_code(419);
                die('Sesi kedaluwarsa atau token CSRF tidak valid. Silakan muat ulang halaman.');
            }
        }
    }
}

