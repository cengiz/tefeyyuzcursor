<?php
declare(strict_types=1);

namespace Tefeyyuz\Service;

use Tefeyyuz\Repository\UserRepository;
use Tefeyyuz\Support\Logger;
use Tefeyyuz\Support\Session;

final class AuthService
{
    public function __construct(
        private readonly UserRepository $users,
        private readonly Session $session,
        private readonly Logger $logger,
    ) {
    }

    public function attempt(string $email, string $password): bool
    {
        try {
            $user = $this->users->findByEmail($email);
            if (!$user || !password_verify($password, (string) $user['password_hash'])) {
                $this->logger->warning('Başarısız giriş denemesi', ['email' => $email]);
                return false;
            }
            $this->session->set('user_id', (int) $user['id']);
            $this->session->set('user_name', (string) $user['name']);
            $this->logger->info('Yönetici girişi', ['email' => $email]);
            return true;
        } catch (\Throwable $e) {
            $this->logger->error('Giriş işlemi hata verdi', ['hata' => $e->getMessage()]);
            throw $e;
        }
    }

    public function user(): ?array
    {
        $id = $this->session->get('user_id');
        if (!is_int($id) && !is_numeric($id)) {
            return null;
        }
        return $this->users->findById((int) $id);
    }

    public function logout(): void
    {
        $this->session->forget('user_id');
        $this->session->forget('user_name');
    }

    public function requireAdmin(): array
    {
        $user = $this->user();
        if (!$user) {
            header('Location: /admin/giris/', true, 302);
            exit;
        }
        return $user;
    }
}
