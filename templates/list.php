<?php
/** @var \Tefeyyuz\Support\View $v */
/** @var list<\Tefeyyuz\Dto\PageDto> $items */
$pageAllows = true;
?>
<div class="container">
  <?= $v->fetch('partials/breadcrumbs', ['v' => $v, 'crumbs' => $crumbs ?? []]) ?>
  <header class="list-head">
    <h1><?= $v->e((string) $listTitle) ?></h1>
    <p class="list-desc"><?= $v->e((string) $listDesc) ?></p>
  </header>
  <?= $v->ads('header', 'ad-leaderboard', 'horizontal') ?>
  <div class="layout-with-sidebar">
    <div class="layout-main">
<?php if (($listType ?? '') === 'question'): ?>
      <ul class="qa-list qa-list--big">
<?php foreach ($items as $i => $item): ?>
        <li class="qa-item">
          <a href="<?= $v->e($item->url) ?>">
            <span class="qa-q"><?= $v->e($item->title) ?></span>
            <span class="qa-a"><?= $v->e($v->excerpt($item->shortAnswer ?: $item->excerpt, 180)) ?></span>
            <span class="qa-meta">
<?php if ($item->categories !== []): ?>
              <span class="chip chip--sm"><?= $v->e($item->categories[0]->name) ?></span>
<?php endif; ?>
              <time datetime="<?= $v->e(substr($item->publishedAt, 0, 10)) ?>"><?= $v->e($v->dateTr($item->publishedAt)) ?></time>
            </span>
          </a>
        </li>
<?php if ($i === 3 && count($items) > 6): ?>
        <li class="qa-ad"><?= $v->ads('inFeed', 'ad-infeed', 'fluid', 'in-feed') ?></li>
<?php endif; ?>
<?php endforeach; ?>
      </ul>
<?php else: ?>
      <div class="grid grid-2">
<?php foreach ($items as $i => $item): ?>
        <?= $v->fetch('partials/card', ['v' => $v, 'item' => $item]) ?>
<?php if ($i === 3 && count($items) > 6): ?>
        <div class="grid-span"><?= $v->ads('inFeed', 'ad-infeed', 'fluid', 'in-feed') ?></div>
<?php endif; ?>
<?php endforeach; ?>
      </div>
<?php endif; ?>
      <?= $v->fetch('partials/pagination', ['v' => $v, 'page' => $page, 'pages' => $pages, 'basePath' => $basePath]) ?>
    </div>
    <?= $v->fetch('partials/sidebar', ['v' => $v, 'sidebar' => $sidebar ?? [], 'adsEnabledOnPage' => $pageAllows]) ?>
  </div>
</div>
