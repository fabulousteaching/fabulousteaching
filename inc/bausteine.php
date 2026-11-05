<?php
/**
 * Wiederverwendbare Bausteine: Aquarell-Klecks und Materialkarte.
 */

/** Oranger Aquarell-Klecks wie im Logo (als Hintergrund hinter Bildern). */
function klecks(string $id = 'k1', string $farbe = '#f7a15c'): string
{
    $id = e($id);
    $farbe = e($farbe);
    return <<<SVG
<svg class="klecks" viewBox="0 0 400 400" aria-hidden="true" focusable="false">
  <defs>
    <filter id="{$id}-f" x="-25%" y="-25%" width="150%" height="150%">
      <feTurbulence type="fractalNoise" baseFrequency="0.012" numOctaves="3" seed="7" result="n"/>
      <feDisplacementMap in="SourceGraphic" in2="n" scale="52" xChannelSelector="R" yChannelSelector="G" result="d"/>
      <feGaussianBlur in="d" stdDeviation="1.2"/>
    </filter>
    <filter id="{$id}-f2" x="-25%" y="-25%" width="150%" height="150%">
      <feTurbulence type="fractalNoise" baseFrequency="0.018" numOctaves="2" seed="21" result="n"/>
      <feDisplacementMap in="SourceGraphic" in2="n" scale="38" xChannelSelector="G" yChannelSelector="R"/>
    </filter>
    <radialGradient id="{$id}-g" cx="45%" cy="40%" r="62%">
      <stop offset="0" stop-color="#fdd2ab"/>
      <stop offset=".7" stop-color="{$farbe}"/>
      <stop offset="1" stop-color="#f3934b"/>
    </radialGradient>
  </defs>
  <g filter="url(#{$id}-f)" opacity=".45">
    <path fill="{$farbe}" d="M205 22c70 4 128 36 150 94s16 112-14 160-84 96-152 94-118-34-146-88S16 166 42 112 135 18 205 22z"/>
  </g>
  <g filter="url(#{$id}-f2)">
    <path fill="url(#{$id}-g)" d="M200 48c60 0 112 26 134 78s12 100-12 144-72 86-132 84-104-30-130-78S30 168 52 120 140 48 200 48z"/>
  </g>
</svg>
SVG;
}

/**
 * Eine Materialkarte.
 * $m = ein Eintrag aus daten/materialien.php
 */
