<?php
/**
 * Verwaltung: Fächer anlegen, umbenennen, sortieren und löschen (nur für Administratoren).
 * Die Fächer erscheinen als Reiter bei den Unterrichtsmaterialien.
 */
require __DIR__ . '/inc/start.php';
verwaltung_erforderlich();

$inhalt      = inhalt_laden();
$faecher     = $inhalt['faecher'];
$materialien = $inhalt['materialien'];

const SYMBOL_NAMEN = [
    'book-open' => 'Buch', 'calculator' => 'Rechner', 'earth' => 'Erde', 'flask-conical' => 'Kolben', 'atom' => 'Atom',
    'microscope' => 'Mikroskop', 'languages' => 'Sprachen', 'palette' => 'Farben', 'scissors' => 'Schere', 'music' => 'Musik',
    'volleyball' => 'Ball', 'laptop' => 'Laptop', 'puzzle' => 'Puzzle', 'pencil' => 'Stift', 'graduation-cap' => 'Hut',
    'heart' => 'Herz', 'sparkles' => 'Funken', 'file-text' => 'Blatt',
];

/** Wie viele Materialien gehören zu einem Fach? */
function fach_anzahl(array $materialien, string $k): int
{
    return count(array_filter($materialien, fn($m) => in_array($k, $m['faecher'], true)));
}

function fach_eingaben(): array
{
    $farbe = (string) ($_POST['farbe'] ?? '');
    $icon  = (string) ($_POST['icon'] ?? '');
    return [
        'name'  => trim(mb_substr((string) ($_POST['name'] ?? ''), 0, 40)),
        'icon'  => in_array($icon, FACH_SYMBOLE, true) ? $icon : 'book-open',
        'farbe' => preg_match('/^#[0-9a-fA-F]{6}$/', $farbe) ? strtolower($farbe) : '#f7a15c',
    ];
}

// ---------- Aktionen ----------
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $aktion = (string) ($_POST['aktion'] ?? '');
    $k = (string) ($_POST['schluessel'] ?? '');
    $ok = null;
    if (!csrf_pruefen()) {
        meldung_setzen('Das Formular ist abgelaufen. Bitte noch einmal versuchen.', 'fehler');
    } elseif ($aktion === 'hinzufuegen') {
        $f = fach_eingaben();
        if ($f['name'] === '') {
            meldung_setzen('Bitte gib einen Namen für das Fach ein.', 'fehler');
        } else {
            $basis = datei_slug($f['name']);
            $neu = $basis;
            for ($n = 2; isset($faecher[$neu]); $n++) {
                $neu = $basis . '-' . $n;
            }
            $faecher[$neu] = $f;
            $ok = inhalt_speichern($faecher, $materialien);
            $text = 'Fach «' . $f['name'] . '» hinzugefügt.';
        }
    } elseif ($aktion === 'aendern' && isset($faecher[$k])) {
        $f = fach_eingaben();
        if ($f['name'] === '') {
            meldung_setzen('Der Name darf nicht leer sein.', 'fehler');
        } else {
            $faecher[$k] = $f;
            $ok = inhalt_speichern($faecher, $materialien);
            $text = 'Fach «' . $f['name'] . '» gespeichert.';
        }
    } elseif ($aktion === 'loeschen' && isset($faecher[$k])) {
        $anzahl = fach_anzahl($materialien, $k);
        if ($anzahl > 0) {
            meldung_setzen('«' . $faecher[$k]['name'] . '» hat noch ' . $anzahl . ' Material(ien). Ordne sie zuerst einem anderen Fach zu.', 'fehler');
        } else {
            $name = $faecher[$k]['name'];
            unset($faecher[$k]);
            $ok = inhalt_speichern($faecher, $materialien);
            $text = 'Fach «' . $name . '» gelöscht.';
        }
    } elseif (($aktion === 'hoch' || $aktion === 'runter') && isset($faecher[$k])) {
        $keys = array_keys($faecher);
        $i = array_search($k, $keys, true);
        $j = $aktion === 'hoch' ? $i - 1 : $i + 1;
        if (isset($keys[$j])) {
            [$keys[$i], $keys[$j]] = [$keys[$j], $keys[$i]];
            $faecher = array_combine($keys, array_map(fn($x) => $faecher[$x], $keys));
            $ok = inhalt_speichern($faecher, $materialien);
            $text = 'Reihenfolge gespeichert.';
        }
    }
    if ($ok === true) {
        meldung_setzen($text);
    } elseif ($ok === false) {
        meldung_setzen('Speichern hat nicht geklappt. Ist der Ordner «daten» auf dem Server beschreibbar?', 'fehler');
    }
    header('Location: verwaltung-faecher.php');
    exit;
}

