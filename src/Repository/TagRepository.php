<?php
declare(strict_types=1);

namespace Tefeyyuz\Repository;

use Tefeyyuz\Dto\TermDto;
use Tefeyyuz\Support\Database;
use Tefeyyuz\Support\Slug;

final class TagRepository
{
    public function __construct(private readonly Database $db)
    {
    }

    /** @return list<TermDto> */
    public function allWithCounts(): array
    {
        $rows = $this->db->all(
            "SELECT t.*, COUNT(p.id) AS cnt
             FROM tags t
             LEFT JOIN page_tags pt ON pt.tag_id = t.id
             LEFT JOIN pages p ON p.id = pt.page_id AND p.status = 'published'
             GROUP BY t.id
             ORDER BY t.name COLLATE NOCASE"
        );
        return array_map(fn (array $r) => $this->map($r), $rows);
    }

    public function findBySlug(string $slug): ?TermDto
    {
        $row = $this->db->one('SELECT * FROM tags WHERE slug = :slug', ['slug' => $slug]);
        return $row ? $this->map($row) : null;
    }

    public function findOrCreate(string $name): int
    {
        $slug = Slug::make($name);
        $row = $this->db->one('SELECT id FROM tags WHERE slug = :slug', ['slug' => $slug]);
        if ($row) {
            return (int) $row['id'];
        }
        $this->db->execute('INSERT INTO tags (name, slug) VALUES (:name, :slug)', ['name' => $name, 'slug' => $slug]);
        return $this->db->lastId();
    }

    private function map(array $r): TermDto
    {
        return new TermDto(
            (int) $r['id'],
            (string) $r['name'],
            (string) $r['slug'],
            '',
            '',
            (int) ($r['cnt'] ?? 0),
            '/etiket/' . $r['slug'] . '/'
        );
    }
}