function material_karte(array $m, array $faecher, array $zyklen): string
{
    $m = material_normalisieren($m);
    $schluessel = array_values(array_filter($m['faecher'], fn($k) => isset($faecher[$k])));
    $fach   = $schluessel ? $faecher[$schluessel[0]] : ['name' => 'Allgemein', 'icon' => 'file-text', 'farbe' => '#f7a15c'];
    $fachText = fach_namen($m, $faecher) ?: $fach['name'];
    $stufen = $m['zyklen'];
    $schutz = $m['nur_angemeldet'];
    $wer    = angemeldet();

    // Suchtext für die Filterfunktion (inkl. Schlagwörter)
    $suche = mb_strtolower(implode(' ', [
        $m['titel'], $m['beschreibung'], $fachText, $m['typ'], implode(' ', $m['schlagwoerter']),
    ]), 'UTF-8');

    // Knopf: Link, Download, Login oder "folgt"
    if (!RAETSELLABOR_OFFEN && $m['link'] !== '' && str_starts_with($m['link'], RAETSELLABOR_URL)) {
        $knopf = '<span class="knopf knopf-klein aus" aria-disabled="true">' . icon('lock') . ' Bald verfügbar</span>';
    } elseif (!empty($m['link'])) {
        $knopf = '<a class="knopf knopf-klein" href="' . e($m['link']) . '" target="_blank" rel="noopener">Öffnen ' . icon('external-link') . '</a>';
    } elseif (empty($m['datei'])) {
        $knopf = '<span class="knopf knopf-klein aus" aria-disabled="true">Folgt bald</span>';
    } elseif ($schutz && !$wer) {
        $weiter = rawurlencode($_SERVER['REQUEST_URI'] ?? '/unterrichtsmaterialien.php');
        $knopf = '<a class="knopf knopf-klein knopf-hell" href="login.php?weiter=' . $weiter . '">' . icon('lock') . ' Anmelden zum Download</a>';
    } elseif ($schutz) {
        $knopf = '<a class="knopf knopf-klein" href="download.php?datei=' . rawurlencode((string) $m['datei']) . '">' . icon('download') . ' Herunterladen</a>';
    } else {
        $url = $m['datei_pfad'] !== '' ? implode('/', array_map('rawurlencode', explode('/', $m['datei_pfad']))) : 'downloads/' . rawurlencode($m['datei']);
        $knopf = '<a class="knopf knopf-klein" href="' . e($url) . '" download>' . icon('download') . ' Herunterladen</a>';
    }

    $bild = !empty($m['bild'])
        ? '<img src="' . e($m['bild']) . '" alt="" loading="lazy">'
        : icon($fach['icon']);

    $links = '';
    if (!empty($m['neu']))      { $links .= '<span class="badge badge-neu">Neu</span>'; }
    if (!empty($m['beispiel'])) { $links .= '<span class="badge badge-beispiel">Beispiel</span>'; }
    $rechts = $schutz ? '<span class="badge" title="Nur nach dem Login">' . icon($wer ? 'lock-open' : 'lock') . ' Login</span>' : '';

    $meta = '';
    if (!empty($m['typ']))    { $meta .= '<li>' . e($m['typ']) . '</li>'; }
    if (!empty($m['umfang'])) { $meta .= '<li>' . e($m['umfang']) . '</li>'; }
    // Der Zyklus wird beim Hochladen erfasst, aber auf der Karte nicht angezeigt.

    ob_start(); ?>
<article class="karte" style="--f:<?= e($fach['farbe']) ?>" data-fach="<?= e(implode(' ', $schluessel)) ?>" data-zyklen="<?= e(implode(' ', $stufen)) ?>" data-suche="<?= e($suche) ?>">
  <div class="karte-bild">
    <?= $bild ?>
    <?php if ($links): ?><div class="abzeichen links"><?= $links ?></div><?php endif; ?>
    <?php if ($rechts): ?><div class="abzeichen rechts"><?= $rechts ?></div><?php endif; ?>
  </div>
  <div class="karte-text">
    <span class="karte-fach"><?= e($fachText) ?></span>
    <h3><?= e($m['titel'] ?? '') ?></h3>
    <p><?= e($m['beschreibung'] ?? '') ?></p>
    <?php if ($meta): ?><ul class="karte-meta"><?= $meta ?></ul><?php endif; ?>
    <?= $knopf ?>
  </div>
</article>
<?php
    return (string) ob_get_clean();
}

/**
 * Logo des Rätsellabors («Puzzle-Kolben»), wie auf raetsellabor.fabulousteaching.ch.
 * Farben: Kolben #F28C38, Umriss schwarz.
 */
function raetsellabor_kolben(string $klasse = 'kolben'): string
{
    $f = '#F28C38';
    return '<svg class="' . e($klasse) . '" viewBox="0 4 100 160" aria-hidden="true" focusable="false">'
        . '<circle cx="34" cy="21" r="5.2" fill="' . $f . '"/><circle cx="42" cy="13" r="5.6" fill="' . $f . '"/>'
        . '<circle cx="54" cy="12" r="5.8" fill="' . $f . '"/><circle cx="64" cy="19" r="5.4" fill="' . $f . '"/>'
        . '<circle cx="66" cy="31" r="5" fill="' . $f . '"/><circle cx="60" cy="41" r="4.6" fill="' . $f . '"/>'
        . '<circle cx="53" cy="49" r="4.2" fill="' . $f . '"/><circle cx="50" cy="59" r="3.8" fill="' . $f . '"/>'
        . '<circle cx="50" cy="72" r="3.5" fill="' . $f . '"/><circle cx="51" cy="94" r="2.4" fill="' . $f . '"/>'
        . '<circle cx="48.5" cy="103" r="1.8" fill="' . $f . '"/>'
        . '<path d="M27.4 132 H42 C44 129 40 124 44.5 121 C47 119.3 53 119.3 55.5 121 C60 124 56 129 58 132 H72.6 L84 152 Q88 160 79 160 H21 Q12 160 16 152 Z" fill="' . $f . '"/>'
        . '<path d="M40 82 V110 L16 152 Q12 160 21 160 H79 Q88 160 84 152 L60 110 V82" fill="none" stroke="#000" stroke-width="5" stroke-linecap="round" stroke-linejoin="round"/>'
        . '<path d="M33 82 H67" fill="none" stroke="#000" stroke-width="5" stroke-linecap="round"/>'
        . '</svg>';
}
