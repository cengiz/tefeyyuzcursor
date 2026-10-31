<?php
/** @var \Tefeyyuz\Support\View $v */
$page = max(1, (int) ($page ?? 1));
$pages = max(1, (int) ($pages ?? 1));
$basePath = (string) ($basePath ?? '/');
if ($pages <= 1) {
    return;
}
$pageUrl = static function (string $base, int $n): string {
    return $n <= 1 ? $base : rtrim($base, '/') . '/sayfa/' . $n . '/';
};
?>
<nav class="pagination" aria-label="Sayfalama">
<?php if ($page > 1): ?>
  <a class="page-btn" href="<?= $v->e($pageUrl($basePath, $page - 1)) ?>" rel="prev">‹ Önceki</a>
<?php else: ?>
  <span class="page-btn is-disabled">‹ Önceki</span>
<?php endif; ?>
  <ul class="page-list">
<?php
$shownEllipsis = false;
for ($n = 1; $n <= $pages; $n++):
    $show = $n <= 2 || $n >= $pages - 1 || abs($n - $page) <= 1;
    if ($show):
        $shownEllipsis = false;
?>
    <li><a href="<?= $v->e($pageUrl($basePath, $n)) ?>"<?= $n === $page ? ' class="is-current" aria-current="page"' : '' ?>><?= $n ?></a></li>
<?php
    elseif (!$shownEllipsis):
        $shownEllipsis = true;
?>
    <li><span class="ellipsis">…</span></li>
<?php
    endif;
endfor;
?>
  </ul>
<?php if ($page < $pages): ?>
  <a class="page-btn" href="<?= $v->e($pageUrl($basePath, $page + 1)) ?>" rel="next">Sonraki ›</a>
<?php else: ?>
  <span class="page-btn is-disabled">Sonraki ›</span>
<?php endif; ?>
</nav>
