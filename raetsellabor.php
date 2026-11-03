<?php
require __DIR__ . '/inc/start.php';

$seite        = 'raetsellabor';
$titel        = 'Rätsellabor';
$beschreibung = 'Im Rätsellabor Rätsel erstellen, differenzieren, ausprobieren und herunterladen.';

require __DIR__ . '/inc/kopf.php';
?>

<section class="seitenkopf">
  <div class="huelle">
    <div class="seitenkopf-karte kritzel">
      <div class="deko deko-logo" aria-hidden="true"><?= raetsellabor_kolben() ?></div>
      <div class="seitenkopf-inhalt">
        <h1>Rätsellabor</h1>
        <p>Hier wird geknobelt, kombiniert und experimentiert – mit Köpfchen statt mit Reagenzglas.</p>
      </div>
    </div>
  </div>
</section>

<section class="abschnitt">
  <div class="huelle labor">
    <div class="labor-text">
      <h2>Willkommen im Labor</h2>
      <p>Im Rätsellabor entstehen Rätsel aus deinen eigenen Inhalten – zum Beispiel Suchsel, Zahlen-Suchsel oder Bilderrätsel (Rebus). In vier Schritten zum fertigen Rätsel:</p>
      <ol class="labor-schritte">
        <li>
          <?= icon('wand-sparkles') ?>
          <span><strong>Erstellen</strong>
          Generator wählen, Thema und eigene Wörter eingeben – oder passende Wörter vorschlagen lassen. Das Labor baut daraus das Rätsel.</span>
        </li>
        <li>
          <?= icon('chart-no-axes-column-increasing') ?>
          <span><strong>Differenzieren</strong>
          Schwierigkeit anpassen: einfach, mittel oder schwer, dazu Feldgrösse und weitere Einstellungen. So entsteht für jedes Niveau das passende Rätsel.</span>
        </li>
        <li>
          <?= icon('mouse-pointer-click') ?>
          <span><strong>Ausprobieren</strong>
          Das Rätsel direkt am Bildschirm lösen, Wörter markieren und die Lösung anzeigen lassen.</span>
        </li>
        <li>
          <?= icon('download') ?>
          <span><strong>Herunterladen</strong>
          Als Arbeitsblatt drucken oder herunterladen – auf Wunsch mit Lösungsblatt.</span>
        </li>
      </ol>
    </div>

    <aside class="tuer" aria-labelledby="tuer-titel">
      <h2 id="tuer-titel">Bereit für das erste Rätsel?</h2>

      <!-- Die Labortür: öffnet sich beim Darüberfahren, ein Klick führt ins Rätsellabor -->
      <a class="labortuer" href="<?= e(RAETSELLABOR_URL) ?>" target="_blank" rel="noopener">
        <span class="sr">Rätsellabor öffnen (neuer Tab)</span>
        <span class="tuer-rahmen" aria-hidden="true">
          <span class="tuer-raum">
            <span class="raum-text">Eintreten <?= icon('arrow-right') ?></span>
          </span>
          <span class="tuer-blatt">
            <span class="tuer-vorne">
              <!-- Logo des Rätsellabors («Puzzle-Kolben»), wie auf raetsellabor.fabulousteaching.ch -->
              <span class="tuer-logo">
                <?= raetsellabor_kolben() ?>
                <span class="wortmarke">Rätsel<span class="akzent">labor</span></span>
              </span>
              <span class="tuer-griff"></span>
            </span>
            <span class="tuer-hinten"></span>
          </span>
        </span>
        <span class="tuer-licht" aria-hidden="true"></span>
      </a>
    </aside>
  </div>
</section>

<?php require __DIR__ . '/inc/fuss.php'; ?>
