<?php
declare(strict_types=1);

namespace Tefeyyuz\Controller;

use Tefeyyuz\Service\HomeService;
use Tefeyyuz\Service\PageService;
use Tefeyyuz\Service\SearchService;
use Tefeyyuz\Service\SeoService;
use Tefeyyuz\Service\SubmissionService;
use Tefeyyuz\Service\TaxonomyService;
use Tefeyyuz\Support\Logger;
use Tefeyyuz\Support\Session;
use Tefeyyuz\Support\View;

final class SiteController
{
    public function __construct(
        private readonly HomeService $home,
        private readonly PageService $pages,
        private readonly TaxonomyService $taxonomy,
        private readonly SearchService $search,
        private readonly SeoService $seo,
        private readonly SubmissionService $submissions,
        private readonly View $view,
        private readonly Session $session,
        private readonly Logger $logger,
        private readonly array $config,
    ) {
    }

    public function home(): string
    {
        $data = $this->home->data();
        return $this->view->render('home', $data + [
            'bodyClass' => 'page-home',
            'seo' => [
                'title' => $this->config['title'] . ' – ' . $this->config['slogan'],
                'description' => $this->config['description'],
                'canonical' => $this->view->url('/'),
                'jsonld' => $this->seo->jsonLdHome(),
            ],
            'sidebar' => $this->pages->sidebar(),
        ]);
    }

    public function articles(string $page = '1'): string
    {
        return $this->listing('article', 'Ansiklopedi', 'Tarihten bilime, dilden felsefeye uzanan, kaynaklarıyla hazırlanmış Türkçe ansiklopedi maddeleri.', '/ansiklopedi/', (int) $page);
    }

    public function questions(string $page = '1'): string
    {
        return $this->listing('question', 'Sorular ve Cevaplar', 'Okurlarımızın merak ettiği soruların, editörlerimiz tarafından kaynaklı ve anlaşılır biçimde hazırlanmış cevapları.', '/sorular/', (int) $page);
    }

    public function article(string $slug): string
    {
        return $this->show('article', $slug);
    }

    public function question(string $slug): string
    {
        return $this->show('question', $slug);
    }

    public function staticPage(string $slug): string
    {
        $item = $this->pages->staticPage($slug);
        if (!$item) {
            return $this->notFound();
        }
        $html = $item->html !== '' ? $item->html : (new \Tefeyyuz\Support\Markdown())->toHtml($item->bodyMd);
        return $this->view->render('static', [
            'bodyClass' => 'page-single page-static',
            'item' => $item,
            'html' => $html,
            'crumbs' => [['name' => 'Ana Sayfa', 'url' => '/'], ['name' => $item->title, 'url' => $item->url]],
            'seo' => [
                'title' => $item->title . ' – ' . $this->config['title'],
                'description' => $item->description,
                'canonical' => $this->view->url($item->url),
                'noindex' => $item->noindex,
            ],
        ]);
    }

    public function categories(): string
    {
        return $this->view->render('taxonomy', [
            'bodyClass' => 'layout-list page-taxonomy',
            'title' => 'Kategoriler',
            'description' => 'Tefeyyüz\'deki tüm ansiklopedi maddeleri ve soru-cevaplar konu başlıklarına göre.',
            'kind' => 'kategoriler',
            'terms' => $this->taxonomy->categories(),
            'crumbs' => [['name' => 'Ana Sayfa', 'url' => '/'], ['name' => 'Kategoriler', 'url' => '/kategoriler/']],
            'seo' => [
                'title' => 'Kategoriler – ' . $this->config['title'],
                'description' => 'Tefeyyüz\'deki tüm içerikler konu başlıklarına göre.',
                'canonical' => $this->view->url('/kategoriler/'),
            ],
        ]);
    }

    public function tags(): string
    {
        return $this->view->render('taxonomy', [
            'bodyClass' => 'layout-list page-taxonomy',
            'title' => 'Etiketler',
            'description' => 'İçerikleri anahtar kelimelere göre keşfedin.',
            'kind' => 'etiketler',
            'terms' => $this->taxonomy->tags(),
            'crumbs' => [['name' => 'Ana Sayfa', 'url' => '/'], ['name' => 'Etiketler', 'url' => '/etiketler/']],
            'seo' => [
                'title' => 'Etiketler – ' . $this->config['title'],
                'description' => 'İçerikleri anahtar kelimelere göre keşfedin.',
                'canonical' => $this->view->url('/etiketler/'),
            ],
        ]);
    }

