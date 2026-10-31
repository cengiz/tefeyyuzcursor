<?php
declare(strict_types=1);

namespace Tefeyyuz\Support;

final class View
{
    /** @var array<string, mixed> */
    private array $globals = [];

    /** @param array<string, mixed> $config */
    public function __construct(
        private readonly string $dir,
        private readonly array $config,
        private readonly Session $session,
    ) {
    }

    /** @param array<string, mixed> $data */
    public function setGlobals(array $data): void
    {
        $this->globals = $data;
    }

    /** @param array<string, mixed> $data */
    public function render(string $template, array $data = [], string $layout = 'layout'): string
    {
        $data = array_merge($this->globals, $data);
        $data['v'] = $this;
        $data['config'] = $this->config;
        $data['bodyClass'] = $data['bodyClass'] ?? '';
        $data['seo'] = $data['seo'] ?? [];
        $data['includeSearchJs'] = $data['includeSearchJs'] ?? false;
        $data['inArticleAd'] = $data['inArticleAd'] ?? false;
        $data['adsEnabledOnPage'] = $data['adsEnabledOnPage'] ?? true;
        $data['flash'] = $this->session->flash('ok');
        $data['flashError'] = $this->session->flash('error');
        $data['csrf'] = $this->session->csrfToken();
        $content = $this->fetch($template, $data);
        $data['content'] = $content;
        return $this->fetch($layout, $data);
    }

    /** @param array<string, mixed> $data */
    public function fetch(string $template, array $data = []): string
    {
        $file = $this->dir . '/' . $template . '.php';
        if (!is_file($file)) {
            throw new \RuntimeException('Şablon bulunamadı: ' . $template);
        }
        extract($data, EXTR_SKIP);
        ob_start();
        include $file;
        return (string) ob_get_clean();
    }

    public function e(?string $value): string
    {
        return htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }

    public function url(string $path): string
    {
        $base = rtrim((string) $this->config['base_url'], '/');
        if ($path === '') {
            return $base . '/';
        }
        return $base . '/' . ltrim($path, '/');
    }

    public function path(string $path): string
    {
        $path = '/' . ltrim($path, '/');
        if (!str_ends_with($path, '/') && !str_contains(basename($path), '.')) {
            $path .= '/';
        }
        return $path;
    }

    public function dateTr(?string $iso): string
    {
        if (!$iso) {
            return '';
        }
        $ts = strtotime($iso) ?: time();
        $months = [1 => 'Ocak', 'Şubat', 'Mart', 'Nisan', 'Mayıs', 'Haziran', 'Temmuz', 'Ağustos', 'Eylül', 'Ekim', 'Kasım', 'Aralık'];
        return date('j', $ts) . ' ' . $months[(int) date('n', $ts)] . ' ' . date('Y', $ts);
    }

    public function mdLine(string $text): string
    {
        $text = $this->e($text);
        $text = preg_replace('/\*(.+?)\*/u', '<em>$1</em>', $text) ?? $text;
        return $text;
    }

    public function excerpt(string $text, int $len = 150): string
    {
        $text = preg_replace('/\{\{<\s*\/?[a-z]+[^>]*\s*>\}\}/u', ' ', $text) ?? $text;
        $text = html_entity_decode(strip_tags($text), ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $text = preg_replace('/[*_`#]+/u', '', $text) ?? $text;
        $text = preg_replace('/\s+/u', ' ', $text) ?? $text;
        if (mb_strlen($text) <= $len) {
            return $text;
        }
        return rtrim(mb_substr($text, 0, $len)) . '…';
    }

    public function readingTime(string $text): int
    {
        $plain = trim(preg_replace('/\s+/u', ' ', strip_tags($text)) ?? '');
        $words = $plain === '' ? 0 : count(preg_split('/\s+/u', $plain) ?: []);
        return max(1, (int) ceil($words / 200));
    }

    public function currentPath(): string
    {
        $path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
        return rtrim($path, '/') . '/';
    }

    public function isActive(string $url): bool
    {
        $current = $this->currentPath();
        $url = rtrim($url, '/') . '/';
        if ($url === '/') {
            return $current === '/';
        }
        return str_starts_with($current, $url);
    }

    public function ads(string $slot, string $class, string $format = 'auto', ?string $layout = null, bool $pageAllows = true): string
    {
        $ads = $this->config['adsense'];
        if (!$pageAllows) {
            return '';
        }
        $debug = (bool) $this->config['debug'];
        $enabled = (bool) $ads['enabled'] && (string) $ads['client'] !== '';
        if (!$enabled && !$debug) {
            return '';
        }
        $slotId = $ads['slots'][$slot] ?? '';
        ob_start();
        ?>
<div class="ad-wrap <?= $this->e($class) ?><?= $enabled ? '' : ' ad-wrap--disabled' ?>" data-ad-slot-name="<?= $this->e($slot) ?>">
  <span class="ad-label"><?= $enabled ? 'Reklam' : 'Reklam alanı' ?></span>
<?php if (!$enabled || $debug): ?>
  <div class="ad-placeholder"><?= $this->e($slot) ?><?= $enabled ? '' : ' · adsense.enabled = false' ?></div>
<?php else: ?>
  <ins class="adsbygoogle"
       style="display:block<?= $layout === 'in-article' ? '; text-align:center' : '' ?>"
       data-ad-client="<?= $this->e((string) $ads['client']) ?>"
       <?= $slotId ? 'data-ad-slot="' . $this->e((string) $slotId) . '"' : '' ?>
       <?= $layout ? 'data-ad-layout="' . $this->e($layout) . '"' : '' ?>
       data-ad-format="<?= $this->e($format) ?>"
       <?= $layout !== 'in-article' ? 'data-full-width-responsive="true"' : '' ?>></ins>
  <script>(adsbygoogle=window.adsbygoogle||[]).push({});</script>
<?php endif; ?>
</div>
<?php
        return (string) ob_get_clean();
    }

    public function logo(string $class = '', int $size = 36): string
    {
        $id = 'tfz-g-' . bin2hex(random_bytes(3));
        $cls = $class !== '' ? ' class="' . $this->e($class) . '"' : '';
        return <<<SVG
<svg{$cls} width="{$size}" height="{$size}" viewBox="0 0 64 64" role="img" aria-hidden="true" focusable="false">
  <defs>
    <linearGradient id="{$id}" x1="0" y1="64" x2="64" y2="0">
      <stop offset="0" stop-color="#0f766e"/>
      <stop offset="1" stop-color="#2dd4bf"/>
    </linearGradient>
  </defs>
  <rect x="2" y="2" width="60" height="60" rx="16" fill="url(#{$id})"/>
  <path d="M14 46h10v-10H14zM27 46h10V24H27zM40 46h10V14H40z" fill="#fff" opacity=".95"/>
  <path d="M16 24 30 16l14-6" fill="none" stroke="#fde68a" stroke-width="3.5" stroke-linecap="round" stroke-linejoin="round"/>
  <circle cx="45.5" cy="9.5" r="3.5" fill="#fde68a"/>
</svg>
SVG;
    }
}
