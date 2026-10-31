<?php
/** @var \Tefeyyuz\Support\View $v */
/** @var ?\Tefeyyuz\Dto\PageDto $item */
/** @var list<\Tefeyyuz\Dto\TermDto> $categories */
$type = $item->type ?? ($type ?? 'article');
$action = $item ? '/admin/icerik/' . $item->id . '/kaydet/' : '/admin/icerik/kaydet/';
$selected = [];
if ($item) {
    foreach ($item->categories as $c) {
        $selected[$c->id] = true;
    }
}
$tagStr = $item ? implode(', ', array_map(static fn ($t) => $t->name, $item->tags)) : '';
$kwStr = $item ? implode(', ', $item->keywords) : '';
$srcStr = $item ? implode("\n", $item->sources) : '';
$faqJson = $item ? json_encode($item->faq, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) : '[]';
?>
<h1><?= $item ? 'Düzenle' : 'Yeni içerik' ?></h1>
<form class="ask-form admin-form" method="post" action="<?= $v->e($action) ?>">
  <input type="hidden" name="_csrf" value="<?= $v->e((string) $csrf) ?>">
  <input type="hidden" name="type" value="<?= $v->e($type) ?>">
  <div class="field">
    <label for="title">Başlık</label>
    <input id="title" name="title" required value="<?= $v->e($item->title ?? '') ?>">
  </div>
  <div class="field-row">
    <div class="field">
      <label for="slug">Slug</label>
      <input id="slug" name="slug" value="<?= $v->e($item->slug ?? '') ?>" placeholder="boş bırakılırsa başlıktan üretilir">
    </div>
    <div class="field">
      <label for="status">Durum</label>
      <select id="status" name="status">
        <option value="published"<?= (($item->status ?? 'published') === 'published') ? ' selected' : '' ?>>Yayında</option>
        <option value="draft"<?= (($item->status ?? '') === 'draft') ? ' selected' : '' ?>>Taslak</option>
      </select>
    </div>
  </div>
  <div class="field">
    <label for="seo_title">SEO başlığı</label>
    <input id="seo_title" name="seo_title" value="<?= $v->e($item->seoTitle ?? '') ?>">
  </div>
  <div class="field">
    <label for="description">Açıklama</label>
    <textarea id="description" name="description" rows="3"><?= $v->e($item->description ?? '') ?></textarea>
  </div>
<?php if ($type === 'question'): ?>
  <div class="field">
    <label for="question_text">Soru metni</label>
    <textarea id="question_text" name="question_text" rows="3"><?= $v->e($item->questionText ?? '') ?></textarea>
  </div>
  <div class="field-row">
    <div class="field">
      <label for="asker">Soran</label>
      <input id="asker" name="asker" value="<?= $v->e($item->asker ?? '') ?>">
    </div>
    <div class="field">
      <label for="short_answer">Kısa cevap</label>
      <input id="short_answer" name="short_answer" value="<?= $v->e($item->shortAnswer ?? '') ?>">
    </div>
  </div>
  <div class="field">
    <label for="votes">Oy</label>
    <input id="votes" name="votes" type="number" value="<?= (int) ($item->votes ?? 0) ?>">
  </div>
<?php endif; ?>
<?php if ($type === 'article'): ?>
  <div class="field">
    <label for="ozet">Özet (Kısaca kutusu)</label>
    <textarea id="ozet" name="ozet" rows="3"><?= $v->e($item->ozet ?? '') ?></textarea>
  </div>
<?php endif; ?>
  <div class="field">
    <label for="body_md">İçerik (Markdown)</label>
    <textarea id="body_md" name="body_md" rows="18"><?= $v->e($item->bodyMd ?? '') ?></textarea>
  </div>
  <fieldset class="field">
    <legend>Kategoriler</legend>
<?php foreach ($categories as $term): ?>
    <label class="check"><input type="checkbox" name="category_ids[]" value="<?= (int) $term->id ?>"<?= isset($selected[$term->id]) ? ' checked' : '' ?>><span><?= $v->e($term->name) ?></span></label>
<?php endforeach; ?>
  </fieldset>
  <div class="field">
    <label for="tags">Etiketler (virgülle)</label>
    <input id="tags" name="tags" value="<?= $v->e($tagStr) ?>">
  </div>
  <div class="field">
    <label for="keywords">Anahtar kelimeler (virgülle)</label>
    <input id="keywords" name="keywords" value="<?= $v->e($kwStr) ?>">
  </div>
  <div class="field">
    <label for="sources">Kaynaklar (her satır bir kaynak)</label>
    <textarea id="sources" name="sources" rows="5"><?= $v->e($srcStr) ?></textarea>
  </div>
<?php if ($type === 'question'): ?>
  <div class="field">
    <label for="faq_json">SSS (JSON)</label>
    <textarea id="faq_json" name="faq_json" rows="6"><?= $v->e((string) $faqJson) ?></textarea>
  </div>
<?php endif; ?>
  <div class="field-row">
    <div class="field">
      <label for="author">Yazar</label>
      <input id="author" name="author" value="<?= $v->e($item->author ?? '') ?>">
    </div>
    <div class="field">
      <label for="image">Görsel URL</label>
      <input id="image" name="image" value="<?= $v->e($item->image ?? '') ?>">
    </div>
  </div>
  <label class="check"><input type="hidden" name="featured" value="0"><input type="checkbox" name="featured" value="1"<?= !empty($item?->featured) ? ' checked' : '' ?>><span>Öne çıkan</span></label>
  <label class="check"><input type="hidden" name="ads_enabled" value="0"><input type="checkbox" name="ads_enabled" value="1"<?= ($item?->adsEnabled ?? true) ? ' checked' : '' ?>><span>Reklam göster</span></label>
  <label class="check"><input type="hidden" name="toc_enabled" value="0"><input type="checkbox" name="toc_enabled" value="1"<?= ($item?->tocEnabled ?? true) ? ' checked' : '' ?>><span>İçindekiler</span></label>
  <label class="check"><input type="hidden" name="noindex" value="0"><input type="checkbox" name="noindex" value="1"<?= !empty($item?->noindex) ? ' checked' : '' ?>><span>noindex</span></label>
  <label class="check"><input type="hidden" name="show_date" value="0"><input type="checkbox" name="show_date" value="1"<?= !empty($item?->showDate) ? ' checked' : '' ?>><span>Tarihi göster</span></label>
  <button class="btn btn-primary" type="submit">Kaydet</button>
</form>
