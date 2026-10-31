<?php
declare(strict_types=1);

namespace Tefeyyuz\Repository;

use Tefeyyuz\Dto\SubmissionDto;
use Tefeyyuz\Support\Database;

final class SubmissionRepository
{
    public function __construct(private readonly Database $db)
    {
    }

    public function create(string $title, string $detail, string $category, string $email, string $displayName): int
    {
        $this->db->execute(
            'INSERT INTO submissions (title, detail, category, email, display_name, status, created_at)
             VALUES (:title, :detail, :category, :email, :display_name, :status, :created_at)',
            [
                'title' => $title,
                'detail' => $detail,
                'category' => $category,
                'email' => $email,
                'display_name' => $displayName,
                'status' => 'pending',
                'created_at' => date('c'),
            ]
        );
        return $this->db->lastId();
    }

    /** @return list<SubmissionDto> */
    public function pending(): array
    {
        $rows = $this->db->all("SELECT * FROM submissions WHERE status = 'pending' ORDER BY created_at DESC");
        return array_map(fn (array $r) => $this->map($r), $rows);
    }

    public function countPending(): int
    {
        return (int) ($this->db->one("SELECT COUNT(*) AS c FROM submissions WHERE status = 'pending'")['c'] ?? 0);
    }

    public function find(int $id): ?SubmissionDto
    {
        $row = $this->db->one('SELECT * FROM submissions WHERE id = :id', ['id' => $id]);
        return $row ? $this->map($row) : null;
    }

    public function setStatus(int $id, string $status): void
    {
        $this->db->execute('UPDATE submissions SET status = :status WHERE id = :id', compact('status', 'id'));
    }

    public function delete(int $id): void
    {
        $this->db->execute('DELETE FROM submissions WHERE id = :id', ['id' => $id]);
    }

    private function map(array $r): SubmissionDto
    {
        return new SubmissionDto(
            (int) $r['id'],
            (string) $r['title'],
            (string) $r['detail'],
            (string) $r['category'],
            (string) $r['email'],
            (string) $r['display_name'],
            (string) $r['status'],
            (string) $r['created_at']
        );
    }
}