/** Eingabefelder für Name, Farbe und Symbol. */
function fach_felder(array $f, string $praefix): string
{
    ob_start(); ?>
    <div class="feld-reihe">
      <div class="feld">
        <label for="<?= $praefix ?>-name">Name</label>
        <input id="<?= $praefix ?>-name" name="name" type="text" maxlength="40" required value="<?= e($f['name']) ?>" placeholder="z. B. Deutsch">
      </div>
      <div class="feld feld-farbe">
        <label for="<?= $praefix ?>-farbe">Farbe</label>
        <input id="<?= $praefix ?>-farbe" name="farbe" type="color" value="<?= e($f['farbe']) ?>">
      </div>
    </div>
    <div class="feld">
      <span class="feld-titel">Symbol</span>
      <div class="symbol-auswahl" style="--f:<?= e($f['farbe']) ?>">
        <?php foreach (SYMBOL_NAMEN as $icon => $name): ?>
          <label title="<?= e($name) ?>"><input type="radio" name="icon" value="<?= e($icon) ?>"<?= $f['icon'] === $icon ? ' checked' : '' ?>><?= icon($icon) ?><span class="sr"><?= e($name) ?></span></label>
        <?php endforeach; ?>
      </div>
    </div>
    <?php
    return (string) ob_get_clean();
}

$seite = 'login';
$titel = 'Fächer verwalten';
$skripte = ['assets/js/verwaltung.js'];
require __DIR__ . '/inc/kopf.php';
$keys = array_keys($faecher);
?>

<section class="abschnitt verwaltung">
  <div class="huelle v-schmal">
    <a class="pfeil-link v-zurueck" href="verwaltung.php"><?= icon('chevron-left') ?> Zurück zur Übersicht</a>
    <h1>Fächer verwalten</h1>
    <p>Jedes Fach erscheint als Reiter bei den Unterrichtsmaterialien – in dieser Reihenfolge.</p>

    <?= meldung_ausgeben() ?>

    <ul class="v-faecher">
      <?php foreach ($faecher as $k => $f):
        $anzahl = fach_anzahl($materialien, $k);
        $pos = array_search($k, $keys, true); ?>
        <li class="v-fach" style="--f:<?= e($f['farbe']) ?>">
          <div class="v-fach-zeile">
            <span class="v-fach-symbol"><?= icon($f['icon']) ?></span>
            <strong><?= e($f['name']) ?></strong>
            <span class="v-fach-anzahl"><?= $anzahl ?> <?= $anzahl === 1 ? 'Material' : 'Materialien' ?></span>
            <form method="post" class="v-ordnen"><?= csrf_feld() ?><input type="hidden" name="schluessel" value="<?= e($k) ?>">
              <button class="v-mini" type="submit" name="aktion" value="hoch" title="Nach vorne"<?= $pos === 0 ? ' disabled' : '' ?>><?= icon('chevron-left') ?><span class="sr">Nach vorne</span></button>
              <button class="v-mini" type="submit" name="aktion" value="runter" title="Nach hinten"<?= $pos === count($keys) - 1 ? ' disabled' : '' ?>><?= icon('chevron-right') ?><span class="sr">Nach hinten</span></button>
            </form>
            <form method="post" onsubmit="return confirm('Fach «<?= e(addslashes($f['name'])) ?>» löschen?')"><?= csrf_feld() ?>
              <input type="hidden" name="schluessel" value="<?= e($k) ?>"><input type="hidden" name="aktion" value="loeschen">
              <button class="v-mini v-loeschen" type="submit" title="Löschen"<?= $anzahl > 0 ? ' disabled' : '' ?>><?= icon('x') ?><span class="sr">Löschen</span></button>
            </form>
          </div>
          <details>
            <summary><?= icon('pencil') ?> Bearbeiten</summary>
            <form method="post" class="v-fach-form"><?= csrf_feld() ?>
              <input type="hidden" name="schluessel" value="<?= e($k) ?>"><input type="hidden" name="aktion" value="aendern">
              <?= fach_felder($f, 'f-' . $k) ?>
              <button class="knopf knopf-klein" type="submit">Speichern</button>
            </form>
          </details>
        </li>
      <?php endforeach; ?>
    </ul>

    <form method="post" class="v-schritt v-fach-neu"><?= csrf_feld() ?>
      <input type="hidden" name="aktion" value="hinzufuegen">
      <h2><?= icon('sparkles') ?> Neues Fach</h2>
      <?= fach_felder(['name' => '', 'icon' => 'book-open', 'farbe' => '#3d8fd6'], 'neu') ?>
      <button class="knopf" type="submit">Fach hinzufügen</button>
    </form>
  </div>
</section>

<?php require __DIR__ . '/inc/fuss.php'; ?>
