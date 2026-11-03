/* fabulous teaching – Verwaltung: Hochladen, Live-Vorschau, Fächer */
(function () {
  'use strict';

  function alle(sel, wurzel) { return Array.prototype.slice.call((wurzel || document).querySelectorAll(sel)); }
  function groesse(b) { return b >= 1048576 ? (b / 1048576).toFixed(1) + ' MB' : Math.max(1, Math.round(b / 1024)) + ' KB'; }

  // ---------- Datei oder Link ----------
  var quellen = alle('input[name="quelle"]');
  function quelleZeigen() {
    var wahl = (quellen.filter(function (r) { return r.checked; })[0] || {}).value || 'datei';
    alle('.v-quelle').forEach(function (q) { q.hidden = q.getAttribute('data-quelle') !== wahl; });
  }
  quellen.forEach(function (r) { r.addEventListener('change', quelleZeigen); });
  if (quellen.length) quelleZeigen();

  // ---------- Ablagefeld (Drag & Drop) ----------
  alle('.dropzone').forEach(function (zone) {
    var input = zone.querySelector('input[type="file"]');
    var anzeige = zone.querySelector('.dropzone-datei');
    var limit = parseInt(zone.getAttribute('data-limit'), 10) || 0;
    function zeigen() {
      var f = input.files && input.files[0];
      zone.classList.toggle('hat-datei', !!f);
      zone.classList.remove('zu-gross');
      if (!f) { anzeige.hidden = true; return; }
      anzeige.hidden = false;
      anzeige.textContent = f.name + ' · ' + groesse(f.size);
      if (limit && f.size > limit) {
        zone.classList.add('zu-gross');
        anzeige.textContent += ' – zu gross (höchstens ' + groesse(limit) + ')';
      }
      // Titel vorschlagen, wenn noch leer
      var titel = document.getElementById('titel');
      if (titel && !titel.value.trim()) {
        titel.value = f.name.replace(/\.[^.]+$/, '').replace(/[_-]+/g, ' ').trim();
        titel.dispatchEvent(new Event('input'));
      }
    }
    input.addEventListener('change', zeigen);
    ['dragenter', 'dragover'].forEach(function (t) {
      zone.addEventListener(t, function (e) { e.preventDefault(); zone.classList.add('drueber'); });
    });
    ['dragleave', 'drop'].forEach(function (t) {
      zone.addEventListener(t, function () { zone.classList.remove('drueber'); });
    });
    zone.addEventListener('drop', function (e) {
      e.preventDefault();
      if (e.dataTransfer && e.dataTransfer.files.length) {
        input.files = e.dataTransfer.files;
        zeigen();
      }
    });
  });

  // ---------- Zeichenzähler ----------
  alle('[data-zaehler]').forEach(function (z) {
    var feld = document.getElementById(z.getAttribute('data-zaehler'));
    if (feld) feld.addEventListener('input', function () { z.textContent = feld.value.length; });
  });

  // ---------- Live-Vorschau der Karte ----------
  var vorschau = document.querySelector('.v-vorschau-karte');
  var form = document.querySelector('.v-formular');
  if (vorschau && form) {
    var karte = vorschau.querySelector('.karte');
    var bildBox = karte.querySelector('.karte-bild');
    var bildUrl = null;

    function wert(name) { var f = form.elements[name]; return f ? String(f.value || '').trim() : ''; }
    function gewaehlt(name) { return alle('input[name="' + name + '"]:checked', form); }

    function badges() {
      var links = bildBox.querySelector('.abzeichen.links');
      var rechts = bildBox.querySelector('.abzeichen.rechts');
      if (!links) { links = document.createElement('div'); links.className = 'abzeichen links'; bildBox.appendChild(links); }
      if (!rechts) { rechts = document.createElement('div'); rechts.className = 'abzeichen rechts'; bildBox.appendChild(rechts); }
      links.innerHTML = '';
      if (form.elements.neu && form.elements.neu.checked) links.insertAdjacentHTML('beforeend', '<span class="badge badge-neu">Neu</span>');
      var status = gewaehlt('status')[0];
      if (status && status.value === 'entwurf') links.insertAdjacentHTML('beforeend', '<span class="badge badge-entwurf">Entwurf</span>');
      var zugriff = gewaehlt('zugriff')[0];
      rechts.innerHTML = zugriff && zugriff.value === 'angemeldet' ? '<span class="badge">Login</span>' : '';
    }

    function bild(fach) {
      var img = bildBox.querySelector('img');
      var entfernen = document.getElementById('bild_entfernen');
      var datei = form.elements.bild && form.elements.bild.files && form.elements.bild.files[0];
      if (datei) {
        if (bildUrl) URL.revokeObjectURL(bildUrl);
        bildUrl = URL.createObjectURL(datei);
        if (!img) { img = document.createElement('img'); img.alt = ''; bildBox.insertBefore(img, bildBox.firstChild); }
        img.src = bildUrl;
      } else if (img && entfernen && entfernen.checked) {
        img.remove(); img = null;
      }
      var symbol = bildBox.querySelector(':scope > svg.icon');
      if (img) { if (symbol) symbol.remove(); return; }
      if (fach) {
        var neues = fach.parentNode.querySelector('svg.icon').cloneNode(true);
        if (symbol) bildBox.replaceChild(neues, symbol); else bildBox.insertBefore(neues, bildBox.firstChild);
      }
    }

    function aktualisieren() {
      var faecher = gewaehlt('faecher[]');
      var erstes = faecher[0];
      karte.style.setProperty('--f', erstes ? erstes.getAttribute('data-farbe') : '#f7a15c');
      karte.querySelector('.karte-fach').textContent = faecher.length
        ? faecher.map(function (f) { return f.getAttribute('data-name'); }).join(' · ') : 'Fach wählen';
      karte.querySelector('h3').textContent = wert('titel') || 'Titel des Materials';
      karte.querySelector('p').textContent = wert('beschreibung') || 'Kurzbeschreibung …';

      var meta = karte.querySelector('.karte-meta');
      if (!meta) { meta = document.createElement('ul'); meta.className = 'karte-meta'; karte.querySelector('p').after(meta); }
      var teile = [wert('typ'), wert('umfang')].concat(gewaehlt('zyklen[]').map(function (z) { return z.getAttribute('data-name'); }));
      meta.innerHTML = '';
      teile.filter(Boolean).forEach(function (t) { var li = document.createElement('li'); li.textContent = t; meta.appendChild(li); });
      meta.hidden = !meta.children.length;

      badges();
      bild(erstes);
    }

    form.addEventListener('input', aktualisieren);
    form.addEventListener('change', aktualisieren);
    aktualisieren();

    // Doppeltes Absenden verhindern
    form.addEventListener('submit', function () {
      var knopf = form.querySelector('button[type="submit"]');
      if (knopf) { knopf.disabled = true; knopf.textContent = 'Wird gespeichert …'; }
    });
  }

  // ---------- Fächer: Symbolfarbe folgt der gewählten Farbe ----------
  alle('input[type="color"]').forEach(function (farbe) {
    var formular = farbe.closest('form');
    var auswahl = formular && formular.querySelector('.symbol-auswahl');
    if (!auswahl) return;
    farbe.addEventListener('input', function () { auswahl.style.setProperty('--f', farbe.value); });
  });
})();
