<?php
/** @var \Tefeyyuz\Support\View $v */
/** @var array<string, mixed> $config */
/** @var array<string, mixed> $seo */
/** @var list<\Tefeyyuz\Dto\TermDto> $footerCategories */
$seoTitle = (string) ($seo['title'] ?? $config['title']);
$seoDesc = (string) ($seo['description'] ?? $config['description']);
$canonical = (string) ($seo['canonical'] ?? $v->url('/'));
$image = $v->url((string) ($seo['image'] ?? $config['default_image']));
$noindex = !empty($seo['noindex']);
$pageAllows = $adsEnabledOnPage ?? true;
?><!DOCTYPE html>
<html lang="tr" dir="ltr">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= $v->e($seoTitle) ?></title>
  <meta name="description" content="<?= $v->e($v->excerpt($seoDesc, 160)) ?>">
<?php if (!empty($seo['keywords']) && is_array($seo['keywords'])): ?>
  <meta name="keywords" content="<?= $v->e(implode(', ', $seo['keywords'])) ?>">
<?php endif; ?>
  <meta name="author" content="<?= $v->e((string) $config['author']) ?>">
  <meta name="theme-color" content="#0f766e" media="(prefers-color-scheme: light)">
  <meta name="theme-color" content="#0b1220" media="(prefers-color-scheme: dark)">
  <meta name="color-scheme" content="light dark">
  <script>(function(){try{var t=localStorage.getItem('tfz-tema');if(t==='dark'||(!t&&matchMedia('(prefers-color-scheme: dark)').matches))document.documentElement.setAttribute('data-theme','dark')}catch(e){}})();</script>
  <link rel="canonical" href="<?= $v->e($canonical) ?>">
<?php if ($noindex): ?>
  <meta name="robots" content="noindex, follow">
<?php else: ?>
  <meta name="robots" content="index, follow, max-image-preview:large, max-snippet:-1, max-video-preview:-1">
<?php endif; ?>
<?php if (!empty($seo['prev'])): ?><link rel="prev" href="<?= $v->e((string) $seo['prev']) ?>"><?php endif; ?>
<?php if (!empty($seo['next'])): ?><link rel="next" href="<?= $v->e((string) $seo['next']) ?>"><?php endif; ?>
<?php if (!empty($config['verification']['google'])): ?><meta name="google-site-verification" content="<?= $v->e((string) $config['verification']['google']) ?>"><?php endif; ?>
<?php if (!empty($config['verification']['bing'])): ?><meta name="msvalidate.01" content="<?= $v->e((string) $config['verification']['bing']) ?>"><?php endif; ?>
<?php if (!empty($config['verification']['yandex'])): ?><meta name="yandex-verification" content="<?= $v->e((string) $config['verification']['yandex']) ?>"><?php endif; ?>
  <meta property="og:site_name" content="<?= $v->e((string) $config['title']) ?>">
  <meta property="og:locale" content="tr_TR">
  <meta property="og:type" content="<?= $v->e((string) ($seo['type'] ?? 'website')) ?>">
  <meta property="og:title" content="<?= $v->e($seoTitle) ?>">
  <meta property="og:description" content="<?= $v->e($v->excerpt($seoDesc, 160)) ?>">
  <meta property="og:url" content="<?= $v->e($canonical) ?>">
  <meta property="og:image" content="<?= $v->e($image) ?>">
  <meta property="og:image:width" content="1200">
  <meta property="og:image:height" content="630">
