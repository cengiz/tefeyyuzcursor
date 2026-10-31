<?php
/** @var \Tefeyyuz\Support\View $v */
$sidebar = $sidebar ?? [];
$pageAllows = $adsEnabledOnPage ?? true;
$toc = (string) ($sidebar['toc'] ?? '');
?>
<aside class="sidebar">
<?php if ($toc !== ''): ?>
  <div class="widget widget-toc" id="icindekiler">
    <h2 class="widget-title">İçindekiler</h2>
    <?= $toc ?>
  </div>
<?php endif; ?>
  <?= $v->ads('sidebar', 'ad-sidebar', 'rectangle', null, $pageAllows) ?>
<?php if (!empty($sidebar['featured'])): ?>
  <div class="widget">
    <h2 class="widget-title">Öne Çıkanlar</h2>
    <ol class="mini-list">
<?php foreach ($sidebar['featured'] as $p): ?>
      <li><a href="<?= $v->e($p->url) ?>"><?= $v->e($p->title) ?></a></li>
<?php endforeach; ?>
    </ol>
  </div>
<?php endif; ?>
  <div class="widget">
    <h2 class="widget-title">Son Sorular</h2>
    <ul class="mini-list">
<?php foreach ($sidebar['questions'] ?? [] as $p): ?>
      <li><a href="<?= $v->e($p->url) ?>"><?= $v->e($p->title) ?></a></li>
<?php endforeach; ?>
    </ul>
    <a class="widget-more" href="/soru-sor/">Sen de soru sor →</a>
  </div>
  <div class="widget">
    <h2 class="widget-title">Kategoriler</h2>
    <ul class="chip-list">
<?php foreach ($sidebar['categories'] ?? [] as $term): ?>
      <li><a class="chip" href="<?= $v->e($term->url) ?>"><?= $v->e($term->name) ?> <small><?= (int) $term->count ?></small></a></li>
<?php endforeach; ?>
    </ul>
  </div>
  <div class="widget widget-sticky">
    <?= $v->ads('sidebar', 'ad-sidebar ad-sidebar--sticky', 'auto', null, $pageAllows) ?>
  </div>
</aside>
