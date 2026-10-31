<?php
/** @var \Tefeyyuz\Support\View $v */
?>
<h1>Özet</h1>
<div class="admin-stats">
  <a class="admin-stat" href="/admin/maddeler/"><strong><?= (int) $articleCount ?></strong><span>Madde</span></a>
  <a class="admin-stat" href="/admin/sorular/"><strong><?= (int) $questionCount ?></strong><span>Soru</span></a>
  <a class="admin-stat" href="/admin/gelen-sorular/"><strong><?= (int) $pending ?></strong><span>Bekleyen soru</span></a>
</div>
<p class="admin-actions">
  <a class="btn btn-primary" href="/admin/icerik/yeni/article/">Yeni madde</a>
  <a class="btn btn-outline" href="/admin/icerik/yeni/question/">Yeni soru</a>
  <a class="btn btn-outline" href="/admin/icerik/yeni/static/">Yeni sayfa</a>
</p>