    public function category(string $slug, string $page = '1'): string
    {
        $data = $this->taxonomy->category($slug, max(1, (int) $page));
        if (!$data) {
            return $this->notFound();
        }
        return $this->term($data);
    }

    public function tag(string $slug, string $page = '1'): string
    {
        $data = $this->taxonomy->tag($slug, max(1, (int) $page));
        if (!$data) {
            return $this->notFound();
        }
        return $this->term($data);
    }

    public function search(): string
    {
        $q = trim((string) ($_GET['q'] ?? ''));
        $data = $this->search->search($q);
        return $this->view->render('search', $data + [
            'bodyClass' => 'page-single page-arama',
            'includeSearchJs' => true,
            'crumbs' => [['name' => 'Ana Sayfa', 'url' => '/'], ['name' => 'Arama', 'url' => '/ara/']],
            'seo' => [
                'title' => 'Arama – ' . $this->config['title'],
                'description' => 'Ansiklopedi maddeleri ve soru-cevaplar içinde arayın.',
                'canonical' => $this->view->url('/ara/'),
                'noindex' => true,
            ],
        ]);
    }

    public function askGet(): string
    {
        $item = $this->pages->staticPage('soru-sor');
        return $this->view->render('ask', [
            'bodyClass' => 'page-single page-soru-sor',
            'item' => $item,
            'categories' => $this->taxonomy->categories(),
            'crumbs' => [['name' => 'Ana Sayfa', 'url' => '/'], ['name' => 'Soru Sor', 'url' => '/soru-sor/']],
            'seo' => [
                'title' => 'Soru Sor – ' . $this->config['title'],
                'description' => $item?->description ?? 'Merak ettiğiniz konuyu Tefeyyüz editörlerine gönderin.',
                'canonical' => $this->view->url('/soru-sor/'),
            ],
        ]);
    }

    public function askPost(): never
    {
        if (!$this->session->verifyCsrf($_POST['_csrf'] ?? null)) {
            $this->session->flash('error', 'Oturum doğrulaması başarısız. Tekrar deneyin.');
            header('Location: /soru-sor/', true, 302);
            exit;
        }
        try {
            $msg = $this->submissions->submit($_POST);
            $this->session->flash('ok', $msg);
        } catch (\InvalidArgumentException $e) {
            $this->session->flash('error', $e->getMessage());
        } catch (\Throwable $e) {
            $this->logger->error('Soru formu hatası', ['hata' => $e->getMessage()]);
            $this->session->flash('error', 'Gönderim sırasında bir sorun oluştu.');
        }
        header('Location: /soru-sor/', true, 302);
        exit;
    }

    public function sitemap(): never
    {
        header('Content-Type: application/xml; charset=UTF-8');
        echo $this->seo->sitemapXml();
        exit;
    }

    public function rss(): never
    {
        header('Content-Type: application/rss+xml; charset=UTF-8');
        echo $this->seo->rssXml();
        exit;
    }

    public function robots(): never
    {
        header('Content-Type: text/plain; charset=UTF-8');
        echo "User-agent: *\nAllow: /\nDisallow: /ara/\nDisallow: /admin/\n\nSitemap: " . $this->view->url('/sitemap.xml') . "\n";
        exit;
    }

    public function adsTxt(): never
    {
        header('Content-Type: text/plain; charset=UTF-8');
        echo (string) $this->config['adsense']['ads_txt'] . "\n";
        exit;
    }

