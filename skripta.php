<?php
$greske = [];
$naslov = '';
$sazetak = '';
$tekst = '';
$kategorija = '';
$arhiva = false;
$putanjaSlike = '';
$poslanaForma = $_SERVER['REQUEST_METHOD'] === 'POST';

if ($poslanaForma) {
    $naslov = trim($_POST['naslov'] ?? '');
    $sazetak = trim($_POST['sazetak'] ?? '');
    $tekst = trim($_POST['tekst'] ?? '');
    $kategorija = trim($_POST['kategorija'] ?? '');
    $arhiva = isset($_POST['arhiva']);

    if ($naslov === '') {
        $greske[] = 'Naslov je obavezan.';
    }

    if ($sazetak === '') {
        $greske[] = 'Kratki sažetak je obavezan.';
    }

    if ($tekst === '') {
        $greske[] = 'Tekst vijesti je obavezan.';
    }

    if (!in_array($kategorija, ['Hrvatska', 'Svijet'], true)) {
        $greske[] = 'Potrebno je odabrati kategoriju.';
    }

    if (!isset($_FILES['slika']) || $_FILES['slika']['error'] !== UPLOAD_ERR_OK) {
        $greske[] = 'Potrebno je odabrati sliku.';
    } else {
        $izvorniNaziv = basename($_FILES['slika']['name']);
        $ekstenzija = strtolower(pathinfo($izvorniNaziv, PATHINFO_EXTENSION));
        $dozvoljeneEkstenzije = ['jpg', 'jpeg', 'png', 'webp'];

        if (!in_array($ekstenzija, $dozvoljeneEkstenzije, true)) {
            $greske[] = 'Dozvoljene su samo JPEG, PNG i WebP slike.';
        } elseif ($_FILES['slika']['size'] > 5 * 1024 * 1024) {
            $greske[] = 'Slika ne smije biti veća od 5 MB.';
        }

        if ($greske === []) {
            $mapaSlika = 'assets/uploads/';

            if (!is_dir($mapaSlika)) {
                mkdir($mapaSlika, 0755, true);
            }

            $noviNaziv = uniqid('vijest_') . '.' . $ekstenzija;
            $putanjaSlike = $mapaSlika . $noviNaziv;

            if (!move_uploaded_file($_FILES['slika']['tmp_name'], $putanjaSlike)) {
                $greske[] = 'Slika nije uspješno spremljena.';
                $putanjaSlike = '';
            }
        }
    }
}

$uspjesno = $poslanaForma && $greske === [];
?>
<!DOCTYPE html>
<html lang="hr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Prikaz vijesti unesene kroz Newsweek formu.">
    <title><?= $uspjesno ? htmlspecialchars($naslov, ENT_QUOTES, 'UTF-8') : 'Obrada vijesti' ?> | Newsweek</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <div class="site-wrapper">
        <header class="site-header">
            <div class="masthead">
                <time datetime="2026-06-19">Petak, 19. lipnja 2026.</time>
                <a class="logo" href="index.html" aria-label="Newsweek početna stranica">Newsweek</a>
            </div>
            <nav class="main-navigation" aria-label="Glavna navigacija">
                <a href="index.html">Početna</a>
                <a href="index.html#hrvatska">Hrvatska</a>
                <a href="index.html#svijet">Svijet</a>
                <a href="unos.html">Unos</a>
                <a href="#" aria-disabled="true" title="Dostupno u kasnijoj fazi projekta">Administracija</a>
            </nav>
        </header>

        <main class="article-content">
            <?php if (!$poslanaForma): ?>
                <section class="message-panel" aria-labelledby="message-title">
                    <h1 id="message-title">Nema poslanih podataka</h1>
                    <p>Vijest je potrebno poslati kroz formu za unos.</p>
                    <a class="button button-primary" href="unos.html">Otvori formu</a>
                </section>
            <?php elseif (!$uspjesno): ?>
                <section class="message-panel message-panel-error" aria-labelledby="error-title">
                    <h1 id="error-title">Vijest nije spremljena</h1>
                    <p>Ispravite sljedeće pogreške i ponovno ispunite formu:</p>
                    <ul>
                        <?php foreach ($greske as $greska): ?>
                            <li><?= htmlspecialchars($greska, ENT_QUOTES, 'UTF-8') ?></li>
                        <?php endforeach; ?>
                    </ul>
                    <a class="button button-primary" href="unos.html">Povratak na formu</a>
                </section>
            <?php else: ?>
                <article class="article-detail">
                    <header class="article-header">
                        <p class="article-category"><?= htmlspecialchars($kategorija, ENT_QUOTES, 'UTF-8') ?></p>
                        <h1><?= htmlspecialchars($naslov, ENT_QUOTES, 'UTF-8') ?></h1>
                        <time datetime="<?= date('Y-m-d') ?>"><?= date('d. m. Y.') ?></time>
                    </header>

                    <figure class="article-figure">
                        <img src="<?= htmlspecialchars($putanjaSlike, ENT_QUOTES, 'UTF-8') ?>" alt="<?= htmlspecialchars($naslov, ENT_QUOTES, 'UTF-8') ?>">
                        <figcaption>Slika prenesena kroz formu za unos vijesti.</figcaption>
                    </figure>

                    <p class="category-label"><?= htmlspecialchars($kategorija, ENT_QUOTES, 'UTF-8') ?></p>

                    <?php if ($arhiva): ?>
                        <p class="archive-notice">Vijest je označena za arhivu i neće se prikazivati na naslovnici.</p>
                    <?php endif; ?>

                    <div class="article-body">
                        <p class="lead"><?= htmlspecialchars($sazetak, ENT_QUOTES, 'UTF-8') ?></p>
                        <p><?= nl2br(htmlspecialchars($tekst, ENT_QUOTES, 'UTF-8')) ?></p>
                    </div>
                </article>
            <?php endif; ?>
        </main>

        <footer class="site-footer">
            <p>&copy; 2026 Newsweek</p>
            <p>Mia Stanković · <a href="mailto:msatnkovi@tvz.hr">[msatnkovi@tvz.hr]</a></p>
        </footer>
    </div>
</body>
</html>
