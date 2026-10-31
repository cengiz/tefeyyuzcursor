<?php
/** @var \Tefeyyuz\Support\View $v */
/** @var array<string, mixed> $config */
/** @var list<\Tefeyyuz\Dto\PageDto> $featured */
/** @var list<\Tefeyyuz\Dto\PageDto> $articles */
/** @var list<\Tefeyyuz\Dto\PageDto> $questions */
/** @var list<\Tefeyyuz\Dto\TermDto> $categories */
?>
<section class="hero">
  <div class="container hero-inner">
    <p class="hero-kicker">Türkçe bilgi bankası</p>
    <h1><?= $v->e((string) $config['slogan']) ?></h1>
    <p class="hero-lead"><?= $v->e((string) $config['description']) ?></p>
    <form class="hero-search" role="search" action="/ara/" method="get">
      <label class="visually-hidden" for="hero-q">Ne öğrenmek istiyorsun?</label>
      <input id="hero-q" type="search" name="q" placeholder="Ne öğrenmek istiyorsun? Örn. Tefeyyüz ne demek?" autocomplete="off" required>
      <button type="submit" class="btn btn-primary">Ara</button>
    </form>
    <ul class="hero-stats" aria-label="Site istatistikleri">
      <li><strong><?= (int) $articleCount ?></strong> ansiklopedi maddesi</li>
      <li><strong><?= (int) $questionCount ?></strong> cevaplanmış soru</li>
      <li><strong><?= (int) $categoryCount ?></strong> kategori</li>
    </ul>
  </div>
</section>

<div class="container">
  <?= $v->ads('header', 'ad-leaderboard', 'horizontal') ?>
</div>

<?php if ($featured !== []): ?>
<section class="container section">
  <div class="section-head">
    <h2 class="section-title">Öne Çıkanlar</h2>
  </div>
  <div class="grid grid-featured">
<?php foreach ($featured as $i => $item): ?>
    <?= $v->fetch('partials/card', ['v' => $v, 'item' => $item, 'compact' => $i !== 0, 'featured' => $i === 0]) ?>
<?php endforeach; ?>
  </div>
</section>
<?php endif; ?>

<section class="container section section-split">
  <div class="split-main">
    <div class="section-head">
      <h2 class="section-title">Son Eklenen Maddeler</h2>
      <a class="section-more" href="/ansiklopedi/">Tümünü gör →</a>
    </div>
    <div class="grid grid-2">
<?php foreach ($articles as $item): ?>
      <?= $v->fetch('partials/card', ['v' => $v, 'item' => $item]) ?>
<?php endforeach; ?>
    </div>
  </div>
  <aside class="split-side">
    <div class="section-head">
      <h2 class="section-title">Son Sorular</h2>
      <a class="section-more" href="/sorular/">Tümü →</a>
    </div>
    <ul class="qa-list">
<?php foreach ($questions as $item): ?>
      <li class="qa-item">
        <a href="<?= $v->e($item->url) ?>">
          <span class="qa-q"><?= $v->e($item->title) ?></span>
          <span class="qa-a"><?= $v->e($v->excerpt($item->shortAnswer ?: $item->excerpt, 110)) ?></span>
        </a>
      </li>
<?php endforeach; ?>
    </ul>
    <a class="btn btn-outline btn-block" href="/soru-sor/">Sen de soru sor</a>
    <?= $v->ads('sidebar', 'ad-sidebar', 'rectangle') ?>
  </aside>
</section>

<section class="container section">
  <div class="section-head">
    <h2 class="section-title">Kategoriler</h2>
    <a class="section-more" href="/kategoriler/">Tümü →</a>
  </div>
  <div class="grid grid-cats">
<?php foreach ($categories as $term): ?>
    <a class="cat-card" href="<?= $v->e($term->url) ?>">
      <span class="cat-icon" aria-hidden="true"><?= $v->e($term->icon ?: '📚') ?></span>
      <span class="cat-name"><?= $v->e($term->name) ?></span>
      <span class="cat-count"><?= (int) $term->count ?> içerik</span>
    </a>
<?php endforeach; ?>
  </div>
</section>

<section class="container section">
  <div class="cta-box">
    <div>
      <h2>Tefeyyüz nedir?</h2>
      <p>Arapça <em>feyz</em> kökünden gelen <strong>tefeyyüz</strong>; bilgi ve irfandan nasiplenmek, feyiz alarak yükselmek demektir. Biz de bu adı, öğrenerek yükselen bir topluluğun bilgi bankası olmak için seçtik.</p>
    </div>
    <a class="btn btn-primary" href="/hakkinda/">Hakkımızda</a>
  </div>
</section>