    public function searchIndex(): never
    {
        header('Content-Type: application/json; charset=UTF-8');
        echo json_encode($this->search->jsonIndex(), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        exit;
    }

    public function notFound(): string
    {
        http_response_code(404);
        return $this->view->render('static', [
            'bodyClass' => 'page-single page-static',
            'item' => null,
            'title' => 'Sayfa bulunamadı',
            'html' => '<p>Aradığınız sayfa yok. <a href="/">Ana sayfaya</a> dönebilir veya <a href="/ara/">arama</a> yapabilirsiniz.</p>',
            'crumbs' => [['name' => 'Ana Sayfa', 'url' => '/'], ['name' => '404', 'url' => '/']],
            'seo' => [
                'title' => 'Sayfa bulunamadı – ' . $this->config['title'],
                'description' => 'Sayfa bulunamadı.',
                'canonical' => $this->view->url('/'),
                'noindex' => true,
            ],
        ]);
    }

    private function listing(string $type, string $title, string $desc, string $path, int $page): string
    {
        $page = max(1, $page);
        $data = $this->pages->listing($type, $page);
        $seoTitle = $title . ($page > 1 ? ' – Sayfa ' . $page : '') . ' – ' . $this->config['title'];
        return $this->view->render('list', $data + [
            'bodyClass' => 'layout-list section-' . ($type === 'question' ? 'sorular' : 'ansiklopedi'),
            'listTitle' => $title,
            'listDesc' => $desc,
            'listType' => $type,
            'basePath' => $path,
            'crumbs' => [['name' => 'Ana Sayfa', 'url' => '/'], ['name' => $title, 'url' => $path]],
            'seo' => [
                'title' => $seoTitle,
                'description' => $desc,
                'canonical' => $this->view->url($page > 1 ? $path . 'sayfa/' . $page . '/' : $path),
                'prev' => $page > 1 ? $this->view->url($page === 2 ? $path : $path . 'sayfa/' . ($page - 1) . '/') : null,
                'next' => $page < $data['pages'] ? $this->view->url($path . 'sayfa/' . ($page + 1) . '/') : null,
            ],
        ]);
    }

    private function show(string $type, string $slug): string
    {
        $data = $this->pages->show($type, $slug);
        if (!$data) {
            return $this->notFound();
        }
        /** @var \Tefeyyuz\Dto\PageDto $item */
        $item = $data['item'];
        $section = $type === 'question' ? ['name' => 'Sorular ve Cevaplar', 'url' => '/sorular/'] : ['name' => 'Ansiklopedi', 'url' => '/ansiklopedi/'];
        $crumbs = [['name' => 'Ana Sayfa', 'url' => '/'], $section, ['name' => $item->title, 'url' => $item->url]];
        $title = ($item->seoTitle ?: $item->title) . ' – ' . $this->config['title'];
        $desc = $item->description ?: $item->shortAnswer ?: $item->excerpt;
        return $this->view->render($type === 'question' ? 'question' : 'article', $data + [
            'bodyClass' => $type === 'question' ? 'page-single page-soru' : 'page-single page-makale',
            'inArticleAd' => $item->adsEnabled,
            'adsEnabledOnPage' => $item->adsEnabled,
            'crumbs' => $crumbs,
            'seo' => [
                'title' => $title,
                'description' => $desc,
                'canonical' => $this->view->url($item->url),
                'type' => 'article',
                'image' => $item->image,
                'published' => $item->publishedAt,
                'modified' => $item->updatedAt,
                'keywords' => $item->keywords,
                'jsonld' => $this->seo->jsonLdPage($item, $crumbs),
            ],
        ]);
    }

    private function term(array $data): string
    {
        $term = $data['term'];
        $kindLabel = $data['kind'] === 'kategori' ? 'Kategori' : 'Etiket';
        $path = $term->url;
        $page = $data['page'];
        return $this->view->render('term', $data + [
            'bodyClass' => 'layout-list page-term',
            'sidebar' => $this->pages->sidebar(),
            'basePath' => $path,
            'crumbs' => [
                ['name' => 'Ana Sayfa', 'url' => '/'],
                ['name' => $data['kind'] === 'kategori' ? 'Kategoriler' : 'Etiketler', 'url' => $data['kind'] === 'kategori' ? '/kategoriler/' : '/etiketler/'],
                ['name' => $term->name, 'url' => $path],
            ],
            'kindLabel' => $kindLabel,
            'seo' => [
                'title' => $term->name . ' ' . $kindLabel . 'si – ' . $this->config['title'],
                'description' => $term->description ?: $term->name,
                'canonical' => $this->view->url($page > 1 ? $path . 'sayfa/' . $page . '/' : $path),
            ],
        ]);
    }
}
