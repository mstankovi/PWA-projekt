<?php
require_once 'connect.php';

$articleId = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$articleId) {
    header('Location: index.php');
    exit;
}

$articleQuery = 'SELECT v.naslov, v.sazetak, v.tekst, v.slika_url, v.datum, k.ime AS kategorija
                 FROM vijesti v
                 JOIN kategorije k ON k.id = v.idKategorija
                 WHERE v.id = ? AND v.arhiva = 0';
$articleStatement = mysqli_prepare($dbc, $articleQuery);
mysqli_stmt_bind_param($articleStatement, 'i', $articleId);
mysqli_stmt_execute($articleStatement);
$articleResult = mysqli_stmt_get_result($articleStatement);
$article = mysqli_fetch_assoc($articleResult);

if (!$article) {
    header('Location: index.php');
    exit;
}

$pageTitle = $article['naslov'];
require 'header.php';
?>
<main class="article-content">
    <article class="article-detail">
        <header class="article-header">
            <p class="article-category"><?= htmlspecialchars($article['kategorija'], ENT_QUOTES, 'UTF-8') ?></p>
            <h1><?= htmlspecialchars($article['naslov'], ENT_QUOTES, 'UTF-8') ?></h1>
            <time datetime="<?= date('Y-m-d', strtotime($article['datum'])) ?>">
                <?= date('d. m. Y.', strtotime($article['datum'])) ?>
            </time>
        </header>

        <figure class="article-figure">
            <img src="<?= htmlspecialchars($article['slika_url'], ENT_QUOTES, 'UTF-8') ?>"
                 alt="<?= htmlspecialchars($article['naslov'], ENT_QUOTES, 'UTF-8') ?>">
            <figcaption>Slika se koristi u svrhu izrade studentskog projekta.</figcaption>
        </figure>

        <p class="category-label"><?= htmlspecialchars($article['kategorija'], ENT_QUOTES, 'UTF-8') ?></p>

        <div class="article-body">
            <p class="lead"><?= htmlspecialchars($article['sazetak'], ENT_QUOTES, 'UTF-8') ?></p>
            <p><?= nl2br(htmlspecialchars($article['tekst'], ENT_QUOTES, 'UTF-8')) ?></p>
        </div>
    </article>
</main>

<?php require 'footer.php'; ?>
