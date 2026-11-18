<?php
require __DIR__ . '/inc/start.php';

$seite        = 'lerntools';
$titel        = 'Lerntools';
$beschreibung = 'Digitale Lerntools für den Unterricht: Vokaboom für Englisch-Vokabeln und Einmaleins mit Spielen wie der Zielscheibe.';

require __DIR__ . '/inc/kopf.php';
?>

<section class="seitenkopf">
  <div class="huelle">
    <div class="seitenkopf-karte kritzel">
      <div class="deko" aria-hidden="true"><?= icon('laptop') ?></div>
      <div class="seitenkopf-inhalt">
        <h1>Lerntools</h1>
        <p>Digitale Werkzeuge für den Unterricht – zum Üben, Spielen und Teilen mit der ganzen Klasse.</p>
      </div>
    </div>
  </div>
</section>

<section class="abschnitt">
  <div class="huelle werkzeuge">

    <!-- Vokaboom -->
    <article class="werkzeug" style="--w:#4F46E5" aria-labelledby="vokaboom-titel">
      <a class="werkzeug-logo" href="<?= e(VOKABOOM_URL) ?>" target="_blank" rel="noopener" tabindex="-1" aria-hidden="true">
        <img src="assets/bilder/vokaboom-logo.svg" width="160" height="156" alt="">
      </a>
      <div class="werkzeug-text">
        <h2 id="vokaboom-titel" class="vokaboom-name">Voka<span>boom</span></h2>
        <p class="werkzeug-unterzeile">Das Vokabelspiel für Englisch</p>
        <p>Vokabeln einfügen – und Vokaboom macht daraus Lernkarten mit Bildern, Spiele und Übungen. Mit einem QR-Code hat die ganze Klasse die Wörter sofort auf dem Tablet.</p>
        <ul class="werkzeug-merkmale">
          <li><?= icon('layers') ?> Lernkarten mit Bild</li>
          <li><?= icon('puzzle') ?> Zuordnen &amp; Memory</li>
          <li><?= icon('mic') ?> Aussprechen</li>
          <li><?= icon('headphones') ?> Hören &amp; Schreiben</li>
          <li><?= icon('circle-check') ?> Test</li>
          <li><?= icon('qr-code') ?> Teilen per QR-Code</li>
        </ul>
        <a class="knopf knopf-vokaboom" href="<?= e(VOKABOOM_URL) ?>" target="_blank" rel="noopener">Vokaboom öffnen <?= icon('external-link') ?></a>
      </div>
    </article>

    <!-- Einmaleins (1x1) -->
    <article class="werkzeug" style="--w:#0F766E" aria-labelledby="einmaleins-titel">
      <a class="werkzeug-logo" href="<?= e(EINMALEINS_URL) ?>" target="_blank" rel="noopener" tabindex="-1" aria-hidden="true">
        <img src="assets/bilder/einmaleins-logo.svg" width="160" height="152" alt="">
      </a>
      <div class="werkzeug-text">
        <h2 id="einmaleins-titel" class="einmaleins-name">Einmal<span>eins</span></h2>
        <p class="werkzeug-unterzeile">Das kleine und das grosse Einmaleins üben</p>
        <p>Rechnungen üben und automatisieren – mit Trainer, Reihen-Treppe und Blitztest. Mit den Münzen aus den Übungen geht es zu den Spielen, zum Beispiel zur Zielscheibe mit der Armbrust.</p>
        <ul class="werkzeug-merkmale">
          <li class="ist-neu"><span class="emo" aria-hidden="true">🎯</span> Zielscheibe <span class="neu-marke">Neu</span></li>
          <li><span class="emo" aria-hidden="true">⚽</span> Elfmeter</li>
          <li><span class="emo" aria-hidden="true">☄️</span> Meteoriten</li>
          <li><span class="emo" aria-hidden="true">🐸</span> Frosch-Hüpfen</li>
          <li><span class="emo" aria-hidden="true">✅</span> Stimmt das?</li>
          <li><span class="emo" aria-hidden="true">🃏</span> Memory</li>
          <li><span class="emo" aria-hidden="true">⚔️</span> Duell zu zweit</li>
        </ul>
        <a class="knopf knopf-einmaleins" href="<?= e(EINMALEINS_URL) ?>" target="_blank" rel="noopener"><span class="emo" aria-hidden="true">🎯</span> Zum Einmaleins-Spiel <?= icon('external-link') ?></a>
      </div>
    </article>

  </div>
</section>

<?php require __DIR__ . '/inc/fuss.php'; ?>
