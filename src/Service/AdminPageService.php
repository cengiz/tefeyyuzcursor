<?php
declare(strict_types=1);

namespace Tefeyyuz\Service;

use Tefeyyuz\Repository\PageRepository;
use Tefeyyuz\Repository\TagRepository;
use Tefeyyuz\Support\Logger;
use Tefeyyuz\Support\Slug;

final class AdminPageService
{
    public function __construct(
        private readonly PageRepository $pages,
        private readonly TagRepository $tags,
        private readonly Logger $logger,
        private readonly array $config,
    ) {
    }

    public function list(?string $type = null): array
    {
        try {
            return $this->pages->allAdmin($type);
        } catch (\Throwable $e) {
            $this->logger->error('Yönetim listesi yüklenemedi', ['hata' => $e->getMessage()]);
            throw $e;
        }
    }

    public function get(int $id)
    {
        try {
            return $this->pages->findById($id);
        } catch (\Throwable $e) {
            $this->logger->error('Yönetim sayfası yüklenemedi', ['id' => $id, 'hata' => $e->getMessage()]);
            throw $e;
        }
    }

    /** @param array<string, mixed> $post */
    public function save(array $post, ?int $id = null): int
    {
        try {
            $type = (string) ($post['type'] ?? 'article');
            $slug = Slug::make((string) ($post['slug'] ?: $post['title'] ?? ''));
            $tagNames = array_filter(array_map('trim', explode(',', (string) ($post['tags'] ?? ''))));
            $tagIds = [];
            foreach ($tagNames as $name) {
                $tagIds[] = $this->tags->findOrCreate($name);
            }
            $sources = array_values(array_filter(array_map('trim', preg_split('/\r\n|\n/', (string) ($post['sources'] ?? '')) ?: [])));
            $now = date('c');
            $data = [
                'type' => $type,
                'slug' => $slug,
                'title' => trim((string) ($post['title'] ?? '')),
                'seo_title' => trim((string) ($post['seo_title'] ?? '')),
                'description' => trim((string) ($post['description'] ?? '')),
                'body_md' => (string) ($post['body_md'] ?? ''),
                'excerpt' => trim((string) ($post['excerpt'] ?? '')),
                'ozet' => trim((string) ($post['ozet'] ?? '')),
                'author' => trim((string) ($post['author'] ?? $this->config['author'])),
                'asker' => trim((string) ($post['asker'] ?? '')),
                'question_text' => trim((string) ($post['question_text'] ?? '')),
                'short_answer' => trim((string) ($post['short_answer'] ?? '')),
                'featured' => empty($post['featured']) ? 0 : 1,
                'ads_enabled' => empty($post['ads_enabled']) ? 0 : 1,
                'toc_enabled' => empty($post['toc_enabled']) ? 0 : 1,
                'noindex' => empty($post['noindex']) ? 0 : 1,
                'show_date' => empty($post['show_date']) ? 0 : 1,
                'image' => trim((string) ($post['image'] ?? '')),
                'image_alt' => trim((string) ($post['image_alt'] ?? '')),
                'image_caption' => trim((string) ($post['image_caption'] ?? '')),
                'votes' => (int) ($post['votes'] ?? 0),
                'keywords_json' => json_encode(array_filter(array_map('trim', explode(',', (string) ($post['keywords'] ?? '')))), JSON_UNESCAPED_UNICODE),
                'sources_json' => json_encode($sources, JSON_UNESCAPED_UNICODE),
                'faq_json' => (string) ($post['faq_json'] ?? '[]'),
                'status' => (string) ($post['status'] ?? 'published'),
                'published_at' => trim((string) ($post['published_at'] ?? '')) ?: $now,
                'updated_at' => $now,
            ];
            $catIds = array_map('intval', (array) ($post['category_ids'] ?? []));
            $saved = $this->pages->save($data, $catIds, $tagIds, $id);
            $this->logger->info('İçerik kaydedildi', ['id' => $saved, 'type' => $type]);
            return $saved;
        } catch (\Throwable $e) {
            $this->logger->error('İçerik kaydedilemedi', ['hata' => $e->getMessage()]);
            throw $e;
        }
    }

    public function delete(int $id): void
    {
        try {
            $this->pages->delete($id);
            $this->logger->info('İçerik silindi', ['id' => $id]);
        } catch (\Throwable $e) {
            $this->logger->error('İçerik silinemedi', ['id' => $id, 'hata' => $e->getMessage()]);
            throw $e;
        }
    }
}
