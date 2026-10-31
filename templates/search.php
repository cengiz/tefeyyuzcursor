<?php
/** @var \Tefeyyuz\Support\View $v */
/** @var list<\Tefeyyuz\Dto\PageDto> $results */
$query = (string) ($query ?? '');
?>
<div class="container container--narrow">
  <?= $v->fetch('partials/breadcrumbs', ['v' => $v, 'crumbs' => $crumbs ?? []]) ?>
  <header class="list-head">
    <h1>Arama</h1>
    <p class="list-desc">Ansiklopedi maddeleri ve soru-cevaplar içinde arayın.</p>
  </header>
  <form class="search-form" role="search" action="/ara/" method="get" id="arama-formu">
    <label class="visually-hidden" for="arama-q">Arama</label>
    <input id="arama-q" type="search" name="q" value="<?= $v->e($query) ?>" placeholder="Örn. Osmanlı'da medrese eğitimi" autocomplete="off" autofocus>
    <button type="submit" class="btn btn-primary">Ara</button>
  </form>
  <div class="search-status muted" id="arama-durum" aria-live="polite">
<?php if ($query !== ''): ?>
    <?= count($results) ?> sonuç bulundu: “<?= $v->e($query) ?>”
<?php endif; ?>
  </div>
  <ol class="search-results" id="arama-sonuclar" data-index="/arama-indeksi.json">
<?php foreach ($results as $item): ?>
    <li class="search-result">
      <span class="badge <?= $item->isQuestion() ? 'badge--soru' : 'badge--makale' ?>"><?= $item->isQuestion() ? 'Soru' : 'Madde' ?></span>
<?php if ($item->categories !== []): ?>
      <span class="muted"><?= $v->e($item->categories[0]->name) ?></span>
<?php endif; ?>
      <h3><a href="<?= $v->e($item->url) ?>"><?= $v->e($item->title) ?></a></h3>
      <p><?= $v->e($v->excerpt($item->shortAnswer ?: $item->description ?: $item->excerpt, 180)) ?></p>
    </li>
<?php endforeach; ?>
  </ol>
  <noscript><p class="muted">Arama özelliği için JavaScript gereklidir. Dilerseniz <a href="/ansiklopedi/">ansiklopedi</a> veya <a href="/sorular/">sorular</a> bölümüne göz atabilirsiniz.</p></noscript>
  <section class="section">
    <h2 class="section-title">Popüler Kategoriler</h2>
    <ul class="chip-list">
<?php foreach (array_slice($categories ?? [], 0, 10) as $term): ?>
      <li><a class="chip" href="<?= $v->e($term->url) ?>"><?= $v->e($term->name) ?> <small><?= (int) $term->count ?></small></a></li>
<?php endforeach; ?>
    </ul>
  </section>
</div>
