<?php
/** @var \Tefeyyuz\Support\View $v */
/** @var list<\Tefeyyuz\Dto\TermDto> $terms */
$kind = $kind ?? 'kategoriler';
?>
<div class="container">
  <?= $v->fetch('partials/breadcrumbs', ['v' => $v, 'crumbs' => $crumbs ?? []]) ?>
  <header class="list-head">
    <h1><?= $v->e((string) $title) ?></h1>
    <p class="list-desc"><?= $v->e((string) $description) ?></p>
  </header>
<?php if ($kind === 'kategoriler'): ?>
  <div class="grid grid-cats grid-cats--big">
<?php foreach ($terms as $term): ?>
    <a class="cat-card" href="<?= $v->e($term->url) ?>">
      <span class="cat-icon" aria-hidden="true"><?= $v->e($term->icon ?: '📚') ?></span>
      <span class="cat-name"><?= $v->e($term->name) ?></span>
<?php if ($term->description !== ''): ?>
      <span class="cat-desc"><?= $v->e($v->excerpt($term->description, 90)) ?></span>
<?php endif; ?>
      <span class="cat-count"><?= (int) $term->count ?> içerik</span>
    </a>
<?php endforeach; ?>
  </div>
<?php else: ?>
  <ul class="chip-list chip-list--cloud">
<?php foreach ($terms as $term): ?>
    <li><a class="chip" href="<?= $v->e($term->url) ?>"><?= $v->e($term->name) ?> <small><?= (int) $term->count ?></small></a></li>
<?php endforeach; ?>
  </ul>
<?php endif; ?>
</div>
