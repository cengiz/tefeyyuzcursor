<?php
declare(strict_types=1);

namespace Tefeyyuz\Service;

use Tefeyyuz\Repository\CategoryRepository;
use Tefeyyuz\Repository\PageRepository;
use Tefeyyuz\Repository\TagRepository;
use Tefeyyuz\Support\Logger;

final class TaxonomyService
{
    public function __construct(
        private readonly CategoryRepository $categories,
        private readonly TagRepository $tags,
        private readonly PageRepository $pages,
        private readonly Logger $logger,
        private readonly int $perPage,
    ) {
    }

    public function categories(): array
    {
        try {
            return $this->categories->allWithCounts();
        } catch (\Throwable $e) {
            $this->logger->error('Kategoriler yüklenemedi', ['hata' => $e->getMessage()]);
            throw $e;
        }
    }

    public function tags(): array
    {
        try {
            return $this->tags->allWithCounts();
        } catch (\Throwable $e) {
            $this->logger->error('Etiketler yüklenemedi', ['hata' => $e->getMessage()]);
            throw $e;
        }
    }

    public function category(string $slug, int $page): ?array
    {
        try {
            $term = $this->categories->findBySlug($slug);
            if (!$term) {
                return null;
            }
            $total = $this->pages->countByCategory($term->id);
            $offset = ($page - 1) * $this->perPage;
            return [
                'term' => $term,
                'kind' => 'kategori',
                'items' => $this->pages->byCategory($term->id, $this->perPage, $offset),
                'total' => $total,
                'page' => $page,
                'pages' => max(1, (int) ceil($total / $this->perPage)),
                'perPage' => $this->perPage,
            ];
        } catch (\Throwable $e) {
            $this->logger->error('Kategori yüklenemedi', ['slug' => $slug, 'hata' => $e->getMessage()]);
            throw $e;
        }
    }

    public function tag(string $slug, int $page): ?array
    {
        try {
            $term = $this->tags->findBySlug($slug);
            if (!$term) {
                return null;
            }
            $total = $this->pages->countByTag($term->id);
            $offset = ($page - 1) * $this->perPage;
            return [
                'term' => $term,
                'kind' => 'etiket',
                'items' => $this->pages->byTag($term->id, $this->perPage, $offset),
                'total' => $total,
                'page' => $page,
                'pages' => max(1, (int) ceil($total / $this->perPage)),
                'perPage' => $this->perPage,
            ];
        } catch (\Throwable $e) {
            $this->logger->error('Etiket yüklenemedi', ['slug' => $slug, 'hata' => $e->getMessage()]);
            throw $e;
        }
    }
}
