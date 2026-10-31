<?php
/** @var \Tefeyyuz\Support\View $v */
$url = $v->url($url ?? '/');
$title = (string) ($title ?? '');
$encUrl = rawurlencode($url);
$encTitle = rawurlencode($title);
?>
<div class="share" aria-label="Paylaş">
  <span class="share-label">Paylaş:</span>
  <a class="share-btn" href="https://twitter.com/intent/tweet?url=<?= $encUrl ?>&text=<?= $encTitle ?>" target="_blank" rel="noopener nofollow" aria-label="X'te paylaş">X</a>
  <a class="share-btn" href="https://www.facebook.com/sharer/sharer.php?u=<?= $encUrl ?>" target="_blank" rel="noopener nofollow" aria-label="Facebook'ta paylaş">Facebook</a>
  <a class="share-btn" href="https://wa.me/?text=<?= $encTitle ?>%20<?= $encUrl ?>" target="_blank" rel="noopener nofollow" aria-label="WhatsApp'ta paylaş">WhatsApp</a>
  <a class="share-btn" href="https://t.me/share/url?url=<?= $encUrl ?>&text=<?= $encTitle ?>" target="_blank" rel="noopener nofollow" aria-label="Telegram'da paylaş">Telegram</a>
  <button class="share-btn" type="button" data-copy="<?= $v->e($url) ?>" aria-label="Bağlantıyı kopyala">Kopyala</button>
</div>
