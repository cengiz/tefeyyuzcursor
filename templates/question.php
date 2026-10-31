<?php
/** @var \Tefeyyuz\Support\View $v */
/** @var \Tefeyyuz\Dto\PageDto $item */
/** @var array<string, mixed> $config */
$pageAllows = $adsEnabledOnPage ?? true;
?>
<div class="container">
  <?= $v->fetch('partials/breadcrumbs', ['v' => $v, 'crumbs' => $crumbs ?? []]) ?>
  <div class="layout-with-sidebar">
    <article class="layout-main qa">
      <header class="qa-head">
        <div class="article-cats">
          <span class="badge badge--soru">Soru</span>
<?php foreach ($item->categories as $term): ?>
          <a class="chip" href="<?= $v->e($term->url) ?>"><?= $v->e($term->name) ?></a>
<?php endforeach; ?>
        </div>
        <h1 class="qa-title"><?= $v->e($item->title) ?></h1>
<?php if ($item->questionText !== ''): ?>
        <p class="qa-text"><?= $v->e($item->questionText) ?></p>
<?php endif; ?>
        <div class="article-meta">
          <span>Soran: <strong><?= $v->e($item->asker !== '' ? $item->asker : 'Tefeyyüz Okuru') ?></strong></span>
          <span class="dot">·</span>
          <time datetime="<?= $v->e(substr($item->publishedAt, 0, 10)) ?>"><?= $v->e($v->dateTr($item->publishedAt)) ?></time>
          <span class="dot">·</span>
          <span><?= (int) $item->readingTime ?> dk okuma</span>
        </div>
      </header>
      <?= $v->ads('header', 'ad-leaderboard', 'horizontal', null, $pageAllows) ?>
<?php if ($item->shortAnswer !== ''): ?>
      <aside class="infobox infobox--cevap">
        <strong>Kısa cevap</strong>
        <p><?= $v->e($item->shortAnswer) ?></p>
      </aside>
<?php endif; ?>
      <section id="cevap" class="answer">
        <div class="answer-head">
          <div class="avatar" aria-hidden="true"><?= $v->logo('', 40) ?></div>
          <div>
            <strong><?= $v->e($item->author ?: (string) $config['author']) ?></strong>
            <span class="muted">· Kabul edilen cevap</span>
          </div>
<?php if ($item->votes > 0): ?><span class="votes" title="Faydalı bulanlar">▲ <?= (int) $item->votes ?></span><?php endif; ?>
        </div>
        <div class="prose" data-inarticle-ad>
          <?= $item->html ?>
        </div>
<?php if (!empty($inArticleAd)): ?>
        <template id="inarticle-ad-tpl">
          <?= $v->ads('inArticle', 'ad-inarticle', 'fluid', 'in-article', $pageAllows) ?>
        </template>
<?php endif; ?>
      </section>
<?php if ($item->faq !== []): ?>
      <section class="faq" aria-labelledby="sss-baslik">
        <h2 id="sss-baslik">Sık Sorulan Sorular</h2>
<?php foreach ($item->faq as $faq): ?>
        <details class="faq-item">
          <summary><?= $v->e((string) ($faq['soru'] ?? '')) ?></summary>
          <div class="prose"><?= $v->mdLine((string) ($faq['cevap'] ?? '')) ?></div>
        </details>
<?php endforeach; ?>
      </section>
<?php endif; ?>
<?php if ($item->sources !== []): ?>
      <section class="sources">
        <h2>Kaynaklar</h2>
        <ol>
<?php foreach ($item->sources as $src): ?>
          <li><?= $v->mdLine((string) $src) ?></li>
<?php endforeach; ?>
        </ol>
      </section>
<?php endif; ?>
      <footer class="article-foot">
<?php if ($item->tags !== []): ?>
        <div class="tags">
          <span class="tags-label">Etiketler:</span>
<?php foreach ($item->tags as $term): ?>
          <a class="chip chip--tag" href="<?= $v->e($term->url) ?>" rel="tag"><?= $v->e($term->name) ?></a>
<?php endforeach; ?>
        </div>
<?php endif; ?>
        <?= $v->fetch('partials/share', ['v' => $v, 'url' => $item->url, 'title' => $item->title]) ?>
        <div class="helpful" data-helpful>
          <span>Bu cevap faydalı oldu mu?</span>
          <button type="button" class="btn btn-ghost btn-sm" data-vote="yes">👍 Evet</button>
          <button type="button" class="btn btn-ghost btn-sm" data-vote="no">👎 Hayır</button>
          <span class="helpful-thanks" hidden>Teşekkürler!</span>
        </div>
      </footer>
      <div class="cta-box cta-box--sm">
        <div>
          <h2>Senin de bir sorun mu var?</h2>
          <p>Merak ettiğin konuyu bize gönder; editörlerimiz araştırıp kaynaklı bir cevap yayınlasın.</p>
        </div>
        <a class="btn btn-primary" href="/soru-sor/">Soru Sor</a>
      </div>
      <?= $v->ads('footer', 'ad-leaderboard', 'auto', null, $pageAllows) ?>
      <?= $v->fetch('partials/related', ['v' => $v, 'related' => $related ?? []]) ?>
    </article>
    <?= $v->fetch('partials/sidebar', ['v' => $v, 'sidebar' => $sidebar ?? [], 'adsEnabledOnPage' => $pageAllows]) ?>
  </div>
</div>
