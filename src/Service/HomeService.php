<?php
declare(strict_types=1);

namespace Tefeyyuz\Service;

use Tefeyyuz\Repository\CategoryRepository;
use Tefeyyuz\Repository\PageRepository;
use Tefeyyuz\Support\Logger;

final class HomeService
{
    public function __construct(
        private readonly PageRepository $pages,
        private readonly CategoryRepository $categories,
        private readonly Logger $logger,
        private readonly array $config,
    ) {
    }

    /** @return array<string, mixed> */
    public function data(): array
    {
        try {
            return [
                'featured' => $this->pages->featured((int) $this->config['home_featured']),
                'articles' => $this->pages->published('article', (int) $this->config['home_articles']),
                'questions' => $this->pages->published('question', (int) $this->config['home_questions']),
                'categories' => $this->categories->allWithCounts(),
                'articleCount' => $this->pages->countPublished('article'),
                'questionCount' => $this->pages->countPublished('question'),
                'categoryCount' => $this->categories->count(),
            ];
        } catch (\Throwable $e) {
            $this->logger->error('Ana sayfa verisi alınamadı', ['hata' => $e->getMessage()]);
            throw $e;
        }
    }
}
