
</main>

<footer class="fuss">
  <div class="fuss-innen">
    <div class="fuss-marke">
      <img src="assets/bilder/logo-192.webp" width="64" height="64" alt="">
      <div>
        <strong>fabulous teaching</strong>
        <span>Ideen und Materialien für den Unterricht</span>
      </div>
    </div>
    <nav aria-label="Fusszeile">
      <ul>
        <li><a href="index.php">Home</a></li>
        <li><a href="unterrichtsmaterialien.php">Unterrichtsmaterialien</a></li>
        <li><a href="raetsellabor.php">Rätsellabor</a></li>
        <li><a href="lerntools.php">Lerntools</a></li>
        <li><a href="uebungslabor.php">Übungslabor</a></li>
        <li><a href="<?= angemeldet() ? 'bereich.php' : 'login.php' ?>"><?= angemeldet() ? 'Mein Bereich' : 'Login' ?></a></li>
      </ul>
    </nav>
    <a class="fuss-insta" href="<?= e(INSTAGRAM_URL) ?>" target="_blank" rel="noopener">
      <span class="fuss-insta-symbol"><?= icon('instagram') ?></span>
      <span><small>Folge mir auf Instagram</small>@fabulous_teaching</span>
    </a>
  </div>
  <p class="fuss-copy">© <?= date('Y') ?> fabulousteaching.ch</p>
</footer>
</body>
</html>
