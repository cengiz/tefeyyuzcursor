<?php
/** @var \Tefeyyuz\Support\View $v */
$isItem = isset($item) && $item instanceof \Tefeyyuz\Dto\PageDto;
$pageTitle = $isItem ? $item->title : (string) ($title ?? 'Sayfa bulunamadı');
$lead = $isItem ? $item->description : '';
$body = $html ?? ($isItem ? $item->html : '');
$showDate = $isItem && $item->showDate;
?>
<div class="container container--narrow">
  <?= $v->fetch('partials/breadcrumbs', ['v' => $v, 'crumbs' => $crumbs ?? []]) ?>
  <article class="article">
    <header class="article-head">
      <h1 class="article-title"><?= $v->e($pageTitle) ?></h1>
<?php if ($lead !== ''): ?>
      <p class="article-lead"><?= $v->e($lead) ?></p>
<?php endif; ?>
<?php if ($showDate): ?>
      <div class="article-meta"><span>Son güncelleme: <time datetime="<?= $v->e(substr($item->updatedAt, 0, 10)) ?>"><?= $v->e($v->dateTr($item->updatedAt)) ?></time></span></div>
<?php endif; ?>
    </header>
    <div class="prose">
      <?= $body ?>
    </div>
  </article>
</div>
