<?php
declare(strict_types=1);

namespace Tefeyyuz\Repository;

use Tefeyyuz\Dto\PageDto;
use Tefeyyuz\Dto\TermDto;
use Tefeyyuz\Support\Database;
use Tefeyyuz\Support\Markdown;
use Tefeyyuz\Support\View;

final class PageRepository
{
    public function __construct(
        private readonly Database $db,
        private readonly Markdown $markdown,
        private readonly View $view,
    ) {
    }

    /** @return list<PageDto> */
    public function published(string $type, int $limit = 50, int $offset = 0): array
    {
        $rows = $this->db->all(
            'SELECT * FROM pages WHERE type = :type AND status = :status ORDER BY published_at DESC LIMIT :limit OFFSET :offset',
            ['type' => $type, 'status' => 'published', 'limit' => $limit, 'offset' => $offset]
        );
        return array_map(fn (array $r) => $this->hydrate($r), $rows);
    }

    public function countPublished(string $type): int
    {
        $row = $this->db->one(
            'SELECT COUNT(*) AS c FROM pages WHERE type = :type AND status = :status',
            ['type' => $type, 'status' => 'published']
        );
        return (int) ($row['c'] ?? 0);
    }

    public function countPublishedAll(): int
    {
        $row = $this->db->one(
            "SELECT COUNT(*) AS c FROM pages WHERE status = :status AND type IN ('article','question')",
            ['status' => 'published']
        );
        return (int) ($row['c'] ?? 0);
    }

    /** @return list<PageDto> */
    public function featured(int $limit): array
    {
        $rows = $this->db->all(
            "SELECT * FROM pages WHERE featured = 1 AND status = :status AND type IN ('article','question') ORDER BY published_at DESC LIMIT :limit",
            ['status' => 'published', 'limit' => $limit]
        );
        return array_map(fn (array $r) => $this->hydrate($r), $rows);
    }

    public function findPublished(string $type, string $slug): ?PageDto
    {
        $row = $this->db->one(
            'SELECT * FROM pages WHERE type = :type AND slug = :slug AND status = :status',
            ['type' => $type, 'slug' => $slug, 'status' => 'published']
        );
        return $row ? $this->hydrate($row, true) : null;
    }

    public function findById(int $id): ?PageDto
    {
        $row = $this->db->one('SELECT * FROM pages WHERE id = :id', ['id' => $id]);
        return $row ? $this->hydrate($row) : null;
    }

    /** @return list<PageDto> */
    public function allAdmin(?string $type = null): array
    {
        if ($type) {
            $rows = $this->db->all('SELECT * FROM pages WHERE type = :type ORDER BY updated_at DESC', ['type' => $type]);
        } else {
            $rows = $this->db->all('SELECT * FROM pages ORDER BY updated_at DESC');
        }
        return array_map(fn (array $r) => $this->hydrate($r), $rows);
    }

    public function neighbors(PageDto $page): array
    {
        $prev = $this->db->one(
            'SELECT * FROM pages WHERE type = :type AND status = :status AND published_at < :dt ORDER BY published_at DESC LIMIT 1',
            ['type' => $page->type, 'status' => 'published', 'dt' => $page->publishedAt]
        );
        $next = $this->db->one(
            'SELECT * FROM pages WHERE type = :type AND status = :status AND published_at > :dt ORDER BY published_at ASC LIMIT 1',
            ['type' => $page->type, 'status' => 'published', 'dt' => $page->publishedAt]
        );
        return [
            $prev ? $this->hydrate($prev) : null,
            $next ? $this->hydrate($next) : null,
        ];
    }

    /** @return list<PageDto> */
    public function related(PageDto $page, int $limit = 6): array
    {
        $tagIds = array_map(fn (TermDto $t) => $t->id, $page->tags);
        $catIds = array_map(fn (TermDto $t) => $t->id, $page->categories);
        if ($tagIds === [] && $catIds === []) {
            return [];
        }
        $parts = [];
        $params = ['id' => $page->id, 'status' => 'published', 'limit' => $limit];
        if ($tagIds !== []) {
            $ph = [];
            foreach ($tagIds as $i => $tid) {
                $key = 't' . $i;
                $ph[] = ':' . $key;
                $params[$key] = $tid;
            }
            $parts[] = "id IN (SELECT page_id FROM page_tags WHERE tag_id IN (" . implode(',', $ph) . "))";
        }
        if ($catIds !== []) {
            $ph = [];
            foreach ($catIds as $i => $cid) {
                $key = 'c' . $i;
                $ph[] = ':' . $key;
                $params[$key] = $cid;
            }
            $parts[] = "id IN (SELECT page_id FROM page_categories WHERE category_id IN (" . implode(',', $ph) . "))";
        }
        $sql = 'SELECT * FROM pages WHERE id != :id AND status = :status AND (' . implode(' OR ', $parts) . ') ORDER BY published_at DESC LIMIT :limit';
        $rows = $this->db->all($sql, $params);
        return array_map(fn (array $r) => $this->hydrate($r), $rows);
    }

