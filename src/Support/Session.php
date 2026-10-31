<?php
declare(strict_types=1);

namespace Tefeyyuz\Support;

final class Session
{
    public function __construct(string $name)
    {
        if (PHP_SAPI === 'cli') {
            if (!isset($_SESSION) || !is_array($_SESSION)) {
                $_SESSION = [];
            }
            return;
        }
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_name($name);
            session_start([
                'cookie_httponly' => true,
                'cookie_samesite' => 'Lax',
            ]);
        }
    }

    public function get(string $key, mixed $default = null): mixed
    {
        return $_SESSION[$key] ?? $default;
    }

    public function set(string $key, mixed $value): void
    {
        $_SESSION[$key] = $value;
    }

    public function forget(string $key): void
    {
        unset($_SESSION[$key]);
    }

    public function flash(string $key, ?string $value = null): ?string
    {
        if ($value !== null) {
            $_SESSION['_flash'][$key] = $value;
            return $value;
        }
        $msg = $_SESSION['_flash'][$key] ?? null;
        unset($_SESSION['_flash'][$key]);
        return $msg;
    }

    public function csrfToken(): string
    {
        $token = $this->get('_csrf');
        if (!is_string($token) || $token === '') {
            $token = bin2hex(random_bytes(32));
            $this->set('_csrf', $token);
        }
        return $token;
    }

    public function verifyCsrf(?string $token): bool
    {
        $expected = $this->get('_csrf');
        return is_string($expected) && is_string($token) && hash_equals($expected, $token);
    }
}
