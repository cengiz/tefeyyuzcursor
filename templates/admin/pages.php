<?php
/** @var \Tefeyyuz\Support\View $v */
/** @var list<\Tefeyyuz\Dto\PageDto> $items */
$type = $type ?? 'article';
?>
<div class="admin-toolbar">
  <h1><?= $v->e((string) $label) ?></h1>
  <a class="btn btn-primary" href="/admin/icerik/yeni/<?= $v->e($type) ?>/">Yeni</a>
</div>
<table class="admin-table">
  <thead>
    <tr><th>Başlık</th><th>Durum</th><th>Güncelleme</th><th></th></tr>
  </thead>
  <tbody>
<?php foreach ($items as $item): ?>
    <tr>
      <td><a href="/admin/icerik/<?= (int) $item->id ?>/"><?= $v->e($item->title) ?></a></td>
      <td><?= $v->e($item->status) ?></td>
      <td><?= $v->e($v->dateTr($item->updatedAt)) ?></td>
      <td class="admin-row-actions">
        <a href="<?= $v->e($item->url) ?>" target="_blank" rel="noopener">Gör</a>
        <form method="post" action="/admin/icerik/<?= (int) $item->id ?>/sil/" onsubmit="return confirm('Silinsin mi?');">
          <input type="hidden" name="_csrf" value="<?= $v->e((string) $csrf) ?>">
          <button type="submit" class="btn btn-ghost btn-sm">Sil</button>
        </form>
      </td>
    </tr>
<?php endforeach; ?>
  </tbody>
</table>
<?php if ($items === []): ?>
<p class="muted">Henüz içerik yok.</p>
<?php endif; ?>
