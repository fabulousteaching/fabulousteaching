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

      <?php if (RAETSELLABOR_OFFEN): ?>
      <!-- Die Labortür: öffnet sich beim Darüberfahren, ein Klick führt ins Rätsellabor -->
      <a class="labortuer" href="<?= e(RAETSELLABOR_URL) ?>" target="_blank" rel="noopener">
        <span class="sr">Rätsellabor öffnen (neuer Tab)</span>
      <?php else: ?>
      <!-- Verschlossen (Schalter RAETSELLABOR_OFFEN in inc/start.php): Schloss, kein Link -->
      <div class="labortuer ist-zu" tabindex="0" role="img" aria-label="Die Labortür ist verschlossen: Das Rätsellabor ist noch nicht für die Öffentlichkeit bestimmt.">
      <?php endif; ?>
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
              <?php if (!RAETSELLABOR_OFFEN): ?>
                <span class="tuer-ueberwurf"></span>
                <svg class="tuer-schloss" viewBox="0 0 40 50" focusable="false">
                  <path d="M12 23V15a8 8 0 0 1 16 0v8" fill="none" stroke="#8b9097" stroke-width="5" stroke-linecap="round"/>
                  <path d="M12 23V15a8 8 0 0 1 16 0v8" fill="none" stroke="#2f2a26" stroke-width="1.4" stroke-linecap="round" opacity=".35"/>
                  <rect x="3.5" y="21" width="33" height="26" rx="6" fill="#e0a516" stroke="#2f2a26" stroke-width="2.5"/>
                  <rect x="8" y="24.5" width="5" height="18" rx="2.5" fill="#fff" opacity=".35"/>
                  <circle cx="20" cy="32" r="3.6" fill="#2f2a26"/>
                  <path d="M18.4 34h3.2l1 6.5h-5.2z" fill="#2f2a26"/>
                </svg>
                <span class="tuer-schild-zu"><?= icon('lock') ?>Noch nicht für die Öffentlichkeit bestimmt</span>
              <?php endif; ?>
            </span>
            <span class="tuer-hinten"></span>
          </span>
        </span>
        <span class="tuer-licht" aria-hidden="true"></span>
      <?php if (RAETSELLABOR_OFFEN): ?></a><?php else: ?></div><?php endif; ?>
    </aside>
  </div>
</section>

<?php require __DIR__ . '/inc/fuss.php'; ?>
