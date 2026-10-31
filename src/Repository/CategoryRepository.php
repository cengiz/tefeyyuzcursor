<?php
declare(strict_types=1);

namespace Tefeyyuz\Repository;

use Tefeyyuz\Dto\TermDto;
use Tefeyyuz\Support\Database;

final class CategoryRepository
{
    public function __construct(private readonly Database $db)
    {
    }

    /** @return list<TermDto> */
    public function allWithCounts(): array
    {
        $rows = $this->db->all(
            "SELECT c.*, COUNT(p.id) AS cnt
             FROM categories c
             LEFT JOIN page_categories pc ON pc.category_id = c.id
             LEFT JOIN pages p ON p.id = pc.page_id AND p.status = 'published'
             GROUP BY c.id
             ORDER BY cnt DESC, c.name ASC"
        );
        return array_map(fn (array $r) => $this->map($r), $rows);
    }

    public function findBySlug(string $slug): ?TermDto
    {
        $row = $this->db->one('SELECT * FROM categories WHERE slug = :slug', ['slug' => $slug]);
        return $row ? $this->map($row) : null;
    }

    public function findById(int $id): ?TermDto
    {
        $row = $this->db->one('SELECT * FROM categories WHERE id = :id', ['id' => $id]);
        return $row ? $this->map($row) : null;
    }

    public function save(string $name, string $slug, string $description, string $icon, ?int $id = null): int
    {
        if ($id) {
            $this->db->execute(
                'UPDATE categories SET name = :name, slug = :slug, description = :description, icon = :icon WHERE id = :id',
                compact('name', 'slug', 'description', 'icon', 'id')
            );
            return $id;
        }
        $this->db->execute(
            'INSERT INTO categories (name, slug, description, icon) VALUES (:name, :slug, :description, :icon)',
            compact('name', 'slug', 'description', 'icon')
        );
        return $this->db->lastId();
    }

    public function delete(int $id): void
    {
        $this->db->execute('DELETE FROM categories WHERE id = :id', ['id' => $id]);
    }

    public function count(): int
    {
        return (int) ($this->db->one('SELECT COUNT(*) AS c FROM categories')['c'] ?? 0);
    }

    private function map(array $r): TermDto
    {
        return new TermDto(
            (int) $r['id'],
            (string) $r['name'],
            (string) $r['slug'],
            (string) ($r['description'] ?? ''),
            (string) ($r['icon'] ?? '📚'),
            (int) ($r['cnt'] ?? 0),
            '/kategori/' . $r['slug'] . '/'
        );
    }
}
