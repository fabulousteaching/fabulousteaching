<?php
require __DIR__ . '/inc/start.php';

$seite        = 'materialien';
$titel        = 'Unterrichtsmaterialien';
$beschreibung = 'Unterrichtsmaterialien zum Herunterladen – sortiert nach Fach und Zyklus.';

$daten       = materialien_laden();
$faecher     = $daten['faecher'];
$zyklen      = $daten['zyklen'];
$materialien = $daten['materialien'];

// Wie viele Materialien pro Fach?
$anzahl = array_fill_keys(array_keys($faecher), 0);
foreach ($materialien as $m) {
    foreach ($m['faecher'] as $k) {
        if (isset($anzahl[$k])) {
            $anzahl[$k]++;
        }
    }
}

require __DIR__ . '/inc/kopf.php';
?>

<section class="seitenkopf">
  <div class="huelle">
    <div class="seitenkopf-karte kritzel">
      <div class="deko" aria-hidden="true"><?= icon('book-open') ?></div>
      <div class="seitenkopf-inhalt">
        <h1>Unterrichtsmaterialien</h1>
        <p>Arbeitsblätter, Werkstätten, Spiele und Ideen zum Herunterladen. Wähle ein Fach oder suche direkt nach einem Thema.</p>
        <form class="suche" role="search" onsubmit="return false">
          <?= icon('search') ?>
          <label class="sr" for="suchfeld">Materialien durchsuchen</label>
          <input id="suchfeld" type="search" placeholder="Thema, Fach oder Stichwort …" autocomplete="off">
        </form>
      </div>
    </div>
  </div>
</section>

<section class="abschnitt" style="padding-top:8px" aria-label="Materialien">
  <div class="huelle" id="materialien">

    <!-- Fächer als Reiter an der Seite (werden in der Verwaltung gepflegt) -->
    <div class="materialien-layout">
    <nav class="fach-leiste" aria-label="Fächer">
      <button class="fach-leiste-knopf" type="button" aria-expanded="false" aria-controls="fach-liste">
        <span>Fach:</span> <strong id="fach-aktuell">Alle Fächer</strong> <?= icon('chevron-right') ?>
      </button>
      <div class="reiter" id="fach-liste" role="group" aria-label="Fächer">
        <button class="reiter-tab" type="button" data-fach="" aria-pressed="true" style="--f:#f7a15c">
          <?= icon('sparkles') ?><span class="reiter-name">Alle Fächer</span><span class="reiter-zahl"><?= count($materialien) ?></span>
        </button>
        <?php foreach ($faecher as $schluessel => $f): ?>
          <button class="reiter-tab" type="button" data-fach="<?= e($schluessel) ?>" aria-pressed="false" style="--f:<?= e($f['farbe']) ?>">
            <?= icon($f['icon']) ?><span class="reiter-name"><?= e($f['name']) ?></span><span class="reiter-zahl"><?= $anzahl[$schluessel] ?></span>
          </button>
        <?php endforeach; ?>
      </div>
    </nav>

    <div class="reiter-inhalt">
      <div class="filterleiste">
        <h2 class="inhalt-titel" id="inhalt-titel">Alle Fächer</h2>
        <p class="treffer" aria-live="polite"><span id="treffer-zahl"><?= count($materialien) ?></span> <?= count($materialien) === 1 ? 'Material' : 'Materialien' ?></p>
      </div>

      <div class="raster" id="raster">
        <?php foreach ($materialien as $m) { echo material_karte($m, $faecher, $zyklen); } ?>
      </div>

      <div class="leer" id="leer" hidden>
        <?= icon('search') ?>
        <h3>Nichts gefunden</h3>
        <p>Zu dieser Auswahl gibt es (noch) kein Material.</p>
        <button class="knopf knopf-klein knopf-hell" type="button" id="filter-zurueck">Filter zurücksetzen</button>
      </div>
    </div>
    </div>

  </div>
</section>

<?php require __DIR__ . '/inc/fuss.php'; ?>
