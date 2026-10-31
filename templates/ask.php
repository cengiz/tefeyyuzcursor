<?php
/** @var \Tefeyyuz\Support\View $v */
/** @var ?\Tefeyyuz\Dto\PageDto $item */
/** @var list<\Tefeyyuz\Dto\TermDto> $categories */
$title = $item?->title ?? 'Soru Sor';
$lead = $item?->description ?? '';
$body = $item?->html ?? '';
?>
<div class="container container--narrow">
  <?= $v->fetch('partials/breadcrumbs', ['v' => $v, 'crumbs' => $crumbs ?? []]) ?>
  <article class="article">
    <header class="article-head">
      <h1 class="article-title"><?= $v->e($title) ?></h1>
<?php if ($lead !== ''): ?>
      <p class="article-lead"><?= $v->e($lead) ?></p>
<?php endif; ?>
    </header>
    <div class="prose"><?= $body ?></div>
    <form class="ask-form" id="soru-formu" method="POST" action="/soru-sor/">
      <input type="hidden" name="_csrf" value="<?= $v->e((string) $csrf) ?>">
      <p class="hp"><label>Web siteniz <input type="text" name="website" tabindex="-1" autocomplete="off"></label></p>
      <div class="field">
        <label for="soru-baslik">Sorunuz <span class="req">*</span></label>
        <input id="soru-baslik" name="soru" type="text" maxlength="200" required placeholder="Örn. Tefeyyüz kelimesinin kökeni nedir?">
      </div>
      <div class="field">
        <label for="soru-detay">Ayrıntı</label>
        <textarea id="soru-detay" name="detay" rows="6" placeholder="Sorunuzu biraz daha açıklayabilirsiniz (isteğe bağlı)."></textarea>
      </div>
      <div class="field-row">
        <div class="field">
          <label for="soru-kategori">Kategori</label>
          <select id="soru-kategori" name="kategori">
            <option value="">Seçiniz</option>
<?php foreach ($categories as $term): ?>
            <option value="<?= $v->e($term->name) ?>"><?= $v->e($term->name) ?></option>
<?php endforeach; ?>
            <option value="Diğer">Diğer</option>
          </select>
        </div>
        <div class="field">
          <label for="soru-eposta">E-posta (cevap yayınlanınca haber verelim)</label>
          <input id="soru-eposta" name="email" type="email" placeholder="ornek@eposta.com">
        </div>
      </div>
      <div class="field">
        <label for="soru-ad">Görünecek adınız</label>
        <input id="soru-ad" name="ad" type="text" maxlength="60" placeholder="Takma ad kullanabilirsiniz">
      </div>
      <label class="check">
        <input type="checkbox" name="kvkk" required>
        <span><a href="/gizlilik-politikasi/">Gizlilik Politikası</a>'nı okudum, sorumun düzenlenerek sitede yayınlanabileceğini kabul ediyorum.</span>
      </label>
      <button class="btn btn-primary" type="submit">Soruyu Gönder</button>
    </form>
  </article>
</div>
