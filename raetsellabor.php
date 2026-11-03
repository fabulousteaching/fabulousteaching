<?php
require __DIR__ . '/inc/start.php';

$seite        = 'raetsellabor';
$titel        = 'Rätsellabor';
$beschreibung = 'Das Rätsellabor: Knobeln, tüfteln und Codes knacken.';

require __DIR__ . '/inc/kopf.php';
?>

<section class="seitenkopf">
  <div class="huelle">
    <div class="seitenkopf-karte kritzel">
      <div class="deko" aria-hidden="true"><?= icon('puzzle') ?></div>
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
      <!-- TODO: Text zum Rätsellabor anpassen -->
      <p>Das Rätsellabor ist ein eigener Bereich mit Rätseln und Knobelaufgaben. Es läuft auf einer eigenen Adresse und öffnet sich in einem neuen Tab.</p>
      <ul>
        <li><?= icon('puzzle') ?><span><strong>Knobeln</strong><br>Rätsel lösen, Muster erkennen, um die Ecke denken.</span></li>
        <li><?= icon('key-round') ?><span><strong>Codes knacken</strong><br>Hinweise kombinieren und das nächste Schloss öffnen.</span></li>
        <li><?= icon('sparkles') ?><span><strong>Für die ganze Klasse</strong><br>Alleine, zu zweit oder im Team.</span></li>
      </ul>
    </div>

    <aside class="tuer" aria-labelledby="tuer-titel">
      <h2 id="tuer-titel">Bereit für das erste Rätsel?</h2>
      <p class="tuer-hinweis">
        <span class="nur-maus">Fahr mit der Maus über die Tür …</span>
        <span class="nur-touch">Tippe auf die Tür, um einzutreten.</span>
      </p>

      <!-- Die Labortür: öffnet sich beim Darüberfahren, ein Klick führt ins Rätsellabor -->
      <a class="labortuer" href="<?= e(RAETSELLABOR_URL) ?>" target="_blank" rel="noopener">
        <span class="sr">Rätsellabor öffnen (neuer Tab)</span>
        <span class="tuer-rahmen" aria-hidden="true">
          <span class="tuer-raum">
            <span class="raum-text">Eintreten <?= icon('arrow-right') ?></span>
          </span>
          <span class="tuer-blatt">
            <span class="tuer-vorne">
              <!-- Logo des Rätsellabors («Puzzle-Kolben»), übernommen von raetsellabor.fabulousteaching.ch -->
              <span class="tuer-logo">
                <svg class="kolben" viewBox="0 4 100 160"><circle cx="34" cy="21" r="5.2" fill="#0075BA"/><circle cx="42" cy="13" r="5.6" fill="#0075BA"/><circle cx="54" cy="12" r="5.8" fill="#0075BA"/><circle cx="64" cy="19" r="5.4" fill="#0075BA"/><circle cx="66" cy="31" r="5" fill="#0075BA"/><circle cx="60" cy="41" r="4.6" fill="#0075BA"/><circle cx="53" cy="49" r="4.2" fill="#0075BA"/><circle cx="50" cy="59" r="3.8" fill="#0075BA"/><circle cx="50" cy="72" r="3.5" fill="#0075BA"/><circle cx="51" cy="94" r="2.4" fill="#0075BA"/><circle cx="48.5" cy="103" r="1.8" fill="#0075BA"/><path d="M27.4 132 H42 C44 129 40 124 44.5 121 C47 119.3 53 119.3 55.5 121 C60 124 56 129 58 132 H72.6 L84 152 Q88 160 79 160 H21 Q12 160 16 152 Z" fill="#0075BA"/><path d="M40 82 V110 L16 152 Q12 160 21 160 H79 Q88 160 84 152 L60 110 V82" fill="none" stroke="#000" stroke-width="5" stroke-linecap="round" stroke-linejoin="round"/><path d="M33 82 H67" fill="none" stroke="#000" stroke-width="5" stroke-linecap="round"/></svg>
                <span class="wortmarke">Rätsel<span class="blau">labor</span></span>
              </span>
              <span class="tuer-feld"></span>
              <span class="tuer-griff"></span>
            </span>
            <span class="tuer-hinten"></span>
          </span>
        </span>
        <span class="tuer-licht" aria-hidden="true"></span>
      </a>

      <a class="adresse" href="<?= e(RAETSELLABOR_URL) ?>" target="_blank" rel="noopener"><?= e(preg_replace('#^https?://#', '', RAETSELLABOR_URL)) ?> <?= icon('external-link') ?></a>
    </aside>
  </div>
</section>

<?php require __DIR__ . '/inc/fuss.php'; ?>
