<?php
declare(strict_types=1);

namespace Tefeyyuz\Service;

use Tefeyyuz\Dto\PageDto;
use Tefeyyuz\Repository\CategoryRepository;
use Tefeyyuz\Repository\PageRepository;
use Tefeyyuz\Support\Logger;
use Tefeyyuz\Support\Slug;

final class SearchService
{
    public function __construct(
        private readonly PageRepository $pages,
        private readonly CategoryRepository $categories,
        private readonly Logger $logger,
    ) {
    }

    /** @return array{query:string,results:list<PageDto>,categories:array} */
    public function search(string $query): array
    {
        try {
            $query = trim($query);
            $results = [];
            if (mb_strlen($query) >= 2) {
                $folded = Slug::fold($query);
                $fts = '"' . str_replace('"', '""', $query) . '"';
                try {
                    $results = $this->pages->search($fts);
                } catch (\Throwable $e) {
                    $this->logger->warning('FTS araması başarısız, LIKE kullanılıyor', ['hata' => $e->getMessage()]);
                    $results = [];
                }
                if ($results === []) {
                    $results = $this->pages->searchLike($query);
                }
                if ($folded !== $query && $results === []) {
                    $results = $this->pages->searchLike($folded);
                }
            }
            return [
                'query' => $query,
                'results' => $results,
                'categories' => $this->categories->allWithCounts(),
            ];
        } catch (\Throwable $e) {
            $this->logger->error('Arama başarısız', ['q' => $query, 'hata' => $e->getMessage()]);
            throw $e;
        }
    }

    public function jsonIndex(): array
    {
        try {
            $out = [];
            foreach ($this->pages->searchIndex() as $row) {
                $url = $row['type'] === 'question'
                    ? '/soru/' . $row['slug'] . '/'
                    : '/ansiklopedi/' . $row['slug'] . '/';
                $out[] = [
                    't' => $row['title'],
                    'u' => $url,
                    'k' => $row['type'] === 'question' ? 'soru' : 'madde',
                    'd' => mb_substr((string) ($row['short_answer'] ?: $row['description'] ?: $row['excerpt']), 0, 180),
                    'c' => mb_substr(strip_tags((string) $row['body_md']), 0, 2500),
                    'kat' => [],
                    'et' => [],
                    'dt' => substr((string) $row['published_at'], 0, 10),
                ];
            }
            return $out;
        } catch (\Throwable $e) {
            $this->logger->error('Arama indeksi üretilemedi', ['hata' => $e->getMessage()]);
            throw $e;
        }
    }
}
