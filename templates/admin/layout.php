<?php
/** @var \Tefeyyuz\Support\View $v */
/** @var array<string, mixed> $config */
/** @var array<string, mixed> $seo */
?><!DOCTYPE html>
<html lang="tr">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= $v->e((string) ($seo['title'] ?? 'Yönetim')) ?> – <?= $v->e((string) $config['title']) ?></title>
  <meta name="robots" content="noindex, nofollow">
  <link rel="icon" href="/favicon.svg" type="image/svg+xml">
  <link rel="stylesheet" href="/assets/css/main.css">
  <link rel="stylesheet" href="/assets/css/admin.css">
  <script>(function(){try{var t=localStorage.getItem('tfz-tema');if(t==='dark'||(!t&&matchMedia('(prefers-color-scheme: dark)').matches))document.documentElement.setAttribute('data-theme','dark')}catch(e){}})();</script>
</head>
<body class="admin-body">
  <header class="admin-header">
    <a class="brand" href="<?= empty($guest) ? '/admin/' : '/admin/giris/' ?>"><?= $v->logo('brand-mark', 28) ?><span>Yönetim</span></a>
<?php if (empty($guest)): ?>
    <nav>
      <a href="/admin/">Özet</a>
      <a href="/admin/maddeler/">Maddeler</a>
      <a href="/admin/sorular/">Sorular</a>
      <a href="/admin/sayfalar/">Sayfalar</a>
      <a href="/admin/kategoriler/">Kategoriler</a>
      <a href="/admin/gelen-sorular/">Gelen sorular</a>
      <a href="/" target="_blank" rel="noopener">Siteyi gör</a>
      <a href="/admin/cikis/">Çıkış</a>
    </nav>
<?php endif; ?>
  </header>
  <main class="admin-main container">
<?php if (!empty($flash)): ?><div class="infobox infobox--ipucu"><p><?= $v->e($flash) ?></p></div><?php endif; ?>
<?php if (!empty($flashError)): ?><div class="infobox infobox--uyari"><p><?= $v->e($flashError) ?></p></div><?php endif; ?>
    <?= $content ?>
  </main>
</body>
</html>
