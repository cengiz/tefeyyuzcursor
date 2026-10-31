<?php
declare(strict_types=1);

namespace Tefeyyuz\Repository;

use Tefeyyuz\Support\Database;

final class UserRepository
{
    public function __construct(private readonly Database $db)
    {
    }

    public function findByEmail(string $email): ?array
    {
        return $this->db->one('SELECT * FROM users WHERE email = :email', ['email' => $email]);
    }

    public function findById(int $id): ?array
    {
        return $this->db->one('SELECT id, email, name, role FROM users WHERE id = :id', ['id' => $id]);
    }
}
