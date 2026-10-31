<?php
/** @var \Tefeyyuz\Support\View $v */
/** @var list<\Tefeyyuz\Dto\TermDto> $items */
?>
<h1>Kategoriler</h1>
<table class="admin-table">
  <thead><tr><th>İkon</th><th>Ad</th><th>Slug</th><th>İçerik</th></tr></thead>
  <tbody>
<?php foreach ($items as $term): ?>
    <tr>
      <td><?= $v->e($term->icon) ?></td>
      <td><?= $v->e($term->name) ?></td>
      <td><?= $v->e($term->slug) ?></td>
      <td><?= (int) $term->count ?></td>
    </tr>
<?php endforeach; ?>
  </tbody>
</table>
<h2>Yeni / güncelle</h2>
<form class="ask-form" method="post" action="/admin/kategoriler/">
  <input type="hidden" name="_csrf" value="<?= $v->e((string) $csrf) ?>">
  <div class="field-row">
    <div class="field">
      <label for="name">Ad</label>
      <input id="name" name="name" required>
    </div>
    <div class="field">
      <label for="slug">Slug</label>
      <input id="slug" name="slug" placeholder="boş bırakılabilir">
    </div>
    <div class="field">
      <label for="icon">İkon</label>
      <input id="icon" name="icon" value="📚">
    </div>
  </div>
  <div class="field">
    <label for="description">Açıklama</label>
    <textarea id="description" name="description" rows="2"></textarea>
  </div>
  <button class="btn btn-primary" type="submit">Kaydet</button>
</form>
