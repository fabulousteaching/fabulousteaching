<?php
/**
 * Verwaltung: Übersicht aller Materialien (nur für Administratoren).
 */
require __DIR__ . '/inc/start.php';
verwaltung_erforderlich();

$inhalt      = inhalt_laden();
$faecher     = $inhalt['faecher'];
$zyklen      = $inhalt['zyklen'];
$materialien = $inhalt['materialien'];

// ---------- Aktionen ----------
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_pruefen()) {
        meldung_setzen('Das Formular ist abgelaufen. Bitte noch einmal versuchen.', 'fehler');
    } else {
        $aktion = (string) ($_POST['aktion'] ?? '');
        $i = material_finden($materialien, (string) ($_POST['id'] ?? ''));
        $ok = null;

        if ($aktion === 'loeschen' && $i !== null) {
            $m = $materialien[$i];
            eigene_datei_loeschen(material_datei_pfad($m));
            vorschaubild_loeschen($m['bild']);
            array_splice($materialien, $i, 1);
            $ok = inhalt_speichern($faecher, $materialien);
            $text = '«' . $m['titel'] . '» wurde gelöscht.';
        } elseif ($aktion === 'status' && $i !== null) {
            $m = &$materialien[$i];
            $m['status'] = $m['status'] === 'entwurf' ? 'veroeffentlicht' : 'entwurf';
            $m['geaendert'] = date('c');
            $text = '«' . $m['titel'] . '» ist jetzt ' . ($m['status'] === 'entwurf' ? 'ein Entwurf (nicht sichtbar).' : 'veröffentlicht.');
            unset($m);
            $ok = inhalt_speichern($faecher, $materialien);
        } elseif (($aktion === 'hoch' || $aktion === 'runter') && $i !== null) {
            $j = $aktion === 'hoch' ? $i - 1 : $i + 1;
            if (isset($materialien[$j])) {
                [$materialien[$i], $materialien[$j]] = [$materialien[$j], $materialien[$i]];
                $ok = inhalt_speichern($faecher, $materialien);
                $text = 'Reihenfolge gespeichert.';
            }
        } elseif ($aktion === 'beispiele_loeschen') {
            $vorher = count($materialien);
            $materialien = array_values(array_filter($materialien, fn($m) => !$m['beispiel']));
            $ok = inhalt_speichern($faecher, $materialien);
            $text = ($vorher - count($materialien)) . ' Beispiel-Einträge gelöscht.';
        }

        if ($ok === true) {
            meldung_setzen($text);
        } elseif ($ok === false) {
            meldung_setzen('Speichern hat nicht geklappt. Ist der Ordner «daten» auf dem Server beschreibbar?', 'fehler');
        }
    }
    header('Location: verwaltung.php');
    exit;
}

$anzahlEntwurf  = count(array_filter($materialien, fn($m) => $m['status'] === 'entwurf'));
$anzahlBeispiel = count(array_filter($materialien, fn($m) => $m['beispiel']));

$seite = 'login';
$titel = 'Verwaltung';
require __DIR__ . '/inc/kopf.php';
?>

