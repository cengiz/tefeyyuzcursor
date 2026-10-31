<?php
/** @var \Tefeyyuz\Support\View $v */
/** @var list<\Tefeyyuz\Dto\SubmissionDto> $items */
?>
<h1>Gelen sorular</h1>
<?php if ($items === []): ?>
<p class="muted">Bekleyen soru yok.</p>
<?php else: ?>
<table class="admin-table">
  <thead><tr><th>Soru</th><th>Kategori</th><th>Gönderen</th><th>Tarih</th><th></th></tr></thead>
  <tbody>
<?php foreach ($items as $row): ?>
    <tr>
      <td>
        <strong><?= $v->e($row->title) ?></strong>
<?php if ($row->detail !== ''): ?><p class="muted"><?= $v->e($row->detail) ?></p><?php endif; ?>
      </td>
      <td><?= $v->e($row->category) ?></td>
      <td><?= $v->e($row->displayName) ?><?php if ($row->email !== ''): ?><br><small><?= $v->e($row->email) ?></small><?php endif; ?></td>
      <td><?= $v->e($v->dateTr($row->createdAt)) ?></td>
      <td>
        <form method="post" action="/admin/gelen-sorular/<?= (int) $row->id ?>/sil/" onsubmit="return confirm('Silinsin mi?');">
          <input type="hidden" name="_csrf" value="<?= $v->e((string) $csrf) ?>">
          <button type="submit" class="btn btn-ghost btn-sm">Sil</button>
        </form>
      </td>
    </tr>
<?php endforeach; ?>
  </tbody>
</table>
<?php endif; ?>
