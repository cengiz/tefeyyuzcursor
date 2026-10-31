(function () {
  'use strict';

  /* ---------- Tema (koyu / açık) ---------- */
  var root = document.documentElement;
  var THEME_KEY = 'tfz-tema';
  function applyTheme(t) {
    if (t === 'dark') root.setAttribute('data-theme', 'dark');
    else root.removeAttribute('data-theme');
  }
  var saved = null;
  try { saved = localStorage.getItem(THEME_KEY); } catch (e) {}
  if (saved) applyTheme(saved);
  else if (window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches) applyTheme('dark');

  var themeBtn = document.getElementById('tema-dugmesi');
  if (themeBtn) {
    themeBtn.addEventListener('click', function () {
      var isDark = root.getAttribute('data-theme') === 'dark';
      var next = isDark ? 'light' : 'dark';
      applyTheme(next);
      try { localStorage.setItem(THEME_KEY, next); } catch (e) {}
    });
  }

  /* ---------- Mobil menü ---------- */
  var menuBtn = document.getElementById('menu-dugmesi');
  var nav = document.getElementById('ana-menu');
  if (menuBtn && nav) {
    menuBtn.addEventListener('click', function () {
      var open = nav.classList.toggle('is-open');
      menuBtn.setAttribute('aria-expanded', open ? 'true' : 'false');
      menuBtn.setAttribute('aria-label', open ? 'Menüyü kapat' : 'Menüyü aç');
    });
    document.addEventListener('click', function (e) {
      if (!nav.classList.contains('is-open')) return;
      if (nav.contains(e.target) || menuBtn.contains(e.target)) return;
      nav.classList.remove('is-open');
      menuBtn.setAttribute('aria-expanded', 'false');
    });
  }

  /* ---------- Çerez bandı ---------- */
  var COOKIE_KEY = 'tfz-cerez';
  var banner = document.getElementById('cerez-bandi');
  if (banner) {
    var choice = null;
    try { choice = localStorage.getItem(COOKIE_KEY); } catch (e) {}
    if (!choice) banner.hidden = false;
    function setConsent(v) {
      try { localStorage.setItem(COOKIE_KEY, v); } catch (e) {}
      banner.hidden = true;
      // Google Consent Mode v2 (gtag yüklüyse)
      if (typeof window.gtag === 'function') {
        var g = v === 'all' ? 'granted' : 'denied';
        window.gtag('consent', 'update', {
          ad_storage: g, ad_user_data: g, ad_personalization: g, analytics_storage: g
        });
      }
    }
    var acc = document.getElementById('cerez-kabul');
    var rej = document.getElementById('cerez-reddet');
    if (acc) acc.addEventListener('click', function () { setConsent('all'); });
    if (rej) rej.addEventListener('click', function () { setConsent('necessary'); });
  }

  /* ---------- Bağlantı kopyala ---------- */
  document.querySelectorAll('[data-copy]').forEach(function (btn) {
    btn.addEventListener('click', function () {
      var url = btn.getAttribute('data-copy');
      var done = function () {
        var old = btn.textContent;
        btn.textContent = 'Kopyalandı ✓';
        setTimeout(function () { btn.textContent = old; }, 1800);
      };
      if (navigator.clipboard && navigator.clipboard.writeText) {
        navigator.clipboard.writeText(url).then(done, done);
      } else {
        var ta = document.createElement('textarea');
        ta.value = url; document.body.appendChild(ta); ta.select();
        try { document.execCommand('copy'); } catch (e) {}
        document.body.removeChild(ta); done();
      }
    });
  });

  /* ---------- İçindekiler aktif başlık vurgusu ---------- */
  var tocLinks = document.querySelectorAll('.widget-toc nav a[href^="#"]');
  if (tocLinks.length && 'IntersectionObserver' in window) {
    var map = {};
    tocLinks.forEach(function (a) {
      var id = decodeURIComponent(a.getAttribute('href').slice(1));
      var h = document.getElementById(id);
      if (h) map[id] = a;
    });
    var ids = Object.keys(map);
    if (ids.length) {
      var io = new IntersectionObserver(function (entries) {
        entries.forEach(function (en) {
          if (en.isIntersecting) {
            tocLinks.forEach(function (a) { a.classList.remove('is-active'); });
            var a = map[en.target.id]; if (a) a.classList.add('is-active');
          }
        });
      }, { rootMargin: '-70px 0px -70% 0px', threshold: 0 });
      ids.forEach(function (id) { io.observe(document.getElementById(id)); });
    }
  }

  /* ---------- "Faydalı oldu mu?" (yerel; analytics'e event gönderir) ---------- */
  var helpful = document.querySelector('[data-helpful]');
  if (helpful) {
    var key = 'tfz-oy:' + location.pathname;
    var voted = null;
    try { voted = localStorage.getItem(key); } catch (e) {}
    var thanks = helpful.querySelector('.helpful-thanks');
    var btns = helpful.querySelectorAll('[data-vote]');
    function markVoted() {
      btns.forEach(function (b) { b.disabled = true; });
      if (thanks) thanks.hidden = false;
    }
    if (voted) markVoted();
    btns.forEach(function (b) {
      b.addEventListener('click', function () {
        var v = b.getAttribute('data-vote');
        try { localStorage.setItem(key, v); } catch (e) {}
        if (typeof window.gtag === 'function') {
          window.gtag('event', 'cevap_faydali', { value: v === 'yes' ? 1 : 0, page_path: location.pathname });
        }
        markVoted();
      });
    });
  }

  /* ---------- Soru formu: gönderim sonrası teşekkür ---------- */
  var askForm = document.getElementById('soru-formu');
  if (askForm && askForm.getAttribute('action') && askForm.getAttribute('action').indexOf('formspree') > -1) {
    askForm.addEventListener('submit', function (e) {
      e.preventDefault();
      var btn = askForm.querySelector('button[type=submit]');
      btn.disabled = true; btn.textContent = 'Gönderiliyor…';
      fetch(askForm.action, { method: 'POST', body: new FormData(askForm), headers: { Accept: 'application/json' } })
        .then(function (r) {
          if (!r.ok) throw new Error('Gönderim başarısız');
          askForm.innerHTML = '<div class="infobox infobox--ipucu"><strong>Teşekkürler!</strong><p>Sorunuz bize ulaştı. Editörlerimiz inceledikten sonra kaynaklı bir cevapla yayınlayacağız.</p></div>';
        })
        .catch(function () {
          btn.disabled = false; btn.textContent = 'Soruyu Gönder';
          alert('Gönderim sırasında bir sorun oluştu. Lütfen tekrar deneyin.');
        });
    });
  }
})();