<section class="abschnitt verwaltung">
  <div class="huelle">
    <div class="v-kopf">
      <div>
        <span class="vorzeile-klein">Verwaltung</span>
        <h1>Meine Materialien</h1>
        <p class="v-zahlen">
          <span><?= count($materialien) - $anzahlEntwurf ?> veröffentlicht</span>
          <span><?= $anzahlEntwurf ?> Entwürfe</span>
          <span><?= count($faecher) ?> Fächer</span>
        </p>
      </div>
      <div class="knopf-reihe">
        <a class="knopf" href="verwaltung-material.php"><?= icon('download', 'icon-hoch') ?> Material hochladen</a>
        <a class="knopf knopf-hell" href="verwaltung-faecher.php"><?= icon('book-open') ?> Fächer verwalten</a>
      </div>
    </div>

    <?= meldung_ausgeben() ?>

    <?php if (!$inhalt['gespeichert']): ?>
      <div class="meldung meldung-info"><?= icon('sparkles') ?><span>Hier siehst du die Einträge aus der Grundeinstellung. Sobald du etwas hochlädst oder änderst, werden deine Materialien auf dem Server gespeichert.</span></div>
    <?php endif; ?>

    <?php if ($anzahlBeispiel): ?>
      <form class="meldung meldung-info v-beispiele" method="post" onsubmit="return confirm('Alle Beispiel-Einträge löschen?')">
        <?= icon('file-text') ?>
        <span>Es gibt noch <?= $anzahlBeispiel ?> Beispiel-Einträge (Platzhalter).</span>
        <?= csrf_feld() ?><input type="hidden" name="aktion" value="beispiele_loeschen">
        <button class="knopf knopf-klein knopf-hell" type="submit">Alle Beispiele löschen</button>
      </form>
    <?php endif; ?>

    <?php if (!$materialien): ?>
      <div class="leer">
        <?= icon('folder-lock') ?>
        <h3>Noch keine Materialien</h3>
        <p>Lade dein erstes Material hoch – es erscheint dann bei den Unterrichtsmaterialien.</p>
        <a class="knopf knopf-klein" href="verwaltung-material.php">Material hochladen</a>
      </div>
    <?php else: ?>
      <ul class="v-liste">
        <?php foreach ($materialien as $i => $m):
          $erstes = $faecher[$m['faecher'][0] ?? ''] ?? ['icon' => 'file-text', 'farbe' => '#f7a15c'];
          $stufen = array_map(fn($z) => $zyklen[$z] ?? '', $m['zyklen']); ?>
          <li class="v-eintrag<?= $m['status'] === 'entwurf' ? ' ist-entwurf' : '' ?>" style="--f:<?= e($erstes['farbe']) ?>">
            <div class="v-daumen">
              <?php if ($m['bild']): ?><img src="<?= e($m['bild']) ?>" alt="" loading="lazy"><?php else: ?><?= icon($erstes['icon']) ?><?php endif; ?>
            </div>
            <div class="v-info">
              <div class="v-titelzeile">
                <strong><?= e($m['titel'] ?: '(ohne Titel)') ?></strong>
                <?php if ($m['status'] === 'entwurf'): ?><span class="badge badge-entwurf">Entwurf</span><?php endif; ?>
                <?php if ($m['beispiel']): ?><span class="badge badge-beispiel">Beispiel</span><?php endif; ?>
                <?php if ($m['neu']): ?><span class="badge badge-neu">Neu</span><?php endif; ?>
                <?php if ($m['nur_angemeldet']): ?><span class="badge"><?= icon('lock') ?> Login</span><?php endif; ?>
              </div>
              <span class="v-meta">
                <?= e(fach_namen($m, $faecher) ?: 'Kein Fach') ?>
                <?= $m['typ'] ? ' · ' . e($m['typ']) : '' ?>
                <?= $stufen ? ' · ' . e(implode(', ', array_filter($stufen))) : '' ?>
              </span>
              <span class="v-datei">
                <?php if ($m['link']): ?><?= icon('external-link') ?> <?= e($m['link']) ?>
                <?php elseif ($m['datei']): ?><?= icon('file-text') ?> <?= e($m['datei_name'] ?: $m['datei']) ?>
                <?php else: ?><?= icon('file-text') ?> noch keine Datei<?php endif; ?>
              </span>
            </div>
            <div class="v-knoepfe">
              <a class="knopf knopf-klein" href="verwaltung-material.php?id=<?= e($m['id']) ?>"><?= icon('pencil') ?> Bearbeiten</a>
              <form method="post"><?= csrf_feld() ?><input type="hidden" name="id" value="<?= e($m['id']) ?>"><input type="hidden" name="aktion" value="status">
                <button class="knopf knopf-klein knopf-hell" type="submit"><?= $m['status'] === 'entwurf' ? icon('sparkles') . ' Veröffentlichen' : icon('lock') . ' Zurückziehen' ?></button>
              </form>
              <form method="post" class="v-ordnen"><?= csrf_feld() ?><input type="hidden" name="id" value="<?= e($m['id']) ?>">
                <button class="v-mini" type="submit" name="aktion" value="hoch" title="Nach oben"<?= $i === 0 ? ' disabled' : '' ?>><?= icon('chevron-left') ?><span class="sr">Nach oben</span></button>
                <button class="v-mini" type="submit" name="aktion" value="runter" title="Nach unten"<?= $i === count($materialien) - 1 ? ' disabled' : '' ?>><?= icon('chevron-right') ?><span class="sr">Nach unten</span></button>
              </form>
              <form method="post" onsubmit="return confirm('«<?= e(addslashes($m['titel'])) ?>» wirklich löschen? Die Datei wird ebenfalls gelöscht.')">
                <?= csrf_feld() ?><input type="hidden" name="id" value="<?= e($m['id']) ?>"><input type="hidden" name="aktion" value="loeschen">
                <button class="v-mini v-loeschen" type="submit" title="Löschen"><?= icon('x') ?><span class="sr">Löschen</span></button>
              </form>
            </div>
          </li>
        <?php endforeach; ?>
      </ul>
      <p class="v-hinweis">Die Reihenfolge hier ist auch die Reihenfolge auf der Website. Entwürfe sind nur für dich sichtbar.</p>
    <?php endif; ?>
  </div>
</section>

<?php require __DIR__ . '/inc/fuss.php'; ?>
