<?php
declare(strict_types=1);

use Tefeyyuz\Controller\AdminController;
use Tefeyyuz\Controller\SiteController;
use Tefeyyuz\Repository\CategoryRepository;
use Tefeyyuz\Repository\PageRepository;
use Tefeyyuz\Repository\SubmissionRepository;
use Tefeyyuz\Repository\TagRepository;
use Tefeyyuz\Repository\UserRepository;
use Tefeyyuz\Service\AdminPageService;
use Tefeyyuz\Service\AuthService;
use Tefeyyuz\Service\HomeService;
use Tefeyyuz\Service\PageService;
use Tefeyyuz\Service\SearchService;
use Tefeyyuz\Service\SeoService;
use Tefeyyuz\Service\SubmissionService;
use Tefeyyuz\Service\TaxonomyService;
use Tefeyyuz\Support\Database;
use Tefeyyuz\Support\Logger;
use Tefeyyuz\Support\Markdown;
use Tefeyyuz\Support\Router;
use Tefeyyuz\Support\Session;
use Tefeyyuz\Support\View;

$config = require dirname(__DIR__) . '/config/app.php';

date_default_timezone_set((string) $config['timezone']);
mb_internal_encoding('UTF-8');

spl_autoload_register(static function (string $class): void {
    $prefix = 'Tefeyyuz\\';
    if (!str_starts_with($class, $prefix)) {
        return;
    }
    $rel = str_replace('\\', '/', substr($class, strlen($prefix)));
    $file = dirname(__DIR__) . '/src/' . $rel . '.php';
    if (is_file($file)) {
        require $file;
    }
});

$parsedown = dirname(__DIR__) . '/vendor/Parsedown.php';
if (!is_file($parsedown)) {
    throw new RuntimeException('Parsedown bulunamadı: vendor/Parsedown.php');
}
require $parsedown;

$logger = new Logger((string) $config['log_path']);

