<?php
require __DIR__ . '/inc/start.php';

$seite        = 'uebungslabor';
$titel        = 'Übungslabor';
$beschreibung = 'Im Übungslabor ein Fach wählen und üben: Mathematik, Englisch und NMG.';

/*
 * Fächer im Übungslabor.
 * Übungen später unter 'uebungen' eintragen, zum Beispiel:
 *   ['titel' => 'Reihen-Treppe', 'beschreibung' => 'Eine Reihe hinauf und hinunter.', 'link' => 'https://…'],
 */
$uebungsfaecher = [
    'mathematik' => [
        'name' => 'Mathematik', 'icon' => 'calculator', 'farbe' => '#3d8fd6',
        'text' => 'Zahlen, Rechnen und Knobeln',
        'uebungen' => [],
    ],
    'englisch' => [
        'name' => 'Englisch', 'icon' => 'languages', 'farbe' => '#8a63d2',
        'text' => 'Wörter, Sätze und Hören',
        'uebungen' => [],
    ],
    'nmg' => [
        'name' => 'NMG', 'icon' => 'earth', 'farbe' => '#3aa856',
        'text' => 'Natur, Mensch, Gesellschaft',
        'uebungen' => [],
    ],
];

require __DIR__ . '/inc/kopf.php';
?>

<section class="seitenkopf">
  <div class="huelle">
    <div class="seitenkopf-karte kritzel">
      <div class="deko" aria-hidden="true"><?= icon('flask-conical') ?></div>
      <div class="seitenkopf-inhalt">
        <h1>Übungslabor</h1>
        <p>Hier wird geübt: Wähle ein Fach und leg los.</p>
      </div>
    </div>
  </div>
</section>

<section class="abschnitt uebungslabor">
  <div class="huelle">
    <h2 class="ul-frage">Welches Fach möchtest du üben?</h2>

    <nav class="ul-faecher" aria-label="Fach wählen">
      <?php foreach ($uebungsfaecher as $k => $f): ?>
        <a class="ul-fach" href="#<?= e($k) ?>" data-fach="<?= e($k) ?>" aria-current="false" style="--f:<?= e($f['farbe']) ?>">
          <span class="ul-fach-symbol"><?= icon($f['icon']) ?></span>
          <span class="ul-fach-name"><?= e($f['name']) ?></span>
          <span class="ul-fach-text"><?= e($f['text']) ?></span>
        </a>
      <?php endforeach; ?>
    </nav>

    <?php foreach ($uebungsfaecher as $k => $f): ?>
      <section class="ul-bereich" id="<?= e($k) ?>" style="--f:<?= e($f['farbe']) ?>" aria-labelledby="ul-titel-<?= e($k) ?>">
        <h2 id="ul-titel-<?= e($k) ?>"><span class="ul-bereich-symbol"><?= icon($f['icon']) ?></span><?= e($f['name']) ?></h2>
        <?php if ($f['uebungen']): ?>
          <ul class="ul-uebungen">
            <?php foreach ($f['uebungen'] as $u): ?>
              <li class="ul-uebung">
                <h3><?= e($u['titel']) ?></h3>
                <?php if (!empty($u['beschreibung'])): ?><p><?= e($u['beschreibung']) ?></p><?php endif; ?>
                <a class="knopf knopf-klein" href="<?= e($u['link']) ?>" target="_blank" rel="noopener">Üben <?= icon('arrow-right') ?></a>
              </li>
            <?php endforeach; ?>
          </ul>
        <?php else: ?>
          <div class="ul-leer">
            <?= icon('sparkles') ?>
            <p><strong>Hier entstehen bald Übungen für <?= e($f['name']) ?>.</strong><br>Schau bald wieder vorbei!</p>
          </div>
        <?php endif; ?>
      </section>
    <?php endforeach; ?>
  </div>
</section>

<?php require __DIR__ . '/inc/fuss.php'; ?>
