<?php
/** @var \Tefeyyuz\Support\View $v */
?>
<div class="admin-login">
  <h1>Yönetici girişi</h1>
  <form method="post" action="/admin/giris/" class="ask-form">
    <input type="hidden" name="_csrf" value="<?= $v->e((string) $csrf) ?>">
    <div class="field">
      <label for="email">E-posta</label>
      <input id="email" name="email" type="email" required autocomplete="username">
    </div>
    <div class="field">
      <label for="password">Parola</label>
      <input id="password" name="password" type="password" required autocomplete="current-password">
    </div>
    <button class="btn btn-primary" type="submit">Giriş yap</button>
  </form>
</div>
