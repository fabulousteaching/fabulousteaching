<?php
/**
 * Inhalte der Website: Fächer und Unterrichtsmaterialien.
 *
 * - Grundeinstellung (Fächer, Zyklen, Beispiele) steht in daten/materialien.php (Repository).
 * - Sobald in der Verwaltung etwas gespeichert wird, gilt daten/inhalt.json (nur auf dem Server).
 *   Vor jedem Speichern wird die alte Fassung in daten/sicherung/ abgelegt.
 * - Hochgeladene Dateien: öffentlich in uploads/materialien/, geschützt in daten/dateien/,
 *   Vorschaubilder in uploads/vorschau/.
 */

const ERLAUBTE_ENDUNGEN = ['pdf', 'doc', 'docx', 'odt', 'rtf', 'txt', 'ppt', 'pptx', 'odp', 'key',
    'xls', 'xlsx', 'ods', 'zip', 'png', 'jpg', 'jpeg', 'mp3', 'm4a', 'mp4'];
const BILD_ENDUNGEN = ['jpg', 'jpeg', 'png', 'webp', 'gif'];
const MATERIALARTEN = ['Arbeitsblatt', 'Werkstatt', 'Unterrichtseinheit', 'Lernkontrolle', 'Spiel',
    'Präsentation', 'Poster', 'Karteikarten', 'Online'];
const FACH_SYMBOLE = ['book-open', 'calculator', 'earth', 'flask-conical', 'atom', 'microscope', 'languages',
    'palette', 'scissors', 'music', 'volleyball', 'laptop', 'puzzle', 'pencil', 'graduation-cap', 'heart', 'sparkles', 'file-text'];

// ---------- Lesen ----------

/** Fächer, Zyklen und alle Materialien (inkl. Entwürfe). */
function inhalt_laden(bool $neu = false): array
{
    static $cache = null;
    if ($cache !== null && !$neu) {
        return $cache;
    }
    $basis = include MATERIAL_DATEI;
    $json  = is_file(INHALT_DATEI) ? json_decode((string) file_get_contents(INHALT_DATEI), true) : null;
    $quelle = is_array($json) ? $json : ['faecher' => $basis['faecher'] ?? [], 'materialien' => $basis['materialien'] ?? []];

    $materialien = [];
    foreach (array_values($quelle['materialien'] ?? []) as $i => $m) {
        if (is_array($m)) {
            $materialien[] = material_normalisieren($m, $i);
        }
    }
    return $cache = [
        'faecher'     => is_array($quelle['faecher'] ?? null) ? $quelle['faecher'] : [],
        'zyklen'      => $basis['zyklen'] ?? [],
        'materialien' => $materialien,
        'gespeichert' => is_array($json),
    ];
}

/** Für die öffentlichen Seiten: nur veröffentlichte Materialien. */
function materialien_laden(): array
{
    $d = inhalt_laden();
    $d['materialien'] = array_values(array_filter($d['materialien'], fn($m) => $m['status'] === 'veroeffentlicht'));
    return $d;
}

/** Jedes Material hat dieselben Felder – auch alte Einträge aus daten/materialien.php. */
function material_normalisieren(array $m, int $i = 0): array
{
    $faecher = $m['faecher'] ?? (isset($m['fach']) ? [$m['fach']] : []);
    $tags = $m['schlagwoerter'] ?? [];
    if (is_string($tags)) {
        $tags = array_filter(array_map('trim', explode(',', $tags)));
    }
    return [
        'id'             => (string) ($m['id'] ?? substr(md5(($m['titel'] ?? '') . '|' . $i), 0, 12)),
        'titel'          => (string) ($m['titel'] ?? ''),
        'beschreibung'   => (string) ($m['beschreibung'] ?? ''),
        'faecher'        => array_values(array_map('strval', (array) $faecher)),
        'zyklen'         => array_values(array_map('intval', (array) ($m['zyklen'] ?? []))),
        'typ'            => (string) ($m['typ'] ?? ''),
        'umfang'         => (string) ($m['umfang'] ?? ''),
        'schlagwoerter'  => array_values(array_map('strval', (array) $tags)),
        'bild'           => (string) ($m['bild'] ?? ''),
        'datei'          => (string) ($m['datei'] ?? ''),
        'datei_pfad'     => (string) ($m['datei_pfad'] ?? ''),
        'datei_name'     => (string) ($m['datei_name'] ?? ($m['datei'] ?? '')),
        'link'           => (string) ($m['link'] ?? ''),
        'nur_angemeldet' => !empty($m['nur_angemeldet']),
        'neu'            => !empty($m['neu']),
        'beispiel'       => !empty($m['beispiel']),
        'status'         => ($m['status'] ?? 'veroeffentlicht') === 'entwurf' ? 'entwurf' : 'veroeffentlicht',
        'erstellt'       => (string) ($m['erstellt'] ?? ''),
        'geaendert'      => (string) ($m['geaendert'] ?? ''),
    ];
}

