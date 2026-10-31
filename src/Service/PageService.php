<?php
declare(strict_types=1);

namespace Tefeyyuz\Service;

use Tefeyyuz\Dto\PageDto;
use Tefeyyuz\Repository\CategoryRepository;
use Tefeyyuz\Repository\PageRepository;
use Tefeyyuz\Support\Logger;

final class PageService
{
    public function __construct(
        private readonly PageRepository $pages,
        private readonly CategoryRepository $categories,
        private readonly Logger $logger,
        private readonly int $perPage,
    ) {
    }

    /** @return array<string, mixed> */
    public function listing(string $type, int $page): array
    {
        try {
            $total = $this->pages->countPublished($type);
            $offset = ($page - 1) * $this->perPage;
            return [
                'items' => $this->pages->published($type, $this->perPage, $offset),
                'total' => $total,
                'page' => $page,
                'perPage' => $this->perPage,
                'pages' => max(1, (int) ceil($total / $this->perPage)),
                'sidebar' => $this->sidebar(),
            ];
        } catch (\Throwable $e) {
            $this->logger->error('Liste yüklenemedi', ['type' => $type, 'hata' => $e->getMessage()]);
            throw $e;
        }
    }

    public function show(string $type, string $slug): ?array
    {
        try {
            $item = $this->pages->findPublished($type, $slug);
            if (!$item) {
                return null;
            }
            [$prev, $next] = $this->pages->neighbors($item);
            return [
                'item' => $item,
                'prev' => $prev,
                'next' => $next,
                'related' => $this->pages->related($item),
                'sidebar' => $this->sidebar($item),
            ];
        } catch (\Throwable $e) {
            $this->logger->error('Sayfa yüklenemedi', ['type' => $type, 'slug' => $slug, 'hata' => $e->getMessage()]);
            throw $e;
        }
    }

    public function staticPage(string $slug): ?PageDto
    {
        try {
            return $this->pages->findPublished('static', $slug);
        } catch (\Throwable $e) {
            $this->logger->error('Statik sayfa yüklenemedi', ['slug' => $slug, 'hata' => $e->getMessage()]);
            throw $e;
        }
    }

    /** @return array<string, mixed> */
    public function sidebar(?PageDto $current = null): array
    {
        return [
            'featured' => $this->pages->featured(5),
            'questions' => $this->pages->published('question', 5),
            'categories' => $this->categories->allWithCounts(),
            'toc' => $current && $current->tocEnabled ? $current->tocHtml : '',
        ];
    }
}