set_exception_handler(static function (Throwable $e) use ($logger, $config): void {
    $logger->error('Yakalanmamış hata', [
        'hata' => $e->getMessage(),
        'dosya' => $e->getFile(),
        'satir' => $e->getLine(),
    ]);
    http_response_code(500);
    header('Content-Type: text/html; charset=UTF-8');
    if ($config['debug']) {
        echo '<pre>' . htmlspecialchars($e->getMessage() . "\n" . $e->getTraceAsString(), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') . '</pre>';
    } else {
        echo '<!DOCTYPE html><html lang="tr"><head><meta charset="utf-8"><title>Hata</title></head><body><p>Bir sorun oluştu. Lütfen daha sonra tekrar deneyin.</p></body></html>';
    }
    exit;
});

$session = new Session((string) $config['session_name']);
$view = new View(dirname(__DIR__) . '/templates', $config, $session);
$db = new Database((string) $config['sqlite_path'], $logger);

$schemaOk = $db->one("SELECT name FROM sqlite_master WHERE type = :type AND name = :name", [
    'type' => 'table',
    'name' => 'pages',
]);
if (!$schemaOk) {
    $sql = file_get_contents(dirname(__DIR__) . '/database/schema.sql');
    if ($sql === false) {
        throw new RuntimeException('Şema dosyası okunamadı.');
    }
    $db->pdo()->exec($sql);
}

$markdown = new Markdown();
$pages = new PageRepository($db, $markdown, $view);
$categories = new CategoryRepository($db);
$tags = new TagRepository($db);
$users = new UserRepository($db);
$submissionRepo = new SubmissionRepository($db);

$view->setGlobals(['footerCategories' => $categories->allWithCounts()]);

$perPage = (int) $config['per_page'];
$home = new HomeService($pages, $categories, $logger, $config);
$pageService = new PageService($pages, $categories, $logger, $perPage);
$taxonomy = new TaxonomyService($categories, $tags, $pages, $logger, $perPage);
$search = new SearchService($pages, $categories, $logger);
$submissions = new SubmissionService($submissionRepo, $logger);
$auth = new AuthService($users, $session, $logger);
$adminPages = new AdminPageService($pages, $tags, $logger, $config);
$seo = new SeoService($pages, $categories, $tags, $view, $logger, $config);

$site = new SiteController($home, $pageService, $taxonomy, $search, $seo, $submissions, $view, $session, $logger, $config);
$admin = new AdminController($auth, $adminPages, $categories, $pages, $submissions, $submissionRepo, $view, $session, $logger);

$router = new Router();
$router->get('/', fn () => $site->home());
$router->get('/ansiklopedi/', fn () => $site->articles());
$router->get('/ansiklopedi/sayfa/(?P<page>\d+)/', fn (string $page) => $site->articles($page));
$router->get('/ansiklopedi/(?P<slug>[a-z0-9-]+)/', fn (string $slug) => $site->article($slug));
$router->get('/sorular/', fn () => $site->questions());
$router->get('/sorular/sayfa/(?P<page>\d+)/', fn (string $page) => $site->questions($page));
$router->get('/soru/(?P<slug>[a-z0-9-]+)/', fn (string $slug) => $site->question($slug));
$router->get('/kategoriler/', fn () => $site->categories());
$router->get('/etiketler/', fn () => $site->tags());
$router->get('/kategori/(?P<slug>[a-z0-9-]+)/', fn (string $slug) => $site->category($slug));
$router->get('/kategori/(?P<slug>[a-z0-9-]+)/sayfa/(?P<page>\d+)/', fn (string $slug, string $page) => $site->category($slug, $page));
$router->get('/etiket/(?P<slug>[a-z0-9-]+)/', fn (string $slug) => $site->tag($slug));
$router->get('/etiket/(?P<slug>[a-z0-9-]+)/sayfa/(?P<page>\d+)/', fn (string $slug, string $page) => $site->tag($slug, $page));
$router->get('/ara/', fn () => $site->search());
$router->get('/soru-sor/', fn () => $site->askGet());
$router->post('/soru-sor/', fn () => $site->askPost());
$router->get('/sitemap.xml/', fn () => $site->sitemap());
$router->get('/index.xml/', fn () => $site->rss());
$router->get('/robots.txt/', fn () => $site->robots());
$router->get('/ads.txt/', fn () => $site->adsTxt());
$router->get('/arama-indeksi.json/', fn () => $site->searchIndex());

$router->get('/admin/', fn () => $admin->dashboard());
$router->get('/admin/giris/', fn () => $admin->loginForm());
$router->post('/admin/giris/', fn () => $admin->loginPost());
$router->get('/admin/cikis/', fn () => $admin->logout());
$router->get('/admin/maddeler/', fn () => $admin->listPages('article'));
$router->get('/admin/sorular/', fn () => $admin->listPages('question'));
$router->get('/admin/sayfalar/', fn () => $admin->listPages('static'));
$router->get('/admin/icerik/yeni/', fn () => $admin->createForm());
$router->get('/admin/icerik/yeni/(?P<type>[a-z]+)/', fn (string $type) => $admin->createForm($type));
$router->get('/admin/icerik/(?P<id>\d+)/', fn (string $id) => $admin->editForm($id));
$router->post('/admin/icerik/kaydet/', fn () => $admin->save());
$router->post('/admin/icerik/(?P<id>\d+)/kaydet/', fn (string $id) => $admin->save($id));
$router->post('/admin/icerik/(?P<id>\d+)/sil/', fn (string $id) => $admin->delete($id));
$router->get('/admin/kategoriler/', fn () => $admin->categories());
$router->post('/admin/kategoriler/', fn () => $admin->saveCategory());
$router->get('/admin/gelen-sorular/', fn () => $admin->incoming());
$router->post('/admin/gelen-sorular/(?P<id>\d+)/sil/', fn (string $id) => $admin->rejectSubmission($id));

$router->get('/(?P<slug>[a-z0-9-]+)/', fn (string $slug) => $site->staticPage($slug));

return [
    'config' => $config,
    'logger' => $logger,
    'router' => $router,
    'site' => $site,
    'db' => $db,
];
