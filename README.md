# fabulousteaching.ch – Lehrerwebseite

Grundlayout der Website mit Startseite («Über mich»), Unterrichtsmaterialien,
Rätsellabor und Login-Bereich. Farben und Stil kommen aus dem Logo
(Aquarell-Orange, Labortisch-Anthrazit, Holzbraun, bunte Kolben).

## Seiten

| Datei | Inhalt |
|---|---|
| `index.php` | Home: Begrüssung, **Über mich** (Foto + Text), Überblick, Materialvorschau |
| `unterrichtsmaterialien.php` | Materialien mit Fach-Kacheln, Zyklus-Filter und Suche |
| `raetsellabor.php` | Infoseite mit Knopf zu https://raetsellabor.fabulousteaching.ch |
| `login.php` | Anmeldung (beim allerersten Aufruf: eigenes Konto einrichten) |
| `bereich.php` | «Mein Bereich» nach dem Login: geschützte Materialien, Abmelden |
| `download.php` | liefert geschützte Dateien nur an angemeldete Personen aus |

Gemeinsame Teile liegen in `inc/` (Kopf mit Navigation, Fusszeile, Symbole),
die Gestaltung in `assets/css/stil.css`.

## Automatisch veröffentlichen (GitHub → Infomaniak)

Jede Änderung auf dem Branch `main` wird automatisch per verschlüsseltem FTP
(FTPS) auf fabulousteaching.ch hochgeladen
(`.github/workflows/veroeffentlichen.yml`). Auf dem Server wird dabei **nichts
gelöscht**: Dein Konto, geschützte Dateien und andere Ordner (z. B. ein
Rätsellabor-Ordner) bleiben unangetastet.

### Einmalig einrichten

1. **Infomaniak:** Im Manager beim Webhosting unter «FTP / SSH» ein FTP-Konto
   anlegen (oder ein bestehendes nehmen). Notieren:
   - Server-Adresse (sieht etwa so aus: `xxxx.ftp.infomaniak.com`)
   - Benutzername und Passwort
2. **Web-Ordner prüfen:** In den Einstellungen der Website *fabulousteaching.ch*
   steht, in welchem Ordner sie liegt – meist `/sites/fabulousteaching.ch`.
   Ist es ein anderer Ordner, siehe Schritt 3 «Variable».
3. **GitHub:** Im Repository *Settings → Secrets and variables → Actions*:
   - unter **Secrets** → *New repository secret* drei Einträge anlegen:
     `FTP_SERVER` (die Server-Adresse), `FTP_USERNAME`, `FTP_PASSWORD`
   - nur falls der Web-Ordner nicht `/sites/fabulousteaching.ch` ist: unter
     **Variables** → `FTP_ORDNER` mit dem richtigen Ordner anlegen
     (wenn das FTP-Konto direkt im Web-Ordner startet: `.`)
4. **Ersten Upload starten:** Im Repository *Actions → «Auf Infomaniak
   veröffentlichen» → Run workflow*. Nach ca. 1 Minute ist die Seite online.
5. **Sofort** `https://fabulousteaching.ch/login.php` öffnen und dein Konto
   einrichten. Danach ist die Einrichtung gesperrt.
6. Im Infomaniak-Manager das SSL-Zertifikat aktivieren und «HTTPS erzwingen»
   einschalten; PHP 8.1 oder neuer einstellen (Standard bei neuen Sites).

Gut zu wissen:
- Dateien mit gleichem Namen auf dem Server werden überschrieben. Eine alte
  `index.html` stört nicht – `index.php` hat Vorrang.
- Wird eine Datei aus dem Repository gelöscht, bleibt sie auf dem Server
  liegen. Bei Bedarf per FTP von Hand entfernen.
- Schlägt ein Upload fehl, zeigt GitHub unter *Actions* den Grund
  (z. B. «Login failed» = Zugangsdaten falsch, «No such file or directory» = Ordner falsch).

### Ohne GitHub (von Hand)

Den **Inhalt** dieses Repositorys (ohne `.git`, `.github`, `README.md`) per FTP
(z. B. FileZilla) oder Web-FTP in den Web-Ordner laden – so, dass `index.php`
direkt darin liegt. Die versteckten `.htaccess`-Dateien unbedingt mitkopieren
(sie sperren `inc/` und `daten/`).

## Inhalte anpassen

- **Über-mich-Text:** in `index.php` beim Kommentar `TODO` den Platzhalter ersetzen.
- **Foto:** als `assets/bilder/ueber-mich.jpg` hochladen (Hochformat, etwa 800 × 1000 px).
  Es erscheint dann automatisch statt des Platzhalters.
- **Materialien:** alles in `daten/materialien.php` – oben in der Datei ist
  beschrieben, welche Felder es gibt. Die Einträge mit `'beispiel' => true`
  sind Platzhalter und können gelöscht werden.
  - Öffentliche Dateien in den Ordner `downloads/` legen.
  - Dateien nur für Angemeldete (`'nur_angemeldet' => true`) in `daten/dateien/` legen.
- **Fächer und Farben:** ebenfalls in `daten/materialien.php` unter `'faecher'`.
- **Adresse des Rätsellabors:** in `inc/start.php` (`RAETSELLABOR_URL`).
- **Menüpunkte:** in `inc/kopf.php` (Liste `$menue`).

## Konten

- Das erste Konto legst du über `login.php` selbst an. Gespeichert wird es in
  `daten/benutzer.php` (Passwort nur als sicherer Hash). Diese Datei kommt nicht ins Git.
- Weitere Konten: in `daten/benutzer.php` eine Zeile `'name' => 'hash',` ergänzen.
  Den Hash erzeugt `php -r 'echo password_hash("NeuesPasswort", PASSWORD_DEFAULT);'`.
- Passwort vergessen: `daten/benutzer.php` per FTP löschen und das Konto neu einrichten.
- Nach 8 Fehlversuchen innert 15 Minuten wird der Login für dieses Gerät kurz gesperrt.

## Lokal ansehen

Mit installiertem PHP im Hauptordner dieses Repositorys:

```bash
php -S localhost:8000
```

Dann http://localhost:8000 öffnen.

## Datenschutz

Schriften (Fredoka, Nunito – SIL Open Font License) und Symbole (Lucide, ISC)
liegen auf dem eigenen Server. Es werden keine Google-Fonts oder andere
externen Dienste geladen. Ein Sitzungs-Cookie entsteht nur auf der Login-Seite
und nach dem Anmelden – normale Besucherinnen und Besucher erhalten keines.
