<?php
/** @var \Tefeyyuz\Support\View $v */
/** @var list<\Tefeyyuz\Dto\PageDto> $related */
$related = $related ?? [];
if ($related === []) {
    return;
}
?>
<section class="related" aria-labelledby="ilgili-baslik">
  <h2 id="ilgili-baslik" class="section-title">İlgili İçerikler</h2>
  <div class="grid grid-3">
<?php foreach ($related as $rel): ?>
    <?= $v->fetch('partials/card', ['v' => $v, 'item' => $rel, 'compact' => true]) ?>
<?php endforeach; ?>
  </div>
</section>
