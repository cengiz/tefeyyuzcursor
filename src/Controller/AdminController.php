<?php
declare(strict_types=1);

namespace Tefeyyuz\Controller;

use Tefeyyuz\Repository\CategoryRepository;
use Tefeyyuz\Repository\PageRepository;
use Tefeyyuz\Repository\SubmissionRepository;
use Tefeyyuz\Service\AdminPageService;
use Tefeyyuz\Service\AuthService;
use Tefeyyuz\Service\SubmissionService;
use Tefeyyuz\Support\Logger;
use Tefeyyuz\Support\Session;
use Tefeyyuz\Support\Slug;
use Tefeyyuz\Support\View;

final class AdminController
{
    public function __construct(
        private readonly AuthService $auth,
        private readonly AdminPageService $adminPages,
        private readonly CategoryRepository $categories,
        private readonly PageRepository $pages,
        private readonly SubmissionService $submissions,
        private readonly SubmissionRepository $submissionRepo,
        private readonly View $view,
        private readonly Session $session,
        private readonly Logger $logger,
    ) {
    }

    public function loginForm(): string
    {
        if ($this->auth->user()) {
            header('Location: /admin/', true, 302);
            exit;
        }
        return $this->view->render('admin/login', [
            'bodyClass' => 'page-single',
            'guest' => true,
            'seo' => ['title' => 'Yönetici girişi', 'noindex' => true, 'canonical' => '/admin/giris/'],
        ], 'admin/layout');
    }

    public function loginPost(): never
    {
        if (!$this->session->verifyCsrf($_POST['_csrf'] ?? null)) {
            $this->session->flash('error', 'Oturum doğrulaması başarısız.');
            header('Location: /admin/giris/', true, 302);
            exit;
        }
        $ok = $this->auth->attempt(trim((string) ($_POST['email'] ?? '')), (string) ($_POST['password'] ?? ''));
        if (!$ok) {
            $this->session->flash('error', 'E-posta veya parola hatalı.');
            header('Location: /admin/giris/', true, 302);
            exit;
        }
        header('Location: /admin/', true, 302);
        exit;
    }

    public function logout(): never
    {
        $this->auth->logout();
        header('Location: /admin/giris/', true, 302);
        exit;
    }

    public function dashboard(): string
    {
        $this->auth->requireAdmin();
        return $this->view->render('admin/dashboard', [
            'articleCount' => $this->pages->countPublished('article'),
            'questionCount' => $this->pages->countPublished('question'),
            'pending' => $this->submissionRepo->countPending(),
            'seo' => ['title' => 'Yönetim', 'noindex' => true],
        ], 'admin/layout');
    }

    public function listPages(string $type): string
    {
        $this->auth->requireAdmin();
        $items = $this->adminPages->list($type);
        return $this->view->render('admin/pages', [
            'type' => $type,
            'items' => $items,
            'label' => $type === 'question' ? 'Sorular' : ($type === 'static' ? 'Sayfalar' : 'Maddeler'),
            'seo' => ['title' => 'İçerikler', 'noindex' => true],
        ], 'admin/layout');
    }

    public function createForm(string $type = 'article'): string
    {
        $this->auth->requireAdmin();
        if (!in_array($type, ['article', 'question', 'static'], true)) {
            $type = 'article';
        }
        return $this->form($type, null);
    }

    public function editForm(string $id): string
    {
        $this->auth->requireAdmin();
        $item = $this->adminPages->get((int) $id);
        if (!$item) {
            http_response_code(404);
            return $this->view->render('admin/dashboard', [
                'articleCount' => 0,
                'questionCount' => 0,
                'pending' => 0,
                'seo' => ['title' => 'Bulunamadı', 'noindex' => true],
            ], 'admin/layout');
        }
        return $this->form($item->type, $item);
    }

    public function save(?string $id = null): never
    {
        $this->auth->requireAdmin();
        if (!$this->session->verifyCsrf($_POST['_csrf'] ?? null)) {
            $this->session->flash('error', 'Oturum doğrulaması başarısız.');
            header('Location: /admin/', true, 302);
            exit;
        }
        try {
            $saved = $this->adminPages->save($_POST, $id ? (int) $id : null);
            $this->session->flash('ok', 'Kaydedildi.');
            header('Location: /admin/icerik/' . $saved . '/', true, 302);
        } catch (\Throwable $e) {
            $this->logger->error('Admin kayıt hatası', ['hata' => $e->getMessage()]);
            $this->session->flash('error', 'Kayıt başarısız: ' . $e->getMessage());
            header('Location: ' . ($_SERVER['HTTP_REFERER'] ?? '/admin/'), true, 302);
        }
        exit;
    }

    public function delete(string $id): never
    {
        $this->auth->requireAdmin();
        if (!$this->session->verifyCsrf($_POST['_csrf'] ?? null)) {
            $this->session->flash('error', 'Oturum doğrulaması başarısız.');
            header('Location: /admin/', true, 302);
            exit;
        }
        $this->adminPages->delete((int) $id);
        $this->session->flash('ok', 'Silindi.');
        header('Location: /admin/maddeler/', true, 302);
        exit;
    }

    public function categories(): string
    {
        $this->auth->requireAdmin();
        return $this->view->render('admin/categories', [
            'items' => $this->categories->allWithCounts(),
            'seo' => ['title' => 'Kategoriler', 'noindex' => true],
        ], 'admin/layout');
    }

    public function saveCategory(): never
    {
        $this->auth->requireAdmin();
        if (!$this->session->verifyCsrf($_POST['_csrf'] ?? null)) {
            header('Location: /admin/kategoriler/', true, 302);
            exit;
        }
        try {
            $name = trim((string) ($_POST['name'] ?? ''));
            $slug = Slug::make((string) ($_POST['slug'] ?: $name));
            $this->categories->save(
                $name,
                $slug,
                trim((string) ($_POST['description'] ?? '')),
                trim((string) ($_POST['icon'] ?? '📚')) ?: '📚',
                !empty($_POST['id']) ? (int) $_POST['id'] : null
            );
            $this->session->flash('ok', 'Kategori kaydedildi.');
        } catch (\Throwable $e) {
            $this->logger->error('Kategori kaydı başarısız', ['hata' => $e->getMessage()]);
            $this->session->flash('error', 'Kategori kaydedilemedi.');
        }
        header('Location: /admin/kategoriler/', true, 302);
        exit;
    }

    public function incoming(): string
    {
        $this->auth->requireAdmin();
        return $this->view->render('admin/submissions', [
            'items' => $this->submissions->pending(),
            'seo' => ['title' => 'Gelen sorular', 'noindex' => true],
        ], 'admin/layout');
    }

    public function rejectSubmission(string $id): never
    {
        $this->auth->requireAdmin();
        if (!$this->session->verifyCsrf($_POST['_csrf'] ?? null)) {
            header('Location: /admin/gelen-sorular/', true, 302);
            exit;
        }
        $this->submissions->reject((int) $id);
        $this->session->flash('ok', 'Soru silindi.');
        header('Location: /admin/gelen-sorular/', true, 302);
        exit;
    }

    private function form(string $type, ?\Tefeyyuz\Dto\PageDto $item): string
    {
        return $this->view->render('admin/edit', [
            'item' => $item,
            'type' => $type,
            'categories' => $this->categories->allWithCounts(),
            'seo' => ['title' => $item ? 'Düzenle' : 'Yeni içerik', 'noindex' => true],
        ], 'admin/layout');
    }
}
