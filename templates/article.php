<?php
/** @var \Tefeyyuz\Support\View $v */
/** @var \Tefeyyuz\Dto\PageDto $item */
/** @var array<string, mixed> $config */
$pageAllows = $adsEnabledOnPage ?? true;
$toc = (string) ($sidebar['toc'] ?? $item->tocHtml);
?>
<div class="container">
  <?= $v->fetch('partials/breadcrumbs', ['v' => $v, 'crumbs' => $crumbs ?? []]) ?>
  <div class="layout-with-sidebar">
    <article class="layout-main article" itemscope itemtype="https://schema.org/Article">
      <header class="article-head">
        <div class="article-cats">
<?php foreach ($item->categories as $term): ?>
          <a class="chip" href="<?= $v->e($term->url) ?>"><?= $v->e($term->name) ?></a>
<?php endforeach; ?>
        </div>
        <h1 class="article-title" itemprop="headline"><?= $v->e($item->title) ?></h1>
<?php if ($item->description !== ''): ?>
        <p class="article-lead" itemprop="description"><?= $v->e($item->description) ?></p>
<?php endif; ?>
        <div class="article-meta">
          <span>✍️ <?= $v->e($item->author ?: (string) $config['author']) ?></span>
          <span class="dot">·</span>
          <time datetime="<?= $v->e(substr($item->publishedAt, 0, 10)) ?>" itemprop="datePublished"><?= $v->e($v->dateTr($item->publishedAt)) ?></time>
<?php if (substr($item->updatedAt, 0, 10) !== substr($item->publishedAt, 0, 10)): ?>
          <span class="dot">·</span>
          <span>Güncelleme: <time datetime="<?= $v->e(substr($item->updatedAt, 0, 10)) ?>" itemprop="dateModified"><?= $v->e($v->dateTr($item->updatedAt)) ?></time></span>
<?php endif; ?>
          <span class="dot">·</span>
          <span><?= (int) $item->readingTime ?> dk okuma</span>
        </div>
      </header>
<?php if ($item->image !== ''): ?>
      <figure class="article-figure">
        <img src="<?= $v->e($item->image) ?>" alt="<?= $v->e($item->imageAlt ?: $item->title) ?>" width="1200" height="630" loading="eager" fetchpriority="high" decoding="async">
<?php if ($item->imageCaption !== ''): ?><figcaption><?= $v->e($item->imageCaption) ?></figcaption><?php endif; ?>
      </figure>
<?php endif; ?>
      <?= $v->ads('header', 'ad-leaderboard', 'horizontal', null, $pageAllows) ?>
<?php if ($item->ozet !== ''): ?>
      <aside class="infobox infobox--ozet">
        <strong>Kısaca</strong>
        <p><?= $v->e($item->ozet) ?></p>
      </aside>
<?php endif; ?>
<?php if ($toc !== ''): ?>
      <details class="toc-mobile">
        <summary>İçindekiler</summary>
        <?= $toc ?>
      </details>
<?php endif; ?>
      <div class="prose" data-inarticle-ad itemprop="articleBody">
        <?= $item->html ?>
      </div>
<?php if (!empty($inArticleAd)): ?>
      <template id="inarticle-ad-tpl">
        <?= $v->ads('inArticle', 'ad-inarticle', 'fluid', 'in-article', $pageAllows) ?>
      </template>
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
        <p class="cite">
          <strong>Alıntılama:</strong>
          <code><?= $v->e((string) $config['title']) ?> (<?= $v->e(substr($item->updatedAt, 0, 4)) ?>). “<?= $v->e($item->title) ?>”. <?= $v->e($v->url($item->url)) ?> (Erişim: <?= $v->e($v->dateTr(date('c'))) ?>)</code>
        </p>
      </footer>
      <?= $v->ads('footer', 'ad-leaderboard', 'auto', null, $pageAllows) ?>
      <nav class="prev-next" aria-label="Önceki / sonraki madde">
<?php if (!empty($prev)): ?><a class="pn pn-prev" href="<?= $v->e($prev->url) ?>"><small>← Önceki</small><span><?= $v->e($prev->title) ?></span></a><?php endif; ?>
<?php if (!empty($next)): ?><a class="pn pn-next" href="<?= $v->e($next->url) ?>"><small>Sonraki →</small><span><?= $v->e($next->title) ?></span></a><?php endif; ?>
      </nav>
      <?= $v->fetch('partials/related', ['v' => $v, 'related' => $related ?? []]) ?>
    </article>
    <?= $v->fetch('partials/sidebar', ['v' => $v, 'sidebar' => $sidebar ?? [], 'adsEnabledOnPage' => $pageAllows]) ?>
  </div>
</div>
