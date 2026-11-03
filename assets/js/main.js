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
    window.matchMedia('(min-width: 901px)').addEventListener('change', function (m) { if (m.matches) schliessen(); });
  }

  // ---------- Schatten unter der Kopfzeile beim Scrollen ----------
  var kopf = document.querySelector('.kopf');
  if (kopf) {
    var pruefen = function () { kopf.classList.toggle('gescrollt', window.scrollY > 8); };
    window.addEventListener('scroll', pruefen, { passive: true });
    pruefen();
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
