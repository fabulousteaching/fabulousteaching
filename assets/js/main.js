/* fabulous teaching – kleine Helfer */
(function () {
  'use strict';
  document.documentElement.classList.remove('no-js');

  // ---------- Menü auf dem Handy ----------
  var knopf = document.querySelector('.menue-knopf');
  var menue = document.getElementById('hauptmenue');
  if (knopf && menue) {
    var schliessen = function () {
      knopf.setAttribute('aria-expanded', 'false');
      menue.classList.remove('offen');
    };
    knopf.addEventListener('click', function () {
      var offen = knopf.getAttribute('aria-expanded') === 'true';
      knopf.setAttribute('aria-expanded', String(!offen));
      menue.classList.toggle('offen', !offen);
    });
    document.addEventListener('keydown', function (e) {
      if (e.key === 'Escape' && menue.classList.contains('offen')) { schliessen(); knopf.focus(); }
    });
    window.matchMedia('(min-width: 1101px)').addEventListener('change', function (m) { if (m.matches) schliessen(); });
  }

  // ---------- Schatten unter der Kopfzeile beim Scrollen ----------
  var kopf = document.querySelector('.kopf');
  if (kopf) {
    var pruefen = function () { kopf.classList.toggle('gescrollt', window.scrollY > 8); };
    window.addEventListener('scroll', pruefen, { passive: true });
    pruefen();
  }

  // ---------- «Bald da»-Fenster mit Konfetti (z. B. Teilmal noch nicht offen) ----------
  var konfetti = function (dialog) {
    if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) return;
    var karte = dialog.querySelector('.bald-karte');
    if (!karte || !karte.animate) return;
    var r = karte.getBoundingClientRect();
    var zeichen = ['×', ':', '+', '=', '×', ':', '●', '■'];
    var farben = ['#0F766E', '#0E7490', '#FF6B4A', '#FFC233', '#ffffff'];
    for (var i = 0; i < 44; i++) {
      var s = document.createElement('span');
      s.className = 'bald-konfetti';
      s.textContent = zeichen[i % zeichen.length];
      s.style.color = farben[i % farben.length];
      s.style.fontSize = (14 + Math.random() * 22) + 'px';
      s.style.textShadow = '0 1px 0 rgba(0,0,0,.25)';
      dialog.appendChild(s);
      var x0 = r.left + r.width / 2, y0 = r.top + 70;
      var winkel = Math.random() * Math.PI * 2, weite = 120 + Math.random() * 260;
      var x1 = x0 + Math.cos(winkel) * weite, y1 = y0 + Math.sin(winkel) * weite * 0.7 - 80;
      var drehung = (Math.random() * 720 - 360) + 'deg';
      var a = s.animate([
        { transform: 'translate(' + x0 + 'px,' + y0 + 'px) scale(.2) rotate(0deg)', opacity: 1 },
        { transform: 'translate(' + x1 + 'px,' + y1 + 'px) scale(1) rotate(' + drehung + ')', opacity: 1, offset: 0.45 },
        { transform: 'translate(' + x1 + 'px,' + (y1 + 260) + 'px) scale(.9) rotate(' + drehung + ')', opacity: 0 }
      ], { duration: 1400 + Math.random() * 900, easing: 'cubic-bezier(.15,.7,.4,1)', delay: 120 + Math.random() * 160 });
      a.onfinish = (function (el) { return function () { el.remove(); }; })(s);
    }
  };
  Array.prototype.slice.call(document.querySelectorAll('[data-bald]')).forEach(function (knopf) {
    var dialog = document.getElementById(knopf.getAttribute('data-bald'));
    if (!dialog) return;
    var schliessen = function () {
      if (!dialog.open || dialog.classList.contains('zu')) return;
      dialog.classList.add('zu');
      setTimeout(function () { dialog.classList.remove('zu'); dialog.close ? dialog.close() : dialog.removeAttribute('open'); }, 220);
    };
    if (!dialog.dataset.verbunden) {
      dialog.dataset.verbunden = '1';
      Array.prototype.slice.call(dialog.querySelectorAll('[data-schliessen]')).forEach(function (b) { b.addEventListener('click', schliessen); });
      dialog.addEventListener('click', function (e) { if (e.target === dialog) schliessen(); }); // Klick neben die Karte
      dialog.addEventListener('cancel', function (e) { e.preventDefault(); schliessen(); }); // Esc
    }
    knopf.addEventListener('click', function () {
      if (dialog.showModal) dialog.showModal(); else dialog.setAttribute('open', '');
      konfetti(dialog);
      var ok = dialog.querySelector('.bald-ok');
      if (ok) ok.focus({ preventScroll: true });
    });
  });

  // ---------- Verschlossene Labortür: Antippen lässt sie rütteln und zeigt das Schild ----------
  Array.prototype.slice.call(document.querySelectorAll('.labortuer.ist-zu')).forEach(function (tuer) {
    tuer.addEventListener('click', function () {
      tuer.classList.remove('wackelt');
      void tuer.offsetWidth; // Animation neu starten
      tuer.classList.add('wackelt', 'zeigt');
    });
  });

  // ---------- Übungslabor: Fach wählen ----------
  var ulFaecher = Array.prototype.slice.call(document.querySelectorAll('.ul-fach'));
  var ulBereiche = Array.prototype.slice.call(document.querySelectorAll('.ul-bereich'));
  if (ulFaecher.length && ulBereiche.length) {
    var ulZeigen = function (scrollen) {
      var id = decodeURIComponent(window.location.hash.slice(1));
      var ziel = null;
      ulBereiche.forEach(function (b) { b.hidden = b.id !== id; if (b.id === id) ziel = b; });
      ulFaecher.forEach(function (f) { f.setAttribute('aria-current', String(!!ziel && f.getAttribute('data-fach') === id)); });
      if (ziel && scrollen) ziel.scrollIntoView({ behavior: 'smooth', block: 'start' });
    };
    ulFaecher.forEach(function (f) {
      f.addEventListener('click', function (e) {
        e.preventDefault();
        window.history.replaceState(null, '', '#' + f.getAttribute('data-fach'));
        ulZeigen(true);
      });
    });
    window.addEventListener('hashchange', function () { ulZeigen(true); });
    ulZeigen(false);
  }

  // ---------- Filter bei den Unterrichtsmaterialien ----------
  var raster = document.getElementById('raster');
  if (!raster) return;

  var karten = Array.prototype.slice.call(raster.querySelectorAll('.karte'));
  var fachKnoepfe = Array.prototype.slice.call(document.querySelectorAll('.reiter-tab'));
  var suchfeld = document.getElementById('suchfeld');
  var zahl = document.getElementById('treffer-zahl');
  var leer = document.getElementById('leer');
  var zurueck = document.getElementById('filter-zurueck');
  var fachLeiste = document.querySelector('.fach-leiste');
  var fachKnopf = document.querySelector('.fach-leiste-knopf');
  var fachAktuell = document.getElementById('fach-aktuell');
  var inhaltTitel = document.getElementById('inhalt-titel');

  var url = new URLSearchParams(window.location.search);
  var zustand = {
    fach: url.get('fach') || '',
    suche: url.get('suche') || ''
  };
  if (suchfeld) suchfeld.value = zustand.suche;

  function anwenden(urlAktualisieren) {
    var begriffe = zustand.suche.toLowerCase().trim().split(/\s+/).filter(Boolean);
    var sichtbar = 0;

    karten.forEach(function (k) {
      var text = k.getAttribute('data-suche') || '';
      var passt =
        (!zustand.fach || (' ' + (k.getAttribute('data-fach') || '') + ' ').indexOf(' ' + zustand.fach + ' ') !== -1) &&
        begriffe.every(function (b) { return text.indexOf(b) !== -1; });
      k.hidden = !passt;
      if (passt) sichtbar++;
    });

    fachKnoepfe.forEach(function (b) {
      var aktiv = b.getAttribute('data-fach') === zustand.fach;
      b.setAttribute('aria-pressed', String(aktiv));
      var name = (b.querySelector('.reiter-name') || b).textContent.trim();
      if (aktiv && fachAktuell) fachAktuell.textContent = name;
      if (aktiv && inhaltTitel) inhaltTitel.textContent = name;
    });
    if (zahl) zahl.textContent = sichtbar;
    if (zahl && zahl.nextSibling) zahl.nextSibling.textContent = sichtbar === 1 ? ' Material' : ' Materialien';
    if (leer) leer.hidden = sichtbar !== 0;

    if (urlAktualisieren) {
      var p = new URLSearchParams();
      if (zustand.fach) p.set('fach', zustand.fach);
      if (zustand.suche.trim()) p.set('suche', zustand.suche.trim());
      var neu = window.location.pathname + (p.toString() ? '?' + p.toString() : '');
      window.history.replaceState(null, '', neu);
    }
  }

  fachKnoepfe.forEach(function (b) {
    b.addEventListener('click', function () {
      zustand.fach = b.getAttribute('data-fach'); anwenden(true);
      if (fachLeiste) { fachLeiste.classList.remove('offen'); if (fachKnopf) fachKnopf.setAttribute('aria-expanded', 'false'); }
    });
  });
  if (suchfeld) {
    suchfeld.addEventListener('input', function () { zustand.suche = suchfeld.value; anwenden(true); });
  }
  if (zurueck) {
    zurueck.addEventListener('click', function () {
      zustand = { fach: '', suche: '' };
      if (suchfeld) suchfeld.value = '';
      anwenden(true);
    });
  }

  // Handy: Fächerliste auf- und zuklappen
  if (fachKnopf && fachLeiste) {
    fachKnopf.addEventListener('click', function () {
      var offen = fachLeiste.classList.toggle('offen');
      fachKnopf.setAttribute('aria-expanded', String(offen));
    });
  }

  anwenden(false);
})();
