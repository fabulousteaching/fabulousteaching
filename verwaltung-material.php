<?php
/**
 * Verwaltung: Material hochladen oder bearbeiten (nur für Administratoren).
 * Ablauf wie bei einer Lernplattform: 1 Datei · 2 Beschreibung · 3 Einordnung · 4 Vorschau & Sichtbarkeit
 */
require __DIR__ . '/inc/start.php';
verwaltung_erforderlich();

$inhalt      = inhalt_laden();
$faecher     = $inhalt['faecher'];
$zyklen      = $inhalt['zyklen'];
$materialien = $inhalt['materialien'];

$id    = (string) ($_GET['id'] ?? $_POST['id'] ?? '');
$index = $id !== '' ? material_finden($materialien, $id) : null;
if ($id !== '' && $index === null && $_SERVER['REQUEST_METHOD'] !== 'POST') {
    meldung_setzen('Dieses Material gibt es nicht (mehr).', 'fehler');
    header('Location: verwaltung.php');
    exit;
}
$alt = $index !== null ? $materialien[$index] : null;
$m   = $alt ?? material_normalisieren(['id' => bin2hex(random_bytes(6)), 'status' => 'veroeffentlicht']);
$quelle = $m['link'] !== '' ? 'link' : 'datei';
$fehler = [];
$limit  = upload_limit();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && empty($_POST) && (int) ($_SERVER['CONTENT_LENGTH'] ?? 0) > 0) {
    // Der Server hat die ganze Anfrage verworfen, weil sie zu gross war
    $fehler[] = 'Die Datei ist zu gross. Der Server nimmt höchstens ' . groesse_text($limit) . ' an.';
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_pruefen()) {
        $fehler[] = 'Das Formular ist abgelaufen. Bitte noch einmal speichern.';
    } else {
        // ---------- Eingaben übernehmen ----------
        $text = fn(string $feld, int $max) => trim(mb_substr((string) ($_POST[$feld] ?? ''), 0, $max));
        $m['titel']        = $text('titel', 140);
        $m['beschreibung'] = $text('beschreibung', 600);
        $m['typ']          = $text('typ', 40);
        $m['umfang']       = $text('umfang', 40);
        $m['faecher']      = array_values(array_intersect(array_keys($faecher), array_map('strval', (array) ($_POST['faecher'] ?? []))));
        $m['zyklen']       = array_values(array_intersect(array_map('intval', array_keys($zyklen)), array_map('intval', (array) ($_POST['zyklen'] ?? []))));
        $tags = array_map(fn($t) => trim(mb_substr($t, 0, 40)), explode(',', $text('schlagwoerter', 800)));
        $m['schlagwoerter'] = array_slice(array_values(array_unique(array_filter($tags, 'strlen'))), 0, 20);
        $quelle            = ($_POST['quelle'] ?? '') === 'link' ? 'link' : 'datei';
        $neuerLink         = $text('link', 500);
        $m['nur_angemeldet'] = ($_POST['zugriff'] ?? '') === 'angemeldet';
        $m['neu']          = !empty($_POST['neu']);
        $m['status']       = ($_POST['status'] ?? '') === 'entwurf' ? 'entwurf' : 'veroeffentlicht';

        // ---------- Prüfen ----------
        if ($m['titel'] === '') {
            $fehler[] = 'Bitte gib einen Titel ein.';
        }
        if (!$m['faecher']) {
            $fehler[] = 'Bitte wähle mindestens ein Fach.';
        }
        if ($quelle === 'link' && !preg_match('#^https?://[^\s]+$#i', $neuerLink)) {
            $fehler[] = 'Bitte gib eine vollständige Webadresse ein (beginnt mit https://).';
        }
        $upload = $_FILES['datei'] ?? null;
        $hatUpload = $quelle === 'datei' && $upload && ($upload['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_NO_FILE;

        // ---------- Dateien ----------
        if (!$fehler) {
            if ($hatUpload) {
                $r = material_datei_annehmen($upload, $m['id'], $m['nur_angemeldet']);
                if ($r['ok']) {
                    if ($alt) {
                        eigene_datei_loeschen(material_datei_pfad($alt));
                    }
                    $m['datei'] = $r['datei'];
                    $m['datei_pfad'] = $r['datei_pfad'];
                    $m['datei_name'] = $r['datei_name'];
                    $m['link'] = '';
                } else {
                    $fehler[] = $r['fehler'];
                }
            } elseif ($quelle === 'link') {
                if ($alt && $alt['datei'] !== '') {
                    eigene_datei_loeschen(material_datei_pfad($alt));
                }
                $m['datei'] = $m['datei_pfad'] = $m['datei_name'] = '';
                $m['link'] = $neuerLink;
            } elseif ($alt) {
                // Keine neue Datei: bestehende Datei bei Bedarf zwischen öffentlich/geschützt verschieben
                $m = material_zugriff_wechseln($alt, $m);
            }
        }

        if (!$fehler) {
            $hinweise = [];
            // Vorschaubild (Probleme damit verhindern das Speichern nicht)
            if (!empty($_POST['bild_entfernen'])) {
                vorschaubild_loeschen($m['bild']);
                $m['bild'] = '';
            }
            $bildUpload = $_FILES['bild'] ?? null;
            if ($bildUpload && ($bildUpload['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_NO_FILE) {
                $neuesBild = vorschaubild_annehmen($bildUpload, $m['id'], $bildFehler);
                if ($neuesBild) {
                    vorschaubild_loeschen($m['bild']);
                    $m['bild'] = $neuesBild;
                } else {
                    $hinweise[] = 'Vorschaubild: ' . $bildFehler;
                }
            } elseif ($m['bild'] === '' && !empty($_POST['auto_vorschau']) && $m['datei'] !== '' && str_ends_with(strtolower($m['datei']), '.pdf')) {
                $auto = vorschaubild_aus_pdf((string) material_datei_pfad($m), $m['id']);
                if ($auto) {
                    $m['bild'] = $auto;
                }
            }

            $m['beispiel']  = false;
            $m['geaendert'] = date('c');
            if ($m['erstellt'] === '') {
                $m['erstellt'] = date('c');
            }
            if ($index === null) {
                array_unshift($materialien, $m);
            } else {
                $materialien[$index] = $m;
            }
            if (inhalt_speichern($faecher, $materialien)) {
                $was = $m['status'] === 'entwurf' ? 'als Entwurf gespeichert' : ($alt ? 'gespeichert' : 'veröffentlicht');
                meldung_setzen('«' . $m['titel'] . '» wurde ' . $was . '.' . ($hinweise ? ' ' . implode(' ', $hinweise) : ''), $hinweise ? 'info' : 'ok');
                header('Location: verwaltung.php');
                exit;
            }
            $fehler[] = 'Speichern hat nicht geklappt. Ist der Ordner «daten» auf dem Server beschreibbar?';
        }
    }
}

$neu   = $alt === null;
$seite = 'login';
$titel = $neu ? 'Material hochladen' : 'Material bearbeiten';
$skripte = ['assets/js/verwaltung.js'];
require __DIR__ . '/inc/kopf.php';
?>

<section class="abschnitt verwaltung">
  <div class="huelle">
    <a class="pfeil-link v-zurueck" href="verwaltung.php"><?= icon('chevron-left') ?> Zurück zur Übersicht</a>
    <h1><?= e($titel) ?></h1>

    <?php if ($fehler): ?>
      <div class="meldung meldung-fehler" role="alert"><?= icon('x') ?><span><?= implode('<br>', array_map('e', $fehler)) ?></span></div>
    <?php endif; ?>

    <form class="v-formular" method="post" action="verwaltung-material.php<?= $neu ? '' : '?id=' . e($m['id']) ?>" enctype="multipart/form-data" novalidate>
      <?= csrf_feld() ?>
      <input type="hidden" name="id" value="<?= $neu ? '' : e($m['id']) ?>">

      <div class="v-raster">
        <div class="v-schritte">

          <!-- 1 Datei -->
          <fieldset class="v-schritt">
            <legend><span class="v-nr">1</span> Datei</legend>
            <div class="v-umschalter" role="radiogroup" aria-label="Art des Materials">
              <label><input type="radio" name="quelle" value="datei"<?= $quelle === 'datei' ? ' checked' : '' ?>> Datei hochladen</label>
              <label><input type="radio" name="quelle" value="link"<?= $quelle === 'link' ? ' checked' : '' ?>> Link (z. B. Online-Übung)</label>
            </div>

            <div class="v-quelle" data-quelle="datei">
              <label class="dropzone" data-limit="<?= $limit ?>">
                <input type="file" name="datei" accept=".<?= implode(',.', ERLAUBTE_ENDUNGEN) ?>">
                <?= icon('download', 'dropzone-icon') ?>
                <strong class="dropzone-text"><?= $m['datei'] ? 'Neue Datei wählen, um sie zu ersetzen' : 'Datei hierher ziehen oder klicken' ?></strong>
                <small>PDF, Word, PowerPoint, Excel, ZIP, Bilder, Audio · höchstens <?= e(groesse_text($limit)) ?></small>
                <span class="dropzone-datei" hidden></span>
              </label>
              <?php if ($m['datei']):
                $pfad = material_datei_pfad($m); ?>
                <p class="v-aktuell"><?= icon('file-text') ?> Aktuelle Datei: <strong><?= e($m['datei_name'] ?: $m['datei']) ?></strong>
                  <?= $pfad && is_file($pfad) ? '(' . e(groesse_text((int) filesize($pfad))) . ')' : '<em>(Datei fehlt auf dem Server)</em>' ?></p>
              <?php endif; ?>
            </div>

            <div class="v-quelle" data-quelle="link">
              <div class="feld">
                <label for="link">Webadresse</label>
                <input id="link" name="link" type="url" placeholder="https://…" value="<?= e($m['link']) ?>">
              </div>
            </div>
          </fieldset>

          <!-- 2 Beschreibung -->
          <fieldset class="v-schritt">
            <legend><span class="v-nr">2</span> Beschreibung</legend>
            <div class="feld">
              <label for="titel">Titel <span class="pflicht">*</span></label>
              <input id="titel" name="titel" type="text" maxlength="140" required value="<?= e($m['titel']) ?>" placeholder="z. B. Wortarten-Detektive">
            </div>
            <div class="feld">
              <label for="beschreibung">Kurzbeschreibung</label>
              <textarea id="beschreibung" name="beschreibung" rows="4" maxlength="600" placeholder="Worum geht es? Was machen die Kinder damit?"><?= e($m['beschreibung']) ?></textarea>
              <small><span data-zaehler="beschreibung"><?= mb_strlen($m['beschreibung']) ?></span> / 600 Zeichen</small>
            </div>
          </fieldset>

          <!-- 3 Einordnung -->
          <fieldset class="v-schritt">
            <legend><span class="v-nr">3</span> Einordnung</legend>
            <div class="feld">
              <span class="feld-titel">Fach <span class="pflicht">*</span> <small>(eines oder mehrere)</small></span>
              <div class="auswahl">
                <?php foreach ($faecher as $k => $f): ?>
                  <label class="auswahl-chip" style="--f:<?= e($f['farbe']) ?>">
                    <input type="checkbox" name="faecher[]" value="<?= e($k) ?>" data-name="<?= e($f['name']) ?>" data-farbe="<?= e($f['farbe']) ?>"<?= in_array($k, $m['faecher'], true) ? ' checked' : '' ?>>
                    <?= icon($f['icon']) ?><span><?= e($f['name']) ?></span>
                  </label>
                <?php endforeach; ?>
              </div>
              <small>Fehlt ein Fach? <a href="verwaltung-faecher.php">Fächer verwalten</a></small>
            </div>
            <div class="feld">
              <span class="feld-titel">Stufe</span>
              <div class="auswahl">
                <?php foreach ($zyklen as $nr => $name): ?>
                  <label class="auswahl-chip">
                    <input type="checkbox" name="zyklen[]" value="<?= (int) $nr ?>" data-name="<?= e($name) ?>"<?= in_array((int) $nr, $m['zyklen'], true) ? ' checked' : '' ?>>
                    <span><?= e($name) ?></span>
                  </label>
                <?php endforeach; ?>
              </div>
            </div>
            <div class="feld-reihe">
              <div class="feld">
                <label for="typ">Materialart</label>
                <input id="typ" name="typ" type="text" list="materialarten" maxlength="40" value="<?= e($m['typ']) ?>" placeholder="z. B. Arbeitsblatt">
                <datalist id="materialarten"><?php foreach (MATERIALARTEN as $a): ?><option value="<?= e($a) ?>"><?php endforeach; ?></datalist>
              </div>
              <div class="feld">
                <label for="umfang">Umfang</label>
                <input id="umfang" name="umfang" type="text" maxlength="40" value="<?= e($m['umfang']) ?>" placeholder="z. B. 12 Seiten">
              </div>
            </div>
            <div class="feld">
              <label for="schlagwoerter">Schlagwörter</label>
              <input id="schlagwoerter" name="schlagwoerter" type="text" maxlength="800" value="<?= e(implode(', ', $m['schlagwoerter'])) ?>" placeholder="mit Komma trennen, z. B. Nomen, Verben, Grammatik">
              <small>Helfen beim Suchen auf der Materialseite.</small>
            </div>
          </fieldset>

          <!-- 4 Vorschau & Sichtbarkeit -->
          <fieldset class="v-schritt">
            <legend><span class="v-nr">4</span> Vorschau &amp; Sichtbarkeit</legend>
            <div class="feld">
              <label for="bild">Vorschaubild</label>
              <input id="bild" name="bild" type="file" accept="image/jpeg,image/png,image/webp,image/gif">
              <small>JPG, PNG oder WebP – z. B. ein Foto oder die erste Seite des Materials.</small>
              <label class="v-check"><input type="checkbox" name="auto_vorschau" value="1" checked> Ohne eigenes Bild: Vorschau aus der ersten PDF-Seite erstellen (falls der Server das kann)</label>
              <?php if ($m['bild']): ?>
                <label class="v-check"><input type="checkbox" name="bild_entfernen" value="1" id="bild_entfernen"> Aktuelles Vorschaubild entfernen</label>
              <?php endif; ?>
            </div>
            <div class="feld">
              <span class="feld-titel">Wer darf herunterladen?</span>
              <div class="v-umschalter">
                <label><input type="radio" name="zugriff" value="alle"<?= !$m['nur_angemeldet'] ? ' checked' : '' ?>> Alle</label>
                <label><input type="radio" name="zugriff" value="angemeldet"<?= $m['nur_angemeldet'] ? ' checked' : '' ?>> <?= icon('lock') ?> Nur nach dem Login</label>
              </div>
            </div>
            <div class="feld">
              <span class="feld-titel">Status</span>
              <div class="v-umschalter">
                <label><input type="radio" name="status" value="veroeffentlicht"<?= $m['status'] === 'veroeffentlicht' ? ' checked' : '' ?>> Veröffentlicht</label>
                <label><input type="radio" name="status" value="entwurf"<?= $m['status'] === 'entwurf' ? ' checked' : '' ?>> Entwurf (nur für mich sichtbar)</label>
              </div>
            </div>
            <label class="v-check"><input type="checkbox" name="neu" value="1"<?= $m['neu'] ? ' checked' : '' ?>> Als «Neu» markieren</label>
          </fieldset>

          <div class="v-speichern">
            <button class="knopf" type="submit"><?= icon('shield-check') ?> <?= $neu ? 'Speichern' : 'Änderungen speichern' ?></button>
            <a class="pfeil-link" href="verwaltung.php">Abbrechen</a>
          </div>
        </div>

        <aside class="v-vorschau" aria-label="Vorschau">
          <span class="vorzeile-klein">Vorschau</span>
          <p class="v-vorschau-text">So erscheint die Karte bei den Unterrichtsmaterialien:</p>
          <div class="v-vorschau-karte" data-zyklen='<?= e(json_encode(array_map('strval', $zyklen), JSON_UNESCAPED_UNICODE)) ?>'>
            <?= material_karte($m, $faecher, $zyklen) ?>
          </div>
        </aside>
      </div>
    </form>
  </div>
</section>

<?php require __DIR__ . '/inc/fuss.php'; ?>