<?php if (!empty($seo['published'])): ?><meta property="article:published_time" content="<?= $v->e((string) $seo['published']) ?>"><?php endif; ?>
<?php if (!empty($seo['modified'])): ?><meta property="article:modified_time" content="<?= $v->e((string) $seo['modified']) ?>"><?php endif; ?>
  <meta name="twitter:card" content="summary_large_image">
  <meta name="twitter:title" content="<?= $v->e($seoTitle) ?>">
  <meta name="twitter:description" content="<?= $v->e($v->excerpt($seoDesc, 160)) ?>">
  <meta name="twitter:image" content="<?= $v->e($image) ?>">
  <link rel="alternate" type="application/rss+xml" title="<?= $v->e((string) $config['title']) ?>" href="<?= $v->e($v->url('/index.xml')) ?>">
  <link rel="icon" href="/favicon.svg" type="image/svg+xml">
  <link rel="apple-touch-icon" href="/apple-touch-icon.png">
  <link rel="manifest" href="/site.webmanifest">
  <link rel="stylesheet" href="/assets/css/main.css">
<?php if (!empty($seo['jsonld'])): ?>
  <script type="application/ld+json"><?= json_encode($seo['jsonld'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?></script>
<?php endif; ?>
<?php if (!empty($config['adsense']['enabled']) && $config['adsense']['client']): ?>
  <script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client=<?= $v->e((string) $config['adsense']['client']) ?>" crossorigin="anonymous"></script>
<?php endif; ?>
<?php if (!empty($config['analytics']['google_tag_id'])): ?>
  <script async src="https://www.googletagmanager.com/gtag/js?id=<?= $v->e((string) $config['analytics']['google_tag_id']) ?>"></script>
  <script>window.dataLayer=window.dataLayer||[];function gtag(){dataLayer.push(arguments)}gtag('js',new Date());gtag('config','<?= $v->e((string) $config['analytics']['google_tag_id']) ?>',{anonymize_ip:true});</script>
<?php endif; ?>
</head>
<body class="<?= $v->e((string) ($bodyClass ?? '')) ?>">
  <a class="skip-link" href="#icerik">İçeriğe atla</a>
  <header class="site-header">
    <div class="container header-inner">
      <a class="brand" href="/" aria-label="<?= $v->e((string) $config['title']) ?> ana sayfa">
        <?= $v->logo('brand-mark') ?>
        <span class="brand-text"><?= $v->e((string) ($config['logo_text'] ?? $config['title'])) ?></span>
      </a>
      <nav class="main-nav" id="ana-menu" aria-label="Ana menü">
        <ul>
<?php foreach ($config['menu'] as $item): ?>
          <li><a href="<?= $v->e($item['url']) ?>"<?= $v->isActive($item['url']) ? ' class="active" aria-current="page"' : '' ?>><?= $v->e($item['name']) ?></a></li>
<?php endforeach; ?>
        </ul>
      </nav>
      <form class="header-search" role="search" action="/ara/" method="get">
        <label class="visually-hidden" for="header-q">Site içinde ara</label>
        <input id="header-q" type="search" name="q" placeholder="Ara…" autocomplete="off" required>
        <button type="submit" aria-label="Ara">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="11" cy="11" r="7"/><path d="m20 20-3.5-3.5"/></svg>
        </button>
      </form>
      <div class="header-actions">
        <button class="icon-btn" id="tema-dugmesi" type="button" aria-label="Koyu / açık tema" title="Tema">
          <svg class="ikon-ay" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M21 12.8A9 9 0 1 1 11.2 3a7 7 0 0 0 9.8 9.8z"/></svg>
          <svg class="ikon-gunes" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="4"/><path d="M12 2v2M12 20v2M4.9 4.9l1.4 1.4M17.7 17.7l1.4 1.4M2 12h2M20 12h2M4.9 19.1l1.4-1.4M17.7 6.3l1.4-1.4"/></svg>
        </button>
        <button class="icon-btn menu-toggle" id="menu-dugmesi" type="button" aria-label="Menüyü aç" aria-expanded="false" aria-controls="ana-menu">
          <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" aria-hidden="true"><path d="M4 7h16M4 12h16M4 17h16"/></svg>
        </button>
      </div>
    </div>
  </header>
  <main id="icerik" class="site-main">
<?php if (!empty($flash)): ?><div class="container"><div class="infobox infobox--ipucu"><p><?= $v->e($flash) ?></p></div></div><?php endif; ?>
<?php if (!empty($flashError)): ?><div class="container"><div class="infobox infobox--uyari"><p><?= $v->e($flashError) ?></p></div></div><?php endif; ?>
    <?= $content ?>
  </main>
  <?= $v->ads('footer', 'ad-footer', 'auto', null, $pageAllows) ?>
  <footer class="site-footer">
    <div class="container footer-grid">
      <div class="footer-brand">
        <a class="brand" href="/">
          <?= $v->logo('brand-mark', 32) ?>
          <span class="brand-text"><?= $v->e((string) $config['title']) ?></span>
        </a>
        <p class="footer-desc"><?= $v->e((string) $config['description']) ?></p>
        <p class="footer-slogan">“<?= $v->e((string) $config['slogan']) ?>”</p>
      </div>
      <div class="footer-col">
        <h3>Keşfet</h3>
        <ul>
<?php foreach ($config['menu'] as $item): ?>
          <li><a href="<?= $v->e($item['url']) ?>"><?= $v->e($item['name']) ?></a></li>
<?php endforeach; ?>
          <li><a href="/etiketler/">Etiketler</a></li>
        </ul>
      </div>
      <div class="footer-col">
        <h3>Kategoriler</h3>
        <ul>
<?php foreach (array_slice($footerCategories ?? [], 0, 8) as $term): ?>
          <li><a href="<?= $v->e($term->url) ?>"><?= $v->e($term->name) ?> <span class="muted">(<?= (int) $term->count ?>)</span></a></li>
<?php endforeach; ?>
        </ul>
      </div>
      <div class="footer-col">
        <h3>Kurumsal</h3>
        <ul>
<?php foreach ($config['footer_menu'] as $item): ?>
          <li><a href="<?= $v->e($item['url']) ?>"><?= $v->e($item['name']) ?></a></li>
<?php endforeach; ?>
          <li><a href="/index.xml" rel="alternate" type="application/rss+xml">RSS</a></li>
        </ul>
      </div>
    </div>
    <div class="container footer-bottom">
      <p>© <?= date('Y') ?> <?= $v->e((string) $config['title']) ?>. Tüm hakları saklıdır.</p>
      <p class="muted">Bu sitedeki içerikler genel bilgilendirme amaçlıdır; profesyonel danışmanlık yerine geçmez.</p>
    </div>
  </footer>
  <div class="cookie-banner" id="cerez-bandi" role="dialog" aria-live="polite" aria-label="Çerez bildirimi" hidden>
    <div class="container cookie-inner">
      <p>
        Bu site deneyiminizi iyileştirmek, trafiği analiz etmek ve kişiselleştirilmiş reklamlar göstermek için çerezler kullanır.
        Ayrıntılar için <a href="/cerez-politikasi/">Çerez Politikası</a> ve <a href="/gizlilik-politikasi/">Gizlilik Politikası</a> sayfalarımıza göz atabilirsiniz.
      </p>
      <div class="cookie-actions">
        <button type="button" class="btn btn-ghost" id="cerez-reddet">Yalnızca gerekli</button>
        <button type="button" class="btn btn-primary" id="cerez-kabul">Kabul et</button>
      </div>
    </div>
  </div>
  <script src="/assets/js/main.js" defer></script>
<?php if (!empty($includeSearchJs)): ?>
  <script src="/assets/js/search.js" defer></script>
<?php endif; ?>
<?php if (!empty($inArticleAd)): ?>
  <script>
    document.addEventListener('DOMContentLoaded',function(){
      var body=document.querySelector('.prose[data-inarticle-ad]');if(!body)return;
      var ps=body.querySelectorAll(':scope > p');if(ps.length<6)return;
      var tpl=document.getElementById('inarticle-ad-tpl');if(!tpl)return;
      ps[2].after(tpl.content.cloneNode(true));
    });
  </script>
<?php endif; ?>
</body>
</html>