    /** @return list<PageDto> */
    public function byCategory(int $categoryId, int $limit, int $offset): array
    {
        $rows = $this->db->all(
            'SELECT p.* FROM pages p INNER JOIN page_categories pc ON pc.page_id = p.id
             WHERE pc.category_id = :cid AND p.status = :status
             ORDER BY p.published_at DESC LIMIT :limit OFFSET :offset',
            ['cid' => $categoryId, 'status' => 'published', 'limit' => $limit, 'offset' => $offset]
        );
        return array_map(fn (array $r) => $this->hydrate($r), $rows);
    }

    public function countByCategory(int $categoryId): int
    {
        $row = $this->db->one(
            'SELECT COUNT(*) AS c FROM pages p INNER JOIN page_categories pc ON pc.page_id = p.id WHERE pc.category_id = :cid AND p.status = :status',
            ['cid' => $categoryId, 'status' => 'published']
        );
        return (int) ($row['c'] ?? 0);
    }

    /** @return list<PageDto> */
    public function byTag(int $tagId, int $limit, int $offset): array
    {
        $rows = $this->db->all(
            'SELECT p.* FROM pages p INNER JOIN page_tags pt ON pt.page_id = p.id
             WHERE pt.tag_id = :tid AND p.status = :status
             ORDER BY p.published_at DESC LIMIT :limit OFFSET :offset',
            ['tid' => $tagId, 'status' => 'published', 'limit' => $limit, 'offset' => $offset]
        );
        return array_map(fn (array $r) => $this->hydrate($r), $rows);
    }

    public function countByTag(int $tagId): int
    {
        $row = $this->db->one(
            'SELECT COUNT(*) AS c FROM pages p INNER JOIN page_tags pt ON pt.page_id = p.id WHERE pt.tag_id = :tid AND p.status = :status',
            ['tid' => $tagId, 'status' => 'published']
        );
        return (int) ($row['c'] ?? 0);
    }

    /** @return list<PageDto> */
    public function search(string $query, int $limit = 50): array
    {
        $rows = $this->db->all(
            'SELECT p.* FROM pages p
             WHERE p.id IN (SELECT rowid FROM pages_fts WHERE pages_fts MATCH :q)
               AND p.status = :status
             ORDER BY p.published_at DESC LIMIT :limit',
            ['q' => $query, 'status' => 'published', 'limit' => $limit]
        );
        return array_map(fn (array $r) => $this->hydrate($r), $rows);
    }

    /** @return list<PageDto> */
    public function searchLike(string $needle, int $limit = 50): array
    {
        $like = '%' . $needle . '%';
        $rows = $this->db->all(
            "SELECT * FROM pages WHERE status = :status AND type IN ('article','question')
             AND (title LIKE :q OR excerpt LIKE :q2 OR body_md LIKE :q3 OR short_answer LIKE :q4)
             ORDER BY published_at DESC LIMIT :limit",
            ['status' => 'published', 'q' => $like, 'q2' => $like, 'q3' => $like, 'q4' => $like, 'limit' => $limit]
        );
        return array_map(fn (array $r) => $this->hydrate($r), $rows);
    }

    /** @return list<array<string, mixed>> */
    public function searchIndex(): array
    {
        return $this->db->all(
            "SELECT slug, type, title, excerpt, short_answer, description, body_md, published_at FROM pages WHERE status = :status AND type IN ('article','question')",
            ['status' => 'published']
        );
    }

    public function sitemapRows(): array
    {
        return $this->db->all(
            "SELECT type, slug, updated_at FROM pages WHERE status = :status AND noindex = 0 ORDER BY updated_at DESC",
            ['status' => 'published']
        );
    }

    public function rss(int $limit = 50): array
    {
        $rows = $this->db->all(
            "SELECT * FROM pages WHERE status = :status AND type IN ('article','question') ORDER BY published_at DESC LIMIT :limit",
            ['status' => 'published', 'limit' => $limit]
        );
        return array_map(fn (array $r) => $this->hydrate($r), $rows);
    }

    /** @param array<string, mixed> $data */
    public function save(array $data, array $categoryIds, array $tagIds, ?int $id = null): int
    {
        $fields = [
            'type', 'slug', 'title', 'seo_title', 'description', 'body_md', 'excerpt', 'ozet',
            'author', 'asker', 'question_text', 'short_answer', 'featured', 'ads_enabled',
            'toc_enabled', 'noindex', 'show_date', 'image', 'image_alt', 'image_caption',
            'votes', 'keywords_json', 'sources_json', 'faq_json', 'status', 'published_at', 'updated_at',
        ];
        if ($id) {
            $sets = [];
            $params = ['id' => $id];
            foreach ($fields as $f) {
                $sets[] = "$f = :$f";
                $params[$f] = $data[$f];
            }
            $this->db->execute('UPDATE pages SET ' . implode(', ', $sets) . ' WHERE id = :id', $params);
        } else {
            $placeholders = implode(', ', array_map(fn ($f) => ':' . $f, $fields));
            $params = [];
            foreach ($fields as $f) {
                $params[$f] = $data[$f];
            }
            $this->db->execute('INSERT INTO pages (' . implode(', ', $fields) . ') VALUES (' . $placeholders . ')', $params);
            $id = $this->db->lastId();
        }
        $this->db->execute('DELETE FROM page_categories WHERE page_id = :id', ['id' => $id]);
        $this->db->execute('DELETE FROM page_tags WHERE page_id = :id', ['id' => $id]);
        foreach ($categoryIds as $cid) {
            $this->db->execute('INSERT INTO page_categories (page_id, category_id) VALUES (:p, :c)', ['p' => $id, 'c' => (int) $cid]);
        }
        foreach ($tagIds as $tid) {
            $this->db->execute('INSERT INTO page_tags (page_id, tag_id) VALUES (:p, :t)', ['p' => $id, 't' => (int) $tid]);
        }
        $this->reindex((int) $id);
        return (int) $id;
    }

