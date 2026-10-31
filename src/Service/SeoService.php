<?php
declare(strict_types=1);

namespace Tefeyyuz\Service;

use Tefeyyuz\Dto\PageDto;
use Tefeyyuz\Repository\CategoryRepository;
use Tefeyyuz\Repository\PageRepository;
use Tefeyyuz\Repository\TagRepository;
use Tefeyyuz\Support\Logger;
use Tefeyyuz\Support\View;

final class SeoService
{
    public function __construct(
        private readonly PageRepository $pages,
        private readonly CategoryRepository $categories,
        private readonly TagRepository $tags,
        private readonly View $view,
        private readonly Logger $logger,
        private readonly array $config,
    ) {
    }

    public function sitemapXml(): string
    {
        try {
            $urls = [
                ['loc' => $this->view->url('/'), 'prio' => '1.0'],
                ['loc' => $this->view->url('/ansiklopedi/'), 'prio' => '0.8'],
                ['loc' => $this->view->url('/sorular/'), 'prio' => '0.8'],
                ['loc' => $this->view->url('/kategoriler/'), 'prio' => '0.6'],
                ['loc' => $this->view->url('/etiketler/'), 'prio' => '0.4'],
            ];
            foreach ($this->pages->sitemapRows() as $row) {
                $path = match ($row['type']) {
                    'article' => '/ansiklopedi/' . $row['slug'] . '/',
                    'question' => '/soru/' . $row['slug'] . '/',
                    default => '/' . $row['slug'] . '/',
                };
                $urls[] = ['loc' => $this->view->url($path), 'last' => substr((string) $row['updated_at'], 0, 10), 'prio' => '0.7'];
            }
            foreach ($this->categories->allWithCounts() as $c) {
                $urls[] = ['loc' => $this->view->url($c->url), 'prio' => '0.5'];
            }
            foreach ($this->tags->allWithCounts() as $t) {
                $urls[] = ['loc' => $this->view->url($t->url), 'prio' => '0.3'];
            }
            $xml = '<?xml version="1.0" encoding="UTF-8"?><urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';
            foreach ($urls as $u) {
                $xml .= '<url><loc>' . htmlspecialchars($u['loc'], ENT_XML1) . '</loc>';
                if (!empty($u['last'])) {
                    $xml .= '<lastmod>' . $u['last'] . '</lastmod>';
                }
                $xml .= '<changefreq>weekly</changefreq><priority>' . $u['prio'] . '</priority></url>';
            }
            return $xml . '</urlset>';
        } catch (\Throwable $e) {
            $this->logger->error('Sitemap üretilemedi', ['hata' => $e->getMessage()]);
            throw $e;
        }
    }

    public function rssXml(): string
    {
        try {
            $items = $this->pages->rss(50);
            $xml = '<?xml version="1.0" encoding="UTF-8"?><rss version="2.0"><channel>';
            $xml .= '<title>' . $this->esc($this->config['title']) . '</title>';
            $xml .= '<link>' . $this->esc($this->view->url('/')) . '</link>';
            $xml .= '<description>' . $this->esc($this->config['description']) . '</description>';
            $xml .= '<language>tr</language>';
            foreach ($items as $item) {
                $xml .= '<item><title>' . $this->esc($item->title) . '</title>';
                $xml .= '<link>' . $this->esc($this->view->url($item->url)) . '</link>';
                $xml .= '<guid>' . $this->esc($this->view->url($item->url)) . '</guid>';
                $xml .= '<pubDate>' . date(DATE_RSS, strtotime($item->publishedAt) ?: time()) . '</pubDate>';
                $xml .= '<description>' . $this->esc($item->description ?: $item->excerpt) . '</description></item>';
            }
            return $xml . '</channel></rss>';
        } catch (\Throwable $e) {
            $this->logger->error('RSS üretilemedi', ['hata' => $e->getMessage()]);
            throw $e;
        }
    }

    public function jsonLdHome(): array
    {
        $org = $this->org();
        return [
            '@context' => 'https://schema.org',
            '@graph' => [
                [
                    '@type' => 'WebSite',
                    '@id' => $this->view->url('/') . '#website',
                    'url' => $this->view->url('/'),
                    'name' => $this->config['title'],
                    'description' => $this->config['description'],
                    'inLanguage' => 'tr-TR',
                    'publisher' => $org,
                    'potentialAction' => [
                        '@type' => 'SearchAction',
                        'target' => ['@type' => 'EntryPoint', 'urlTemplate' => $this->view->url('/ara/') . '?q={search_term_string}'],
                        'query-input' => 'required name=search_term_string',
                    ],
                ],
                $org + ['@id' => $this->view->url('/') . '#organization'],
            ],
        ];
    }

    public function jsonLdPage(PageDto $page, array $crumbs): array
    {
        $graph = [$this->breadcrumb($crumbs)];
        if ($page->type === 'article') {
            $graph[] = [
                '@type' => 'Article',
                'headline' => $page->title,
                'description' => $page->description,
                'datePublished' => $page->publishedAt,
                'dateModified' => $page->updatedAt,
                'inLanguage' => 'tr-TR',
                'author' => ['@type' => 'Organization', 'name' => $page->author ?: $this->config['author']],
                'publisher' => $this->org(),
                'mainEntityOfPage' => $this->view->url($page->url),
                'image' => [$this->view->url($page->image ?: $this->config['default_image'])],
            ];
        }
        if ($page->type === 'question') {
            $graph[] = [
                '@type' => 'QAPage',
                'mainEntity' => [
                    '@type' => 'Question',
                    'name' => $page->title,
                    'text' => $page->questionText ?: $page->title,
                    'answerCount' => 1,
                    'acceptedAnswer' => [
                        '@type' => 'Answer',
                        'text' => mb_substr(strip_tags($page->html ?: $page->bodyMd), 0, 1500),
                        'url' => $this->view->url($page->url) . '#cevap',
                    ],
                ],
            ];
        }
        if ($page->faq !== []) {
            $faq = [];
            foreach ($page->faq as $item) {
                $faq[] = [
                    '@type' => 'Question',
                    'name' => $item['soru'] ?? '',
                    'acceptedAnswer' => ['@type' => 'Answer', 'text' => $item['cevap'] ?? ''],
                ];
            }
            $graph[] = ['@type' => 'FAQPage', 'mainEntity' => $faq];
        }
        return ['@context' => 'https://schema.org', '@graph' => $graph];
    }

    private function org(): array
    {
        return [
            '@type' => 'Organization',
            'name' => $this->config['title'],
            'url' => $this->view->url('/'),
            'logo' => ['@type' => 'ImageObject', 'url' => $this->view->url('/images/logo-512.png')],
        ];
    }

    /** @param list<array{name:string,url:string}> $crumbs */
    private function breadcrumb(array $crumbs): array
    {
        $items = [];
        $i = 1;
        foreach ($crumbs as $c) {
            $items[] = ['@type' => 'ListItem', 'position' => $i++, 'name' => $c['name'], 'item' => $this->view->url($c['url'])];
        }
        return ['@type' => 'BreadcrumbList', 'itemListElement' => $items];
    }

    private function esc(string $s): string
    {
        return htmlspecialchars($s, ENT_XML1 | ENT_QUOTES, 'UTF-8');
    }
}
