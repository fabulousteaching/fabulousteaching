<?php
require __DIR__ . '/inc/start.php';

$seite        = 'uebungslabor';
$titel        = 'Übungslabor';
$beschreibung = 'Das Übungslabor: Vokaboom, die Vokabelplattform für Fremdsprachen, und Einmaleins zum Üben des kleinen und grossen Einmaleins.';

require __DIR__ . '/inc/kopf.php';
?>

<section class="seitenkopf">
  <div class="huelle">
    <div class="seitenkopf-karte kritzel">
      <div class="deko" aria-hidden="true"><?= icon('laptop') ?></div>
      <div class="seitenkopf-inhalt">
        <h1>Übungslabor</h1>
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
        <p>Rechnungen üben, festigen und automatisieren – am Tablet, am Computer und auf Papier. Schwierige Rechnungen kommen automatisch wieder, bis sie sitzen.</p>
        <ul class="werkzeug-merkmale">
          <li><?= icon('calculator') ?> Trainer mit Rechenweg</li>
          <li><?= icon('chart-no-axes-column-increasing') ?> Reihen-Treppe</li>
          <li><?= icon('zap') ?> Blitztest</li>
          <li><?= icon('gamepad-2') ?> Spiele</li>
          <li><?= icon('users') ?> Klassen &amp; Kinder-Logins</li>
          <li><?= icon('file-text') ?> Übungsblätter als PDF</li>
        </ul>
        <a class="knopf knopf-einmaleins" href="<?= e(EINMALEINS_URL) ?>" target="_blank" rel="noopener">Einmaleins öffnen <?= icon('external-link') ?></a>
      </div>
    </article>

  </div>
</section>

<?php require __DIR__ . '/inc/fuss.php'; ?>