/** Namen der Fächer eines Materials, z. B. «Deutsch · NMG». */
function fach_namen(array $m, array $faecher): string
{
    $namen = [];
    foreach ($m['faecher'] as $k) {
        if (isset($faecher[$k])) {
            $namen[] = $faecher[$k]['name'];
        }
    }
    return implode(' · ', $namen);
}

function material_finden(array $materialien, string $id): ?int
{
    foreach ($materialien as $i => $m) {
        if ($m['id'] === $id) {
            return $i;
        }
    }
    return null;
}

// ---------- Speichern ----------

function ordner_sicherstellen(string $pfad): bool
{
    return is_dir($pfad) || @mkdir($pfad, 0755, true);
}

/** Speichert Fächer und Materialien. Legt vorher eine Sicherung an (die letzten 30 bleiben). */
function inhalt_speichern(array $faecher, array $materialien): bool
{
    $lock = @fopen(INHALT_DATEI . '.lock', 'c');
    if ($lock) {
        flock($lock, LOCK_EX);
    }
    try {
        if (is_file(INHALT_DATEI) && ordner_sicherstellen(SICHERUNG_ORDNER)) {
            @copy(INHALT_DATEI, SICHERUNG_ORDNER . '/inhalt-' . date('Ymd-His') . '-' . bin2hex(random_bytes(2)) . '.json');
            $alte = glob(SICHERUNG_ORDNER . '/inhalt-*.json') ?: [];
            sort($alte);
            foreach (array_slice($alte, 0, max(0, count($alte) - 30)) as $f) {
                @unlink($f);
            }
        }
        $json = json_encode(['faecher' => $faecher, 'materialien' => array_values($materialien)],
            JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        $tmp = INHALT_DATEI . '.tmp';
        if ($json === false || file_put_contents($tmp, $json) === false) {
            return false;
        }
        $ok = rename($tmp, INHALT_DATEI);
        inhalt_laden(true);
        return $ok;
    } finally {
        if ($lock) {
            flock($lock, LOCK_UN);
            fclose($lock);
        }
    }
}

// ---------- Dateien ----------

/** «Wortarten Detektive (Kl. 3).pdf» → «wortarten-detektive-kl-3» */
function datei_slug(string $text): string
{
    $text = strtr(mb_strtolower($text, 'UTF-8'), ['ä' => 'ae', 'ö' => 'oe', 'ü' => 'ue', 'ß' => 'ss', 'é' => 'e', 'è' => 'e', 'à' => 'a', 'ç' => 'c']);
    $text = trim((string) preg_replace('/[^a-z0-9]+/', '-', $text), '-');
    return substr($text !== '' ? $text : 'material', 0, 60);
}

function ini_bytes(string $wert): int
{
    $wert = trim($wert);
    $zahl = (int) $wert;
    switch (strtolower(substr($wert, -1))) {
        case 'g': $zahl *= 1024;
        // weiter
        case 'm': $zahl *= 1024;
        // weiter
        case 'k': $zahl *= 1024;
    }
    return $zahl;
}

/** Grösste Datei, die der Server annimmt (in Bytes). */
function upload_limit(): int
{
    $werte = array_filter([ini_bytes((string) ini_get('upload_max_filesize')), ini_bytes((string) ini_get('post_max_size'))]);
    return $werte ? min($werte) : 2 * 1024 * 1024;
}

function groesse_text(int $bytes): string
{
    if ($bytes >= 1048576) {
        return number_format($bytes / 1048576, 1, '.', "'") . ' MB';
    }
    return max(1, (int) round($bytes / 1024)) . ' KB';
}

function upload_fehler_text(int $code): string
{
    return match ($code) {
        UPLOAD_ERR_INI_SIZE, UPLOAD_ERR_FORM_SIZE => 'Die Datei ist zu gross (höchstens ' . groesse_text(upload_limit()) . ').',
        UPLOAD_ERR_PARTIAL => 'Die Datei wurde nur teilweise übertragen. Bitte noch einmal versuchen.',
        UPLOAD_ERR_NO_TMP_DIR, UPLOAD_ERR_CANT_WRITE, UPLOAD_ERR_EXTENSION => 'Der Server konnte die Datei nicht speichern.',
        default => 'Beim Hochladen ist ein Fehler aufgetreten.',
    };
}

/** Ordner + Dateiname einer Materialdatei auf dem Server. */
function material_datei_pfad(array $m): ?string
{
    if ($m['datei'] === '') {
        return null;
    }
    if ($m['nur_angemeldet']) {
        return GESCHUETZT_ORDNER . '/' . basename($m['datei']);
    }
    if ($m['datei_pfad'] !== '') {
        return __DIR__ . '/../' . $m['datei_pfad'];
    }
    return __DIR__ . '/../downloads/' . basename($m['datei']);
}

/** Nur Dateien löschen, die die Verwaltung selbst abgelegt hat. */
function eigene_datei_loeschen(?string $pfad): void
{
    if ($pfad === null || !is_file($pfad)) {
        return;
    }
    $echt = realpath($pfad);
    foreach ([UPLOAD_ORDNER, GESCHUETZT_ORDNER] as $ordner) {
        $o = realpath($ordner);
        if ($echt && $o && str_starts_with($echt, $o . DIRECTORY_SEPARATOR)) {
            @unlink($echt);
            return;
        }
    }
}

/**
 * Verschiebt die Datei eines Materials zwischen öffentlich und geschützt.
 * Gibt das angepasste Material zurück.
 */
function material_zugriff_wechseln(array $alt, array $neu): array
{
    if ($neu['datei'] === '' || $alt['nur_angemeldet'] === $neu['nur_angemeldet']) {
        return $neu;
    }
    $von = material_datei_pfad($alt);
    if ($von === null || !is_file($von)) {
        return $neu;
    }
    $name = basename($von);
    if ($neu['nur_angemeldet']) {
        ordner_sicherstellen(GESCHUETZT_ORDNER);
        $ziel = GESCHUETZT_ORDNER . '/' . $name;
        $pfad = '';
    } else {
        ordner_sicherstellen(UPLOAD_ORDNER . '/materialien');
        $ziel = UPLOAD_ORDNER . '/materialien/' . $name;
        $pfad = 'uploads/materialien/' . $name;
    }
    if (@rename($von, $ziel)) {
        $neu['datei'] = $name;
        $neu['datei_pfad'] = $pfad;
    } else {
        // Verschieben ging nicht: Zugriff bleibt wie bisher, damit Datei und Eintrag zusammenpassen
        $neu['nur_angemeldet'] = $alt['nur_angemeldet'];
    }
    return $neu;
}

/**
 * Nimmt eine hochgeladene Materialdatei an.
 * Rückgabe: ['ok' => true, 'datei' => …, 'datei_pfad' => …, 'datei_name' => …] oder ['ok' => false, 'fehler' => …]
 */
function material_datei_annehmen(array $upload, string $id, bool $geschuetzt): array
{
    if (($upload['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
        return ['ok' => false, 'fehler' => upload_fehler_text((int) ($upload['error'] ?? UPLOAD_ERR_NO_FILE))];
    }
    $original = (string) ($upload['name'] ?? 'material');
    $endung = strtolower(pathinfo($original, PATHINFO_EXTENSION));
    if (!in_array($endung, ERLAUBTE_ENDUNGEN, true)) {
        return ['ok' => false, 'fehler' => 'Dieser Dateityp ist nicht erlaubt. Erlaubt: ' . implode(', ', ERLAUBTE_ENDUNGEN) . '.'];
    }
    if (!is_uploaded_file((string) $upload['tmp_name'])) {
        return ['ok' => false, 'fehler' => 'Die Datei konnte nicht übernommen werden.'];
    }
    $name = $id . '-' . datei_slug(pathinfo($original, PATHINFO_FILENAME)) . '.' . $endung;
    if ($geschuetzt) {
        $ordner = GESCHUETZT_ORDNER;
        $pfad = '';
    } else {
        $ordner = UPLOAD_ORDNER . '/materialien';
        $pfad = 'uploads/materialien/' . $name;
    }
    if (!ordner_sicherstellen($ordner) || !move_uploaded_file((string) $upload['tmp_name'], $ordner . '/' . $name)) {
        return ['ok' => false, 'fehler' => 'Die Datei konnte auf dem Server nicht gespeichert werden.'];
    }
    @chmod($ordner . '/' . $name, 0644);
    return ['ok' => true, 'datei' => $name, 'datei_pfad' => $pfad, 'datei_name' => $original];
}

/**
 * Speichert ein Vorschaubild (verkleinert auf max. 900 px Breite, wenn möglich als WebP).
 * Rückgabe: Pfad relativ zur Website oder null.
 */
function vorschaubild_annehmen(array $upload, string $id, ?string &$fehler = null): ?string
{
    if (($upload['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
        $fehler = upload_fehler_text((int) ($upload['error'] ?? UPLOAD_ERR_NO_FILE));
        return null;
    }
    $tmp = (string) $upload['tmp_name'];
    $info = @getimagesize($tmp);
    $endung = strtolower(pathinfo((string) ($upload['name'] ?? ''), PATHINFO_EXTENSION));
    if (!$info || !in_array($endung, BILD_ENDUNGEN, true) || !in_array($info[2], [IMAGETYPE_JPEG, IMAGETYPE_PNG, IMAGETYPE_WEBP, IMAGETYPE_GIF], true)) {
        $fehler = 'Das Vorschaubild muss ein JPG-, PNG-, WebP- oder GIF-Bild sein.';
        return null;
    }
    if (!ordner_sicherstellen(UPLOAD_ORDNER . '/vorschau')) {
        $fehler = 'Der Ordner für Vorschaubilder konnte nicht angelegt werden.';
        return null;
    }
    $basis = 'uploads/vorschau/' . $id . '-' . time();
    $ziel = __DIR__ . '/../' . $basis;

    // Mit GD verkleinern und als WebP speichern (entfernt auch Metadaten)
    if (function_exists('imagecreatefromstring') && function_exists('imagewebp')) {
        $bild = @imagecreatefromstring((string) file_get_contents($tmp));
        if ($bild) {
            $b = imagesx($bild);
            $h = imagesy($bild);
            if ($b > 900) {
                $neu = imagescale($bild, 900, (int) round($h * 900 / $b));
                if ($neu) {
                    imagedestroy($bild);
                    $bild = $neu;
                }
            }
            if (@imagewebp($bild, $ziel . '.webp', 82)) {
                imagedestroy($bild);
                return $basis . '.webp';
            }
            imagedestroy($bild);
        }
    }
    // Sonst unverändert übernehmen (Bild wurde oben geprüft)
    $endung = image_type_to_extension($info[2], false);
    $endung = $endung === 'jpeg' ? 'jpg' : $endung;
    if (move_uploaded_file($tmp, $ziel . '.' . $endung)) {
        return $basis . '.' . $endung;
    }
    $fehler = 'Das Vorschaubild konnte nicht gespeichert werden.';
    return null;
}

/** Versucht, aus der ersten Seite eines PDFs ein Vorschaubild zu machen (nur wenn der Server das kann). */
function vorschaubild_aus_pdf(string $pdf, string $id): ?string
{
    if (!class_exists('Imagick') || !is_file($pdf) || !ordner_sicherstellen(UPLOAD_ORDNER . '/vorschau')) {
        return null;
    }
    try {
        $im = new Imagick();
        $im->setResolution(110, 110);
        $im->readImage($pdf . '[0]');
        $im->setImageBackgroundColor('white');
        $im = $im->mergeImageLayers(Imagick::LAYERMETHOD_FLATTEN);
        $im->thumbnailImage(900, 0);
        $im->setImageFormat('webp');
        $basis = 'uploads/vorschau/' . $id . '-' . time() . '.webp';
        $im->writeImage(__DIR__ . '/../' . $basis);
        $im->clear();
        return $basis;
    } catch (Throwable $e) {
        return null;
    }
}

function vorschaubild_loeschen(string $bild): void
{
    if ($bild !== '' && str_starts_with($bild, 'uploads/vorschau/')) {
        eigene_datei_loeschen(__DIR__ . '/../' . $bild);
    }
}
