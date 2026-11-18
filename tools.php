<?php
require __DIR__ . '/inc/start.php';

$seite        = 'lerntools';
$titel        = 'Lerntools';
$beschreibung = 'Lerntools: Vokaboom, die Vokabelplattform für Fremdsprachen, und Teilmal zum Üben von Mal und Geteilt.';

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
        <p class="werkzeug-unterzeile">Die Vokabelplattform für Fremdsprachen</p>
        <p>Vokabeln einfügen – und Vokaboom macht daraus Lernkarten mit Bildern, diverse Spiele und Übungen. Über einen QR-Code lassen sie sich mit der ganzen Klasse teilen. Als Lehrperson kann man Klassen anlegen, Aufgaben zuweisen und Statistiken einsehen. Auch Live-Spiele mit der Klasse sind möglich.</p>
        <ul class="werkzeug-merkmale">
          <li><?= icon('layers') ?> Lernkarten mit Bildern</li>
          <li><?= icon('gamepad-2') ?> Spiele &amp; Übungen</li>
          <li><?= icon('qr-code') ?> Teilen per QR-Code</li>
          <li><?= icon('users') ?> Klassen anlegen</li>
          <li><?= icon('clipboard-list') ?> Aufgaben zuweisen</li>
          <li><?= icon('chart-column') ?> Statistiken</li>
          <li><?= icon('radio') ?> Live-Spiele</li>
        </ul>
        <a class="knopf knopf-vokaboom" href="<?= e(VOKABOOM_URL) ?>" target="_blank" rel="noopener">Vokaboom öffnen <?= icon('external-link') ?></a>
      </div>
    </article>

    <!-- Teilmal (1x1, früher «Einmaleins») -->
    <article class="werkzeug" style="--w:#0F766E" aria-labelledby="teilmal-titel">
      <a class="werkzeug-logo" href="<?= e(TEILMAL_URL) ?>" target="_blank" rel="noopener" tabindex="-1" aria-hidden="true">
        <img src="assets/bilder/teilmal-logo.svg" width="160" height="160" alt="">
      </a>
      <div class="werkzeug-text">
        <h2 id="teilmal-titel" class="teilmal-name"><span class="teil">Teil</span><span class="mal">mal</span></h2>
        <p class="werkzeug-unterzeile">Mal &amp; Geteilt üben</p>
        <p>Das kleine und das grosse Einmaleins üben, festigen und automatisieren – am Tablet, am Computer und auf Papier. Schwierige Rechnungen kommen automatisch wieder, bis sie sitzen.</p>
        <ul class="werkzeug-merkmale">
          <li><?= icon('calculator') ?> Trainer mit Rechenweg</li>
          <li><?= icon('chart-no-axes-column-increasing') ?> Reihen-Treppe</li>
          <li><?= icon('zap') ?> Blitztest</li>
          <li><?= icon('gamepad-2') ?> Spiele</li>
          <li><?= icon('users') ?> Klassen &amp; Kinder-Logins</li>
          <li><?= icon('file-text') ?> Übungsblätter als PDF</li>
        </ul>
        <a class="knopf knopf-teilmal" href="<?= e(TEILMAL_URL) ?>" target="_blank" rel="noopener">Teilmal öffnen <?= icon('external-link') ?></a>
      </div>
    </article>

  </div>
</section>

<?php require __DIR__ . '/inc/fuss.php'; ?>