    public function delete(int $id): void
    {
        $this->db->execute('DELETE FROM pages_fts WHERE rowid = :id', ['id' => $id]);
        $this->db->execute('DELETE FROM pages WHERE id = :id', ['id' => $id]);
    }

    public function reindex(int $id): void
    {
        $row = $this->db->one('SELECT id, title, body_md, excerpt, short_answer, description FROM pages WHERE id = :id', ['id' => $id]);
        if (!$row) {
            return;
        }
        $extra = trim(($row['excerpt'] ?? '') . ' ' . ($row['short_answer'] ?? '') . ' ' . ($row['description'] ?? ''));
        $this->db->execute('DELETE FROM pages_fts WHERE rowid = :id', ['id' => $id]);
        $this->db->execute(
            'INSERT INTO pages_fts(rowid, title, body, extra) VALUES (:id, :title, :body, :extra)',
            ['id' => $id, 'title' => $row['title'], 'body' => $row['body_md'], 'extra' => $extra]
        );
    }

    private function hydrate(array $row, bool $full = false): PageDto
    {
        $html = $full ? $this->markdown->toHtml((string) $row['body_md']) : '';
        $toc = ($full && $html !== '') ? $this->markdown->toc($html) : '';
        $cats = $this->termsFor((int) $row['id'], 'category');
        $tags = $this->termsFor((int) $row['id'], 'tag');
        $type = (string) $row['type'];
        $slug = (string) $row['slug'];
        $url = match ($type) {
            'article' => '/ansiklopedi/' . $slug . '/',
            'question' => '/soru/' . $slug . '/',
            default => '/' . $slug . '/',
        };
        $excerpt = (string) $row['excerpt'];
        if ($excerpt === '') {
            $excerpt = (string) ($row['short_answer'] ?: $row['description']);
        }
        return new PageDto(
            id: (int) $row['id'],
            type: $type,
            slug: $slug,
            title: (string) $row['title'],
            seoTitle: (string) $row['seo_title'],
            description: (string) $row['description'],
            bodyMd: (string) $row['body_md'],
            html: $html,
            excerpt: $excerpt,
            ozet: (string) $row['ozet'],
            author: (string) $row['author'],
            asker: (string) $row['asker'],
            questionText: (string) $row['question_text'],
            shortAnswer: (string) $row['short_answer'],
            featured: (int) $row['featured'] === 1,
            adsEnabled: (int) $row['ads_enabled'] === 1,
            tocEnabled: (int) $row['toc_enabled'] === 1,
            noindex: (int) $row['noindex'] === 1,
            showDate: (int) $row['show_date'] === 1,
            image: (string) $row['image'],
            imageAlt: (string) $row['image_alt'],
            imageCaption: (string) $row['image_caption'],
            votes: (int) $row['votes'],
            keywords: json_decode((string) $row['keywords_json'], true) ?: [],
            sources: json_decode((string) $row['sources_json'], true) ?: [],
            faq: json_decode((string) $row['faq_json'], true) ?: [],
            status: (string) $row['status'],
            publishedAt: (string) $row['published_at'],
            updatedAt: (string) $row['updated_at'],
            url: $url,
            readingTime: $this->view->readingTime((string) $row['body_md']),
            tocHtml: $toc,
            categories: $cats,
            tags: $tags,
        );
    }

    /** @return list<TermDto> */
    private function termsFor(int $pageId, string $kind): array
    {
        if ($kind === 'category') {
            $rows = $this->db->all(
                'SELECT c.* FROM categories c INNER JOIN page_categories pc ON pc.category_id = c.id WHERE pc.page_id = :id ORDER BY c.name',
                ['id' => $pageId]
            );
            return array_map(fn (array $r) => new TermDto(
                (int) $r['id'], (string) $r['name'], (string) $r['slug'], (string) $r['description'], (string) $r['icon'], 0, '/kategori/' . $r['slug'] . '/'
            ), $rows);
        }
        $rows = $this->db->all(
            'SELECT t.* FROM tags t INNER JOIN page_tags pt ON pt.tag_id = t.id WHERE pt.page_id = :id ORDER BY t.name',
            ['id' => $pageId]
        );
        return array_map(fn (array $r) => new TermDto(
            (int) $r['id'], (string) $r['name'], (string) $r['slug'], '', '', 0, '/etiket/' . $r['slug'] . '/'
        ), $rows);
    }
}
