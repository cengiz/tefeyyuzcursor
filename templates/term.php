<?php
/** @var \Tefeyyuz\Support\View $v */
/** @var \Tefeyyuz\Dto\TermDto $term */
/** @var list<\Tefeyyuz\Dto\PageDto> $items */
?>
<div class="container">
  <?= $v->fetch('partials/breadcrumbs', ['v' => $v, 'crumbs' => $crumbs ?? []]) ?>
  <header class="list-head">
    <p class="list-kicker"><?= $v->e((string) ($kindLabel ?? 'Kategori')) ?></p>
    <h1><?php if ($term->icon !== ''): ?><span aria-hidden="true"><?= $v->e($term->icon) ?></span> <?php endif; ?><?= $v->e($term->name) ?></h1>
<?php if ($term->description !== ''): ?>
    <p class="list-desc"><?= $v->e($term->description) ?></p>
<?php endif; ?>
    <p class="muted"><?= (int) $total ?> içerik</p>
  </header>
  <?= $v->ads('header', 'ad-leaderboard', 'horizontal') ?>
  <div class="layout-with-sidebar">
    <div class="layout-main">
      <div class="grid grid-2">
<?php foreach ($items as $item): ?>
        <?= $v->fetch('partials/card', ['v' => $v, 'item' => $item]) ?>
<?php endforeach; ?>
      </div>
      <?= $v->fetch('partials/pagination', ['v' => $v, 'page' => $page, 'pages' => $pages, 'basePath' => $basePath]) ?>
    </div>
    <?= $v->fetch('partials/sidebar', ['v' => $v, 'sidebar' => $sidebar ?? []]) ?>
  </div>
</div>
