<?php
require_once 'connect.php';
require_once 'auth.php';

$pageTitle = $pageTitle ?? 'Newsweek';
$navigationCategories = mysqli_query($dbc, 'SELECT id, ime FROM kategorije ORDER BY id');
?>
<!DOCTYPE html>
<html lang="hr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Newsweek portal s vijestima iz Hrvatske i svijeta.">
    <title><?= htmlspecialchars($pageTitle, ENT_QUOTES, 'UTF-8') ?> | Newsweek</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <div class="site-wrapper">
        <header class="site-header">
            <div class="masthead">
                <time datetime="<?= date('Y-m-d') ?>"><?= date('d. m. Y.') ?></time>
                <a class="logo" href="index.php" aria-label="Newsweek početna stranica">Newsweek</a>
            </div>
            <nav class="main-navigation" aria-label="Glavna navigacija">
                <a href="index.php">Početna</a>
                <?php while ($navigationCategory = mysqli_fetch_assoc($navigationCategories)): ?>
                    <a href="kategorija.php?id=<?= $navigationCategory['id'] ?>">
                        <?= htmlspecialchars($navigationCategory['ime'], ENT_QUOTES, 'UTF-8') ?>
                    </a>
                <?php endwhile; ?>
                <a href="administracija.php">Administracija</a>
                <?php if (korisnikJeAdministrator()): ?>
                    <a href="unos.php">Unos</a>
                <?php endif; ?>
                <?php if (korisnikJePrijavljen()): ?>
                    <a href="odjava.php">Odjava</a>
                <?php else: ?>
                    <a href="prijava.php">Prijava</a>
                    <a href="registracija.php">Registracija</a>
                <?php endif; ?>
            </nav>
        </header>
