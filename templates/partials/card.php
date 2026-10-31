<?php
/** @var \Tefeyyuz\Support\View $v */
/** @var \Tefeyyuz\Dto\PageDto $item */
$compact = !empty($compact);
$featured = !empty($featured);
$isSoru = $item->isQuestion();
$len = $featured ? 420 : 150;
$excerpt = $item->shortAnswer ?: $item->description ?: $item->excerpt;
?>
<article class="card<?= $compact ? ' card--compact' : '' ?><?= $featured ? ' card--featured' : '' ?><?= $isSoru ? ' card--soru' : '' ?>">
<?php if (!$compact && $item->image !== ''): ?>
  <a class="card-media" href="<?= $v->e($item->url) ?>" tabindex="-1" aria-hidden="true">
    <img src="<?= $v->e($item->image) ?>" alt="" loading="lazy" decoding="async" width="640" height="360">
  </a>
<?php endif; ?>
  <div class="card-body">
    <div class="card-meta">
      <span class="badge <?= $isSoru ? 'badge--soru' : 'badge--makale' ?>"><?= $isSoru ? 'Soru' : 'Madde' ?></span>
<?php if ($item->categories !== []): ?>
      <a class="card-cat" href="<?= $v->e($item->categories[0]->url) ?>"><?= $v->e($item->categories[0]->name) ?></a>
<?php endif; ?>
    </div>
    <h3 class="card-title"><a href="<?= $v->e($item->url) ?>"><?= $v->e($item->title) ?></a></h3>
<?php if (!$compact): ?>
    <p class="card-excerpt"><?= $v->e($v->excerpt($excerpt, $len)) ?></p>
<?php if ($featured): ?>
    <p class="card-excerpt card-excerpt--more"><?= $v->e($v->excerpt($item->bodyMd, 360)) ?></p>
    <a class="card-readmore" href="<?= $v->e($item->url) ?>">Devamını oku →</a>
<?php endif; ?>
<?php endif; ?>
    <div class="card-foot">
      <time datetime="<?= $v->e(substr($item->publishedAt, 0, 10)) ?>"><?= $v->e($v->dateTr($item->publishedAt)) ?></time>
      <span class="dot" aria-hidden="true">·</span>
      <span><?= (int) $item->readingTime ?> dk okuma</span>
    </div>
  </div>
</article>
