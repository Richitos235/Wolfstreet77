<?php

declare(strict_types=1);

namespace App\Helpers;

class SessionHelper
{
    public static function start(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    public static function destroy(): void
    {
        session_destroy();
    }

    public static function set(string $key, $value): void
    {
        $_SESSION[$key] = $value;
    }

    public static function get(string $key, $default = null)
    {
        return $_SESSION[$key] ?? $default;
    }

    public static function has(string $key): bool
    {
        return isset($_SESSION[$key]);
    }

    public static function remove(string $key): void
    {
        unset($_SESSION[$key]);
    }

    public static function isAuthenticated(): bool
    {
        return self::has('user_id') && self::has('username');
    }

    public static function getCurrentUserId(): ?int
    {
        return self::get('user_id');
    }

    public static function getCurrentUser(): ?array
    {
        return self::get('user');
    }

    public static function generateCSRFToken(): string
    {
        if (!self::has('csrf_token')) {
            self::set('csrf_token', bin2hex(random_bytes(32)));
        }
        return self::get('csrf_token');
    }

    public static function verifyCSRFToken(string $token): bool
    {
        return hash_equals(self::get('csrf_token', ''), $token);
    }
}
