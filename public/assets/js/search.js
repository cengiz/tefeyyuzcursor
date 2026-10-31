(function () {
  'use strict';
  var list = document.getElementById('arama-sonuclar');
  var status = document.getElementById('arama-durum');
  var input = document.getElementById('arama-q');
  var form = document.getElementById('arama-formu');
  if (!list || !input) return;

  var indexUrl = list.getAttribute('data-index');
  var docs = null;
  var loading = null;

  /* Türkçe karakterleri sadeleştirip küçük harfe çevirir */
  function norm(s) {
    return (s || '')
      .toLocaleLowerCase('tr')
      .replace(/ı/g, 'i').replace(/İ/g, 'i')
      .replace(/ş/g, 's').replace(/ğ/g, 'g').replace(/ü/g, 'u').replace(/ö/g, 'o').replace(/ç/g, 'c')
      .replace(/â/g, 'a').replace(/î/g, 'i').replace(/û/g, 'u')
      .replace(/[^a-z0-9\s]/g, ' ')
      .replace(/\s+/g, ' ').trim();
  }
  function tokens(q) { return norm(q).split(' ').filter(function (t) { return t.length > 1; }); }
  function esc(s) { return s.replace(/[&<>"']/g, function (c) { return ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' })[c]; }); }

  function load() {
    if (docs) return Promise.resolve(docs);
    if (loading) return loading;
    status.textContent = 'İndeks yükleniyor…';
    loading = fetch(indexUrl).then(function (r) { return r.json(); }).then(function (data) {
      docs = data.map(function (d) {
        return {
          raw: d,
          t: norm(d.t), d: norm(d.d), c: norm(d.c),
          kat: norm((d.kat || []).join(' ')), et: norm((d.et || []).join(' '))
        };
      });
      return docs;
    });
    return loading;
  }

  function score(doc, toks) {
    var s = 0;
    for (var i = 0; i < toks.length; i++) {
      var t = toks[i], hit = false;
      if (doc.t.indexOf(t) > -1) { s += doc.t.indexOf(t) === 0 ? 14 : 10; hit = true; }
      if (doc.et.indexOf(t) > -1) { s += 6; hit = true; }
      if (doc.kat.indexOf(t) > -1) { s += 4; hit = true; }
      if (doc.d.indexOf(t) > -1) { s += 3; hit = true; }
      if (doc.c.indexOf(t) > -1) { s += 1; hit = true; }
      if (!hit) return 0; // tüm kelimeler geçmeli
    }
    return s;
  }

  function highlight(text, toks) {
    var out = esc(text);
    toks.forEach(function (t) {
      if (t.length < 3) return;
      try { out = out.replace(new RegExp('(' + t.replace(/[.*+?^${}()|[\]\\]/g, '\\$&') + ')', 'gi'), '<mark>$1</mark>'); } catch (e) {}
    });
    return out;
  }

  function snippet(doc, toks) {
    var raw = doc.raw.c || '';
    var n = norm(raw);
    var pos = -1;
    for (var i = 0; i < toks.length && pos < 0; i++) pos = n.indexOf(toks[i]);
    if (pos < 0) return doc.raw.d || '';
    var start = Math.max(0, pos - 80);
    var end = Math.min(raw.length, pos + 160);
    return (start > 0 ? '…' : '') + raw.slice(start, end) + (end < raw.length ? '…' : '');
  }

  function render(q) {
    var toks = tokens(q);
    list.innerHTML = '';
    if (!toks.length) { status.textContent = 'Aramak için en az iki karakter yazın.'; return; }
    load().then(function (all) {
      var results = all.map(function (d) { return { d: d, s: score(d, toks) }; })
        .filter(function (r) { return r.s > 0; })
        .sort(function (a, b) { return b.s - a.s || (b.d.raw.dt > a.d.raw.dt ? 1 : -1); })
        .slice(0, 50);
      if (!results.length) {
        status.innerHTML = '“' + esc(q) + '” için sonuç bulunamadı. Sorunuzu <a href="/soru-sor/">bize gönderebilirsiniz</a>.';
        return;
      }
      status.textContent = results.length + ' sonuç bulundu: “' + q + '”';
      var frag = document.createDocumentFragment();
      results.forEach(function (r) {
        var d = r.d.raw;
        var li = document.createElement('li');
        li.className = 'search-result';
        li.innerHTML =
          '<span class="badge ' + (d.k === 'soru' ? 'badge--soru' : 'badge--makale') + '">' + (d.k === 'soru' ? 'Soru' : 'Madde') + '</span>' +
          (d.kat && d.kat.length ? ' <span class="muted">' + esc(d.kat[0]) + '</span>' : '') +
          '<h3><a href="' + d.u + '">' + highlight(d.t, toks) + '</a></h3>' +
          '<p>' + highlight(snippet(r.d, toks), toks) + '</p>';
        frag.appendChild(li);
      });
      list.appendChild(frag);
    }).catch(function () {
      status.textContent = 'Arama indeksi yüklenemedi. Lütfen sayfayı yenileyin.';
    });
  }

  var params = new URLSearchParams(location.search);
  var q = params.get('q') || '';
  if (q) { input.value = q; render(q); document.title = '“' + q + '” araması – ' + document.title; }

  var timer;
  input.addEventListener('input', function () {
    clearTimeout(timer);
    timer = setTimeout(function () {
      var v = input.value.trim();
      var url = new URL(location.href);
      if (v) url.searchParams.set('q', v); else url.searchParams.delete('q');
      history.replaceState(null, '', url);
      render(v);
    }, 220);
  });
  if (form) form.addEventListener('submit', function (e) { e.preventDefault(); render(input.value.trim()); });
})();
