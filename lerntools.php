<?php
require __DIR__ . '/inc/start.php';

$seite        = 'lerntools';
$titel        = 'Lerntools';
$beschreibung = 'Digitale Lerntools für den Unterricht, zum Beispiel Vokaboom – das Vokabelspiel für Englisch.';

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

  </div>
</section>

<?php require __DIR__ . '/inc/fuss.php'; ?>
