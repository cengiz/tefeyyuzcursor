<?php
/** @var \Tefeyyuz\Support\View $v */
/** @var list<array{name:string,url:string}> $crumbs */
$crumbs = $crumbs ?? [];
if ($crumbs === []) {
    return;
}
$last = count($crumbs) - 1;
?>
<nav class="breadcrumbs" aria-label="Sayfa yolu">
  <ol>
<?php foreach ($crumbs as $i => $c): ?>
<?php if ($i === $last): ?>
    <li aria-current="page"><span><?= $v->e($v->excerpt($c['name'], 60)) ?></span></li>
<?php else: ?>
    <li><a href="<?= $v->e($c['url']) ?>"><?= $v->e($c['name']) ?></a></li>
<?php endif; ?>
<?php endforeach; ?>
  </ol>
</nav>
