<?php

declare(strict_types=1);

namespace App\Security;

use App\Core\Environment;

final class AdminAuth
{
    public static function start(): void
    {
        if (session_status() === PHP_SESSION_ACTIVE) {
            return;
        }

        session_name('pje_admin');
        session_set_cookie_params([
            'lifetime' => 0,
            'path' => '/admin',
            'secure' => self::isHttps(),
            'httponly' => true,
            'samesite' => 'Strict',
        ]);
        session_start();
    }

    public static function isAuthenticated(): bool
    {
        self::start();
        return ($_SESSION['admin_authenticated'] ?? false) === true;
    }

    public static function csrfToken(): string
    {
        self::start();
        $_SESSION['admin_csrf'] ??= bin2hex(random_bytes(32));
        return (string) $_SESSION['admin_csrf'];
    }

    public static function verifyCsrf(string $token): bool
    {
        return hash_equals(self::csrfToken(), $token);
    }

    public static function attempt(string $username, string $password): bool
    {
        self::start();
        $configuredUsername = Environment::get('ADMIN_USERNAME');
        $configuredHash = Environment::get('ADMIN_PASSWORD_HASH');

        if ($configuredUsername === '' || $configuredHash === '' || !hash_equals($configuredUsername, $username) || !password_verify($password, $configuredHash)) {
            return false;
        }

        session_regenerate_id(true);
        $_SESSION['admin_authenticated'] = true;
        $_SESSION['admin_login_at'] = time();
        return true;
    }

    public static function logout(): void
    {
        self::start();
        $_SESSION = [];
        session_destroy();
    }

    private static function isHttps(): bool
    {
        return (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || (($_SERVER['SERVER_PORT'] ?? null) === '443');
    }
}
